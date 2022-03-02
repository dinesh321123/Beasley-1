<?php

/**
 * Custom Nav Menu walker
 *
 * Supports custom menu formats made available by mega-menu-admin.php
 *
 * Class GreaterMediaNavWalker
 */
class GreaterMediaNavWalker extends Walker_Nav_Menu {

	/**
	 * Keeps track of if we are currently doing a featured item menu
	 *
	 * Allows us to do stuff in methods that don't let us inspect any item properties
	 * This will normally be false
	 * If we're currently in a featured item menu it will store that menu item's ID.
	 *
	 * @var bool | integer
	 */
	public static $current_featured_item_menu = false;

	/**
	 * Keeps track of the current item's parent ID
	 * @var int
	 */
	public static $current_parent_item = 0;

	/**
	 * Keeps a count of how many items have been output for a given $current_parent_item
	 * Works in conjunction with the added item property sibling_count to determine how far we are
	 * through a given subnav. Only works 2 levels — a third level would reset the count.
	 * @var int
	 */
	public static $count = 0;

	/**
	 * Keeps track of the WordPress filters to see if they've been called/attached previously.
	 * @var bool
	 */
	static $filters_called = false;

	public function __construct() {
		if ( false === self::$filters_called ) {
			add_filter( 'wp_nav_menu_objects', array( __CLASS__, 'add_menu_item_data' ), null, 2 );
			self::$filters_called = true;
		}
	}

	/**
	 * Iterates over an array of menu items. Adds data (menu_item_parent_title) to the item object
	 * if the current item is a child.
	 *
	 * Also adds a count of how many siblings are present (siblings_count) to the item object.
	 *
	 * @param $sorted_menu_items array
	 * @param $args              array
	 *
	 * @return array
	 */
	public static function add_menu_item_data( $sorted_menu_items, $args ) {
		foreach ( $sorted_menu_items as $id => &$item ) {
			// check if the $item has a parent $item.
			if ( ! empty( $item->menu_item_parent ) ) {

				// Get the associated parent item and assign it to the new property menu_item_parent_title
				$matching_parents = (array) wp_filter_object_list( $sorted_menu_items, array( 'ID' => $item->menu_item_parent ), 'and', 'post_title' );
				$item->menu_item_parent_title = array_shift( $matching_parents );

				// Find out how many items also have this parent and add it to the new property siblings_count
				$siblings = count( wp_filter_object_list( $sorted_menu_items,array( 'menu_item_parent' => $item->menu_item_parent ), 'and', 'ID' ) );
				$item->siblings_count = (int) $siblings;

			}
		}

		return $sorted_menu_items;
	}

	/**
	 * Formatting helper for the small previews anchor.
	 * Includes the current item's title, image, and some meta data.
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public static function format_small_previews_link( $item ) {

		$return = '';
		// Try to find an image to use.
		$src = get_post_meta( $item->object_id, 'logo_image', true );
		if ( ! empty( $src ) && $src ) {
			$return .= wp_get_attachment_image( absint( $src ) );
		} else if ( has_post_thumbnail( $item->object_id ) ) {
			$return .= get_the_post_thumbnail( $item->object_id, 'thumbnail' );
		}

		$return .= '<div class="group">';
		$return .= apply_filters( 'the_title', $item->title, $item->ID );

		// Try to find some meta text to use
		if ( 'show' === $item->object ) {
			/**
			 * @todo create helper function or do_action to keep this from failing.
			 */
			$object_id = $item->object_id;
			$days = \GreaterMedia\Shows\get_show_days( $object_id );
			$times = \GreaterMedia\Shows\get_show_times( $object_id );

			if ( ! empty( $days ) || ! empty( $times ) ) {
				$return .= '<div class="meta-text">';
				$return .= '<span class="days">' . esc_html( $days ) . '</span>';
				$return .= '<span class="times">' . esc_html( $times ) . '</span>';
				$return .= '</div>';
			}

		} else if ( 'tribe_events' === $item->object && function_exists( 'tribe_get_start_date' ) ) {
			$return .= '<span class="meta-text">' . tribe_get_start_date( $item->object_id ) . '</span>';
		}

		$return .= '</div>';

		return $return;

	}

	/**
	 * Start the element output.
	 *
	 * @see   Walker::start_el()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$format = GreaterMediaMegaMenuAdmin::get_nav_menu_format( $item->ID );
		$parent_format = GreaterMediaMegaMenuAdmin::get_nav_menu_format( $item->menu_item_parent );

		/**
		 * This is for the 'music' or Featured Item nav menu.
		 * It adds some markup if this is the first item in the sub menu.
		 *
		 * Markup goal for the subnav of a featured item nav menu:
		 * <ul class="subnav">
		 *   <li>
		 *     <ul class="header__nav-submenu--list"></ul>
		 *     <ul class="header__nav-submenu--features"></ul>
		 *   </li>
		 * </ul>
		 *
		 * To achieve this markup is complex, spanning a few different methods and class property flags.
		 * We start the nonsense here:
		 */
		if ( self::$current_featured_item_menu && self::$current_parent_item !== $item->menu_item_parent ) {
			$output .= '<li><ul class="header__nav-submenu--list">';
			self::$current_parent_item = $item->menu_item_parent;
			self::$count = 1;
		} else {
			self::$count++;
		}

		if ( $format ) {
			$classes[] = 'format-' . esc_attr( $format );
		}

		/*
		 * Setting the doing_featured_item_menu flag here kicks in different markup (see above)
		 * for sub-menu items and also allows us to build the featured items itself.
		 */
		if ( 'fi' === $format ) {
			self::$current_featured_item_menu = $item->ID;
		}

		/**
		 * Filter the CSS class(es) applied to a menu item's list item element.
		 *
		 * @since 3.0.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filter the ID applied to a menu item's list item element.
		 *
		 * @since 3.0.1
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names . '>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';

		/**
		 * Filter the HTML attributes applied to a menu item's anchor element.
		 *
		 * @since 3.6.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array  $atts   {
		 *                       The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 * @type string  $title  Title attribute.
		 * @type string  $target Target attribute.
		 * @type string  $rel    The rel attribute.
		 * @type string  $href   The href attribute.
		 * }
		 *
		 * @param object $item   The current menu item.
		 * @param array  $args   An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = ! empty( $args->before ) ? $args->before : '';
		$item_output .= '<a' . $attributes . '>';

		/**
		 * Format: Small Previews
		 * @todo check performance here, may want to query all the object_ids at once so they are cached.
		 */
		if ( 'sp' === $parent_format ) {
			$item_output .= self::format_small_previews_link( $item );
		} else {
			/** This filter is documented in wp-includes/post-template.php */
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		}

		$item_output .= '</a>';
		$item_output .= $args->after;

		/**
		 * Filter a menu item's starting output.
		 *
		 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
		 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
		 * no filter for modifying the opening and closing `<li>` for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item        Menu item data object.
		 * @param int    $depth       Depth of menu item. Used for padding.
		 * @param array  $args        An array of {@see wp_nav_menu()} arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see   Walker::end_el()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Page data object. Not used.
	 * @param int    $depth  Depth of page. Not Used.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>\n";

		// We close the menu for the featured item menu's list. But not the featured items
		if ( self::$count === $item->siblings_count && self::$current_featured_item_menu ) {
			$output .= '</ul>';
		}
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see   Walker::end_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {

		/*
		 * Actually build the featured item menu and close the submenu <li>
		 */
		if ( self::$current_featured_item_menu ):
			ob_start();

			$featured_items_query = array(
				'posts_per_page'      => 4,
				'ignore_sticky_posts' => true,
			);

			$featured_item_ids = get_post_meta( self::$current_featured_item_menu, 'gmr_music_menu', true );


			if ( ! empty( $featured_item_ids ) ) {
				$featured_items_query['post__in'] = explode( ',', $featured_item_ids );
				$featured_items_query['post_type'] = array( 'post', 'contest', 'gmr_gallery', 'gmr_album' );
				$featured_items_query['orderby'] = 'post__in';
			}

			$featured_items = new WP_Query( $featured_items_query );
			?>
			<ul class="header__nav-submenu--features">
			<?php

			while ( $featured_items->have_posts() ):
				$featured_items->the_post();


				?>
				<li>
					<a href="<?php the_permalink(); ?>">
					<div class="entry2__thumbnail format-<?php echo get_post_format(); ?>"
					     style="background-image: url(<?php bbgi_post_thumbnail_url( null, true, 500, 375 ); ?>);">
					</div>
					<p><?php the_title(); ?></p>
					</a>
				</li>
			<?php
			endwhile;
			wp_reset_postdata();
			?>
			</ul>
			</li>
			<?php

			$output .= ob_get_clean();

			// Set the flag to false now that we are not doing a featured item menu anymore.
			self::$current_featured_item_menu = false;

		endif;

		$indent = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

}
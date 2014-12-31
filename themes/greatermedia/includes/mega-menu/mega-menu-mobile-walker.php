<?php
/**
 * Custom Nav Menu walker
 *
 * Supports custom menu formats made available by mega-menu-admin.php
 *
 * Class GreaterMediaNavWalker
 */
class GreaterMediaMobileNavWalker extends Walker_Nav_Menu {

	/**
	 * This number iterates for each item until $current_depth changes at which point it resets to 0
	 *
	 * So if we want conditional code for the first item in a sub-menu, check that
	 * $item_count_in_sub_menu is 0 and $currrent_depth is 1.
	 *
	 * @var integer
	 */
	static $item_count_in_sub_menu = 0;

	/**
	 * Current depth. This is set to the $depth of each el as it passes.
	 * @var int
	 */
	static $current_depth = 0;


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

		// Iterate item count or reset if needed
		if ( $depth != 0 ) {
			self::$item_count_in_sub_menu ++;
		} else {
			self::$item_count_in_sub_menu = 0;
		}

		if ( 1 === self::$item_count_in_sub_menu && $depth === 1 ) {
			$output .= '<li class="mobile-menu-submenu-header">';
			$output .= '<a href="#" class="mobile-menu-submenu-back-link icon-arrow-prev">Back</a>';
			$output .= '<span class="mobile-menu-submenu-heading">' . esc_html( $item->menu_item_parent_title ) . '</span>';
			$output .= '</li>';
		}

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$format = GreaterMediaMegaMenuAdmin::get_nav_menu_format( $item->ID );

		if ( $format ) {
			$classes[] = 'format-' . esc_attr( $format );
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

		$item_output = $args->before;
		$item_output .= '<a' . $attributes . '>';
		/** This filter is documented in wp-includes/post-template.php */
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		/* if an item has sub-navigation, expose it via an arrow */
		if ( in_array( 'menu-item-has-children', $item->classes ) ) {
			$item_output .= '<a href="#" class="show-subnavigation icon-arrow-next">';
			$item_output .= '<span class="screen-reader-text">Expand Sub-Navigation</span>';
			$item_output .= '</a>';
		}


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

}

add_action( 'after_setup_theme', function() {

	add_filter( 'wp_nav_menu_objects', function( $sorted_menu_items, $args ) {

		foreach( $sorted_menu_items as $id => &$item ) {
			// check if the $item has a parent $item.
			if ( !empty( $item->menu_item_parent ) ) {

				// Get the associated parent item
				$matching_parents = (array) wp_filter_object_list( $sorted_menu_items, array( 'ID' => $item->menu_item_parent ), 'and', 'post_title' );
				$item->menu_item_parent_title = array_shift( $matching_parents );
			}
		}

		return $sorted_menu_items;

	}, null, 2);

});

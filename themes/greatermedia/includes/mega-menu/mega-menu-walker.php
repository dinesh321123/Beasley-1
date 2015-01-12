<?php

/**
 * Custom Nav Menu walker
 *
 * Supports custom menu formats made available by mega-menu-admin.php
 *
 * Class GreaterMediaNavWalker
 */
class GreaterMediaNavWalker extends Walker_Nav_Menu {

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

}
<?php

class Filterable_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {

	/**
	 * Injects our custom fields into the default core navigation menu items.
	 *
	 * @param string $output
	 * @param object $item
	 * @param int    $depth
	 * @param array  $args
	 * @param int    $id
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$item_output = '';
		parent::start_el( $item_output, $item, $depth, $args, $id );

		ob_start();
		do_action( 'wp_nav_menu_item_custom_fields', $item->ID, $item, $depth, $args ); // conform to https://core.trac.wordpress.org/attachment/ticket/14414/nav_menu_custom_fields.patch
		$extra = ob_get_clean();

		$position = '<fieldset class="field-move';
		$output .= str_replace( $position, $extra . $position, $item_output );
	}

}

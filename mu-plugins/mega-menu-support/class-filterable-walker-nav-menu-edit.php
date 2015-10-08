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

		$position = '<p class="field-move';

		$extra = $this->get_fields( $item, $depth, $args, $id );

		$output .= str_replace( $position, $extra . $position, $item_output );
	}

	/**
	 * Calls the action that allows inserting custom fields to the nav menu items in the admin screens.
	 *
	 * @param       $item
	 * @param       $depth
	 * @param array $args
	 * @param int   $id
	 *
	 * @return string
	 */
	protected function get_fields( $item, $depth, $args = array(), $id = 0 ) {
		ob_start();

		// conform to https://core.trac.wordpress.org/attachment/ticket/14414/nav_menu_custom_fields.patch
		do_action( 'wp_nav_menu_item_custom_fields', $item->ID, $item, $depth, $args );

		return ob_get_clean();
	}
}


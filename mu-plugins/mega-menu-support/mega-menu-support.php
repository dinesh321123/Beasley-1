<?php
/**
 * Extends the default core *edit* nav menu walker with a few custom methods to add actions for customizing the admin
 * interface with additional fields.
 *
 * Hook into the `wp_nav_menu_item_custom_fields` action to add/render fields on a nav menu item
 * This action will take up to 4 args: $item_id, $item, $depth, $args
 *
 * Hook into the `wp_update_nav_menu_item` action to save any data related to the custom fields rendered in the previous
 * action.
 *
 * IMPORTANT: If you notice certain nav manu fields getting chopped off, check the server/php's post max_vars settings.
 * While this is possible to hit with no customizations, having more fields makes it more likely the default limit
 * will be hit.
 */
add_filter( 'wp_edit_nav_menu_walker', function( $class ) {
	include_once __DIR__ . '/class-filterable-walker-nav-menu-edit.php';

	if ( class_exists( 'Filterable_Walker_Nav_Menu_Edit' ) ) {
		$class = 'Filterable_Walker_Nav_Menu_Edit';
	}

	return $class;
});

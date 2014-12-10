<?php

/*
Plugin Name: Edit Flow Hotfixes
Description: Patches for Edit Flow to work around incompatibliities
*/

define( 'GREATER_MEDIA_EDIT_FLOW_HOTFIXES_URL', plugin_dir_url( __FILE__ ) );
define( 'GREATER_MEDIA_EDIT_FLOW_HOTFIXES_PATH', dirname( __FILE__ ) );

/**
 * Before adding a hotfix (monkey patch) to this plugin, please think about whether the issue you're trying to fix
 * can/should be solved in Edit Flow itself. If so, submit a Pull Request to Edit Flow on Github
 * (https://github.com/Automattic/Edit-Flow) and reference that PR in a comment here. That way we can remove patches
 * when they're no longer needed.
 */

/**
 * Tighten the scope of search for .editing elements in the page
 * https://github.com/Automattic/Edit-Flow/pull/296
 */
function edit_flow_hotfix_296() {

	// Make sure Edit Flow is present
	if ( ! defined( 'EDIT_FLOW_VERSION' ) ) {
		return;
	}

	// Dequeue the stock calendar.js
	wp_deregister_script( 'edit-flow-calendar-js' );

	// Dependencies defined in Edit Flow's calendar.php
	$js_libraries = array(
		'jquery',
		'jquery-ui-core',
		'jquery-ui-sortable',
		'jquery-ui-draggable',
		'jquery-ui-droppable',
	);

	// Enqueue an alternate calendar.js
	wp_enqueue_script( 'edit-flow-calendar-js', GREATER_MEDIA_EDIT_FLOW_HOTFIXES_URL . 'js/calendar.js', $js_libraries, EDIT_FLOW_VERSION, true );

	// "Localize" the script with configuration options. See calendar.php.
	$create_post_cap = apply_filters( 'ef_calendar_create_post_cap', 'edit_posts' );
	$ef_cal_js_params = array( 'can_add_posts' => current_user_can( $create_post_cap ) ? 'true' : 'false' );
	wp_localize_script( 'edit-flow-calendar-js', 'ef_calendar_params', $ef_cal_js_params );

}

add_action( 'admin_enqueue_scripts', 'edit_flow_hotfix_296', 100 );
<?php

add_action( 'admin_enqueue_scripts', 'block_edit_flow_notification_js', 99 );

function block_edit_flow_notification_js() {
	if ( is_admin() && get_current_screen()->id === 'nav-menus' ) {
		wp_dequeue_script( 'edit-flow-notifications-js' );
	}
}

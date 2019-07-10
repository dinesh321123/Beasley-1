<?php

function contest_failure_admin_notice() {
	$class   = 'notice notice-error';
	$message = 'Due to a critical site issue, all contests have been unpublished and are in draft status.  Please republish active contests ASAP';

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

add_action( 'admin_notices', 'contest_failure_admin_notice' );

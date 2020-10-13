<?php

// https://support.theeventscalendar.com/719006-Fixing-HTTP-404-errors
function tribe_attachment_404_fix () {
	if ( class_exists( 'Tribe__Events__Main')) {
		remove_action( 'init', array( Tribe__Events__Main::instance(), 'init' ), 10 );
		add_action( 'init', array( Tribe__Events__Main::instance(), 'init' ), 1 );
	}
}

add_action( 'after_setup_theme', 'tribe_attachment_404_fix' );



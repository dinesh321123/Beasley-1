<?php 

add_filter( 'tribe_events_event_schedule_details_inner', 'ee_update_event_schedule_details' );

if ( ! function_exists( 'ee_update_event_schedule_details' ) ) :
	function ee_update_event_schedule_details( $details ) {
		$replace = '<span class="sep"></span>';

		$details = preg_replace( '/\s*(,|\@)\s*/', $replace, $details );

		return $details;
	}
endif;

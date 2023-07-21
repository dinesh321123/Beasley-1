<?php 

add_filter( 'tribe_events_event_schedule_details_inner', 'ee_update_event_schedule_details' );
add_action('pre_get_posts', 'tribe_events_archive_posts_per_page');

if ( ! function_exists( 'ee_update_event_schedule_details' ) ) :
	function ee_update_event_schedule_details( $details ) {
		$replace = '<span class="sep"></span>';

		$details = preg_replace( '/\s*(,|\@)\s*/', $replace, $details );

		return $details;
	}
endif;

if ( ! function_exists( 'tribe_events_archive_posts_per_page' ) ) :
	function tribe_events_archive_posts_per_page($query) {
		if (is_post_type_archive('tribe_events')) { // Replace 'triue-event' with your custom post type slug.
			$query->set('posts_per_page', tribe_get_option( 'postsPerPage')); // Set the number of posts you want to display per page.
		}
	}
endif;

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

if ( ! function_exists( 'ee_get_tribe_events_max_num_pages' ) ) :
	function ee_get_tribe_events_max_num_pages() {
	
		$args = array(
			'post_type' => 'tribe_events', // Adjust the post type if needed.
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => '_EventHideFromUpcoming',
					'compare' => 'NOT EXISTS',
				)
			),
			'tax_query' => array(
				'relation' => 'AND',
			),
			'date_query' => array(
				'relation' => 'AND',
			),
			'orderby' => 'event_date',
			'order' => 'ASC',
			'posts_per_page' => '-1',
		);
	
		$filtered_events_query = new WP_Query( $args );

		return $filtered_events_query->max_num_pages;

	}
endif;
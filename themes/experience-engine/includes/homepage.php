<?php

if ( ! function_exists( 'ee_homepage_feeds' ) ) :
	function ee_homepage_feeds() {
		$feeds = array();
		foreach ( bbgi_ee_get_publisher_feeds_with_content() as $feed ) {
			if ( ! empty( $feed['id'] ) && ( $feed['id'] == 'feedback' || $feed['id'] == 'utilities' ) ) {
				continue;
			}

			if ( ! in_array( $feed['type'], array( 'events', 'contests', 'news', 'video', 'podcast' ) ) ) {
				continue;
			}

			$feeds[] = $feed;
		}

		$count = count( $feeds );
		for ( $index = 1; $index <= $count; $index++ ) {
			$feed = $feeds[ $index - 1 ];
			if ( empty( $feed['content'] ) || ! is_array( $feed['content'] ) ) {
				continue;
			}

			echo '<div class="ribon">';
				if ( ! empty( $feed['title'] ) ) {
					ee_the_subtitle( $feed['title'] );
					if ( ! empty( $feed['description'] ) ) {
						echo '<p>', esc_html( $feed['description'] ), '</p>';
					}
				}

				echo '<div class="ribon-items">';
					foreach ( $feed['content'] as $item ) {
						if ( $item['contentType'] == 'link' || $item['contentType'] == 'podcast' ) {
							$post = ee_setup_post_from_feed_item( $item, $feed );
							get_template_part( 'partials/tile', $post->post_type );
						}
					}
				echo '</div>';
			echo '</div>';

			// below first two ribbons, then after 5th ribbon and every 3 ribbons thereafter.
			if ( $index < $count ) {
				if ( ( $index == 2 ) || ( $index > 2 && ( $index - 2 ) % 3 == 0 ) ) {
					do_action( 'dfp_tag', 'dfp_ad_inlist_infinite' );
				}
			}
		}

		wp_reset_postdata();
	}
endif;

if ( ! function_exists( 'ee_setup_post_from_feed_item' ) ) :
	function ee_setup_post_from_feed_item( $item, $feed ) {
		$post_type = 'post';
		if ( $feed['type'] == 'contest' ) {
			$post_type = 'contest';
		} elseif ( $feed['type'] == 'podcast' ) {
			$post_type = 'episode';
		} elseif ( $feed['type'] == 'events' ) {
			$post_type = 'tribe_events';
		}

		$post = new \stdClass();
		$post->filter = 'raw';

		$post->ID = 0;
		$post->post_title = $item['title'];
		$post->post_status = 'publish';
		$post->post_type = ee_is_network_domain( $item['link'] ) ? $post_type : 'external';
		$post->post_content = $item['excerpt'];
		$post->post_excerpt = $item['excerpt'];
		$post->post_date = $post->post_date_gmt = $post->post_modified = $post->post_modified_gmt = date( 'Y:m:d H:i:s', strtotime( $item['publishedAt'] ) );

		$post->id = $item['id'];
		$post->link = $item['link'];

		if ( ! empty( $item['picture']['large'] ) ) {
			$post->picture = $item['picture']['large'];
		}

		if ( ! empty( $item['media'] ) ) {
			$post->media = $item['media'];
		}

		$post = new \WP_Post( $post );
		setup_postdata( $post );
		$GLOBALS['post'] = $post;

		return $post;
	}
endif;

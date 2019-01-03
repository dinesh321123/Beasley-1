<?php

if ( ! function_exists( 'ee_homepage_feeds' ) ) :
	function ee_homepage_feeds( $feeds ) {
		$supported_feeds = array();
		$supported_types = array(
			'events'   => 'ee_render_homepage_standard_feed',
			'contests' => 'ee_render_homepage_standard_feed',
			'news'     => 'ee_render_homepage_standard_feed',
			'video'    => 'ee_render_homepage_standard_feed',
			'podcast'  => 'ee_render_homepage_standard_feed',
			'cta'      => 'ee_render_homepage_cta_feed',
			'stream'   => 'ee_render_homepage_stream',
		);

		foreach ( $feeds as $feed ) {
			if ( ! empty( $feed['id'] ) && ( $feed['id'] == 'feedback' || $feed['id'] == 'utilities' ) ) {
				continue;
			}

			if ( isset( $supported_types[ $feed['type'] ] ) ) {
				$supported_feeds[] = $feed;
			}
		}

		$count = count( $supported_feeds );
		if ( $count > 0 ) {
			for ( $i = 0; $i < $count; $i++ ) {
				$feed = $supported_feeds[ $i ];
				if ( ! empty( $feed['content'] ) && is_array( $feed['content'] ) ) {
					call_user_func( $supported_types[ $feed['type'] ], $feed, $count );
				}
			}

			wp_reset_postdata();
		} else {
			ee_render_discovery_cta();
		}
	}
endif;

if ( ! function_exists( 'ee_render_homepage_standard_feed' ) ) :
	function ee_render_homepage_standard_feed( $feed, $feeds_count ) {
		static $index = 1;
		$size = $index === 1 ? '-large' : '-small';
		echo '<div class="content-wrap">';
			if ( ! empty( $feed['title'] ) ) {
				if ( $index <= 1 ) {
					ee_the_subtitle( $feed['title'] );
				} else {
					ee_the_subtitle( $feed['title'], 'true' );
				}
			}

			echo '<div class="archive-tiles -carousel swiper-container ' . esc_attr( $size ) .'">';
				echo '<div class="swiper-wrapper">';
					foreach ( $feed['content'] as $item ) {
						echo '<div class="swiper-slide">';
							if ( $item['contentType'] == 'link' || $item['contentType'] == 'podcast' ) {
								$post = ee_setup_post_from_feed_item( $item, $feed );
								get_template_part( 'partials/tile', $post->post_type );
							}
						echo '</div>';
					}
					echo '</div>';
				echo '<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>';
			echo '</div>';
		echo '</div>';

		// below first two ribbons, then after 5th ribbon and every 3 ribbons thereafter.
		if ( $index < $feeds_count ) {
			if ( ( $index == 2 ) || ( $index > 2 && ( $index - 2 ) % 3 == 0 ) ) {
				do_action( 'dfp_tag', 'in-list' );
			}
		}

		if ( $index == 4 ) {
			ee_render_discovery_cta();
		}

		$index++;
	}
endif;

if ( ! function_exists( 'ee_render_discovery_cta' ) ) :
	function ee_render_discovery_cta() {
		echo '<div class="discovery-cta"></div>';
	}
endif;

if ( ! function_exists( 'ee_render_homepage_cta_feed' ) ) :
	function ee_render_homepage_cta_feed( $feed ) {
		foreach ( $feed['content'] as $item ) {
			$type = explode( '-', $item['contentType'] );
			if ( $type[0] == 'cta' || $type[0] == 'countdown' ) {
				printf(
					'<div class="%s" data-payload="%s"></div>',
					esc_attr( $type[0] ),
					esc_attr( json_encode( $item ) )
				);
			}
		}
	}
endif;

if ( ! function_exists( 'ee_render_homepage_stream' ) ) :
	function ee_render_homepage_stream( $feed ) {
		foreach ( $feed['content'] as $item ) {
			printf(
				'<div class="stream-cta" data-payload="%s"></div>',
				esc_attr( json_encode( $item ) )
			);
		}
	}
endif;

if ( ! function_exists( 'ee_setup_post_from_feed_item' ) ) :
	function ee_setup_post_from_feed_item( $item, $feed ) {
		$post = ee_get_post_by_link( $item['link'] );
		if ( ! is_a( $post, '\WP_Post' ) ) {
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
			$post->post_type = ee_is_network_domain( $item['link'] ) || $post_type == 'episode' ? $post_type : 'external';
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
		}

		setup_postdata( $post );
		$GLOBALS['post'] = $post;

		return $post;
	}
endif;

if ( ! function_exists( 'ee_get_post_by_link' ) ) :
	function ee_get_post_by_link( $link ) {
		$key = 'ee:post-by-link:' . $link;
		$post_id = wp_cache_get( $key );
		if ( $post_id === false ) {
			// @todo: find a better way to get post by its link

			/*$wp = new \WP();
			$query = new \WP_Query();

			$url = $_SERVER['REQUEST_URI'];
			$_SERVER['REQUEST_URI'] = $link;

			$wp->parse_request();
			$ids = $query->query( array_merge( $wp->query_vars, array(
				'ignore_sticky_posts' => true,
				'posts_per_page'      => 1,
				'fields'              => 'ids',
			) ) );

			$_SERVER['REQUEST_URI'] = $url;

			$post_id = current( $ids );
			wp_cache_set( $key, $post_id );*/
		}

		if ( ! empty( $post_id ) ) {
			$post = get_post( $post_id );
			if ( is_a( $post, '\WP_Post' ) ) {
				return $post;
			}
		}

		return false;
	}
endif;

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

if ( ! function_exists( 'ee_edit_feed_button' ) ) :
	function ee_edit_feed_button( $feed ) {
		$title = '';
		if ( ! empty( $feed['title'] ) ) {
			$title = $feed['title'];
		}

		echo '<div class="edit-feed" data-feed="', esc_attr( $feed['id'] ), '" data-title="', esc_attr( $title ), '"></div>';
	}
endif;

if ( ! function_exists( 'ee_render_homepage_standard_feed' ) ) :
	function ee_render_homepage_standard_feed( $feed, $feeds_count ) {
		static $index = 1;
		$size = $index === 1 ? '-large' : '-small';
		echo '<div id="', esc_attr( $feed['id'] ), '" class="content-wrap">';
			ee_edit_feed_button( $feed );

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
		$post = false;
		switch ( $feed['type'] ) {
			case 'podcast':
				$post = ee_get_post_by_omny_audio( $item['media']['url'] );
				break;
			default:
				$post = ee_get_post_by_link( $item['link'] );
				break;
		}

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
			if ( ! ee_is_current_domain( $item['link'] ) && ee_is_network_domain( $item['link'] ) ) {
				$parts = explode( '://', $item['link'], 2 );
				$post->link = home_url( '/' . end( $parts ) );
			}

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

if ( ! function_exists( 'ee_get_post_by_omny_audio' ) ) :
	function ee_get_post_by_omny_audio( $audio ) {
		global $wpdb;

		$audio = explode( '?', $audio );
		$audio = current( $audio );

		$key = 'ee:post-by-audio:' . $audio;
		$post_id = wp_cache_get( $key );
		if ( $post_id === false ) {
			$audio = esc_sql( $audio );

			$post_id = $wpdb->get_var( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'omny-audio-url' AND meta_value LIKE '{$audio}%'" );
			$post_id = intval( $post_id );

			wp_cache_set( $key, $post_id, DAY_IN_SECONDS );
		}

		if ( $post_id > 0 ) {
			$post = get_post( $post_id );
			if ( is_a( $post, '\WP_Post' ) ) {
				return $post;
			}
		}

		return false;
	}
endif;

if ( ! function_exists( 'ee_get_post_by_link' ) ) :
	function ee_get_post_by_link( $link ) {
		global $wp_rewrite;

		$request = parse_url( $link );
		if ( $request['host'] != parse_url( home_url(), PHP_URL_HOST ) ) {
			return false;
		}

		$key = 'ee:post-by-link:' . $link;
		$post_id = wp_cache_get( $key );
		if ( $post_id === false ) {
			$request_path = trim( $request['path'], '/' );

			$rewrite = $wp_rewrite->wp_rewrite_rules();
			foreach ( $rewrite as $match => $query ) {
				if ( preg_match( "#^{$match}#", $request_path, $matches ) || preg_match( "#^{$match}#", urldecode( $request_path ), $matches ) ) {
					$query = parse_url( $query, PHP_URL_QUERY );
					$query = addslashes( \WP_MatchesMapRegex::apply( $query, $matches ) );

					parse_str( $query, $query_vars );
					if ( ! emptY( $query_vars ) ) {
						$query = new \WP_Query();
						$posts = $query->query( array_merge( $query_vars, array(
							'ignore_sticky_posts' => true,
							'posts_per_page'      => 1,
							'fields'              => 'ids',
						) ) );

						if ( ! empty( $posts ) ) {
							$post_id = current( $posts );
						}
						break;
					}
				}
			}

			$post_id = intval( $post_id );
			wp_cache_set( $key, $post_id, HOUR_IN_SECONDS );
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

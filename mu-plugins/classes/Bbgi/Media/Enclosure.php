<?php

namespace Bbgi\Media;

/**
 * Enclosure manages podcast episode audio metadata. When a Podcast
 * episode is saved it generates the metadata from the mp3 url and
 * updates the corresponding post meta.
 */
class Enclosure {

	/**
	 * Hook to WordPress save post
	 */
	public function register() {
		add_action( 'save_post', [ $this, 'did_save_post' ] );
	}

	/**
	 * If the Podcast Episode needs an enclosure update and it has a valid
	 * mp3 url then we update the post meta.
	 *
	 * @param int $post_id The saved post id
	 * @return void
	 */
	public function did_save_post( $post_id ) {
		if ( $this->needs_enclosure_update( $post_id ) ) {
			$mp3 = $this->get_episode_mp3( $post_id );

			if ( ! empty( $mp3 ) ) {
				$this->update_enclosure( $post_id, $mp3 );
			}
		}
	}

	/**
	 * Updates the enclosure metadata for the specified podcast episode.
	 *
	 * - if internal enclosure, updates from the audio metadata
	 * - if audioboom, updates from the embed markup
	 * - if omny, ignore as omny plugin handles this
	 *
	 * @param int $post_id The episode post id.
	 * @param string $mp3 The url of the mp3 for the episode.
	 * @return void
	 */
	public function update_enclosure( $post_id, $mp3 ) {
		update_post_meta( $post_id, 'enclosure', $mp3 );

		if ( $this->is_local_enclosure( $mp3 ) ) {
			$attachment_id = $this->get_id_from_url( $mp3 );

			if ( ! empty( $attachment_id ) ) {
				$metadata = wp_get_attachment_metadata( $attachment_id );
			} else {
				$metadata = $this->get_audio_metadata( $mp3 );
			}

			if ( isset( $metadata['length_formatted'] ) ) {
				update_post_meta( $post_id, 'duration', esc_html( $metadata['length_formatted'] ) );
			}
		} else if ( stripos( $mp3, 'audioboom' ) !== false ) {
			$duration = $this->get_audio_boom_duration( $mp3 );

			if ( ! empty( $duration ) ) {
				update_post_meta( $post_id, 'duration', $duration );
			}
		}
	}

	/**
	 * Checks if the specified post id and context needs to update
	 * enclosure metadata.
	 *
	 * @param int $post_id The post id to check.
	 * @return bool
	 */
	public function needs_enclosure_update( $post_id ) {
		$post_type = get_post_type( $post_id );

		if ( wp_is_post_autosave( $post_id ) ) {
			return false;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return false;
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return false;
		}

		if ( defined( 'WP_IMPORTING' ) && WP_IMPORTING ) {
			return false;
		}

		if ( $post_type !== 'episode' ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if the MP3 url is a local enclosure.
	 *
	 * @param string $url The enclosure url
	 * @return bool
	 */
	function is_local_enclosure( $url ) {
		if ( stripos( $url, 'omny.fm' ) !== false ) {
			return false;
		} else if ( stripos( $url, 'audioboom' ) !== false ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Returns the audio metadata for the specified mp3 url.
	 */
	function get_audio_metadata( $audio_url ) {
		$metadata_defaults = [
			'title'            => '',
			'length_formatted' => '',
			'artist'           => '',
		];

		$fileinfo  = parse_url( $audio_url );
		$file_path = ABSPATH . $fileinfo['path'];

		if ( file_exists( $file_path ) ) {
			$metadata = $this->lazy_read_audio_metadata( $file_path );
			$metadata = wp_parse_args( $metadata, $metadata_defaults );
		}

		return $metadata;
	}

	/**
	 * Loads WP media includes and returns the metadata for the specified
	 * file.
	 */
	function lazy_read_audio_metadata( $file_path ) {
		if ( ! function_exists( 'wp_read_audio_metadata' ) ) {
			require_once trailingslashit( ABSPATH ) . 'wp-admin/includes/media.php';
		}

		return wp_read_audio_metadata( $file_path );
	}

	/**
	 * Returns the URL of the mp3 specified in the audio shortcode in the
	 * specified episode post id.
	 *
	 * @param int $post_id
	 * @return string
	 */
	function get_episode_mp3( $post_id ) {
		$post_content = $this->get_episode_content( $post_id );
		$shortcodes   = $this->get_episode_shortcodes( $post_content );
		$mp3          = $this->get_episode_mp3_from_shortcodes( $shortcodes );

		if ( ! empty( $mp3 ) ) {
			return $mp3[0];
		} else {
			return false;
		}
	}

	/**
	 * Returns the list of mp3s from the scanned shortcodes.
	 */
	function get_episode_mp3_from_shortcodes( $shortcodes ) {
		$mp3 = [];

		if ( ! empty( $shortcodes ) ) {
			foreach ( $shortcodes as $shortcode ) {
				if ( ! empty( $shortcode['attr']['mp3'] ) ) {
					$mp3[] = $shortcode['attr']['mp3'];
				}
			}
		}

		return $mp3;
	}

	/**
	 * Scans for episode audio shortcodes in specified post content.
	 */
	function get_episode_shortcodes( $post_content ) {
		$shortcodes = [];
		$trap = function( $output, $attr, $content, $instance ) use ( &$shortcodes ) {
			if ( ! empty( $attr ) ) {
				$shortcodes[] = [
					'output'  => $output,
					'attr'    => $attr,
					'content' => $content,
				];
			}

			return $output;
		};


		add_filter(
			'wp_audio_shortcode_override', $trap , 1000, 4
		);

		$post_content = apply_filters( 'the_content', $post_content );

		remove_filter( 'wp_audio_shortcode_override', $trap );

		return $shortcodes;
	}

	/**
	 * Returns the post content for the specified post id
	 */
	function get_episode_content( $post_id ) {
		$post         = get_post( $post_id );
		$post_content = $post->post_content;

		return $post_content;
	}

	/**
	 * Essentially a cached version of attachment_url_to_postid
	 *
	 * @param $url
	 *
	 * @return bool|int
	 */
	public function get_id_from_url( $url ) {
		// Use local URL instead of S3, to ensure we get the correct post.
		if ( false !== strpos( $url, 'amazonaws' ) || false !== strpos( $url, 'files.greatermedia.com' ) ) {
			$url = strstr( $url, 'sites' );
			$url = str_replace( 'sites/' . get_current_blog_id() . '/', '', $url );
		}

		$post_id = attachment_url_to_postid( $url );

		if ( $post_id ) {
			return $post_id;
		} else {
			return false;
		}
	}

	/**
	 * Fetchs the duration of the audio boom mp3 url via markup in the
	 * embed code.
	 */
	function get_audio_boom_duration( $url ) {
		$embed    = str_replace( '.mp3', '', $url ) . '/embed/';
		$opts     = [];
		$response = wp_remote_get( $embed, $opts );

		if ( ! is_wp_error( $response ) ) {
			$body = wp_remote_retrieve_body( $response );

			if ( ! empty( $body ) ) {
				return $this->parse_audio_boom_duration( $body );
			}
		}

		return false;
	}

	/**
	 * Parse audio duration from audioboom markup.
	 */
	function parse_audio_boom_duration( $html ) {
		$pattern = '#data-duration-value.*>(.*)</span>#';
		$result  = preg_match( $pattern, $html, $matches );

		if ( $result === 1 && count( $matches ) > 1 ) {
			return $matches[1];
		} else {
			return false;
		}
	}

}

<?php

class GMPFeed {

	public static function init() {
		if ( self::is_podcast_feed() ) {
			add_action( 'template_redirect', array( __CLASS__, 'feed_template' ) );
			add_action( 'rss2_item', array( __CLASS__, 'add_mrss_node_to_rss' ) );
			add_action( 'rss2_ns', array( __CLASS__, 'add_mrss_ns_to_rss' ) );
		}
	}

	public static function is_podcast_feed() {
		return isset( $_GET['feed'] ) && $_GET['feed'] == 'podcast';
	}

	public static function feed_template() {
		require __DIR__ . '/feed-podcast.php';
		exit;
	}

	public static function get_file_info( $file = false ) {
		$default = array(
			'size'      => 1,
			'mime-type' => 'audio/mpeg',
		);

		if ( $file ) {
			$cached = wp_cache_get( $file, 'podcast' );
			if ( ! $cached ) {
				$response = wp_remote_head( $file );
				if ( ! is_wp_error( $response ) ) {
					$redirect = wp_remote_retrieve_header( $response, 'location' );
					if ( ! empty( $redirect ) ) {
						$response = wp_remote_head( $redirect );
					}

					$default['size'] = intval( wp_remote_retrieve_header( $response, 'content-length' ) );
					$default['mime-type'] = wp_remote_retrieve_header( $response, 'content-type' );
					if ( ! in_array( $default['mime-type'], wp_get_mime_types() ) ) {
						$default['mime-type'] = 'audio/mpeg';
					}
				}

				$cached = $default;
				wp_cache_set( $file, $default, 'podcast' );
			}

			return $cached;
		}

		return $default;
	}

	public static function add_mrss_node_to_rss() {
		global $post;

		if ( has_post_thumbnail( $post->ID ) ):
			$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'original' );
			if ( ! empty( $thumbnail[0] ) ) { ?>
				<media:thumbnail url="<?php echo esc_attr( $thumbnail[0] ); ?>"  width="<?php echo esc_attr( $thumbnail[1] ); ?>"  height="<?php echo esc_attr( $thumbnail[2] ); ?>" /><?php
			}
		endif;
	}

	public static function add_mrss_ns_to_rss() {
		echo ' xmlns:media="http://search.yahoo.com/mrss/" ';
	}

}

GMPFeed::init();

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

	public static function get_file_size( $file = false ) {
		if ( $file ) {
			$data = wp_remote_head( $file );
			if ( ! is_wp_error( $data ) && isset( $data['headers']['content-length'] ) ) {
				$raw = $data['headers']['content-length'];
				$formatted = self::format_bytes( $raw );

				$size = array(
					'raw'       => $raw,
					'formatted' => $formatted
				);

				return $size;
			}
		}

		return false;
	}

	public static function format_bytes( $size , $precision = 2 ) {
		if ( $size ) {
		    $base = log ( $size ) / log( 1024 );
		    $suffixes = array( '' , 'k' , 'M' , 'G' , 'T' );
		    $bytes = round( pow( 1024 , $base - floor( $base ) ) , $precision ) . $suffixes[ floor( $base ) ];

		    return $bytes;
		}

		return false;
	}

	public static function get_attachment_mimetype( $attachment = false ) {
		if ( $attachment ) {
			global $wpdb;

			$prefix = $wpdb->prefix;
			$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$prefix}posts WHERE guid=%s", $attachment ) );
			if ( $attachment[0] ) {
				$id = $attachment[0];
				$mime_type = get_post_mime_type( $id );

				return $mime_type;
			}
		}

		return false;
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

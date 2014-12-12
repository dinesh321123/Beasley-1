<?php
/**
 * Helpful functions for the migration process.
 */

// Requirements for everything in this class to work properly
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

class MTM_Migration_Utils {

	public static function stop_the_insanity() {
		global $wpdb, $wp_object_cache;

		self::log( "Stopping the insanity" );
		self::log( " -- Memory Usage Before: " . memory_get_usage() );

		$wpdb->queries = array(); // or define( 'WP_IMPORTING', true );

		if ( is_object( $wp_object_cache ) ) {
			$wp_object_cache->group_ops = array();
			$wp_object_cache->stats = array();
			$wp_object_cache->memcache_debug = array();
			$wp_object_cache->cache = array();
			if ( method_exists( $wp_object_cache, '__remoteset' ) )
				$wp_object_cache->__remoteset();
		}

		self::log( " -- Memory Usage After: " . memory_get_usage() );
	}

	/**
	 * Same as media_sideload_image, except it returns the attachment id instead of html.
	 *
	 * @param string $file The url of the image to import.
	 * @param int $post_id The post id of the post this should be associated with.
	 * @param string $desc Description for the image.
	 *
	 * @return int|bool The attachment id for the image or false if there was an error.
	 */
	public static function sideload_image( $file, $post_id, $desc = null ) {
		if ( ! empty( $file ) ) {
			// Download file to temp location
			$tmp = download_url( $file );

			// Set variables for storage
			// fix file filename for query strings
			preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
			$file_array['name'] = basename($matches[0]);
			$file_array['tmp_name'] = $tmp;

			// If error storing temporarily, unlink
			if ( is_wp_error( $tmp ) ) {
				@unlink($file_array['tmp_name']);
				$file_array['tmp_name'] = '';
			}

			// do the validation and storage stuff
			$id = media_handle_sideload( $file_array, $post_id, $desc );
			// If error storing permanently, unlink
			if ( is_wp_error($id) ) {
				@unlink($file_array['tmp_name']);
				return $id;
			}
		}

		// Finally check to make sure the file has been saved, then return the id
		if ( ! empty($id) ) {
			return $id;
		}

		return false;
	}

	/**
	 * Searches provided text for images, imports them to the media library, and updates the image source url.
	 *
	 * @param string $content The content to search for images.
	 * @param int $post_id The post ID to associate the images with
	 *
	 * @return string The final content with the urls replaced.
	 */
	public static function import_images_from_content( $content, $post_id = 0 ) {
		if ( false !== strpos( $content, '<img' ) ) {
			$dom = new DOMDocument();
			$dom->preserveWhiteSpace = true;
			$dom->formatOutput = false;
			$dom->loadHTML( $content );

			$images = $dom->getElementsByTagName('img');
			foreach ( $images as $image ) {
				$src = $image->getAttribute( 'src' );
				$image_id = MTM_Migration_Utils::sideload_image( $src, $post_id );
				if ( is_wp_error( $image_id ) ) {
					MTM_Migration_Utils::warning( "Error importing image: " . $image_id->get_error_message() );
					continue;
				}
				$new_src = wp_get_attachment_image_src( $image_id, 'full' )[0];
				$image->setAttribute( 'src', $new_src );

				$parent = $image->parentNode;
				if ( 'a' == $parent->tagName ) {
					$parent->setAttribute( 'href', $new_src );
				}
				set_post_thumbnail( $post_id, $image_id );
			}

			$bodyNode = $dom->getElementsByTagName( 'body' )->item(0);
			$content = str_replace( array( "<body>", "</body>" ), '', $dom->saveHTML( $bodyNode ) );
			$content = trim( $content );
		}

		return $content;
	}

	/*
	Log Helpers

	Smart enough to use WP_CLI if they should, but not cause fatal errors by calling the WP_CLI functions directly
	*/

	/**
	 * General purpose log function.
	 *
	 * @param string $message The message to log.
	 */
	public static function log( $message ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::log( $message );
		}
	}

	/**
	 * Logs a warning.
	 *
	 * @param string $message The message to log.
	 */
	public static function warning( $message ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::warning( $message );
		}
	}

	/**
	 * Logs a success message.
	 *
	 * @param string $message The message to log.
	 */
	public static function success( $message ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::success( $message );
		}
	}

	/**
	 * Log an error message.
	 *
	 * @param string $message The message to log.
	 */
	public static function error( $message ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::error( $message );
		}
	}

	/**
	 * Determine if a string if equivalent to true or false.
	 *
	 * @param string $value The string we need to determine true/false for.
	 *
	 * @return bool
	 */
	public static function parse_bool( $value ) {
		return 'true' == trim( strtolower( $value ) ) ? true : false;
	}

}
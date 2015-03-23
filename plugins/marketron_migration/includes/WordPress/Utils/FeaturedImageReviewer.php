<?php

namespace WordPress\Utils;

class FeaturedImageReviewer {

	function review( $log_file, $min_width = 300 ) {
		$post_ids     = $this->find_posts_with_featured_images();
		$total_posts  = count( $post_ids );

		\WP_CLI::success( "Found $total_posts Posts with Featured Images." );

		$msg          = "Reviewing $total_posts Posts with Featured Images";
		$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total_posts );
		$invalid_count = 0;
		$file          = fopen( $log_file, 'w' );

		foreach ( $post_ids as $post_id ) {
			$attachment_id = get_post_meta( $post_id, '_thumbnail_id', true );
			$attachment_id = intval( $attachment_id );

			if ( $attachment_id > 0 ) {
				if ( ! $this->is_valid_featured_image( $attachment_id, $min_width ) ) {
					$post = get_post( $post_id );
					$fields = array(
						$post->ID,
						$post->post_type,
						get_permalink( $post->ID ),
					);

					delete_post_thumbnail( $post->ID );
					fputcsv( $file, $fields, ',' );
					$invalid_count++;
				}
			} else {
				\WP_CLI::error( "Invalid Attachment ID: $post->ID - $attachment_id" );
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();
		fclose( $file );

		if ( $invalid_count === 0 ) {
			\WP_CLI::success( 'No Invalid Featured Image was found' );
		} else {
			\WP_CLI::success( "$invalid_count Posts with invalid featured images found." );
		}
	}

	function is_valid_featured_image( $attachment_id, $min_width ) {
		$meta       = wp_get_attachment_metadata( $attachment_id );
		$upload_dir = wp_upload_dir();
		$file       = $upload_dir['basedir'] . '/' . $meta['file'];

		if ( ! file_exists( $file ) ) {
			\WP_CLI::warning( "Attachment( $attachment_id ) is missing: $file" );
			return false;
		}

		if ( empty( $meta ) ) {
			\WP_CLI::warning( "Regenerating($attachment_id) Attachment Metadata: $file" );
			$meta = wp_generate_attachment_metadata( $attachment_id, $file );
		}

		if ( ! empty( $meta['width'] ) ) {
			$width = intval( $meta['width'] );
			return $width >= $min_width;
		} else {
			return true;
		}
	}

	function find_posts_with_featured_images() {
		$args = array(
			'nopaging' => true,
			'post_type' => 'any',
			'meta_key'     => '_thumbnail_id',
			'meta_compare' => 'EXISTS',
			'fields' => 'ids'
		);

		$query = new \WP_Query( $args );

		return $query->get_posts();
	}

	function count_posts_with_featured_images() {
		global $wpdb;

		$query = <<<SQL
Select Count(*) From {$wpdb->prefix}posts
Inner Join {$wpdb->prefix}postmeta
On
	{$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
Where
	{$wpdb->prefix}postmeta.meta_key = '_thumbnail_id';
SQL;

		//error_log( $query );
		return intval( $wpdb->get_var( $query ) );
	}

}

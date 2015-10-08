<?php

namespace GreaterMedia\Import\Repair;

class PostDeletor {

	function delete() {
		\WP_CLI::log( 'Finding Posts to delete ...' );

		$post_ids          = $this->get_posts_to_delete();
		//$attachment_ids    = $this->get_attachments_to_delete( $post_ids );
		//$gallery_image_ids = $this->get_gallery_images_to_delete();

		$ids = $post_ids;
		//$ids   = array_merge( $post_ids, $attachment_ids, $gallery_image_ids );
		$ids   = array_unique( $ids );

		sort( $ids );

		$total = count( $ids );

		\WP_CLI::confirm( 'Delete ' . $total . ' Posts?' );

		$msg           = "Deleting $total Posts";
		$progress_bar  = new \WordPress\Utils\ProgressBar( $msg, $total );
		$this->delete_counts = array();

		foreach ( $ids as $id ) {
			$result = $this->delete_post( $id );

			if ( $result !== false ) {
				if ( ! array_key_exists( $result->post_type, $this->delete_counts ) ) {
					$this->delete_counts[ $result->post_type ] = 0;
				}

				$this->delete_counts[ $result->post_type ]++;
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();

		$total_deletes = 0;
		foreach ( $this->delete_counts as $post_type => $count ) {
			\WP_CLI::success( "Delete Count ($post_type): $count" );
			$total_deletes += $count;
		}

		\WP_CLI::success( "Delete Count (total): $total_deletes" );
	}

	function delete_post( $id ) {
		$post              = get_post( $id );
		$featured_image_id = get_post_thumbnail_id( $id );

		//\WP_CLI::log( $post->ID . ' - ' . $post->post_type . ': ' . $post->post_parent . ' - ' . $post->post_title );
		if ( ! empty( $featured_image_id ) && $featured_image_id > 0 ) {
			if ( ! empty( $featured_image_id && ! is_wp_error( $post ) ) ) {
				wp_delete_post( $post->ID );
			}

			//\WP_CLI::log( "Featured Image($post->ID}): " . $featured_image_id );
			if ( ! array_key_exists( 'featured_images', $this->delete_counts ) ) {
				$this->delete_counts['featured_images'] = 0;
			}

			$this->delete_counts['featured_images']++;

		}

		if ( ! empty( $post ) && ! is_wp_error( $post ) ) {
			wp_delete_post( $post->ID );
		}

		return $post;
	}

	function get_posts_to_delete() {
		$params = array(
			'post_type' => array(
				'post',
				'attachment',
				'gmr_album',
				'gmr_gallery',
			),
			'fields' => 'ids',
			'posts_per_page' => -1,
			'date_query' => array(
				'before' => array(
					'year'   => 2014,
					'month'  => 1,
					'day'    => 1,
				)
			)
		);

		//$params = array( 'post_type' => 'any', 'posts_per_page' => -1, 'fields' => 'ids' );

		$query = new \WP_Query( $params );
		$ids = $query->get_posts();

		return $ids;
	}

	function get_attachments_to_delete( $post_ids ) {
		$params = array(
			'post_type' => array(
				'any',
			),
			'post_parent__in' => $post_ids,
			'fields'          => 'ids',
			'posts_per_page'  => -1,
		);

		$query = new \WP_Query( $params );
		$ids = $query->get_posts();

		return $ids;
	}

	function get_gallery_images_to_delete() {
		$params = array(
			'post_type' => array(
				'gmr_gallery',
			),
			'fields' => 'ids',
			'posts_per_page' => -1,
			'date_query' => array(
				'before' => array(
					'year'   => 2014,
					'month'  => 1,
					'day'    => 1,
				)
			)
		);

		//$params = array( 'post_type' => 'any', 'posts_per_page' => -1, 'fields' => 'ids' );

		$query     = new \WP_Query( $params );
		$ids       = $query->get_posts();
		$image_ids = array();
		$pattern   = get_shortcode_regex();

		foreach ( $ids as $id ) {
			$post = get_post( $id );

			preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches );

			if ( array_key_exists( 2, $matches ) && in_array( 'gallery', $matches[2] ) ) {
				$keys = array_keys( $matches[2], 'gallery' );

				foreach ( $keys as $key ) {
					$atts = shortcode_parse_atts( $matches[3][ $key ] );

					if ( array_key_exists( 'ids', $atts ) ) {
						$gallery_image_ids = explode( ',', $atts['ids'] );
						foreach ( $gallery_image_ids as $gallery_image_id ) {
							$image_ids[] = $gallery_image_id;
						}
					}
				}
			}
		}

		return $image_ids;
	}

}

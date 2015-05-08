<?php

namespace GreaterMedia\Import\Repair;

class FeaturedImageRepairer {

	public $container;
	public $errors = array();

	function repair() {
		$this->container->opts['repair'] = true;
		$this->container->opts['fake_media'] = true;

		$feed_tool = $this->container->tool_factory->build( 'feed' );
		$feed_tool->load();

		$sources       = $feed_tool->sources;
		$feed_importer = $this->container->importer_factory->build( 'feed' );
		$offset_date   = strtotime( '2015/05/09' );

		foreach ( $sources as $source ) {
			$articles     = $feed_importer->articles_from_source( $source );
			$total        = count( $articles );
			$msg          = "Repairing $total Featured Images";
			$progress_bar = new \WordPress\Utils\ProgressBar( $msg, $total );

			foreach ( $articles as $article ) {
				$post_entity    = $feed_importer->post_from_article( $article );
				$post_title     = $post_entity['post_title'];
				$wordpress_post = get_page_by_title( $post_title, ARRAY_A, 'post' );
				$created_on = strtotime( $wordpress_post['post_date_gmt'] );

				if ( ! empty( $wordpress_post ) && $created_on < $offset_date ) {
					if ( ! empty( $post_entity['featured_image'] ) ) {
						$featured_image = $post_entity['featured_image'];
						$this->repair_featured_image( $wordpress_post, $featured_image );
					}
				}

				$progress_bar->tick();
			}

			$progress_bar->finish();

			if ( count( $this->errors ) > 0 ) {
				\WP_CLI::log( 'There were ' . count( $this->errors ) . ' errors' );
				print_r( $this->errors );
			}
		}
	}

	function repair_featured_image( $post, $featured_image ) {
		$post_id             = $post['ID'];
		$asset_locator       = $this->container->asset_locator;
		$featured_image_path = $asset_locator->find( $featured_image );

		if ( ! empty( $featured_image_path ) ) {
			$attachment_id = $this->sideload( $featured_image_path, $post_id );

			if ( ! empty( $attachment_id ) ) {
				set_post_thumbnail( $post_id, $attachment_id );
				\WP_CLI::success( "Updated Featured Image: $post_id - $attachment_id" );
			}
		}
	}

	function sideload( $image, $post_id ) {
		$tmp_file = wp_tempnam( basename( $image ) );
		$result   = copy( $image, $tmp_file );

		if ( ! $result ) {
			\WP_CLI::error( "Failed to copy $image to $tmp_file" );
		} else {
			clearstatcache( true, $tmp_file );
		}

		$file_array = array(
			'name' => basename( $image ),
			'tmp_name' => $tmp_file,
		);

		$id = media_handle_sideload( $file_array, $post_id );

		if ( ! is_wp_error( $id ) ) {
			return $id;
		} else {
			$this->errors[] = array( 'post_id' => $post_id, 'image' => $image );
			return false;
		}
	}

}

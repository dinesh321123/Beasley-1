<?php

namespace WordPress\Utils;

class ThumbnailListRegenerator {

	public $container;
	public $thumbnail_regenerator;
	public $errors;

	function regenerate( $ids = array(), $opts ) {
		if ( empty( $ids ) ) {
			/* default to all if ids is empty */
			$ids = $this->find_all_attachments();
		}

		$regenerator  = $this->get_thumbnail_regenerator();
		$total        = count( $ids );
		$msg          = "Regenerating $total Thumbnails";
		$progress_bar = new ProgressBar( $msg, $total );
		$errors_file  = $this->get_errors_file( $opts );

		foreach ( $ids as $id ) {
			$id     = intval( $id );
			$result = $regenerator->regenerate( $id, $opts );

			if ( is_wp_error( $result ) ) {
				$this->log_error( $id, $error );
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();

		$this->write_errors( $errors_file );
	}

	/* helpers */
	function find_all_attachments() {
		$params = $this->get_query_params();
		$query  = new \WP_Query( $params );

		return $query->posts;
	}

	function get_query_params() {
		return array(
			'post_type'      => 'attachment',
			'post__in'       => array(),
			'post_mime_type' => 'image',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		);
	}

	function get_thumbnail_regenerator() {
		if ( is_null( $this->thumbnail_regenerator ) ) {
			$this->thumbnail_regenerator = new ThumbnailRegenerator();
			$this->thumbnail_regenerator->parent = $this;
		}

		return $this->thumbnail_regenerator;
	}

	function log_error( $id, $error ) {
		if ( is_wp_error( $error ) ) {
			$message = $error->get_error_message();
		} else {
			$message = $error;
		}

		$this->errors[] = array(
			'id'      => $id,
			'message' => $message,
		);
	}

	function write_errors( $path ) {
		$total_errors = count( $this->errors );

		if ( $total_errors === 0 ) {
			\WP_CLI::success(
				'Thumbnails regeneration successful, No errors occurred.'
			);
		} else {
			file_put_contents(
				$path, json_encode( $this->errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES )
			);

			\WP_CLI::warning( "$total_errors Errors written to $path." );
		}
	}

	function get_errors_file( &$opts ) {
		if ( empty( $opts['errors_file'] ) ) {
			$output_dir = $this->container->config->get_output_dir();

			return $output_dir . '/thumbnail_regeneration_errors.json';
		} else {
			return $opts['errors_file'];
		}
	}

}

<?php

namespace WordPress\Utils;

class ThumbnailListRegenerator {

	public $container;
	public $thumbnail_regenerator;
	public $errors;

	function register() {
		add_action( 'init', array( $this, 'do_register' ) );
	}

	function do_register() {
		add_action(
			'regenerate_attachment_async_job',
			array( $this, 'do_regenerate_attachment' )
		);
	}

	function regenerate( $ids = array(), $opts ) {
		if ( empty( $ids ) ) {
			/* default to all if ids is empty */
			$ids = $this->find_all_attachments();
		}

		$errors_file  = $this->get_errors_file( $opts );

		if ( file_exists( $errors_file ) ) {
			unlink( $errors_file );
		}

		$total        = count( $ids );
		$msg          = "Regenerating $total Thumbnails";
		$progress_bar = new ProgressBar( $msg, $total );
		$async        = $this->get_is_async( $opts );

		foreach ( $ids as $index => $id ) {
			$opts['index'] = $index + 1;
			$opts['total'] = $total;

			if ( $async ) {
				$this->regenerate_attachment_async( $id, $opts );
			} else {
				$this->regenerate_attachment( $id, $opts );
			}

			$progress_bar->tick();
		}

		$progress_bar->finish();

		$this->write_errors( $errors_file );
	}

	function regenerate_attachment_async( $id, $opts ) {
		wp_async_task_add(
			'regenerate_attachment_async_job',
			array( $id, $opts ),
			'low'
		);
	}

	function do_regenerate_attachment( $params ) {
		$id   = $params[0];
		$opts = $params[1];

		$this->regenerate_attachment( $id, $opts );
	}

	function regenerate_attachment( $id, $opts ) {
		$id          = intval( $id );
		$regenerator = $this->get_thumbnail_regenerator();
		$result      = $regenerator->regenerate( $id, $opts );

		if ( is_wp_error( $result ) ) {
			$this->log_error( $id, $error );
		} else {
			$percent = round( $opts['index'] / $opts['total'] * 100, 2 );
			error_log( "Regenerated Attachment ($id) - $percent%" );
		}
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
			$lines = '';

			foreach ( $this->errors as $error ) {
				$line = $error['id'] . ': ' . $error['message'];
				$lines .= $line . "\n";
			}

			file_put_contents(
				$path, $lines, FILE_APPEND | LOCK_EX
			);

			\WP_CLI::warning( "$total_errors Errors written to $path" );
		}
	}

	function get_errors_file( &$opts ) {
		if ( empty( $opts['errors_file'] ) ) {
			$output_dir = $this->container->config->get_output_dir();

			return $output_dir . '/thumbnail_regeneration_errors.log';
		} else {
			return $opts['errors_file'];
		}
	}

	function get_is_async( &$opts ) {
		if ( empty( $opts['async'] ) ) {
			return false;
		} else if ( $opts['async'] === '' ) {
			return true;
		} else {
			return filter_var( $opts['async'], FILTER_VALIDATE_BOOLEAN );
		}
	}

}

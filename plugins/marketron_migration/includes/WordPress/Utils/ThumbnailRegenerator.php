<?php

namespace WordPress\Utils;

class ThumbnailRegenerator {

	public $parent;

	function regenerate( $id, $opts = array() ) {
		$skip_delete = $this->get_skip_delete( $opts );

		if ( ! $skip_delete ) {
			$this->remove_variants( $id );
		}

		$owner = $this->get_owner( $opts );
		$group = $this->get_group( $opts );

		return $this->regenerate_attachment( $id, $owner, $group );
	}

	function regenerate_attachment( $id, $owner, $group ) {
		$path     = get_attached_file( $id );
		$metadata = wp_generate_attachment_metadata( $id, $path );

		if ( ! is_wp_error( $metadata ) ) {
			wp_update_attachment_metadata( $id, $metadata );

			$new_variants   = $this->get_variants_from_meta( $metadata );
			$new_variants[] = array( 'path' => $path, 'meta' => $metadata );

			$this->update_variants( $id, $new_variants, $owner, $group );

			return true;
		} else {
			return $metadata;
		}
	}

	/* helpers */
	function remove_variants( $id ) {
		$variants = $this->get_variants( $id );

		foreach ( $variants as $variant ) {
			$variant_path = $variant['path'];

			if ( file_exists( $variant_path ) ) {
				unlink( $variant_path );
			}
		}
	}

	function update_variants( $id, $variants, $owner, $group ) {
		foreach ( $variants as $variant ) {
			$variant_path = $variant['path'];
			$this->update_ownership( $id, $variant_path, $owner, $group );
		}
	}

	function get_variants( $id ) {
		$metadata = $this->get_metadata( $id );
		return $this->get_variants_from_meta( $metadata );
	}

	function get_variants_from_meta( $metadata ) {
		$variants = array();

		if ( ! empty( $metadata['sizes'] ) ) {
			$sizes            = $metadata['sizes'];
			$uploads_dir_meta = wp_upload_dir();
			$uploads_dir      = $uploads_dir_meta['basedir'];
			$parent_dir       = $uploads_dir . '/' . dirname( $metadata['file'] );
			$parent_file      = $parent_dir . '/' . basename( $metadata['file'] );

			foreach ( $sizes as $size_meta ) {
				$variant_path = $parent_dir . '/' . $size_meta['file'];
				$variants[] = array(
					'meta' => $size_meta,
					'path' => $variant_path,
				);
			}
		}

		return $variants;
	}

	function update_ownership( $id, $path, $owner = 'nginx', $group = 'nginx' ) {
		if ( file_exists( $path ) && is_file( $path ) ) {
			chown( $path, $owner );
			chgrp( $path, $group );
		} else {
			$this->parent->log_error( $id, "File not found: $path" );
		}
	}

	function get_metadata( $id ) {
		$metadata = wp_get_attachment_metadata( $id );

		if ( ! empty( $metadata ) ) {
			return $metadata;
		} else {
			return array();
		}
	}

	function get_skip_delete( &$opts ) {
		if ( array_key_exists( 'skip-delete', $opts ) ) {
			$skip_delete = empty( $opts['skip-delete'] ) ? 'yes' : $opts['skip-delete'];
			$skip_delete = filter_var(
				$skip_delete, FILTER_VALIDATE_BOOLEAN
			);

			return $skip_delete;
		} else {
			return false;
		}
	}

	function get_owner( &$opts ) {
		if ( ! empty( $opts['owner'] ) ) {
			return $opts['owner'];
		} else {
			return 'nginx';
		}
	}

	function get_group( &$opts ) {
		if ( ! empty( $opts['group'] ) ) {
			return $opts['group'];
		} else {
			return 'nginx';
		}
	}

}

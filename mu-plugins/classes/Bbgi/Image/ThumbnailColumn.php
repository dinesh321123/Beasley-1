<?php

namespace Bbgi\Image;

class ThumbnailColumn extends \Bbgi\Module {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		add_filter( 'manage_posts_columns', array( $this, 'filter_columns' ), 10, 2 );
		add_action( 'manage_posts_custom_column', array( $this, 'do_custom_column' ), 10, 2 );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
	}

	protected function _get_supported_post_types() {
		return array(
			'post',
			'contest',
			'show',
			'gmr_gallery',
			'gmr_album',
			'podcast',
			'episode',
		);
	}

	/**
	 * Add the custom column to the columns list.
	 *
	 * @param array $columns
	 * @return array
	 */
	public function filter_columns( $columns, $post_type ) {
		// Make sure this is a post type we're handling.
		if ( ! in_array( $post_type, $this->_get_supported_post_types() ) ) {
			return $columns;
		}

		// Put the thumbnail right after the checkbox.
		return array_merge(
			array_slice( $columns, 0, 1 ),
			array( 'thumbnail' => 'Thumbnail' ),
			array_slice( $columns, 1 )
		);
	}

	/**
	 * Render the custom column.
	 *
	 * @param string $column_name
	 * @param int $post_id
	 */
	public function do_custom_column( $column_name, $post_id ) {
		// Make sure this is our column.
		if ( $column_name != 'thumbnail' ) {
			return;
		}

		// Make sure we have a thumbnail.
		if ( has_post_thumbnail( $post_id ) ) {
			$thumbnail_id = get_post_thumbnail_id( $post_id );
			$url = wp_get_attachment_image_url( $thumbnail_id, 'original' );
			if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
				$url = add_query_arg( array( 'width' => 100, 'height' => 75, 'mode' => 'crop' ), $url );
				echo '<div style="background-image: url(', esc_url( $url ), ')"></div>';
				return;
			}
		}

		// empty div if there is no thumbnail for this post
		echo '<div></div>';
	}

	/**
	 * Add some CSS to the page head that sets the column width to match the
	 * thumbnail width.
	 */
	public function admin_head() {
		global $typenow, $pagenow;

		if ( in_array( $typenow, $this->_get_supported_post_types() ) && $pagenow == 'edit.php' ) {
			?><style type="text/css">
				.column-thumbnail, .column-thumbnail div { width: 100px; }
				.column-thumbnail div {
					background-position: center;
					background-repeat: no-repeat;
					background-size: cover;
					border: 1px solid #ccc;
					height: 75px;
					width: 100px;
				}
			</style><?php
		}
	}

}

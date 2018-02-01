<?php

namespace Beasley\DirectoryListing;

trait FeaturedImage {

	/**
	 * Registers featured image settings.
	 *
	 * @access public
	 */
	public function register_featured_image( $taxonomy ) {
		add_action( $taxonomy . '_edit_form_fields', array( $this, 'render_featured_image_field' ) );
		add_action( 'edited_' . $taxonomy, array( $this, 'save_featured_image' ), 10, 2 );

		add_filter( 'manage_edit-' . $taxonomy . '_columns', array( $this, 'add_featured_image_column' ) );
		add_filter( 'manage_' . $taxonomy . '_custom_column', array( $this, 'get_featured_image_column' ), 10, 3 );
	}

	/**
	 * Rendres featured image field.
	 *
	 * @access public
	 * @action {taxonomy}_edit_form_fields
	 * @param \WP_Term $term
	 */
	public function render_featured_image_field( $term ) {
		wp_enqueue_media();
		wp_enqueue_script( 'featured-image', BEASLEY_LISTINGS_ABSURL . 'assets/media-assets.js', array( 'jquery' ), null, true );

		$image_id = get_term_meta( $term->term_id, 'featured-image', true );
		$thumbnail_image = '';
		$thumbnail_image_src = wp_get_attachment_image_src( $image_id, 'thumbnail' );
		if ( ! empty( $thumbnail_image_src ) ) {
			$thumbnail_image = current( $thumbnail_image_src );
		}

		$styles = "background: url('{$thumbnail_image}') 50% 50% / cover no-repeat; width: 240px; height: 135px; margin-bottom: 1em; border: 1px solid #aaa";

		?><tr class="form-field">
			<th valign="top" scope="row">
				<label>Featured Image</label>
			</th>
			<td>
				<input type="hidden" name="featured-image" value="<?php echo esc_attr( $image_id ); ?>">
				<div style="<?php echo esc_attr( $styles ); ?>"></div>
				<button type="button" class="button select-image" title="Select Image">Select</button>
				<button type="button" class="button clear-image">Clear</button>
				<?php wp_nonce_field( 'featured-image-' . $term->term_id, '__featured_image_nonce', false ); ?>
			</td>
		</tr><?php
	}

	/**
	 * Saves featured image for a term.
	 *
	 * @access public
	 * @action edited_{taxonomy}
	 * @param int $term_id
	 * @param int $tt_id
	 */
	public function save_featured_image( $term_id, $tt_id ) {
		$term = get_term_by( 'term_taxonomy_id', $tt_id );
		if ( ! is_a( $term, '\WP_Term' ) ) {
			return;
		}

		$taxonomy_ct = get_taxonomy( $term->taxonomy );
		if ( ! current_user_can( $taxonomy_ct->cap->manage_terms ) ) {
			return;
		}

		$nonce = filter_input( INPUT_POST, '__featured_image_nonce' );
		if ( ! wp_verify_nonce( $nonce, 'featured-image-' . $term_id ) ) {
			return;
		}

		$featured_image = filter_input( INPUT_POST, 'featured-image', FILTER_VALIDATE_INT );
		update_term_meta( $term_id, 'featured-image', $featured_image );
	}

	/**
	 * Adds featured image column.
	 *
	 * @access public
	 * @filter manage_edit-{taxonomy}_columns
	 * @param array $columns
	 * @return array
	 */
	public function add_featured_image_column( $columns ) {
		$newcolumns = array();
		foreach ( $columns as $key => $label ) {
			$newcolumns[ $key ] = $label;
			if ( 'cb' == $key ) {
				$newcolumns['thumbnail'] = 'Thumbnail';
			}
		}

		return $newcolumns;
	}

	/**
	 * Returns featured image value.
	 *
	 * @access public
	 * @filter manage_{taxonomy}_custom_column
	 * @param mixed $value
	 * @param string $column
	 * @param int $term_id
	 * @return mixed
	 */
	public function get_featured_image_column( $value, $column, $term_id ) {
		if ( 'thumbnail' == $column ) {
			$image = '';
			$class = 'newsroom-thumbnail thumbnail-image';
			$post_thumbnail_id = get_term_meta( $term_id, 'featured-image', true );
			if ( $post_thumbnail_id ) {
				$image = current( wp_get_attachment_image_src( $post_thumbnail_id ) );
				$class .= ' -with-image';
			}

			return sprintf(
				'<div class="%s" style="width:100px;height:75px;border:1px solid #ccc;background:url(%s) center"></div>',
				esc_attr( $class ),
				esc_url( $image )
			);
		}

		return $value;
	}

}

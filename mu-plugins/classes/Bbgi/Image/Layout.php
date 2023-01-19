<?php

namespace Bbgi\Image;

/**
 * Layout module manages Featured Image display variations. The
 * currently supported Featured Image layout variations are,
 *
 * - Top ( Above title )
 * - Inline ( Below title )
 * - None ( Hide Featured Image )
 * - Poster ( Legacy layout, this is converted to Inline on the fly )
 */
class Layout extends \Bbgi\Module {

	/**
	 * Connects the layout module with WordPress.
	 */
	public function register() {
		add_action( 'post_submitbox_misc_actions', [ $this, 'render' ], 1000 );
		add_action( 'save_post', [ $this, 'save_layout_if' ] );

		wp_register_script(
			'feature-image-layout',
			plugins_url( 'feature-image-layout.js', __FILE__ ),
			[ 'jquery' ],
			'1.0.0',
			true
		);
	}

	/**
	 * Currently supported Featured image layouts. Key is saved as
	 * postmeta values.
	 */
	public function get_choices() {
		return [
			'top'    => 'Top',
			'inline' => 'Inline',
			'none'   => 'None',
		];
	}

	/**
	 * Returns the saved featured image preference. Defaults to 'inline'
	 * if absent.
	 *
	 * @param int $post_id The post id
	 * @return string
	 */
	public function get_choice( $post_id ) {
		$choice = get_post_meta( $post_id, 'post_feature_image_preference', true );

		if ( empty( $choice ) ) {
			$choice = 'inline';
		} else if ( $choice === 'poster' ) {
			$choice = 'inline';
		}

		return $choice;
	}

	/**
	 * Returns the label for the feature image layout key
	 *
	 * @param string $choice The choice key
	 * @return string
	 */
	public function get_choice_label( $choice ) {
		$choices = $this->get_choices();

		if ( ! empty( $choices[ $choice ] ) ) {
			return $choices[ $choice ];
		} else {
			return '';
		}
	}

	/**
	 * Renders the featured image markup into the publish metabox if
	 * supported.
	 */
	public function render() {
		global $post;
		if ( ! post_type_supports( $post->post_type, 'flexible-feature-image' ) ) {
			return;
		}

		$post_id       = $post->ID;
		$choices       = $this->get_choices();
		$current       = $this->get_choice( $post_id );
		$current_label = $this->get_choice_label( $current );

		wp_enqueue_script( 'feature-image-layout' );

		?>
			<div class="misc-pub-section misc-pub-feature-image-layout" id="feature-image-layout">
				<i class="dashicons dashicons-format-image" style="opacity: 0.5"></i>
				Feature Image Layout:
				<span id="post-visibility-display" class="selected-feature-image-layout">
					<?php echo esc_html( $current_label ); ?>
				</span>

				<a
					href="#feature-image-layout"
					class="edit-feature-image-layout" role="button" >
					<span aria-hidden="true">Edit</span>
					<span class="screen-reader-text">Edit Feature Image Layout</span>
				</a>

				<input
					type="hidden"
					name="feature_image_layout_nonce"
					value="<?php echo esc_attr( wp_create_nonce( 'feature_image_layout' ) ); ?>">

				<input
					type="hidden"
					name="feature_image_layout"
					id="feature_image_layout"
					value="<?php echo esc_attr( ! empty ( $current ) ? $current : '' ); ?>">

				<div id="feature-image-layout-drawer" style="display: none">

					<?php foreach ( $choices as $choice_key => $choice_label ) {

						$checked = $choice_key === $current;
						$id      = 'choice-' . $choice_key;

					?>
						<input
							type="radio"
							name="feature_layout_choice"
							id="<?php echo esc_attr( $id ); ?>"
							data-label=<?php echo esc_attr( $choice_label ); ?>
							value="<?php echo esc_attr( $choice_key ); ?>"
							<?php echo $checked ? 'checked=checked' : ''; ?>
						>

						<label
							for="<?php echo esc_attr( $id ); ?>"
							class="selectit">
							<?php echo esc_html( $choice_label ); ?>
						</label>

						<br>
					<?php } ?>

					<p>
						<a
							href="#feature-image-layout"
							class="button button-save-image-layout">OK</a>
						<a
							href="#feature-image-layout"
							class="button-cancel button-cancel-image-layout">Cancel</a>
					</p>

				</div>

			</div>

		<?php
	}

	/**
	 * Saves the layout from POST if the current context is valid.
	 *
	 * @param int $post_id The post to save
	 * @return void
	 */
	public function save_layout_if( $post_id ) {
		if ( $this->can_save_layout( $post_id ) ) {
			$this->save_layout( $post_id );
		}
	}

	/**
	 * Saves the Featured Image Layout preference to post meta
	 *
	 * @param int $post_id The post to update
	 * @return void
	 */
	public function save_layout( $post_id ) {
		$layout = ! empty( $_POST['feature_image_layout'] )
			? sanitize_text_field( $_POST['feature_image_layout'] ) : '';

		update_post_meta( $post_id, 'post_feature_image_preference', $layout );
	}

	/**
	 * Checks if the current post save context is valid to save layout
	 * meta.
	 *
	 * @param int $post_id The post to save
	 * @return bool
	 */
	public function can_save_layout( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return false;
		}

		if ( current_user_can( 'edit_post', $post_id ) ) {
			$nonce = ! empty( $_POST['feature_image_layout_nonce'] )
				? sanitize_text_field( $_POST['feature_image_layout_nonce'] ) : '';

			return wp_verify_nonce( $nonce, 'feature_image_layout' );
		} else {
			return false;
		}
	}

}

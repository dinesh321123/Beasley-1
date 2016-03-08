<?php

namespace Greater_Media\Flexible_Feature_Images;

/**
 * Class GM_FlexibleFeatureImages
 *
 * Adds meta data to a post or custom post type to allow flexible display of the feature image.
 *
 */
class GM_FlexibleFeatureImages {

	public function __construct(){

		add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ), 1000 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 20, 0 );
    add_action( 'save_post', array( $this, 'save_post' ) );

	}

	/**
	 * Render feature image preference field in the post submitbox
	 */
	public function post_submitbox_misc_actions() {

		global $post;

		if ( ! post_type_supports( $post->post_type, 'flexible-feature-image' ) ) {
			return;
		}

		$feature_image_preference      = self::sanitize_feature_image_preference( get_post_meta( $post->ID, 'post_feature_image_preference', true ) );
		$feature_image_preference_desc = self::feature_image_preference_description( $feature_image_preference );

		include trailingslashit( GMR_FLEXIBLE_FEATURE_IMAGES_PATH ) . 'tpl/post-submitbox-feature-image-preference.tpl.php';

	}

	/**
	 * Make sure an age restriction value is one of the accepted ones
	 *
	 * @param string $input value to sanitize
	 *
	 * @return string valid age restriction value or ''
	 */
	protected static function sanitize_feature_image_preference( $input ) {

		// Immediate check for something way wrong
		if ( ! is_string( $input ) ) {
			return '';
		}

		static $valid_values;
		if ( ! isset( $valid_values ) ) {
			$valid_values = array( 'poster', 'top', 'inline', 'none' );
		}

		// Sanitize
		if ( in_array( $input, $valid_values ) ) {
			return $input;
		} else {
			return '';
		}

	}

	/**
	 * Returns a translated description of a feature image preference
	 *
	 * @param string $feature_image_preference
	 *
	 * @return string description
	 */
	protected static function feature_image_preference_description( $feature_image_preference ) {

		if ( 'none' === $feature_image_preference ) {
			return __( 'None', 'greatermedia-feature-image-preference' );
		} else if ( 'top' === $feature_image_preference ) {
			return __( 'Top', 'greatermedia-feature-image-preference' );
		} else if ( 'inline' === $feature_image_preference ) {
			return __( 'Inline', 'greatermedia-feature-image-preference' );
		} else {
			return __( 'Poster', 'greatermedia-feature-image-preference' );
		}

	}

	/**
	 * Print out HTML form elements for editing post or comment publish date.
	 *
	 * @param int|bool $edit              Accepts 1|true for editing the date, 0|false for adding the date.
	 * @param int      $age_restriction   Current age restriction setting
	 * @param int      $tab_index         Starting tab index
	 * @param int      $multi             Optional. Whether the additional fields and buttons should be added.
	 *                                    Default 0|false.
	 *
	 * @return string HTML
	 * @see  touch_time() in wp-admin/includes/template.php
	 * @todo use a template instead of string concatenation for building HTML
	 */
	public function touch_feature_image_preference( $edit = 1, $feature_image_preference = '', $tab_index = 0, $multi = 0 ) {

		global $wp_locale;

		$html = '';

		$tab_index_attribute = '';
		if ( (int) $tab_index > 0 ) {
			$tab_index_attribute = " tabindex=\"$tab_index\"";
		}

		$feature_image_preference = self::sanitize_feature_image_preference( $feature_image_preference );

		$html .= '<div class="feature-image-preference-wrap">';
		$html .= '<label for="fip_status" class="screen-reader-text">' . __( 'Feature Image Preference', 'greatermedia-feature-image-preference' ) . '</label>';
		$html .= '<fieldset id="fip_status"' . $tab_index_attribute . ">\n";
		$html .= '<p><input type="radio" name="fip_status" value="poster" ' . ( empty( $feature_image_preference ) ? 'checked="checked"' : checked( 'poster', $feature_image_preference, false ) ) . ' />' .
		         __( 'Poster', 'greatermedia-feature-image-preference' ) .
		         '</p>';
		$html .= '<p><input type="radio" name="fip_status" value="top" ' . checked( 'top', $feature_image_preference, false ) . ' />' .
		         __( 'Top', 'greatermedia-feature-image-preference' ) .
		         '</p>';
		$html .= '<p><input type="radio" name="fip_status" value="inline" ' . checked( 'inline', $feature_image_preference, false ) . ' />' .
			       __( 'Inline', 'greatermedia-feature-image-preference' ) .
			       '</p>';
		$html .= '<p><input type="radio" name="fip_status" value="none" ' . checked( 'none', $feature_image_preference, false ) . ' />' .
		         __( 'None', 'greatermedia-feature-image-preference' ) .
		         '</p>';
		$html .= '<input type="hidden" id="hidden_feature_image_preference" name="hidden_feature_image_preference" value="' . esc_attr( $feature_image_preference ) . '" />';
		$html .= '</fieldset>';
		$html .= '<p>';
		$html .= '<a href="#edit_feature_image_preference" class="save-feature-image-preference hide-if-no-js button">' . __( 'OK' ) . '</a>';
		$html .= '<a href="#edit_feature_image_preference" class="cancel-feature-image-preference hide-if-no-js button-cancel">' . __( 'Cancel' ) . '</a>';
		$html .= '</p>';
		$html .= '</div>';

		return $html;

	}

	/**
	 * Enqueue JavaScript and CSS resources for admin functionality as needed
	 */
	public function admin_enqueue_scripts() {

		global $post;

		if ( $post && post_type_supports( $post->post_type, 'flexible-feature-image' ) ) {

			// Enqueue JavaScript
			wp_enqueue_script( 'greatermedia-fip-admin-js', trailingslashit( GMR_FLEXIBLE_FEATURE_IMAGES_URL ) . 'js/flexible-feature-images-admin.js', array(
				'jquery',
			), false, true );

			$feature_image_preference = get_post_meta( $post->ID, 'post_feature_image_preference', true );

			// Settings & translation strings used by the JavaScript code
			$settings = array(
				'templates'          => array(
					'feature_image_preference' => self::touch_feature_image_preference( 1, $feature_image_preference ),
				),
				'rendered_templates' => array(),
				'strings'            => array(
					'Poster'                    => __( 'Poster', 'greatermedia-feature-image-preference' ),
					'Top'                    => __( 'Top', 'greatermedia-feature-image-preference' ),
					'Inline'                => __( 'Inline', 'greatermedia-feature-image-preference' ),
					'None'                 => __( 'None', 'greatermedia-feature-image-preference' ),
				),
			);

			wp_localize_script( 'greatermedia-fip-admin-js', 'GreaterMediaFeatureImagePreference', $settings );

		}
	}

	/**
	 * On admin UI post save, update the feature image preference postmeta
	 *
	 * @param int $post_id Post ID
	 */
	public function save_post( $post_id ) {

		$post = get_post( $post_id );

		if ( post_type_supports( $post->post_type, 'flexible-feature-image' ) ) {

			delete_post_meta( $post_id, 'post_feature_image_preference' );

			if ( isset( $_POST['fip_status'] ) ) {

				$feature_image_preference = self::sanitize_feature_image_preference( $_POST['fip_status'] );
				if ( '' !== $feature_image_preference ) {
					add_post_meta( $post_id, 'post_feature_image_preference', $feature_image_preference );
				}

			}

		} else {

			// Clean up any post expiration data that might already exist, in case the post support changed
			delete_post_meta( $post_id, 'post_feature_image_preference' );

			return;

		}

	}

}

new GM_FlexibleFeatureImages();

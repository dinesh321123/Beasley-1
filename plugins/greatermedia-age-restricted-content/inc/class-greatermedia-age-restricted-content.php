<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaAgeRestrictedContent extends VisualShortcode {

	function __construct() {

		parent::__construct(
			'age-restricted',
			'GreaterMediaAgeRestrictedContentAdmin',
			'dashicons-businessman',
			null,
			__( 'Age Restriction', 'age-restricted-content' )
		);

		add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ), 30, 0 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 20, 0 );
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
		add_filter( 'the_content', array( $this, 'the_content' ) );

		/**
		 * wpautop usually runs before shortcode processing, meaning the shortcodes'
		 * output isn't properly wrapped in paragraphs. This forces it to run later
		 * and catch unwrapped shortcode output, like this plugin's.
		 */
		add_filter( 'the_content', 'wpautop', 20 );

	}

	/**
	 * Set up the textdomain, even thought we don't really use it
	 */
	public function plugins_loaded() {
		load_plugin_textdomain( 'greatermedia-age-restricted-content', false, GREATER_MEDIA_AGE_RESTRICTED_CONTENT_PATH );
	}

	/**
	 * Render an expiration time field in the post submitbox
	 */
	public function post_submitbox_misc_actions() {

		global $post;

		if ( ! post_type_supports( $post->post_type, 'age-restricted-content' ) ) {
			return;
		}

		$age_restriction      = self::sanitize_age_restriction( get_post_meta( $post->ID, 'post_age_restriction', true ) );
		$age_restriction_desc = self::age_restriction_description( $age_restriction );

		include trailingslashit( GREATER_MEDIA_AGE_RESTRICTED_CONTENT_PATH ) . 'tpl/post-submitbox-misc-actions.tpl.php';

	}

	/**
	 * Enqueue JavaScript and CSS resources for admin functionality as needed
	 */
	public function admin_enqueue_scripts() {

		global $post;

		if ( $post && post_type_supports( $post->post_type, 'age-restricted-content' ) ) {

			// Enqueue CSS
			wp_enqueue_style( 'greatermedia-ac', trailingslashit( GREATER_MEDIA_AGE_RESTRICTED_CONTENT_URL ) . 'css/greatermedia-age-restricted-content.css' );

			// Enqueue JavaScript
			wp_enqueue_script( 'greatermedia-ac-admin-js', trailingslashit( GREATER_MEDIA_AGE_RESTRICTED_CONTENT_URL ) . 'js/greatermedia-age-restricted-content-admin.js', array(
				'jquery',
			), false, true );

			$age_restriction = get_post_meta( $post->ID, 'post_age_restriction', true );

			// Settings & translation strings used by the JavaScript code
			$settings = array(
				'templates'          => array(
					'tinymce'         => file_get_contents( trailingslashit( GREATER_MEDIA_AGE_RESTRICTED_CONTENT_PATH ) . 'tpl/tinymce-view-template.js' ),
					'age_restriction' => self::touch_age_restriction( 1, $age_restriction ),
				),
				'rendered_templates' => array(),
				'strings'            => array(
					'Age Restricted Content' => __( 'Age Restricted Content', 'greatermedia-age-restricted-content' ),
					'Restricted to'          => __( 'Restricted to', 'greatermedia-age-restricted-content' ),
					/**
					 * Separate string for "Must be:" with a colon because the colon may be used differently in
					 * a different locale
					 */
					'Restricted to:'         => __( 'Restricted to:', 'greatermedia-age-restricted-content' ),
					'18+'                    => __( '18+', 'greatermedia-age-restricted-content' ),
					'21+'                    => __( '21+', 'greatermedia-age-restricted-content' ),
					'Content'                => __( 'Content', 'greatermedia-age-restricted-content' ),
					'Status'                 => __( 'Status', 'greatermedia-age-restricted-content' ),
					'No restriction'         => __( 'No restriction', 'greatermedia-age-restricted-content' ),
				),
			);

			wp_localize_script( 'greatermedia-ac-admin-js', 'GreaterMediaAgeRestrictedContent', $settings );

		}
	}

	/**
	 * On admin UI post save, update the expiration date postmeta
	 *
	 * @param int $post_id Post ID
	 */
	public function save_post( $post_id ) {

		$post = get_post( $post_id );

		if ( post_type_supports( $post->post_type, 'age-restricted-content' ) ) {

			delete_post_meta( $post_id, 'post_age_restriction' );

			if ( isset( $_POST['ar_status'] ) ) {

				$age_restriction = self::sanitize_age_restriction( $_POST['ar_status'] );
				if ( '' !== $age_restriction ) {
					add_post_meta( $post_id, 'post_age_restriction', $age_restriction );
				}

			}

		} else {

			// Clean up any post expiration data that might already exist, in case the post support changed
			delete_post_meta( $post_id, 'post_age_restriction' );

			return;

		}

	}

	/**
	 * Print out HTML form date elements for editing post or comment publish date.
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
	public function touch_age_restriction( $edit = 1, $age_restriction = '', $tab_index = 0, $multi = 0 ) {

		global $wp_locale;

		$html = '';

		$tab_index_attribute = '';
		if ( (int) $tab_index > 0 ) {
			$tab_index_attribute = " tabindex=\"$tab_index\"";
		}

		$age_restriction = self::sanitize_age_restriction( $age_restriction );

		$html .= '<div class="age-restriction-wrap">';
		$html .= '<label for="ar_status" class="screen-reader-text">' . __( 'Status', 'greatermedia-age-restricted-content' ) . '</label>';
		$html .= '<fieldset id="ar_status"' . $tab_index_attribute . ">\n";
		$html .= '<p><input type="radio" name="ar_status" value="18plus" ' . checked( '18plus', $age_restriction, false ) . ' />' .
		         __( '18+', 'greatermedia-age-restricted-content' ) .
		         '</p>';
		$html .= '<p><input type="radio" name="ar_status" value="21plus" ' . checked( '21plus', $age_restriction, false ) . ' />' .
		         __( '21+', 'greatermedia-age-restricted-content' ) .
		         '</p>';
		$html .= '<p><input type="radio" name="ar_status" value="" ' . ( empty( $age_restriction ) ? 'checked="checked"' : '' ) . ' />' .
		         __( 'No restriction', 'greatermedia-age-restricted-content' ) .
		         '</p>';
		$html .= '<input type="hidden" id="hidden_age_restriction" name="hidden_age_restriction" value="' . esc_attr( $age_restriction ) . '" />';
		$html .= '</fieldset>';
		$html .= '<p>';
		$html .= '<a href="#edit_age_restriction" class="save-age-restriction hide-if-no-js button">' . __( 'OK' ) . '</a>';
		$html .= '<a href="#edit_age_restriction" class="cancel-age-restriction hide-if-no-js button-cancel">' . __( 'Cancel' ) . '</a>';
		$html .= '</p>';
		$html .= '</div>';

		return $html;

	}

	/**
	 * Process the age-restricted shortcode
	 *
	 * @param      array  $attributes
	 * @param string|null $content optional content to display
	 *
	 * @return null|string output to display
	 */
	public function process_shortcode( array $attributes, $content = null ) {

		global $wp;
		if ( isset( $attributes['status'] ) ) {
			$age_restriction = self::sanitize_age_restriction( $attributes['status'] );
		} else {
			$age_restriction = '';
		}

		$current_url = '/' . trim( $wp->request, '/' );
		$login_url   = gigya_profile_path( 'login', array( 'dest' => $current_url ) );

		if ( ( '18plus' === $age_restriction ) && ( ! is_gigya_user_logged_in() || 18 > absint( get_gigya_user_field( 'age' ) ) ) ) {
			ob_start();
			include GREATER_MEDIA_AGE_RESTRICTED_CONTENT_PATH . '/tpl/age-restricted-shortcode-render.tpl.php';

			return ob_get_clean();
		} elseif ( ( '21plus' === $age_restriction ) && ( ! is_gigya_user_logged_in() || 21 > absint( get_gigya_user_field( 'age' ) ) ) ) {
			ob_start();
			include GREATER_MEDIA_AGE_RESTRICTED_CONTENT_PATH . '/tpl/age-restricted-shortcode-render.tpl.php';

			return ob_get_clean();
		}


		// Fall-through, return content as-is
		return $content;

	}

	/**
	 * Make sure an age restriction value is one of the accepted ones
	 *
	 * @param string $input value to sanitize
	 *
	 * @return string valid age restriction value or ''
	 */
	protected static function sanitize_age_restriction( $input ) {

		// Immediate check for something way wrong
		if ( ! is_string( $input ) ) {
			return '';
		}

		static $valid_values;
		if ( ! isset( $valid_values ) ) {
			$valid_values = array( '18plus', '21plus' );
		}

		// Sanitize
		if ( in_array( $input, $valid_values ) ) {
			return $input;
		} else {
			return '';
		}

	}

	/**
	 * Returns a translated description of an age restriction
	 *
	 * @param string $age_restriction
	 *
	 * @return string description
	 */
	protected static function age_restriction_description( $age_restriction ) {

		if ( '18plus' === $age_restriction ) {
			return __( '18+', 'greatermedia-age-restricted-content' );
		} else if ( '21plus' === $age_restriction ) {
			return __( '21+', 'greatermedia-age-restricted-content' );
		} else {
			return __( 'No restriction', 'greatermedia-age-restricted-content' );
		}

	}

	public function the_content( $content ) {

		global $post, $wp;

		$age_restriction = self::sanitize_age_restriction( get_post_meta( $post->ID, 'post_age_restriction', true ) );
		$current_url     = home_url( add_query_arg( array(), $wp->request ) );

		if ( ( '18plus' === $age_restriction ) && ( ! is_gigya_user_logged_in() || 18 > absint( get_gigya_user_field( 'age' ) ) ) ) {
			include GREATER_MEDIA_AGE_RESTRICTED_CONTENT_PATH . '/tpl/age-restricted-post-render.tpl.php';

			return;
		} elseif ( ( '21plus' === $age_restriction ) && ( ! is_gigya_user_logged_in() || 21 > absint( get_gigya_user_field( 'age' ) ) ) ) {
			include GREATER_MEDIA_AGE_RESTRICTED_CONTENT_PATH . '/tpl/age-restricted-post-render.tpl.php';

			return;
		}

		// Fall-through, return content as-is
		return $content;

	}

}

$GreaterMediaAgeRestrictedContent = new GreaterMediaAgeRestrictedContent ();
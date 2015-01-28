<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaLoginRestrictedContent extends VisualShortcode {

	public function __construct() {

		parent::__construct(
			'login-restricted',
			'GreaterMediaLoginRestrictedContentAdmin',
			'dashicons-admin-network',
			null,
			__( 'Login Restriction', 'login-restricted-content' ),
			12
		);

		add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ), 30, 0 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 20, 0 );
		add_action( 'save_post', array( $this, 'save_post' ) );
		
		add_filter( 'the_excerpt', array( $this, 'the_content' ), 100 );
		add_filter( 'the_content', array( $this, 'the_content' ), 100 );
		add_filter( 'wp_trim_words', array( $this, 'untrim_restricted_markup' ), 10, 4 );
		
	}

	public function untrim_restricted_markup( $text, $num_words, $more, $original_text ) {
		$anchors = array( 'login-restricted-shield-', 'logout-restricted-shield-' );
		foreach ( $anchors as $anchor ) {
			if ( mb_stripos( $original_text, $anchor ) !== false ) {
				return str_replace( PHP_EOL, '', $original_text );
			}
		}

		return $text;
	}

	/**
	 * Render an expiration time field in the post submitbox
	 */
	public function post_submitbox_misc_actions() {

		global $post;

		if ( ! post_type_supports( $post->post_type, 'login-restricted-content' ) ) {
			return;
		}

		$login_restriction      = self::sanitize_login_restriction( get_post_meta( $post->ID, 'post_login_restriction', true ) );
		$login_restriction_desc = self::login_restriction_description( $login_restriction );

		include trailingslashit( GREATER_MEDIA_LOGIN_RESTRICTED_CONTENT_PATH ) . 'tpl/post-submitbox-misc-actions.tpl.php';

	}

	/**
	 * Enqueue JavaScript and CSS resources for admin functionality as needed
	 */
	public function admin_enqueue_scripts() {

		global $post;

		if ( $post && post_type_supports( $post->post_type, 'login-restricted-content' ) ) {

			// Enqueue CSS
			wp_enqueue_style( 'greatermedia-lc', trailingslashit( GREATER_MEDIA_LOGIN_RESTRICTED_CONTENT_URL ) . 'css/greatermedia-login-restricted-content.css' );

			// Enqueue JavaScript
			wp_enqueue_script( 'greatermedia-lc-admin-js', trailingslashit( GREATER_MEDIA_LOGIN_RESTRICTED_CONTENT_URL ) . 'js/greatermedia-login-restricted-content-admin.js', array(
				'jquery',
			), false, true );

			$login_restriction = get_post_meta( $post->ID, 'post_login_restriction', true );

			// Settings & translation strings used by the JavaScript code
			$settings = array(
				'templates'          => array(
					'tinymce'           => file_get_contents( trailingslashit( GREATER_MEDIA_LOGIN_RESTRICTED_CONTENT_PATH ) . 'tpl/tinymce-view-template.js' ),
					'login_restriction' => self::touch_login_restriction( 1, $login_restriction ),
				),
				'rendered_templates' => array(),
				'strings'            => array(
					'Login Restricted Content' => __( 'Login Restricted Content', 'greatermedia-login-restricted-content' ),
					'Must be'                  => __( 'Must be', 'greatermedia-login-restricted-content' ),
					/**
					 * Separate string for "Must be:" with a colon because the colon may be used differently in
					 * a different locale
					 */
					'Must be:'                 => __( 'Must be:', 'greatermedia-login-restricted-content' ),
					'logged in'                => __( 'logged in', 'greatermedia-login-restricted-content' ),
					'logged out'               => __( 'logged out', 'greatermedia-login-restricted-content' ),
					'Content'                  => __( 'Content', 'greatermedia-login-restricted-content' ),
					'Status'                   => __( 'Status', 'greatermedia-login-restricted-content' ),
					'Logged in'                => __( 'Logged in', 'greatermedia-login-restricted-content' ),
					'Logged out'               => __( 'Logged out', 'greatermedia-login-restricted-content' ),
					'No restriction'           => __( 'No restriction', 'greatermedia-login-restricted-content' ),
				),
			);

			wp_localize_script( 'greatermedia-lc-admin-js', 'GreaterMediaLoginRestrictedContent', $settings );

		}
	}

	/**
	 * On admin UI post save, update the expiration date postmeta
	 *
	 * @param int $post_id Post ID
	 */
	public function save_post( $post_id ) {

		$post = get_post( $post_id );

		if ( post_type_supports( $post->post_type, 'login-restricted-content' ) ) {

			delete_post_meta( $post_id, 'post_login_restriction' );

			if ( isset( $_POST['lr_status'] ) ) {

				$login_restriction = self::sanitize_login_restriction( $_POST['lr_status'] );
				if ( '' !== $login_restriction ) {
					add_post_meta( $post_id, 'post_login_restriction', $login_restriction );
				}

			}

		} else {

			// Clean up any post expiration data that might already exist, in case the post support changed
			delete_post_meta( $post_id, 'post_login_restriction' );

			return;

		}

	}

	/**
	 * Print out HTML form date elements for editing post or comment publish date.
	 *
	 * @param int|bool $edit              Accepts 1|true for editing the date, 0|false for adding the date.
	 * @param int      $login_restriction Current login restriction setting
	 * @param int      $tab_index         Starting tab index
	 * @param int      $multi             Optional. Whether the additional fields and buttons should be added.
	 *                                    Default 0|false.
	 *
	 * @return string HTML
	 * @see  touch_time() in wp-admin/includes/template.php
	 * @todo use a template instead of string concatenation for building HTML
	 */
	public function touch_login_restriction( $edit = 1, $login_restriction = '', $tab_index = 0, $multi = 0 ) {

		global $wp_locale;

		$html = '';

		$tab_index_attribute = '';
		if ( (int) $tab_index > 0 ) {
			$tab_index_attribute = " tabindex=\"$tab_index\"";
		}

		$login_restriction = self::sanitize_login_restriction( $login_restriction );

		$html .= '<div class="login-restriction-wrap">';
		$html .= '<label for="lr_status" class="screen-reader-text">' . __( 'Status', 'greatermedia-login-restricted-content' ) . '</label>';
		$html .= '<fieldset id="lr_status"' . $tab_index_attribute . ">\n";
		$html .= '<p><input type="radio" name="lr_status" value="logged-in" ' . checked( 'logged-in', $login_restriction, false ) . ' />' .
		         __( 'Logged in', 'greatermedia-login-restricted-content' ) .
		         '</p>';
		$html .= '<p><input type="radio" name="lr_status" value="logged-out" ' . checked( 'logged-out', $login_restriction, false ) . ' />' .
		         __( 'Logged out', 'greatermedia-login-restricted-content' ) .
		         '</p>';
		$html .= '<p><input type="radio" name="lr_status" value="" ' . ( empty( $login_restriction ) ? 'checked="checked"' : '' ) . ' />' .
		         __( 'None', 'greatermedia-login-restricted-content' ) .
		         '</p>';
		$html .= '<input type="hidden" id="hidden_login_restriction" name="hidden_login_restriction" value="' . esc_attr( $login_restriction ) . '" />';
		$html .= '</fieldset>';
		$html .= '<p>';
		$html .= '<a href="#edit_login_restriction" class="save-login-restriction hide-if-no-js button">' . __( 'OK' ) . '</a>';
		$html .= '<a href="#edit_login_restriction" class="cancel-login-restriction hide-if-no-js button-cancel">' . __( 'Cancel' ) . '</a>';
		$html .= '</p>';
		$html .= '</div>';

		return $html;

	}

	/**
	 * Process the login-restricted shortcode
	 *
	 * @param      array  $attributes
	 * @param string|null $content optional content to display
	 *
	 * @return null|string output to display
	 */
	public function process_shortcode( array $attributes, $content = null ) {

		if ( isset( $attributes['status'] ) ) {
			$login_restriction = self::sanitize_login_restriction( $attributes['status'] );
		} else {
			$login_restriction = '';
		}

		if ( ( 'logged-in' === $login_restriction ) && ! is_gigya_user_logged_in() ) {
			ob_start();
			
			include GREATER_MEDIA_LOGIN_RESTRICTED_CONTENT_PATH . '/tpl/login-restricted-shortcode-render.tpl.php';
			
			return ob_get_clean();
		} elseif ( ( 'logged-out' === $login_restriction ) && is_gigya_user_logged_in() ) {
			return '';
		}

		/**
		 * wpautop usually runs before shortcode processing, meaning the shortcodes'
		 * output isn't properly wrapped in paragraphs. Run it directly to catch unwrapped shortcode output, like this plugin's.
		 */
		return wpautop( $content );

	}

	/**
	 * Make sure a login restriction value is one of the accepted ones
	 *
	 * @param string $input value to sanitize
	 *
	 * @return string valid login restriction value or ''
	 */
	protected static function sanitize_login_restriction( $input ) {

		// Immediate check for something way wrong
		if ( ! is_string( $input ) ) {
			return '';
		}

		static $valid_values;
		if ( ! isset( $valid_values ) ) {
			$valid_values = array( 'logged-in', 'logged-out' );
		}

		// Sanitize
		if ( in_array( $input, $valid_values ) ) {
			return $input;
		} else {
			return '';
		}

	}

	/**
	 * Returns a translated description of a login restriction
	 *
	 * @param string $login_restriction
	 *
	 * @return string description
	 */
	protected static function login_restriction_description( $login_restriction ) {

		if ( 'logged-in' === $login_restriction ) {
			return __( 'Logged in', 'greatermedia-login-restricted-content' );
		} else if ( 'logged-out' === $login_restriction ) {
			return __( 'Logged out', 'greatermedia-login-restricted-content' );
		} else {
			return __( 'None', 'greatermedia-login-restricted-content' );
		}

	}

	public function the_content( $content ) {

		global $post, $wp;

		// do nothing if the $post variable is not set
		if ( empty( $post ) ) {
			return $content;
		}

		$login_restriction = self::sanitize_login_restriction( get_post_meta( $post->ID, 'post_login_restriction', true ) );
		$current_url = '/' . trim( $wp->request, '/' );

		if ( ( 'logged-in' === $login_restriction ) && ! is_gigya_user_logged_in() ) {
			ob_start();
			include GREATER_MEDIA_LOGIN_RESTRICTED_CONTENT_PATH . '/tpl/login-restricted-post-render.tpl.php';
			return ob_get_clean();
		} elseif ( ( 'logged-out' === $login_restriction ) && is_gigya_user_logged_in() ) {
			return '';
		}

		// Fall-through, return content as-is
		return $content;

	}

}

$GreaterMediaLoginRestrictedContent = new GreaterMediaLoginRestrictedContent ();
<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaContestsMetaboxes {

	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'register_settings_fields' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );

	}

	/**
	 * Enqueue JavaScript & CSS
	 * Implements admin_enqueue_scripts action
	 */
	public function admin_enqueue_scripts() {

		global $post;

		// Make sure this is an admin screen
		if ( ! is_admin() ) {
			return;
		}

		// Make sure this is the post editor
		$current_screen = get_current_screen();
		if ( 'post' !== $current_screen->base ) {
			return;
		}

		// Make sure there's a post
		if ( ! isset( $GLOBALS['post'] ) || ! ( $GLOBALS['post'] instanceof WP_Post ) ) {
			return;
		}

		if ( $post && 'contest' === $post->post_type ) {

			wp_enqueue_style( 'formbuilder' );
			wp_enqueue_style( 'datetimepicker' );

			wp_enqueue_script( 'ie8-node-enum' );
			wp_enqueue_script( 'jquery-scrollwindowto' );
			wp_enqueue_script( 'underscore-mixin-deepextend' );
			wp_enqueue_script( 'backbone-deep-model' );
			wp_enqueue_script( 'datetimepicker' );

			wp_enqueue_script( 'formbuilder' );
			wp_enqueue_script( 'rivets' );
			wp_enqueue_style( 'font-awesome' );

			wp_enqueue_script( 'greatermedia-contests-admin', trailingslashit( GREATER_MEDIA_CONTESTS_URL ) . 'js/greatermedia-contests-admin.js', array( 'formbuilder' ), false, true );
			$embedded_form = get_post_meta( $post->ID, 'embedded_form', true );
			$settings      = array(
				'form' => trim( $embedded_form, '"' ),
			);
			wp_localize_script( 'greatermedia-contests-admin', 'GreaterMediaContestsForm', $settings );

		};
	}

	/**
	 * Register meta box fields through the Settings API
	 * Implements admin_enqueue_scripts action to be sure global $post is set by then
	 */
	public function register_settings_fields() {

		// Make sure this is an admin screen
		if ( ! is_admin() ) {
			return;
		}

		// Make sure this is the post editor
		$current_screen = get_current_screen();
		if ( 'post' !== $current_screen->base ) {
			return;
		}

		// Make sure there's a post
		if ( ! isset( $GLOBALS['post'] ) || ! ( $GLOBALS['post'] instanceof WP_Post ) ) {
			return;
		}

		$post_id = absint( $GLOBALS['post']->ID );

		add_settings_section(
			'greatermedia-contest-rules',
			null,
			array( $this, 'render_generic_settings_section' ),
			'greatermedia-contest-rules'
		);

		add_settings_section(
			'greatermedia-contest-form',
			null,
			array( $this, 'render_generic_settings_section' ),
			'greatermedia-contest-form'
		);

		add_settings_field(
			'contest-start-date',
			'Start Date',
			array( $this, 'render_date_field' ),
			'greatermedia-contest-rules',
			'greatermedia-contest-rules',
			array(
				'post_id' => $post_id,
				'id'      => 'greatermedia_contest_start',
				'name'    => 'greatermedia_contest_start',
				'value'   => get_post_meta( $post_id, 'contest-start', true )
			)
		);

		add_settings_field(
			'contest-end-date',
			'End Date',
			array( $this, 'render_date_field' ),
			'greatermedia-contest-rules',
			'greatermedia-contest-rules',
			array(
				'post_id' => $post_id,
				'id'      => 'greatermedia_contest_end',
				'name'    => 'greatermedia_contest_end',
				'value'   => get_post_meta( $post_id, 'contest-end', true )
			)
		);

		add_settings_field(
			'prizes-desc',
			'What You Win',
			array( $this, 'render_wysiwyg' ),
			'greatermedia-contest-rules',
			'greatermedia-contest-rules',
			array(
				'post_id' => $post_id,
				'id'      => 'greatermedia_contest_prizes',
				'name'    => 'greatermedia_contest_prizes',
				'value'   => get_post_meta( $post_id, 'prizes-desc', true )
			)
		);

		add_settings_field(
			'enter-desc',
			'How to Enter',
			array( $this, 'render_wysiwyg' ),
			'greatermedia-contest-rules',
			'greatermedia-contest-rules',
			array(
				'post_id' => $post_id,
				'id'      => 'greatermedia_contest_enter',
				'name'    => 'greatermedia_contest_enter',
				'value'   => get_post_meta( $post_id, 'how-to-enter-desc', true )
			)
		);

		add_settings_field(
			'rules-desc',
			'Official Contest Rules',
			array( $this, 'render_wysiwyg' ),
			'greatermedia-contest-rules',
			'greatermedia-contest-rules',
			array(
				'post_id' => $post_id,
				'id'      => 'greatermedia_contest_rules',
				'name'    => 'greatermedia_contest_rules',
				'value'   => get_post_meta( $post_id, 'rules-desc', true )
			)
		);

		$submit_text = get_post_meta( $post_id, 'form-submitbutton', true );
		if ( empty( $submit_text ) ) {
			// If you change this string, be sure to get all the places it's used in this class
			$submit_text = __( 'Submit', 'greatermedia_contests' );
		}

		add_settings_field(
			'form-submitbutton',
			'Submit Button Text',
			array( $this, 'render_input' ),
			'greatermedia-contest-form',
			'greatermedia-contest-form',
			array(
				'post_id' => $post_id,
				'id'      => 'greatermedia_contest_form_submit',
				'name'    => 'greatermedia_contest_form_submit',
				'size'    => 50,
				'value'   => $submit_text
			)
		);

		$thank_you = get_post_meta( $post_id, 'form-thankyou', true );
		if ( empty( $thank_you ) ) {
			// If you change this string, be sure to get all the places it's used in this class
			$thank_you = __( 'Thanks for entering!', 'greatermedia_contests' );
		}

		add_settings_field(
			'form-thankyou',
			'"Thank You" Message',
			array( $this, 'render_input' ),
			'greatermedia-contest-form',
			'greatermedia-contest-form',
			array(
				'post_id' => $post_id,
				'id'      => 'greatermedia_contest_form_thankyou',
				'name'    => 'greatermedia_contest_form_thankyou',
				'size'    => 50,
				'value'   => $thank_you
			)
		);

	}

	/**
	 * Render instructions for the settings section
	 */
	public function render_generic_settings_section() {
		// No special rendering
	}

	/**
	 * Render a WYSIWYG editor meta field
	 *
	 * @param array $args
	 */
	public function render_wysiwyg( array $args ) {

		wp_editor(
			isset( $args['value'] ) ? sanitize_text_field( $args['value'] ) : '',
			$args['id']
		);

	}

	/**
	 * Return an array of active Gravity Forms
	 *
	 */
	public function get_gravity_forms() {
		if ( class_exists( 'RGFormsModel' ) ) {
			$forms      = RGFormsModel::get_forms( null, 'title' );
			$form_array = array();
			foreach ( $forms as $form ) {
				$form_array[ $form->id ] = $form->title;
			}

			return $form_array;
		}
	}

	/**
	 * Render an HTML5 date input meta field
	 *
	 * @param array $args
	 */
	public function render_date_field( array $args ) {

		if ( isset( $args['value'] ) && is_numeric( $args['value'] ) ) {
			// HTML5 date input needs date in Y-m-d and will convert to local format on display
			$args['value'] = date( 'Y-m-d', $args['value'] );
		} else {
			$args['value'] = ''; // invalid, should be a unix timestamp
		}

		$args['type'] = 'date';
		self::render_input( $args );

	}

	public function render_input( array $args ) {

		if ( ! isset( $args['type'] ) || empty( $args['type'] ) ) {
			$args['type'] = 'text';
		}

		if ( isset( $args['size'] ) ) {
			$size_attr = 'size="' . absint( $args['size'] ) . '"';
		} else {
			$size_attr = '';
		}

		echo '<input type="' . esc_attr( $args['type'] ) . '" id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $args['name'] ) . '" value="' . esc_attr( $args['value'] ) . '" ' . $size_attr . ' />';

	}

	/**
	 * Register meta boxes on the Contest editor
	 * Implements add_meta_boxes action
	 */
	public function add_meta_boxes() {

		add_meta_box( 'rules', 'Contest Rules', array( $this, 'rules_meta_box' ), GreaterMediaContests::CPT_SLUG, 'normal', 'default', array() );
		add_meta_box( 'form', 'Form', array( $this, 'contest_embedded_form' ), GreaterMediaContests::CPT_SLUG, 'advanced', 'default', array() );
		add_meta_box( 'gallery', 'Gallery', array( $this, 'gallery_meta_box' ), GreaterMediaContests::CPT_SLUG, 'side' );

	}

	public function gallery_meta_box( WP_Post $post ) {
		
		$images = get_children( array(
			'numberposts'    => 500, // do we need more?
			'post_parent'    => $post->ID,
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_status'    => 'inherit',
		) );

		?><input type="text" class="widefat" readonly disabled value="<?php echo esc_attr( '[gallery ids="' . implode( ',', array_keys( $images ) ) . '"]' ); ?>">
		<span class="description">
			To create a standalone gallery of the entries, copy the content of this field, create a new Gallery post, then paste it in the content for that Gallery.
		</span><?php

	}

	/**
	 * Render the "rules" meta box on the Contest editor
	 */
	public function rules_meta_box() {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'contest_rules_meta_box', 'contest_rules_meta_box' );
		do_settings_sections( 'greatermedia-contest-rules' );

	}

	/**
	 * Render an embedded form editor on the Contest editor
	 */
	public function contest_embedded_form() {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'contest_form_meta_box', 'contest_form_meta_box' );
		include trailingslashit( GREATER_MEDIA_CONTESTS_PATH ) . 'tpl/contest-form-meta-box.tpl.php';
		do_settings_sections( 'greatermedia-contest-form' );

	}

	/**
	 * Save meta fields on post save
	 *
	 * @param int $post_id
	 */
	public function save_post( $post_id ) {

		$post = get_post( $post_id );

		// Check if our nonces are set.
		if ( ! isset( $_POST['contest_form_meta_box'] ) || ! isset( $_POST['contest_rules_meta_box'] ) ) {
			return;
		}

		// Verify that the form nonce is valid.
		if ( ! wp_verify_nonce( $_POST['contest_form_meta_box'], 'contest_form_meta_box' ) ) {
			return;
		}

		// Verify that the rules nonce is valid
		if ( ! wp_verify_nonce( $_POST['contest_rules_meta_box'], 'contest_rules_meta_box' ) ) {
			return;
		}

		// If this is an autosave, the editor has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Make sure the post type is correct
		if ( 'contest' !== $post->post_type ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		/**
		 * Update the form's meta field
		 * The form JSON has slashes in it which need to be stripped out.
		 * json_decode() and json_encode() are used here to sanitize the JSON & keep out invalid values
		 */
		$form = json_encode( json_decode( urldecode( $_POST['contest_embedded_form'] ) ) );
		// PHP's json_encode() may add quotes around the encoded string. Remove them.
		$form = trim( $form, '"' );
		update_post_meta( $post_id, 'embedded_form', $form );

		// Update the form's "submit button" text
		$submit_text = isset( $_POST['greatermedia_contest_form_submit'] ) ? $_POST['greatermedia_contest_form_submit'] : '';
		if ( empty( $submit_text ) ) {
			// If you change this string, be sure to get all the places it's used in this class
			$submit_button = __( 'Submit', 'greatermedia_contests' );
		}
		update_post_meta( $post_id, 'form-submitbutton', sanitize_text_field( $submit_text ) );

		// Update the form's "thank you" message
		$thank_you = isset( $_POST['greatermedia_contest_form_thankyou'] ) ? $_POST['greatermedia_contest_form_thankyou'] : '';
		if ( empty( $thank_you ) ) {
			// If you change this string, be sure to get all the places it's used in this class
			$thank_you = __( 'Thanks for entering!', 'greatermedia_contests' );
		}
		update_post_meta( $post_id, 'form-thankyou', sanitize_text_field( $thank_you ) );

		// Update the contest rules meta fields
		update_post_meta( $post_id, 'prizes-desc', wp_kses_post( $_POST['greatermedia_contest_prizes'] ) );
		update_post_meta( $post_id, 'how-to-enter-desc', wp_kses_post( $_POST['greatermedia_contest_enter'] ) );
		update_post_meta( $post_id, 'rules-desc', wp_kses_post( $_POST['greatermedia_contest_rules'] ) );
		update_post_meta( $post_id, 'contest-start', strtotime( $_POST['greatermedia_contest_start'] ) );
		update_post_meta( $post_id, 'contest-end', strtotime( $_POST['greatermedia_contest_end'] ) );

	}

}

$GreaterMediaContestsMetaboxes = new GreaterMediaContestsMetaboxes();
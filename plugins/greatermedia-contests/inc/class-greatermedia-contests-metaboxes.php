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

		// Make sure this is the post editor
		$current_screen = get_current_screen();
		if ( 'post' !== $current_screen->base ) {
			return;
		}

		// Make sure there's a post
		if ( ! isset( $GLOBALS['post'] ) || ! ( $GLOBALS['post'] instanceof WP_Post ) ) {
			return;
		}

		if ( $post && GMR_CONTEST_CPT === $post->post_type ) {
			$base_path = trailingslashit( GREATER_MEDIA_CONTESTS_URL );
			$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

			wp_enqueue_style( 'formbuilder' );
			wp_enqueue_style( 'datetimepicker' );
			wp_enqueue_style( 'font-awesome' );

			wp_enqueue_script( 'ie8-node-enum' );
			wp_enqueue_script( 'jquery-scrollwindowto' );
			wp_enqueue_script( 'underscore-mixin-deepextend' );
			wp_enqueue_script( 'backbone-deep-model' );
			wp_enqueue_script( 'datetimepicker' );

			wp_enqueue_script( 'formbuilder' );
			wp_enqueue_script( 'rivets' );

			$form = @json_decode( get_post_meta( $post->ID, 'embedded_form', true ), true );
			if ( empty( $form ) ) {
				$form = array(
					array(
						'cid'           => 'c5',
						'label'         => 'Name',
						'field_type'    => 'text',
						'required'      => true,
						'sticky'        => true,
						'field_options' => array( 'size' => 'medium' ),
					),
					array(
						'cid'           => 'c6',
						'label'         => 'Email Address',
						'field_type'    => 'email',
						'required'      => true,
						'sticky'        => true,
						'field_options' => array( 'sticky' => true ),
					),
				);
			}

			wp_enqueue_script( 'greatermedia-contests-admin', "{$base_path}js/contests-admin{$postfix}.js", array( 'formbuilder' ), false, true );
			wp_localize_script( 'greatermedia-contests-admin', 'GreaterMediaContestsForm', array(
				'form' => $form,
			) );
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
			'__return_false',
			'greatermedia-contest-rules'
		);

		add_settings_section(
			'greatermedia-contest-form',
			null,
			'__return_false',
			'greatermedia-contest-form'
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

		echo '<input type="' . esc_attr( $args['type'] ) . '" id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $args['name'] ) . '" value="' . esc_attr( $args['value'] ) . '" ' . $size_attr . '>';

	}

	/**
	 * Register meta boxes on the Contest editor
	 * Implements add_meta_boxes action
	 */
	public function add_meta_boxes() {
		add_meta_box( 'contest-settings', 'Settings', array( $this, 'contest_settings_metabox' ), GMR_CONTEST_CPT, 'normal' );
		add_meta_box( 'gallery', 'Gallery', array( $this, 'gallery_meta_box' ), GMR_CONTEST_CPT, 'side' );
	}

	public function contest_settings_metabox( WP_Post $post ) {
		$post_id = $post->ID;

		wp_nonce_field( 'contest_meta_boxes', '__contest_nonce' );
		
		?><ul class="tabs">
			<li class="active"><a href="#what-you-win">What You Win</a></li>
			<li><a href="#how-to-enter">How to Enter</a></li>
			<li><a href="#contest-rules">Official Contest Rules</a></li>
			<li><a href="#contest-form">Form</a></li>
			<li><a href="#restrictions">Restrictions</a></li>
		</ul>

		<div id="what-you-win" class="tab active">
			<?php wp_editor( get_post_meta( $post_id, 'prizes-desc', true ), 'greatermedia_contest_prizes' ); ?>
		</div>

		<div id="how-to-enter" class="tab">
			<?php wp_editor( get_post_meta( $post_id, 'how-to-enter-desc', true ), 'greatermedia_contest_enter' ); ?>
		</div>

		<div id="contest-rules" class="tab">
			<?php wp_editor( get_post_meta( $post_id, 'rules-desc', true ), 'greatermedia_contest_rules' ); ?>
		</div>

		<div id="contest-form" class="tab">
			<div id="contest_embedded_form"></div>
			<input type="hidden" id="contest_embedded_form_data" name="contest_embedded_form">
			<?php do_settings_sections( 'greatermedia-contest-form' ); ?>
		</div>

		<div id="restrictions" class="tab">
			<?php $this->_restrictions_settings( $post ); ?>
		</div><?php
	}

	private function _restrictions_settings( WP_Post $post ) {
		?><table class="form-table">
			<tr>
				<th scope="row"><label for="greatermedia_contest_start">Start date</label></th>
				<td>
					<?php $this->render_date_field( array(
						'post_id' => $post->ID,
						'id'      => 'greatermedia_contest_start',
						'name'    => 'greatermedia_contest_start',
						'value'   => get_post_meta( $post->ID, 'contest-start', true )
					) ); ?>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="greatermedia_contest_end">End date</label></th>
				<td>
					<?php $this->render_date_field( array(
						'post_id' => $post->ID,
						'id'      => 'greatermedia_contest_end',
						'name'    => 'greatermedia_contest_end',
						'value'   => get_post_meta( $post->ID, 'contest-end', true )
					) ); ?>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="greatermedia_contest_members_only">Who can enter</label></th>
				<td>
					<select id="greatermedia_contest_members_only" name="greatermedia_contest_members_only">
						<option value="0">Members and guests</option>
						<option value="1"<?php selected( get_post_meta( $post->ID, 'contest-members-only', true ) ); ?>>Members only</option>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="greatermedia_contest_single_entry">Entries per person</label></th>
				<td>
					<select id="greatermedia_contest_single_entry" name="greatermedia_contest_single_entry">
						<option value="1">One Entry Per Person</option>
						<option value="0"<?php selected( get_post_meta( $post->ID, 'contest-single-entry', true ), false ); ?>>No Limit</option>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="greatermedia_contest_max_entries">Total entries for the contest</label></th>
				<td>
					<input type="text" id="greatermedia_contest_max_entries" name="greatermedia_contest_max_entries" value="<?php echo esc_attr( get_post_meta( $post->ID, 'contest-max-entries', true ) ); ?>">
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="greatermedia_contest_min_age">Minimum age to enter</label></th>
				<td>
					<input type="text" id="greatermedia_contest_min_age" name="greatermedia_contest_min_age" value="<?php echo esc_attr( get_post_meta( $post->ID, 'contest-min-age', true ) ); ?>">
				</td>
			</tr>
		</table><?php
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
	 * Save meta fields on post save
	 *
	 * @param int $post_id
	 */
	public function save_post( $post_id ) {

		$post = get_post( $post_id );

		// Verify that the form nonce is valid.
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, '__contest_nonce' ), 'contest_meta_boxes' ) ) {
			return;
		}

		// If this is an autosave, the editor has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Make sure the post type is correct
		if ( GMR_CONTEST_CPT !== $post->post_type ) {
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
		$form = addslashes( json_encode( json_decode( urldecode( $_POST['contest_embedded_form'] ) ) ) );
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

		$members_only = filter_input( INPUT_POST, 'greatermedia_contest_members_only', FILTER_VALIDATE_BOOLEAN );
		update_post_meta( $post_id, 'contest-members-only', $members_only );

		$single_entry = filter_input( INPUT_POST, 'greatermedia_contest_single_entry', FILTER_VALIDATE_BOOLEAN );
		update_post_meta( $post_id, 'contest-single-entry', $single_entry );

		$max_etries = filter_input( INPUT_POST, 'greatermedia_contest_max_entries', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1, 'default' => '' ) ) );
		update_post_meta( $post_id, 'contest-max-entries', $max_etries );

		$min_age = filter_input( INPUT_POST, 'greatermedia_contest_min_age', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1, 'default' => '' ) ) );
		update_post_meta( $post_id, 'contest-min-age', $min_age );
	}

}

$GreaterMediaContestsMetaboxes = new GreaterMediaContestsMetaboxes();
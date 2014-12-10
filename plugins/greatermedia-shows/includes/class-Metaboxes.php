<?php

/**
 * Created by Eduard
 * Date: 15.10.2014
 */
class GMR_Show_Metaboxes {

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ) );
		add_action( 'save_post', array( $this, 'save_box' ), 20 );

		add_action( 'show_user_profile', array( $this, 'admin_user_meta_fields' ), 10, 1 );
		add_action( 'edit_user_profile', array( $this, 'admin_user_meta_fields' ), 10, 1 );

		add_action( 'personal_options_update', array( $this, 'admin_save_user_meta_fields' ), 10, 1 );
		add_action( 'edit_user_profile_update', array( $this, 'admin_save_user_meta_fields' ), 10, 1 );
	}

	/**
	 * Enqueues necessary scripts and styles.
	 *
	 * @action admin_enqueue_scripts
	 * @access public
	 */
	public function admin_enqueue_scripts() {
		global $pagenow, $typenow;
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		$post_types = ShowsCPT::get_supported_post_types();

		if ( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) && $typenow == ShowsCPT::SHOW_CPT ) {
			wp_enqueue_script( 'meta_box', GMEDIA_SHOWS_URL . "assets/js/greatermedia_shows{$postfix}.js", array( 'jquery', 'jquery-ui-datepicker' ), GMEDIA_SHOWS_VERSION, true );
			wp_enqueue_style( 'meta_box', GMEDIA_SHOWS_URL . "assets/css/greatermedia_shows{$postfix}.css", array(), GMEDIA_SHOWS_VERSION );
		}

		// Add support for auto-selecting a user's show ONLY when creting a new post.
		if ( in_array( get_post_type(), (array) $post_types ) && 'post-new.php' === $pagenow ) {
			$term_ids = array();
			$show_taxonomy_id = get_user_option( 'show_taxonomy_id', get_current_user_id() );

			if ( false !== $show_taxonomy_id ) {
				$term_ids[] = $show_taxonomy_id;
			}

			wp_register_script( 'admin_show_selector', GMEDIA_SHOWS_URL . "assets/js/admin_show_selector{$postfix}.js", array( 'jquery'), GMEDIA_SHOWS_VERSION, true );

			wp_localize_script( 'admin_show_selector', 'SHOW_JS', array(
				'usersShow' => $term_ids,
			));

			wp_enqueue_script( 'admin_show_selector' );
		}
	}

	/**
	 * Adds meta boxes to show edit page.
	 *
	 * @action add_meta_boxes
	 * @access public
	 */
	public function add_meta_boxes() {
		add_meta_box( 'show_logo', 'Logo', array( $this, 'render_logo_meta_box' ), ShowsCPT::SHOW_CPT, 'side' );
	}

	/**
	 * Displays show settings.
	 *
	 * @action post_submitbox_misc_actions
	 * @access public
	 */
	public function post_submitbox_misc_actions() {
		global $typenow;
		if ( ShowsCPT::SHOW_CPT != $typenow ) {
			return;
		}

		wp_nonce_field( 'gmr_show', 'show_nonce', false );

		$has_homepage = filter_var( get_post_meta( get_the_ID(), 'show_homepage', true ), FILTER_VALIDATE_BOOLEAN );

		?><div id="show-homepage" class="misc-pub-section misc-pub-gmr mis-pub-radio">
			Has home page:
			<span class="post-pub-section-value radio-value"><?php echo $has_homepage ? 'Yes' : 'No' ?></span>
			<a href="#" class="edit-radio hide-if-no-js" style="display: inline;"><span aria-hidden="true">Edit</span></a>

			<div class="radio-select hide-if-js">
				<label><input type="radio" name="show_homepage" value="0"<?php checked( $has_homepage, false ) ?>> No</label><br>
				<label><input type="radio" name="show_homepage" value="1"<?php checked( $has_homepage, true ) ?>> Yes</label><br>

				<p>
					<a href="#" class="save-radio hide-if-no-js button"><?php esc_html_e( 'OK' ) ?></a>
					<a href="#" class="cancel-radio hide-if-no-js button-cancel"><?php esc_html_e( 'Cancel' ) ?></a>
				</p>
			</div>
		</div><?php
	}

	/**
	 * Outputs the logo meta box.
	 *
	 * @access public
	 */
	public function render_logo_meta_box( WP_Post $post ) {
		$image = '';
		$image_id = intval( get_post_meta( $post->ID, 'logo_image', true ) );
		if ( $image_id ) {
			$image = current( (array) wp_get_attachment_image_src( $image_id, 'medium' ) );
		}

		echo '<input name="logo_image" type="hidden" class="meta_box_upload_image" value="', $image_id, '">';
		echo '<img src="', esc_attr( $image ), '" class="meta_box_preview_image">';
		echo '<div style="text-align:center">';
			echo '<a href="#" class="meta_box_upload_image_button button button-primary" rel="', $post->ID, '">Choose Image</a> ';
			echo '<a href="#" class="meta_box_clear_image_button button">Remove Image</a>';
		echo '</div>';
	}

	/**
	 * Saves the captured data.
	 *
	 * @action save_post
	 * @access public
	 */
	public function save_box( $post_id ) {
		$doing_autosave = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
		$valid_nonce = wp_verify_nonce( filter_input( INPUT_POST, 'show_nonce' ), 'gmr_show' );
		$can_edit_post = current_user_can( 'edit_page', $post_id );
		if ( $doing_autosave || ! $valid_nonce || ! $can_edit_post ) {
			return;
		}

		update_post_meta( $post_id, 'show_homepage', filter_input( INPUT_POST, 'show_homepage', FILTER_VALIDATE_BOOLEAN ) );
		update_post_meta( $post_id, 'logo_image', filter_input( INPUT_POST, 'logo_image', FILTER_VALIDATE_INT ) );
	}

	/**
	 * Add an associated show option to the user profile page.
	 *
	 * @param  WP_User $user The user being edited
	 */
	public function admin_user_meta_fields( $user ) {
		$terms = get_terms( ShowsCPT::SHOW_TAXONOMY, array( 'hide_empty' => false ) );
		$current_show = get_user_option( 'show_taxonomy_id', intval( $user->ID ) );

		?><h3><?php esc_html_e( 'Show Info', 'greatermedia' ); ?></h3>

		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="user-show"><?php esc_html_e( 'Show', 'greatermedia' ); ?></label></th>
					<td>
						<?php if ( ! empty( $terms ) ) : ?>
							<?php wp_dropdown_categories( array(
								'show_option_all' => __( 'None', 'greatermedia' ),
								'hierarchical'    => false,
								'name'            => 'user_show',
								'id'              => 'user-show',
								'class'           => '',
								'orderby'         => 'name',
								'taxonomy'        => ShowsCPT::SHOW_TAXONOMY,
								'hide_if_empty'   => true,
								'selected'        => intval( $current_show ),
							) ); ?>
							<br>
							<span class="description"><?php esc_html_e( 'Choose the show this user is associated with.', 'greatermedia' ); ?></span>
						<?php else : ?>
							<?php esc_html_e( 'There are no shows available.', 'greatermedia' ); ?>
						<?php endif; ?>
					</td>
				</tr>
			</tbody>
		</table><?php
	}

	/**
	 * Save the associated show option on user profile page.
	 *
	 * @param  int $user_id The user ID
	 */
	public function admin_save_user_meta_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		$user_show = filter_input( INPUT_POST, 'user_show', FILTER_VALIDATE_INT );
		if ( 0 < $user_show ) {
			$term = get_term_by( 'id', $user_show, ShowsCPT::SHOW_TAXONOMY );
			if ( false !== $term ) {
				update_user_option( $user_id, 'show_taxonomy_id', $term->term_id, false );
			}
		} else {
			// Remove the show association
			delete_user_option( $user_id, 'show_taxonomy_id', false );
		}
	}

}

$gmr_show_metaboxes = new GMR_Show_Metaboxes();

<?php
/**
 * Created by Eduard
 * Date: 27.11.2014 2:20
 */

if ( ! defined( 'WPINC' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaSurveys {

	public function __construct() {
		add_action( 'init', array( $this, 'register_survey_cpt' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'survey_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_form' ) );
	}

	public function survey_enqueue_scripts() {

		global $post;

		if( $post && $post->post_type == GMR_SURVEY_CPT ) {
			$base_path = trailingslashit( GREATER_MEDIA_CONTESTS_URL );
			$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

			wp_enqueue_style( 'formbuilder' );
			wp_enqueue_style( 'datetimepicker' );
			wp_enqueue_style( 'font-awesome' );

			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script( 'jquery-scrollwindowto', "{$base_path}bower_components/jquery.scrollWindowTo/index.js", array( 'jquery' ) );
			wp_enqueue_script( 'underscore' );
			wp_enqueue_script( 'underscore-mixin-deepextend' );
			wp_enqueue_script( 'backbone' );
			wp_enqueue_script( 'backbone-deep-model' );
			wp_enqueue_script( 'ie8-node-enum' );
			wp_enqueue_script( 'rivets' );


			wp_enqueue_script( 'formbuilder' );
			wp_enqueue_script( 'greatermedia-surveys-admin', "{$base_path}/js/surveys_admin{$postfix}.js", array( 'formbuilder' ), false, true );

			$embedded_form = get_post_meta( $post->ID, 'survey_embedded_form', true );
			wp_localize_script( 'greatermedia-surveys-admin', 'GreaterMediaContestsForm', array(
				'form' => json_decode( trim( $embedded_form, '"' ), true ),
			) );
		}

	}

	/**
	 * Registers survey cpt
	 */
	public function register_survey_cpt() {

		$labels = array(
			'name'                => 'Surveys',
			'singular_name'       => 'Survey',
			'add_new'             => 'Add New Survey',
			'add_new_item'        => 'Add New Survey',
			'edit_item'           => 'Edit Survey',
			'new_item'            => 'New Survey',
			'view_item'           => 'View Survey',
			'search_items'        => 'Search Surveys',
			'not_found'           => 'No surveys found',
			'not_found_in_trash'  => 'No surveys found in Trash',
			'parent_item_colon'   => 'Parent Survey:',
			'menu_name'           => 'Surveys',
		);

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'description'         => 'description',
			'taxonomies'          => array(),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-welcome-write-blog',
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => array( 'slug' => 'survey', 'ep_mask' => EP_GMR_SURVEY ),
			'capability_type'     => 'post',
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
		);

		register_post_type( GMR_SURVEY_CPT, $args );
	}

	public function add_meta_boxes() {
		add_meta_box( 'form', 'Form', array( $this, 'survey_form_builder' ), 'survey', 'normal' );
	}

	public function survey_form_builder() {
		global $post;
		$post_id = $post->ID;

		$title = get_post_meta( $post_id, 'form-title', true );

		$thankyou = get_post_meta( $post_id, 'form-thankyou', true );
		$thankyou = $thankyou ? $thankyou : "Thanks for your response";

		wp_nonce_field( 'survey_form_meta_box', 'survey_form_meta_box' );

		echo '<div id="survey_embedded_form"></div>';
		echo '<input type="hidden" id="survey_embedded_form_data" name="survey_embedded_form">';

		echo '<table class="form-table"><tbody>';
			echo '<tr>';
				echo '<th scope="row">';
					echo 'Form Title Text:';
				echo '</th>';
				echo '<td>';
					echo '<input size="50" type="text" id="form-title" name="form-title" value="', esc_attr( $title ), '">';
				echo '</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<th scope="row">';
					echo '"Thank you" message:';
				echo '</th>';
				echo '<td>';
					echo '<input size="50" type="text" id="form-thankyou" name="form-thankyou" value="', esc_attr( $thankyou ), '">';
				echo '</td>';
			echo '</tr>';
		echo '</tbody></table>';
	}

	public function save_form( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['survey_form_meta_box'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['survey_form_meta_box'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'survey_form_meta_box' ) ) {
			return $post_id;
		}

		// If this is an autosave, our form has not been submitted,
		//     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		// time to save the form
		if ( isset( $_POST['survey_embedded_form'] ) ) {
			$form = addslashes( json_encode( json_decode( urldecode( $_POST['survey_embedded_form'] ) ) ) );
			update_post_meta( $post_id, 'survey_embedded_form', $form );
		}

		if ( isset( $_POST['form-title'] ) ) {
		    $thank_you = sanitize_text_field( $_POST['form-title'] );
			update_post_meta( $post_id, 'form-title', $thank_you );
		}

		if ( isset( $_POST['form-thankyou'] ) ) {
		    $thank_you = sanitize_text_field( $_POST['form-thankyou'] );
			update_post_meta( $post_id, 'form-thankyou', $thank_you );
		}
	}

}


$GreatermediaSurveys = new GreaterMediaSurveys();

<?php
/**
 * Created by Eduard
 * Date: 31.10.2014 4:05
 */

class RestrictionMetaboxes {

	private $post_type = 'contest';

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_box' ) );
		add_action( 'save_post',  array( $this, 'save_box' ), 20);
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function admin_enqueue_scripts() {
		global $pagenow;

		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

		if ( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) && get_post_type() == $this->post_type ) {
			wp_enqueue_script( 'restrict_meta_js', GMEDIA_CONTEST_RESTRICTION_URL . "assets/js/greater_media_contest_restriction{$postfix}.js", array( 'jquery' ), GMEDIA_CONTEST_RESTRICTION_VERSION, true );
			//wp_enqueue_script( 'jquery-ui-datepicker' );

			wp_enqueue_style( 'restrict_meta_css', GMEDIA_CONTEST_RESTRICTION_URL . "assets/css/greater_media_contest_restriction{$postfix}.css", array(), GMEDIA_CONTEST_RESTRICTION_VERSION );
			wp_enqueue_style( 'restrict_meta_jquery_ui', GMEDIA_CONTEST_RESTRICTION_URL . "assets/css/jquery-ui.min.css", array(), '1.11.2' );

		}
	}

	/**
	 * Adds the meta box container.
	 *
	 * @param $post_type
	 */
	public function add_box( $post_type ) {
		if ( $post_type == $this->post_type ) {
			add_meta_box(
				'_contest_restriction'
				,__( 'Contest restrictions', 'greatermedia' )
				,array( $this, 'render_meta_box_content' )
				,$post_type
				,'advanced'
				,'low'
			);
		}
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_box( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['contest_restriction_field'] ) )
			return $post_id;

		$nonce = $_POST['contest_restriction_field'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'contest_restriction_action' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
		//     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		// Sanitize the user input.
		$member_only = sanitize_text_field( $_POST['member_only'] );
		$restrict_number =  sanitize_text_field( $_POST['restrict_number'] );
		$max_entries =  intval( $_POST['max_entries'] );
		$restrict_age =  sanitize_text_field( $_POST['restrict_age'] );
		$min_age =  intval( $_POST['min_age'] );
		$start_date  = sanitize_text_field( $_POST['start_date'] );
		$end_date   = sanitize_text_field( $_POST['end_date'] );

		// Update the meta field.
		update_post_meta( $post_id, '_member_only', $member_only );
		update_post_meta( $post_id, '_restrict_number', $restrict_number );
		update_post_meta( $post_id, '_max_entries', $max_entries );
		update_post_meta( $post_id, '_restrict_age', $restrict_age );
		update_post_meta( $post_id, '_min_age', $min_age );
		update_post_meta( $post_id, '_start_date', $start_date );
		update_post_meta( $post_id, '_end_date', $end_date );

	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'contest_restriction_action', 'contest_restriction_field' );

		// Use get_post_meta to retrieve an existing value from the database.
		$member_only        = get_post_meta( $post->ID, '_member_only', true );
		$restrict_number    = get_post_meta( $post->ID, '_restrict_number', true );
		$max_entries        = get_post_meta( $post->ID, '_max_entries', true );
		$restrict_age       = get_post_meta( $post->ID, '_restrict_age', true );
		$min_age            = get_post_meta( $post->ID, '_min_age', true );
		$start_date         = get_post_meta( $post->ID, '_start_date', true );
		$end_date           = get_post_meta( $post->ID, '_end_date', true );

		//$start_date = get_post_meta( $post->ID, '_contest_start_date', true );
		//$end_date = get_post_meta( $post->ID, '_contest_end_date', true );

		// Metabox for members only.
		echo '<div class="meta_group">';
			echo '<label for="member_only">';
			_e( 'Member Only:', 'greatermedia' );
			echo '</label> ';
			echo '<input type="checkbox" id="member_only" name="member_only" ' . checked( 'on', $member_only, false ) . ' />';
		echo '</div>';

		echo '<br/>';

		// Restrict by max entires
		echo '<div class="meta_group">';
			echo '<label for="restrict_number">';
			_e( 'Restrict number of entries:', 'greatermedia' );
			echo '</label> ';
			echo '<input type="checkbox" id="restrict_number" name="restrict_number" ' . checked( 'on', $restrict_number, false ) . ' />';

			echo '<br/>';
			$hidden = $restrict_number ? '' : 'hidden';
			echo '<div class="' . $hidden . ' max_entries">';
				echo '<label for="max_entries">';
				_e( 'Max entries', 'greatermedia' );
				echo '</label> ';
				echo '<input type="text" id="max_entries" name="max_entries" value="' . $max_entries . '" size="25" />';
			echo '</div>';
		echo '</div>';

		echo '<br/>';

		// Restrict by age
		echo '<div class="meta_group">';
			echo '<label for="restrict_age">';
			_e( 'Restrict by age:', 'greatermedia' );
			echo '</label> ';
			echo '<input type="checkbox" id="restrict_age" name="restrict_age" ' . checked( 'on', $restrict_age , false ) . ' />';

			echo '<br/>';
			$hidden = $restrict_number ? '' : 'hidden';
			echo '<div class="' . $hidden . ' min_age">';
				echo '<label for="min_age">';
				_e( 'Min age', 'greatermedia' );
				echo '</label> ';
				echo '<input type="text" id="min_age" name="min_age" value="' . $min_age . '" size="25" />';
			echo '</div>';
		echo '</div>';

		echo '<br/>';


		// Restrict duration
/*		echo '<div class="meta_group">';
			echo '<label for="restrict_age">';
			_e( 'Duration:', 'greatermedia' );
			echo '</label> ';

			echo '<br/>';

			echo '<div class="duration">';
				echo '<label for="start_date">';
				_e( 'Start', 'greatermedia' );
				echo '</label> ';
				echo '<input type="text" id="start_date" name="start_date" value="' . $start_date . '" size="25" />';
				echo '<br/>';
				echo '<label for="end_date">';
				_e( 'End', 'greatermedia' );
				echo '</label> ';
				echo '<input type="text" id="end_date" name="end_date" value="' . $end_date . '" size="25" />';
			echo '</div>';
		echo '</div>';*/

	}
}

new RestrictionMetaboxes();

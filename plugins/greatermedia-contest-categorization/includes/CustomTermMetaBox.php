<?php
/**
 * Created by Eduard
 * Date: 29.10.2014
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class CustomTermMetaBox {

	private $id;
	private $title;
	private $fields;
	private $page;
	private $context;
	private $priority;

	public function __construct() {

	}
	public function init( $id, $title, $fields, $page, $context, $priority ) {
		$this->id       = $id;
		$this->fields    = $fields;
		$this->title    = $title;
		$this->page     = $page;
		$this->context  = $context;
		$this->priority = $priority;

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_box' ) );
		add_action( 'save_post',  array( $this, 'save_box' ), 20);
	}

	/**
	 * enqueue necessary scripts and styles
	 */
	public function admin_enqueue_scripts() {
		global $pagenow;
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		if ( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) && get_post_type() == $this->page ) {
			wp_enqueue_style( 'meta_box', GMEDIA_CONTESTS_CATS_URL . "assets/css/greatermedia_contests_categorization{$postfix}.css", array(), GMEDIA_SHOWS_VERSION );
		}
	}

	/**
	 * adds the meta box for every post type in $page
	 */
	public function add_box() {
		add_meta_box( $this->id, $this->title, array( $this, 'meta_box_callback' ), $this->page, $this->context, $this->priority );
	}

	/**
	 * outputs the meta box
	 */
	public function meta_box_callback() {
		// Use nonce for verification
		wp_nonce_field( 'custom_meta_box_nonce_action', 'custom_meta_box_nonce_field' );

		$id = $name = isset( $this->fields['id'] ) ? $this->fields['id'] : null;
		$args = array(
			'get' => 'all',
		);
		$terms = get_terms( $id, $args );
		$term_ids = wp_list_pluck( $terms, 'term_id' );
		$post_terms = wp_list_pluck( wp_get_object_terms( get_the_ID(), $id ), 'term_id' );
		$taxonomy = get_taxonomy( $id );

		echo '<span class="description">' . esc_html( $this->fields['desc'] ) . '</span>';

		// Begin the field table and loop
		echo '<table class="form-table meta_box">';
			if( !is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					echo '<tr><td><input type="checkbox" value="' . esc_attr( $term->term_id ) . '" name="' . esc_attr( $id ) . '[]" id="term-' . $term->term_id . '"' . checked( true, in_array( $term->term_id, $post_terms), false ) . ' /><label for="term-' . intval( $term->term_id ) . '">' . esc_html( $term->name ) . '</label></td></tr>';
				}
			}
		echo '</table>'; // end table
	}

	/**
	 * saves the captured data
	 */
	public function save_box( $post_id ) {
		$post_type = get_post_type();
		$post_title = get_the_title();

		// verify nonce
		if ( ! isset( $_POST['custom_meta_box_nonce_field'] ) ) {
			return $post_id;
		}

		if ( $post_type != $this->page || !wp_verify_nonce( $_POST['custom_meta_box_nonce_field'],  'custom_meta_box_nonce_action' ) ) {
			return $post_id;
		}
		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// check permissions
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}

		$old_terms = get_post_meta( $post_id, '_post_terms', true );

		if( !is_array($old_terms) ) {
			$old_terms = array();
		}

		foreach ( $old_terms as $old_term ) {
			wp_remove_object_terms( $post_id, $old_term, $this->fields['id'] );
		}

		if ( isset( $_POST[$this->fields['id']] ) ) {

			$new_terms = array_map('absint', $_POST[$this->fields['id']]);

			foreach( $new_terms as $new_term_id ) {
				wp_set_post_terms( $post_id, $new_term_id, $this->fields['id'], true);
			}

			update_post_meta( $post_id, '_post_terms', $new_terms );

		} else {
			update_post_meta( $post_id, '_post_terms', array() );
		}

	}

	public function custom_meta_box_field( $field, $meta = null, $type = null ) {
		if ( ! ( $field || is_array( $field ) ) ) {
			return;
		}

		// get field data
		$label = isset( $field['label'] ) ? $field['label'] : null;
		$desc = isset( $field['desc'] ) ? '<span class="description">' . $field['desc'] . '</span>' : null;
		$place = isset( $field['place'] ) ? $field['place'] : null;
		$size = isset( $field['size'] ) ? $field['size'] : null;
		$post_type = isset( $field['post_type'] ) ? $field['post_type'] : null;
		$options = isset( $field['options'] ) ? $field['options'] : null;
		$settings = isset( $field['settings'] ) ? $field['settings'] : null;

		// the id and name for each field
		$id = $name = isset( $field['id'] ) ? $field['id'] : null;
		echo '<input type="checkbox" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" ' . checked( $meta, true, false ) . ' value="1" />
		<label for="' . esc_attr( $id ) . '">' . esc_html( $desc ) . '</label>';
	}

}

$images = array(
	'label'	=> '',
	'desc'	=> 'Choose contest type.',
	'id'	=> 'contest_type'
);

$image_meta = new CustomTermMetaBox();
$image_meta->init( 'contest_type', 'Contest Types', $images, 'contest', 'side', 'default', true);
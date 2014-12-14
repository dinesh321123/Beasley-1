<?php

class GreaterMediaChooseClass {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ) );
		add_filter( 'post_class', array( __CLASS__, 'add_post_class' ) );
		add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'add_checkbox' ) );
		add_action( 'save_post', array( __CLASS__, 'save_class_state' ) );
	}

	/**
	 * registers a new taxonomy called `class-format` for use with a post
	 *
	 * hooked into the init action
	 */
	public static function register_taxonomies() {

		$labels = array(

			'name'              => __( 'Class Formats', 'greatermedia' ),
			'singular_name'     => __( 'Class Format', 'greatermedia' ),
			'search_items'      => __( 'Search Class Formats', 'greatermedia' ),
			'all_items'         => __( 'All Class Formats', 'greatermedia' ),
			'parent_item'       => __( 'Parent Class Format', 'greatermedia' ),
			'parent_item_colon' => __( 'Parent Class Format: ', 'greatermedia' ),
			'edit_item'         => __( 'Edit', 'greatermedia' ),
			'update_item'       => __( 'Update', 'greatermedia' ),
			'add_new_item'      => __( 'Add New Class Format', 'greatermedia' ),
			'new_item_name'     => __( 'New Class Format', 'greatermedia' ),
			'menu_name'         => __( 'Class Formats', 'greatermedia' ),

		);

		$rewrite = array(
			'slug'                       => 'class-format',
			'with_front'                 => true,
			'hierarchical'               => false,
		);

		$args = array(

			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => false,
			'show_in_nav_menus' => false,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => $rewrite,

		);

		register_taxonomy( 'class-format', array( 'post' ), $args );

	}

	/**
	 * add a checkbox to the Publish box
	 *
	 * hooked into post_submitbox_misc_actions
	 */
	public static function add_checkbox(){

		global $post;

		if ( 'post' !== get_post_type() ) {
			return;
		}

		wp_nonce_field( 'save_class_format', 'class_format_nonce' );

		$tax = 'class-format';
		$term = get_term_by( 'slug', 'promoted', $tax );

		// Add the term if it doesn't exist
		if ( false === $term) {
			wp_insert_term( 'Promoted', 'promoted' );
			$term = get_term_by( 'slug', 'promoted', $tax );
		}

		$checked_term = is_object_in_term( $post->ID, $tax, $term );

		?>

		<div id="article-type" class="misc-pub-section">
			<input type="checkbox" name="promoted" id="promoted" value="promoted" <?php checked( 1, $checked_term ); ?> /> <label for="promoted"><?php _e( 'Promote this Post', 'publications' ); ?></label>
		</div>

	<?php
	}

	/**
	 * save the new object terms when the post is saved.
	 *
	 * hooked into save_post
	 */
	public static function save_class_state( $post_id ) {
		global $post;

		// Check if our nonce is set and that it validates it.
		if ( ! isset( $_POST['class_format_nonce'] ) || ! wp_verify_nonce( $_POST['class_format_nonce' ], 'save_class_format' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted we don't want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		/* OK, its safe for us to save the data now. */
		if ( isset( $_POST['promoted']) && $_POST['promoted'] == 'promoted' ) {
			wp_set_object_terms( $post->ID, 'promoted', 'class-format', true );
		} else {
			wp_remove_object_terms( $post->ID, 'promoted', 'class-format' );
		}
	}

	/**
	 * checks for a term of a post, if it is present, a new class is added to post_class()
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	public static function add_post_class( $classes ) {

		global $post;

		if ( has_term( 'promoted', 'class-format', $post->ID ) ) {

			$classes[] = 'promoted';

		}

		return $classes;
	}

}

GreaterMediaChooseClass::init();
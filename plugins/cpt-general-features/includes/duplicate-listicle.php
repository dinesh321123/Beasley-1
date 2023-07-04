<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
class DuplicateListicle {
	function __construct()
	{
		add_action( 'admin_init', array( $this, 'wp_admin_init' ), 1 );
	}
	public function wp_admin_init() {
		add_filter( 'post_row_actions', array( $this, 'listicle_cpt_duplicate_post_link' ), 10, 2 );
		add_action( 'admin_action_listicle_cpt_duplicate', array( $this, 'listicle_cpt_duplicate_function' ) );
		add_action( 'admin_notices', array( $this, 'listicle_cpt_duplication_admin_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'listicle_cpt_duplication_enqueue_scripts' ) );
	}
	public function listicle_cpt_duplication_enqueue_scripts() {
		global $typenow, $pagenow;
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		wp_register_script('duplicate-listicle-script', GENERAL_SETTINGS_CPT_URL . 'assets/js/duplicate-listicle'. $postfix .'.js', array('jquery'), '1.0');
		$data = array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'post_type' => $typenow,
			'currunt_page' => $pagenow,
		);
		wp_localize_script('duplicate-listicle-script', 'duplicateListicleData', $data);
		wp_enqueue_script('duplicate-listicle-script');
	}
	// Add the duplicate link to action list for Listicle
	public function listicle_cpt_duplicate_post_link ( $actions, $post ) {
		if( ! current_user_can( 'edit_posts' ) ) {
			return $actions;
		}

		if ( isset($post->post_type) && $post->post_type == 'listicle_cpt' ) {
			$url = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'listicle_cpt_duplicate',
						'post' => $post->ID,
					),
					'admin.php'
				),
				basename(__FILE__),
				'duplicate_nonce'
			);

			$actions['duplicate'] = '<a href="' . $url . '" title="Clone this item" rel="permalink">Clone Listicle</a>';
		}

		return $actions;
	}

	/*
	 * Function creates post duplicate as a draft and redirects then to the edit post screen
	 */
	public function listicle_cpt_duplicate_function(){
		// check if post ID has been provided and action
		if ( empty( $_GET[ 'post' ] ) ) {
			wp_die( 'No post to duplicate has been provided!' );
		}

		// Nonce verification
		if ( ! isset( $_GET[ 'duplicate_nonce' ] ) || ! wp_verify_nonce( $_GET[ 'duplicate_nonce' ], basename( __FILE__ ) ) ) {
			return;
		}

		// Get the original post id
		$post_id = absint( $_GET[ 'post' ] );

		// And all the original post data then
		$post = get_post( $post_id );

		/*
		 * if you don't want current user to be the new post author,
		 * then change next couple of lines to this: $new_post_author = $post->post_author;
		 */
		$current_user = wp_get_current_user();
		$new_post_author = $current_user->ID;

		if ( $post ) {

			// new post data array
			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $new_post_author,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_name'      => $post->post_name,
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => 'draft',
				'post_title'     => $post->post_title,
				'post_type'      => $post->post_type,
				'to_ping'        => $post->to_ping,
				'menu_order'     => $post->menu_order
			);

			// insert the post by wp_insert_post() function
			$new_post_id = wp_insert_post( $args );

			/*
			 * get all current post terms ad set them to the new post draft
			 */
			$taxonomies = get_object_taxonomies( get_post_type( $post ) ); // returns array of taxonomy names for post type, ex array("category", "post_tag");
			if( $taxonomies ) {
				foreach ( $taxonomies as $taxonomy ) {
					$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
					wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
				}
			}
			// duplicate all listicle meta
			$post_meta = get_post_meta( $post_id );
			if( $post_meta ) {
				foreach ( $post_meta as $meta_key => $meta_values ) {
					// echo " Key: ", $meta_key, " -- Value: ", print_r($meta_values), "<br>";
					if( '_wp_old_slug' == $meta_key ) { // do nothing for this meta key
						continue;
					}

					foreach ( $meta_values as $meta_value ) {
						$final_meta_value =		$meta_value;
						if( in_array( $meta_key, array("cpt_item_name", "cpt_item_description", "cpt_item_order", "cpt_item_type") ) ) {
							$final_meta_value = maybe_unserialize($meta_value);
							// echo " Key: ", $meta_key, " -- Value: ", print_r($final_meta_value), " --------------- Old one - ", print_r($meta_value),  "<br>";
						}
						add_post_meta( $new_post_id, $meta_key, $final_meta_value, true );
					}
				}
			}

			$args = [];
			if ( 'post' !== $post->post_type ) {
				$args['post_type'] = $post->post_type;
			}
			$url  = add_query_arg( $args, admin_url( 'edit.php?saved=post_duplication_created' ) );
			wp_safe_redirect( $url );
			exit;
		} else {
			wp_die( 'Post creation failed, could not find original post.' );
		}
	}

	/*
	 * In case we decided to add admin notices
	 */
	public function listicle_cpt_duplication_admin_notice() {
		// Get the current screen
		$screen = get_current_screen();
		if ( 'edit' !== $screen->base ) {
			return;
		}
		//Checks if settings updated
		if ( isset( $_GET[ 'saved' ] ) && 'post_duplication_created' == $_GET[ 'saved' ] ) {
			echo '<div class="notice notice-success is-dismissible"><p>Listicle copy created.</p></div>';
		}
	}

}
new DuplicateListicle();

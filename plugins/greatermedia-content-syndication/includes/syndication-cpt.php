<?php
/**
 * Created by Eduard
 * Date: 06.11.2014
 */

class SyndicationCPT {

	private $post_type = 'subscription';

	public function __construct() {
		add_action( 'init', array( $this, 'register_syndication_cpt' ) );
		add_action( 'init', array( $this, 'register_collections_taxonomy' ) );
		add_action( 'admin_menu', array( $this, 'hide_publish_meta' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'subscription_admin_scripts' ) );
		add_action( 'admin_head-post.php', array( $this, 'hide_publishing_actions' ) );
		add_action( 'admin_head-post-new.php', array( $this, 'hide_publishing_actions' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'custom_publish_meta' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_action( 'manage_subscription_posts_custom_column' , array( $this, 'subscription_column_data' ), 10, 2 );

		add_filter( 'views_edit-' . $this->post_type, array( $this, 'change_status_labels' ), 10, 1);
		add_filter( 'display_post_states' , array( $this, 'change_state_labels' ), 10, 1);
		add_filter( 'gettext', array( $this, 'change_publish_button' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ), 10, 2);
		add_filter( 'is_protected_meta', array( $this, 'hide_meta_keys' ), 10, 2);
		add_filter( 'manage_edit-subscription_columns', array( $this, 'subscription_columns_filter' ),10, 1 );

	}

	/**
	 * Register collections taxonomy for posts
	 */
	public function register_collections_taxonomy() {
		$labels = array(
			'name'					=> _x( 'Collections', 'Taxonomy plural name', 'greatermedia' ),
			'singular_name'			=> _x( 'Collections', 'Taxonomy singular name', 'greatermedia' ),
			'search_items'			=> __( 'Search Collections', 'greatermedia' ),
			'popular_items'			=> __( 'Popular Collections', 'greatermedia' ),
			'all_items'				=> __( 'All Collections', 'greatermedia' ),
			'parent_item'			=> __( 'Parent Collections', 'greatermedia' ),
			'parent_item_colon'		=> __( 'Parent Collections', 'greatermedia' ),
			'edit_item'				=> __( 'Edit Collections', 'greatermedia' ),
			'update_item'			=> __( 'Update Collections', 'greatermedia' ),
			'add_new_item'			=> __( 'Add New Collections', 'greatermedia' ),
			'new_item_name'			=> __( 'New Collections Name', 'greatermedia' ),
			'add_or_remove_items'	=> __( 'Add or remove Collections', 'greatermedia' ),
			'choose_from_most_used'	=> __( 'Choose from most used greatermedia', 'greatermedia' ),
			'menu_name'				=> __( 'Collections', 'greatermedia' ),
		);

		if( BlogData::$content_site_id != get_current_blog_id() ) {
			$args = array(
				'labels'            => $labels,
				'public'            => false,
				'show_in_nav_menus' => false,
				'show_admin_column' => false,
				'hierarchical'      => false,
				'show_tagcloud'     => false,
				'show_ui'           => false,
				'query_var'         => true,
				'rewrite'           => true,
				'query_var'         => true,
				'capabilities'      => array(),
			);
		} else {
			$args = array(
				'labels'            => $labels,
				'public'            => true,
				'show_in_nav_menus' => true,
				'show_admin_column' => false,
				'hierarchical'      => false,
				'show_tagcloud'     => true,
				'show_ui'           => true,
				'query_var'         => true,
				'rewrite'           => true,
				'query_var'         => true,
				'capabilities'      => array(),
			);
		}

		register_taxonomy( 'collection', array( 'post' ), $args );
	}


	public function subscription_columns_filter( $columns ) {

		$column_labels = array();
		foreach( BlogData::$taxonomies as $supported_taxonomy ) {
			// get taxonomy label
			$taxonomy_obj = get_taxonomies( array( 'name' => $supported_taxonomy ), 'object' );
			$column_labels[ $taxonomy_obj[$supported_taxonomy]->name ] = $taxonomy_obj[$supported_taxonomy]->label;
		}

		return array_merge($columns, $column_labels );
	}

	public function subscription_column_data( $column, $post_id ) {

		$terms = sanitize_text_field(  get_post_meta( $post_id , 'subscription_filter_terms-' . $column , true ) );
		echo esc_attr( $terms );

	}

	/**
	 * Hide meta keys from custom fields
	 *
	 * @param $protected - boolean
	 * @param $meta_key  - string meta key
	 *
	 * @return bool
	 */

	public function hide_meta_keys( $protected, $meta_key ) {
		$hidden_keys = array(
			'subscription_post_status',
			'subscription_filter_terms',
			'subscription_default_terms-',
			'syndication_import',
			'syndication_old_data',
			'syndication_old_name',
			'syndication_attachment_old_id'
		);

		foreach( $hidden_keys as $hidden_key ) {
			if ( strpos( $meta_key, $hidden_key ) !== false ) {
				return true;
			}
		}

		return $protected;
	}

	public function remove_quick_edit( $actions ) {
		global $post;
		if( $post->post_type == $this->post_type ) {
			unset($actions['inline hide-if-no-js']);
		}
		return $actions;
	}

	public function subscription_admin_scripts() {
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

		if( wp_script_is( 'select2', $list = 'registered' ) ) {

			wp_enqueue_script( 'select2' );
			wp_enqueue_style( 'select2' );

			wp_enqueue_script(
				'syndication_js'
				,GMR_SYNDICATION_URL . "assets/js/greater_media_content_syndication{$postfix}.js"
				,array( 'jquery' , 'select2')
				,'0.0.1'
			);

			wp_localize_script(
				'syndication_js'
				,'syndication_ajax'
				,array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'syndication_nonce' => wp_create_nonce( 'perform-syndication-nonce' )
				)
			);

			wp_enqueue_style(
				'syndication_css'
				,GMR_SYNDICATION_URL . "assets/css/greater_media_content_syndication{$postfix}.css"
				,array()
				,'0.0.1'
			);
		}
	}

	/**
	 * Registers a new post type for syndications
	 */
	public function register_syndication_cpt() {

		$labels = array(
			'name'                => __( 'Subscriptions', 'greatermedia' ),
			'singular_name'       => __( 'Subscription', 'greatermedia' ),
			'add_new'             => _x( 'Add New Subscription', 'greatermedia', 'greatermedia' ),
			'add_new_item'        => __( 'Add New Subscription', 'greatermedia' ),
			'edit_item'           => __( 'Edit Subscription', 'greatermedia' ),
			'new_item'            => __( 'New Subscription', 'greatermedia' ),
			'view_item'           => __( 'View Subscription', 'greatermedia' ),
			'search_items'        => __( 'Search Subscriptions', 'greatermedia' ),
			'not_found'           => __( 'No Subscriptions found', 'greatermedia' ),
			'not_found_in_trash'  => __( 'No Subscriptions found in Trash', 'greatermedia' ),
			'parent_item_colon'   => __( 'Parent Syndication:', 'greatermedia' ),
			'menu_name'           => __( 'Subscriptions', 'greatermedia' ),
		);


		$args = array(
			'labels'                => $labels,
			'hierarchical'        => false,
			'description'         => 'description',
			'taxonomies'          => array(),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 75,
			'menu_icon'           => 'dashicons-rss',
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => true,
			'capability_type'     => 'post',
			'supports'            => array(
				'title'
			)
		);

		register_post_type( $this->post_type, $args );
	}


	public function change_state_labels( $post_states ) {
		global $post;

		if( isset( $post_states['draft'] ) && $post->post_type == $this->post_type ) {
			$post_states['draft'] = __('Inactive');
		}

		if( isset( $post_states['publish'] ) && $post->post_type == $this->post_type ) {
			$post_states['publish'] = __('Active');
		}

		return $post_states;
	}

	public function change_status_labels( $views )
	{
		if( isset( $views['draft'] ) ) {
			$views['draft'] = str_replace( 'Draft', 'Inactive', $views['draft'] );
		}

		if( isset( $views['publish'] ) ) {
			$views['publish'] = str_replace( 'Published', 'Active', $views['publish'] );
		}

		return $views;
	}


	public function hide_publishing_actions(){
		global $post;
		if( $post->post_type == $this->post_type ){
			echo '
                <style type="text/css">
                    .misc-pub-section:not(.misc-pub-section-last),
                    #minor-publishing-actions{
                        display:none;
                    }
                </style>
            ';
		}
	}

	/**
	 * Change publish button text
	 *
	 * @param $translation
	 * @param $text
	 *
	 * @return string
	 */
	public function change_publish_button( $translation, $text ) {
		global $typenow;

		if( $typenow == $this->post_type ) {
			if( $text == 'Publish' || $text == 'Update' ) {
				return 'Save Changes';
			}
		}

		return $translation;
	}

	/**
	 * Hide default publish metabox
	 */
	public function hide_publish_meta() {
		remove_meta_box( 'submitdiv', $this->post_type, 'normal' );
	}

	public function add_meta_box() {
		global $post;

		add_meta_box(
			'submitdiv'
			,__( 'Subscription control' )
			,'post_submit_meta_box'
			,$this->post_type
			,'side'
			,'high'
		);

		add_meta_box(
			'filter_metaboxes'
			,__( 'Filters', 'greatermedia' )
			,array( $this, 'render_filter_metabox' )
			,$this->post_type
			,'advanced'
			,'high'
		);

		//$taxonomy_names = get_object_taxonomies( 'post', 'objects' );

		add_meta_box(
			'subscription_default_metabox'
			,__( 'Defaults' )
			,array( $this, 'render_defaults_metabox' )
			,$this->post_type
			,'advanced'
			,'high'
		);


		// Temp metabox to perform syndication
		add_meta_box(
			'syndicate'
			,__( 'Syndication control' )
			,array( $this, 'render_syndication_control' )
			,$this->post_type
			,'side'
			,'default'
		);
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {

		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['subscription_custom_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['subscription_custom_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'save_subscription_status' ) ) {
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

		$terms = '';

		// Sanitize the user input.
		if( isset( $_POST[ 'active_inactive' ] ) ) {

			$status = sanitize_text_field( $_POST['active_inactive'] );

			/**
			 * from http://codex.wordpress.org/Function_Reference/wp_update_post
			 */
			if ( ! wp_is_post_revision( $post_id ) ) {

				$update_post['ID'] = $post_id;
				$update_post['post_status'] = $status;

				// unhook this function so it doesn't loop infinitely
				remove_action( 'save_post', array( $this, 'save' ) );

				// update the post, which calls save_post again
				wp_update_post( $update_post );

				// re-hook this function
				add_action( 'save_post', array( $this, 'save' ) );

			}

		}

		// get all taxonomies for post
		$taxonomy_names  = get_object_taxonomies( 'post', 'objects' );

		// foreach taxonomy pare defaults
		foreach( $taxonomy_names as $taxonomy ) {
			if( isset( $_POST[ 'subscription_default_terms-' . $taxonomy->name ] ) ) {
				// sanitize defaults
				$sanitized = array_map( 'sanitize_text_field', $_POST['subscription_default_terms-' . $taxonomy->name] );
				$default_terms = implode( ',', $sanitized );
				update_post_meta( $post_id, 'subscription_default_terms-' . $taxonomy->name, $default_terms  );
			}
		}

		// save deafult status
		if( isset( $_POST[ 'subscription_post_status' ] ) ) {
			$sanitized = sanitize_text_field( $_POST[ 'subscription_post_status' ] );

			// Update the meta field.
			update_post_meta( $post_id, 'subscription_post_status', $sanitized  );
		}

		// get filter metas
		foreach( BlogData::$taxonomies as $taxonomy ) {
			$terms = '';

			if( isset( $_POST[ 'subscription_filter_terms-' . $taxonomy ] ) ) {
				$sanitized = array_map( 'sanitize_text_field', $_POST[ 'subscription_filter_terms-' . $taxonomy ] );
				$terms = implode( ',', $sanitized );
			}

			// Update the meta field.
			update_post_meta( $post_id, 'subscription_filter_terms-' . $taxonomy, $terms  );

		}

		if( isset( $_POST['enabled_filter_taxonomy'] ) ) {
			$enabled_taxonomy = sanitize_text_field( $_POST['enabled_filter_taxonomy'] );
			// Update the meta field.
			update_post_meta( $post_id, 'subscription_enabled_filter', $enabled_taxonomy  );
		}

	}

	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_filter_metabox( $post ) {

		$allterms = BlogData::getTerms();

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'save_subscription_status', 'subscription_custom_nonce' );

		foreach( $allterms as $taxonomy => $terms ) {
			// Use get_post_meta to retrieve an existing value from the database.
			$filter_terms = get_post_meta( $post->ID, 'subscription_filter_terms-' . $taxonomy , true );
			$filter_terms = explode( ',', $filter_terms );
			$enabled_filter = get_post_meta( $post->ID, 'subscription_enabled_filter' , true );

			// get taxonomy label
			$taxonomy_obj = get_taxonomies( array( 'name' => $taxonomy ), 'object' );
			$taxonomy_name = $taxonomy_obj[$taxonomy]->label;

			// Display the form, using the current value.
			echo '<p><label for="subscription_filter_terms">';
			esc_html_e( $taxonomy_name, 'greatermedia' );
			echo '</label> ';

			$multiple = $taxonomy == 'collection' ? '' : 'multiple';
			$disabled = $enabled_filter == $taxonomy ? '' : 'disabled';
			$checked =  $enabled_filter == $taxonomy ? 'checked' : '';

			echo '<select ' . $disabled . ' ' .$multiple . ' id="' . esc_attr( $taxonomy )
			     . '" name="subscription_filter_terms-' . esc_attr( $taxonomy )
			     . '[]" class="subscription_terms" style="width: 300px;">';
			foreach( $terms as $single_term ) {
				echo '<option', in_array( $single_term->name, $filter_terms) ? ' selected="selected"' : ''
				, ' value="' . esc_attr( $single_term->name ) .'">' . esc_html( $single_term->name ) . '</option>';
			}

			echo '</select>';

			echo '<input ' . esc_attr( $checked ) . ' data-enabled="' . esc_attr( $taxonomy ). '" class="enabled_filter" type="radio" name="enabled_filter" />';
			echo '</p>';
		}
		echo '<input type="hidden" id="enabled_filter_taxonomy" name="enabled_filter_taxonomy" value="' . $enabled_filter. '">';
	}

	/**
	 * Custom publish metabox for subscription active/inactive
	 */
	public function custom_publish_meta() {
		global $post;
		if ( get_post_type($post) == $this->post_type ) {
			echo '<div class="misc-pub-section misc-pub-section-last">';

				wp_nonce_field( 'save_subscription_status', 'subscription_custom_nonce' );

				$val = get_post_status( $post->ID ) == 'publish' ? 'publish' : 'draft';

				echo '<input type="radio" name="active_inactive" id="active_inactive-active" value="publish" '
				     . checked( $val, 'publish', false)
				     . ' /> <label for="active_inactive-active" class="select-it">Active</label><br />';
				echo '<input type="radio" name="active_inactive" id="active_inactive-inactive" value="draft" '
				     . checked( $val,'draft', false)
				     . '/> <label for="active_inactive-inactive" class="select-it">Inactive</label>';
			echo '</div>';
		}
	}


	public function render_defaults_metabox( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'save_subscription_status', 'subscription_custom_nonce' );

		// available statuses
		$list_status = array( 'draft', 'publish' );

		// get default status from meta
		$default_status = get_post_meta( $post->ID, 'subscription_post_status', 'true');

		// post status metabox
		echo '<p><label for="subscription_post_status">';
		esc_html_e( 'Status', 'greatermedia' );
		echo '</label> ';

		echo '<select name="subscription_post_status" id="subscription_post_status" style="width: 300px;">';
			foreach( $list_status as $status ) {
				echo '<option', esc_attr( $status == $default_status ? ' selected="selected"' : '' )
				, ' value="' . esc_attr( $status ) .'">' . esc_html( ucfirst( $status ) ) . '</option>';
			}
		echo '</select></p>';

		// get all taxonomies of "post"
		$taxonomy_names = get_object_taxonomies( 'post', 'objects' );

		foreach( $taxonomy_names as $taxonomy ) {
			//$taxonomy = $taxonomy['args']['taxonomy'];
			$name = $taxonomy->label;
			$label = $taxonomy->name;

			// Use get_post_meta to retrieve an existing value from the database.
			$terms = get_post_meta( $post->ID, 'subscription_default_terms-' . $label, true );
			$terms = explode( ',', $terms );

			// Display the form, using the current value.
			echo '<p> ';

			$term_args = array(
				'get'           => 'all',
				'hide_empty'    => false
			);

			$allterms[] = get_terms( $label, $term_args );
			echo '<h4>' . esc_html( $name ) . '</h4>';

			if( !empty( $allterms[0] ) ) {
				foreach( $allterms as $index => $term ) {
					foreach( $term as $single_term ) {
						$checked = in_array( $single_term->term_id, $terms) ? 'yes' : 'no';
						echo '<label for="subscription_default_terms-' . esc_attr( $label ) . '[]">';
						echo '<input name="subscription_default_terms-' . esc_attr( $label ) . '[]" id="subscription_default_terms" type="checkbox" ', checked( $checked, 'yes' )
						, ' value="' . intval( $single_term->term_id ) .'">' . esc_html( $single_term->name );
						echo '</label><br/>';
					}
				}
			} else {
				echo "No existing term";
			}

			unset($allterms);
			$allterms = array();
			echo '</p>';
		}
	}

	// Add button to syndicate at any time, with ajax call
	public function render_syndication_control( $post ) {
		echo '<div id="syndication_status">';
		echo '</div>';
		echo '<button data-postid="' . intval( $post->ID )
		     . '" name="syndicate_now" id="syndicate_now" class="button button-primary button-large"'
		     . '>Syndicate</button>';
	}

}

$SyndicationCPT = new SyndicationCPT();
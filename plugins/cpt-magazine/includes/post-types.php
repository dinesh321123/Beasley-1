<?php

/**
 * Class MagazineCPT
 */
class MagazineCPT {

	const MAGAZINE_POST_TYPE = 'magazine_cpt';
	const MAGAZINE_CPT_NAME = 'Category Featured Post Selection';
	const MAGAZINE_CPT_SLUG = 'category_featured_post_meta_box';

	/**
	 * Hook into the appropriate actions when the class is initiated.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'magazine_cpt' ), 0 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'save_post', array( __CLASS__, 'save_meta_data' ) );
		add_action( 'wp_ajax_validate_magazine_feilds', array( __CLASS__, 'validate_magazine_feilds' ) );
		
		// Add the custom columns to the magazine post type:
		add_action( 'manage_'.self::MAGAZINE_POST_TYPE.'_posts_columns', array( __CLASS__, 'set_custom_edit_magazine_columns' ) );
		add_action( 'manage_'.self::MAGAZINE_POST_TYPE.'_posts_custom_column', array( __CLASS__, 'custom_magazine_column' ), 10, 2);
	}

	public static function set_custom_edit_magazine_columns($columns) {
		$columns = array(
			'cb' => $columns['cb'],
			'title' => __( 'Title' ),
			'magazine_cat' => __( 'Magazine Category', MAGAZINE_CPT_TEXT_DOMAIN ),
			'date' => __( 'Date' ),
		);
		$columns['magazine_cat'] = __( 'Magazine Category', MAGAZINE_CPT_TEXT_DOMAIN );
		return $columns;
	}

	public static function custom_magazine_column( $column, $post_id ) {
		$selected_category = get_field( 'select_category_magazine_cpt', $post_id );
		$termObj = get_term( $selected_category );
		$category = $selected_category ? ( $termObj ? $termObj->name : "" ) : "";

		switch ( $column ) {
			case 'magazine_cat' :
				if ( is_string( $category ) )
					echo $category;
				break;
		}
	
	}

	public static function validate_magazine_feilds() {
		$acf_selected_category = filter_input( INPUT_GET, 'selectedCat', FILTER_VALIDATE_INT );
		$post_id = filter_input( INPUT_GET, 'post_id', FILTER_VALIDATE_INT );

		if($acf_selected_category) {
			$posts = get_posts([
				'post_type'      => self::MAGAZINE_POST_TYPE,
				'post_status'    => 'publish',
				'exclude'		 => array( $post_id ),
				'meta_query'     => [
					[
						'key'     => 'select_category_magazine_cpt',
						'value'   => $acf_selected_category,
					]
				]
			]);

			$result = array( "alreadyExist" => false );
			if(!empty($posts) && count($posts) > 0) {
				$result = array( "alreadyExist" => true );
			}
			wp_send_json_success( $result );
		}
		return;
	}

	/**
	 * Add the listicle Content Type
	 */
	public static function magazine_cpt() {
		load_plugin_textdomain( MAGAZINE_CPT_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		$labels = array(
			'name'                  => _x( 'Magazine', 'Post type general name', MAGAZINE_CPT_TEXT_DOMAIN ),
			'singular_name'         => _x( 'Magazine', 'Post type singular name', MAGAZINE_CPT_TEXT_DOMAIN ),
			'menu_name'             => _x( 'Magazine', 'Admin Menu text', MAGAZINE_CPT_TEXT_DOMAIN ),
			'add_new'               => __( 'Add New', MAGAZINE_CPT_TEXT_DOMAIN ),
			'add_new_item'          => __( 'Add New magazine', MAGAZINE_CPT_TEXT_DOMAIN ),
			'edit_item'             => __( 'Edit magazine', MAGAZINE_CPT_TEXT_DOMAIN ),
			'new_item'              => __( 'New magazine', MAGAZINE_CPT_TEXT_DOMAIN ),
			'view_item'             => __( 'View magazine', MAGAZINE_CPT_TEXT_DOMAIN ),
			'search_items'          => __( 'Search magazine', MAGAZINE_CPT_TEXT_DOMAIN ),
			'not_found'             => __( 'No magazine found.', MAGAZINE_CPT_TEXT_DOMAIN ),
			'not_found_in_trash'    => __( 'No magazine found in Trash.', MAGAZINE_CPT_TEXT_DOMAIN ),
			'parent_item_colon'     => __( 'Parent magazine:', MAGAZINE_CPT_TEXT_DOMAIN ),
			'name_admin_bar'        => _x( 'Magazine', 'Add New on Toolbar', MAGAZINE_CPT_TEXT_DOMAIN ),
			'all_items'             => __( 'All magazine', MAGAZINE_CPT_TEXT_DOMAIN ),
			'filter_items_list'     => _x( 'Filter magazine list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', MAGAZINE_CPT_TEXT_DOMAIN ),
			'items_list_navigation' => _x( 'magazine list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', MAGAZINE_CPT_TEXT_DOMAIN ),
			'items_list'            => _x( 'magazine list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', MAGAZINE_CPT_TEXT_DOMAIN ),
		);
		$rewrite = array(
			'slug'                => 'magazine',
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'labels'             => $labels,
			'description'        => 'magazine custom post type.',
			'hierarchical'       => false,
			'taxonomies'         => array(),
			'public'             => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_position'      => 29,
			'menu_icon'           => 'dashicons-list-view',
			'publicly_queryable' => true,
			'query_var'          => true,
			'rewrite'            => $rewrite,
			'capability_type'    => array( 'magazine', 'magazines' ),
			'map_meta_cap'        => true,
			'has_archive'        => true,
			'supports'           => array( 'title' ),
			'show_in_rest'       => true,
			'register_meta_box_cb' => array( __CLASS__, 'register_meta_boxes' ),
		);
		register_post_type( self::MAGAZINE_POST_TYPE, $args );

		$categories = get_categories();
		foreach ( $categories as $category ) {
			$posts = get_posts([
				'post_type'      => self::MAGAZINE_POST_TYPE,
				'post_status'    => 'publish',
				'meta_query'     => [
					[
						'key'     => 'select_category_magazine_cpt',
						'value'   => $category->term_id,
					]
				]
			]);
			$category_choise[$category->term_id] = $category->name;
		}

		/*
		* Create custom metabox in right side
		*/
		acf_add_local_field_group( array(
			'key'                   => 'magazine_category_selection',
			'title'                 => 'Category Selection',
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => 1,
			'description'           => '',
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => self::MAGAZINE_POST_TYPE,
					),
				),
			),
			'fields'                => array(
				array(
					'key'           => 'field_select_category_magazine_cpt',
					'label'         => 'Select Category',
					'name'          => 'select_category_magazine_cpt',
					'type'          => 'select',
					'choices' 		=> $category_choise,
					'multiple' => 0,
					'allow_null' 	=> 0,
					'ui' 			=> 1,
					'ajax' 			=> 1,
					'required'      => 1,
				),
			),
		) );
	}

	/**
	 * Registers meta boxe
	 *
	 */
	public static function register_meta_boxes( ) {
		add_meta_box(
			self::MAGAZINE_CPT_SLUG,
			self::MAGAZINE_CPT_NAME,
			array( __CLASS__, 'render_source_meta_box' ),
			self::MAGAZINE_POST_TYPE,
			'normal',
			'high',
			array( 'slug' => self::MAGAZINE_CPT_SLUG )
		);
	}

	public static function render_source_meta_box( $magazine, $metabox ) {
		global $post;
		$selected_category = get_field( 'select_category_magazine_cpt', $post );
		
		$post_picker_args = array (
			'show_numbers'            => true,
			'show_icons'              => true,
			'show_recent' 			  => false,
			'limit' 				  => 5,
			'args'                    => array (
				'post_type'   => self::get_supported_post_types(),
				'post_status' => 'publish',
			),
		);
		
		$post_ids = get_post_meta( absint( $magazine->ID ), sanitize_text_field( self::MAGAZINE_CPT_SLUG ), true );
		$post_ids = implode( ',', array_slice( explode( ',', $post_ids ), 0, $post_picker_args['limit'] ) );
	
		self::render_post_picker( $metabox['args']['slug'], $post_ids, $post_picker_args );
	}
	
	/**
	 * Render a post picker field.
	 *
	 * @param string $name Name of input
	 * @param string $value Expecting comma separated post ids
	 * @param array $options Field options
	 */
	function render_post_picker( $name, $value, $options = array() ) {
		if ( class_exists( 'NS_Post_Finder' ) ) {
			\NS_Post_Finder::render( $name, $value, $options );
		} else {
			?><p>The Post Finder plugin was not found.</p><?php
		}
	}

	/**
	 * Saves meta box data.
	 *
	 * @param int $post_id The post id.
	 * @return boolean TRUE if meta data have been saved, otherwise FALSE.
	 */
	function save_meta_data( $post_id ) {
		// do nothing if it is autosave request
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		// do nothing if current user can't edit homepage posts
		$post_type = get_post_type_object( self::MAGAZINE_POST_TYPE );
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return false;
		}
		
		$value = filter_input( INPUT_POST, self::MAGAZINE_CPT_SLUG );		
		delete_post_meta( absint( $post_id ), sanitize_text_field( self::MAGAZINE_CPT_SLUG ) );

		// Save the preview or live meta data, depending on the situation.
		if ( ! empty( $value ) ) {
			add_post_meta(
				absint( $post_id ),
				sanitize_text_field( self::MAGAZINE_CPT_SLUG ),
				sanitize_text_field( $value )
			);
		}

		return true;
	}

	/**
	 * Get supported post types which will be quiried
	 *
	 * @return array
	 */
	public static function get_supported_post_types() {
		return (array) apply_filters( 'magazine-cpt-curation-post-types', array( 'post', 'contest', 'gmr_gallery', 'affiliate_marketing', 'listicle_cpt' )  );
	}

	/**
	 * Enqueues admin scripts and styles.
	 *
	 * @global string $typenow The current type.
	 * @global string $pagenow The current page.
	 */
	public static function enqueue_scripts() {
		global $typenow, $pagenow;
		if ( self::MAGAZINE_POST_TYPE == $typenow && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			wp_dequeue_script( 'select2');
			wp_deregister_script( 'select2' );
			wp_dequeue_style('select2');
			wp_deregister_style('select2');
			wp_enqueue_style('select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
			wp_enqueue_script('select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', '', '', false);
			wp_register_style('magazine-admin',MAGAZINE_CPT_URL . "assets/css/magazine_admin.css", array(), MAGAZINE_CPT_VERSION, 'all');
			wp_enqueue_style('magazine-admin');
			wp_enqueue_script( 'magazine-admin', MAGAZINE_CPT_URL . "assets/js/magazine_admin.js", array('jquery', 'jquery-ui-dialog'), MAGAZINE_CPT_VERSION, true);
		}
   }
}

MagazineCPT::init();

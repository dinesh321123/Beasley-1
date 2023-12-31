<?php
/**
 * Class CoAuthorSettingMetaboxes
 */
class CoAuthorSettingMetaboxes {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
			add_action( 'wp_loaded', array( __CLASS__, 'load_coauthor' ), 0 );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	public static function load_coauthor() {
		$roles = [ 'administrator' ];

		foreach ( $roles as $role ) {
			$role_obj = get_role( $role );

			if ( is_a( $role_obj, \WP_Role::class ) ) {
				$role_obj->add_cap( 'manage_co_author_setting', false );
			}
		}

		$location = array();
		$currnet_author = isset($_GET['post']) ? (get_post($_GET['post']) ? get_post($_GET['post'])->post_author : 0) : 0;
		$currnet_author = $currnet_author ? (int)trim($currnet_author) : get_current_user_id();
		// $current_author_name = get_the_author_meta( 'display_name', $currnet_author ? $currnet_author : get_current_user_id() );

		$args = array( 'blog_id' => 0, 'fields' => array( 'ID', 'display_name','user_login' ) );

		$network_users = get_users( $args );

		$user_choise = array();
		foreach ( $network_users as $user ) {
			$user_choise[$user->ID] = $user->display_name." ( $user->user_login )";
		}

		foreach ( self::tag_permissions_posttype_list() as $type ) {
			$location[] =
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => $type,
				),
			);
		}
		/*
		* Create custom metabox for Segments Navigation in right side
		*/
		acf_add_local_field_group( array(
			'key'                   => 'author_settings',
			'title'                 => 'Author Settings',
			'menu_order'            => 0,
			'position'              => 'side',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => 1,
			'description'           => '',
			'location'              => $location,
			'fields'                => array(
				array(
					'key'           => 'field_primary_author_cpt',
					'label'         => 'Primary Author',
					'name'          => 'primary_author_cpt',
					'type'          => 'select',
					'layout' 		=> 'vertical',
					'choices' 		=> $user_choise,
					'allow_null' 	=> 0,
					'ui' 			=> 1,
					'ajax' 			=> 1,
					'required'      => 1,
					'default_value' => array(
						0 => $currnet_author
					),
				),
				array(
					'key'           => 'field_secondary_author_cpt',
					'label'         => 'Secondary Author',
					'name'          => 'secondary_author_cpt',
					'type'          => 'select',
					'layout' 		=> 'vertical',
					'choices' 		=> $user_choise,
					'allow_null' 	=> 1,
					'ui' 			=> 1,
					'ajax' 			=> 1,
					'required'      => 0,
					'default_value' => 0,
				),
			),
		) );
	}

	/**
	 * Enqueues admin scripts and styles.
	 *
	 * @global string $typenow The current type.
	 * @global string $pagenow The current page.
	 */
	public static function enqueue_scripts() {
		global $typenow, $pagenow;
		$post_types = self::tag_permissions_posttype_list();
		if ( in_array( $typenow, $post_types ) && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			wp_dequeue_script( 'select2');
			wp_deregister_script( 'select2' );
			wp_dequeue_style('select2');
			wp_deregister_style('select2');
			wp_enqueue_style('select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
			// wp_enqueue_script('select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', '', '', false);
			wp_enqueue_script('select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js', '', '', false);
			wp_register_style('coauthor-settings-admin', COAUTHOR_SETTINGS_URL . "assets/css/coauthor_settings.css", array(), COAUTHOR_SETTINGS_VERSION, 'all');
			wp_enqueue_style('coauthor-settings-admin');
			wp_enqueue_script( 'coauthor-settings-admin', COAUTHOR_SETTINGS_URL . "assets/js/coauthor_settings.js", array('jquery'), COAUTHOR_SETTINGS_VERSION, true);
			wp_localize_script( 'coauthor-settings-admin', 'my_ajax_object', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
		}
   }

	public static function tag_permissions_posttype_list() {
		$result = array();

		if(current_user_can('manage_co_author_setting')){
			$result	= (array) apply_filters( 'co-author-post-types', array( 'post', 'listicle_cpt', 'affiliate_marketing', 'gmr_gallery', 'show', 'gmr_album', 'tribe_events', 'announcement', 'contest', 'podcast', 'episode', 'content-kit' )  );
		}
		return $result;
	}
}

CoAuthorSettingMetaboxes::init();

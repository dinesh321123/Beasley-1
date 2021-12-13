<?php
/**
 * Class CoAuthorSettingMetaboxes
 */
class CoAuthorSettingMetaboxes {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
			add_action( 'init', array( __CLASS__, 'load_coauthor' ), 0 );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	public static function load_coauthor() {
		
		$location = array();
		$currnet_author = $_GET['post'] ? (get_post($_GET['post']) ? get_post($_GET['post'])->post_author : 0) : 0;
		$current_author_name = get_the_author_meta( 'display_name', $currnet_author ? $currnet_author : get_current_user_id() );
		
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
			'key'                   => 'co_author_settings',
			'title'                 => 'Co Author Settings',
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
					'key'           => 'field_post_creator_cpt',
					'label'         => 'Post Creator',
					'name'          => 'post_creator_cpt',
					'instructions'  => $current_author_name,
					'required'      => 0,
					'default_value' => 0,
				),
				array(
					'key'           => 'field_is_co_author_cpt',
					'name'          => 'is_co_author_cpt',
					'type'          => 'checkbox',
					'choices' => array(
						'true'	=> 'Is Co Author?  '
					),
					'layout' => 'vertical',
					'required'      => 0,
					'default_value' => 0,
				),
				array(
					'key'           => 'field_reported_attribution_cpt',
					'label'         => 'Reported Attribution',
					'name'          => 'reported_attribution_cpt',
					'type'          => 'user',
					'layout' => 'vertical',
					'allow_null' => 1,
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
			wp_register_style('coauthor-settings-admin', COAUTHOR_SETTINGS_URL . "assets/css/coauthor_settings.css", array(), COAUTHOR_SETTINGS_VERSION, 'all');
			wp_enqueue_style('coauthor-settings-admin');  	
			wp_enqueue_script( 'coauthor-settings-admin', COAUTHOR_SETTINGS_URL . "assets/js/coauthor_settings.js", array('jquery'), COAUTHOR_SETTINGS_VERSION, true);
			wp_localize_script( 'coauthor-settings-admin', 'my_ajax_object', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
		}
   }

	public static function tag_permissions_posttype_list() {
		return (array) apply_filters( 'tag-permissions-allow-post-types', array( 'post', 'listicle_cpt', 'gmr_gallery', 'show', 'gmr_album', 'tribe_events', 'announcement', 'contest', 'podcast', 'episode', 'content-kit' )  );
	}
}

CoAuthorSettingMetaboxes::init();

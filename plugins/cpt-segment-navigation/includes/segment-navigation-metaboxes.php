<?php
/**
 * Class SegmentPermissionsMetaboxes
 */
class SegmentPermissionsMetaboxes {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
			add_action( 'init', array( __CLASS__, 'load_segmentation' ), 0 );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	public static function load_segmentation() {

		$location = array();
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
			'key'                   => 'segmentation_settings',
			'title'                 => 'Segmentation Settings',
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
					'key'           => 'field_display_segmentation_cpt',
					'label'         => 'Display Segmentation',
					'name'          => 'display_segmentation',
					'type'          => 'true_false',
					'instructions'  => 'Whether or not to display segmentation on front side.',
					'required'      => 0,
					'default_value' => 0,
					'ui'            => 1,
					'ui_on_text'    => '',
					'ui_off_text'   => '',
				),
				array(
					'key'               => 'field_segmentation_ordering_cpt',
					'label'             => 'Segments Ordering Type',
					'name'              => 'segmentation_ordering',
					'type'				=> 'radio',
					'instructions'      => 'Choose the order want to display.',
					'default_value'     => 'ascending',
					'layout'			=> 'horizontal', 
					'choices'			=> array(
						'asc'			=> 'Ascending',
						'desc'			=> 'Descending',
						'header'		=> 'Heading',
					)
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
			wp_register_style('segment-navigation-admin', SEGMENT_NAVIGATION_URL . "assets/css/sn_admin.css", array(), SEGMENT_NAVIGATION_VERSION, 'all');
			wp_enqueue_style('segment-navigation-admin');  	
			wp_enqueue_script( 'segment-navigation-admin', SEGMENT_NAVIGATION_URL . "assets/js/sn_admin.js", array('jquery'), SEGMENT_NAVIGATION_VERSION, true);
			wp_localize_script( 'segment-navigation-admin', 'my_ajax_object', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
		}
   }

	public static function tag_permissions_posttype_list() {
		return (array) apply_filters( 'tag-permissions-allow-post-types', array( 'post', 'listicle_cpt', 'affiliate_marketing', 'gmr_gallery', 'show', 'tribe_events', 'announcement', 'contest', 'podcast', 'episode' )  );
	}
}

SegmentPermissionsMetaboxes::init();

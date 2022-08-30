<?php
/**
 * Class SecondStreetWidget
 */
class SecondStreetWidget {
	function __construct()
	{
		$this->init();
	}
	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function init() {
		// add_action( 'init', array( $this, 'ssw_rendering_init' ), 0 );	// Front hook
		add_action( 'admin_init', array( $this, 'ssw_admin_init' ), 0 );	// Admin hook
		// add action hooks
		add_action( 'bbgi_register_settings', array( $this, 'register_settings' ), 10, 2 );
	}
	public function ssw_rendering_init() {
		if ( is_admin() ) {
			return;
			exit;
		}
	}
	public function register_settings( $group, $page ) {
		$section_id = 'beasley_secondstreet_settings';

		// add_settings_section( $section_id, 'SecondStreet', '__return_false', $page );
		add_settings_field( 'secondstreet_op_id', 'Station op_id', 'bbgi_input_field', $page, $section_id, 'name=secondstreet_op_id' );
		register_setting( $group, 'secondstreet_op_id', 'sanitize_text_field' );
	}

	/**
	 * Returns array of post type.
	 *
	 * @return array
	 */
	public function get_ssw_posttype_list() {
		$result	= (array) apply_filters( 'draft-ssw-post-types', array( 'post', 'gmr_gallery', 'listicle_cpt', 'affiliate_marketing', 'page' )  );
		return $result;
	}

	/**
	 * Add the Draft King iFrame Settings
	 */
	public function ssw_admin_init() {
		$role_obj = get_role('administrator');
		if (is_a($role_obj, \WP_Role::class)) {
			$role_obj->add_cap('manage_ssw_onoff_setting', false);
		}

		global $pagenow;
		$currentPostType = get_post_type( $_GET['post'] );
		if( ! current_user_can('manage_ssw_onoff_setting') ){
			return;
			exit;
		}

		/* if ( ! in_array( $currentPostType, $this->get_ssw_posttype_list() ) ) {
			return;
			exit;
		} */

		if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			return;
			exit;
		}

		$location = array();
		foreach ( $this->get_ssw_posttype_list() as $type ) {
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
		* Create Second Street email opt-in widget metabox in right side
		*/
		acf_add_local_field_group( array(
			'key'                   => 'ssw_settings',
			'title'                 => 'Second street email opt-in widget settings',
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
					'key'           => 'field_hide_ssw',
					'label'         => 'Show Second Street email widget',
					'name'          => 'hide_ssw',
					'type'          => 'true_false',
					'instructions'  => 'Whether or not to display Second street email opt-in widget on the page.',
					'required'      => 0,
					'default_value' => 1,
					'ui'            => 1,
					'ui_on_text'    => '',
					'ui_off_text'   => '',
				),
			),
		) );
	}
}

new SecondStreetWidget();

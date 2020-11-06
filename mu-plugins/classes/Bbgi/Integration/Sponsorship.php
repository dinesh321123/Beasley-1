<?php
/**
 * Module responsible for integrating Sponsorship fields.
 *
 * @package Bbgi
 */

namespace Bbgi\Integration;
define('EDIT_SPONSORSHIP', 'edit_sponsorship');

class Sponsorship extends \Bbgi\Module {
	/**
	 * Register actions and hooks.
	 *
	 * @return void
	 */
	public function register() {
		$this->register_custom_cap();
		add_action( 'wp_loaded', $this( 'register_meta_box' ), 20 );
	}

	/**
	 * Get post types whiltelist
	 *
	 * @return array
	 */
	public function get_post_types_whitelist() {
		$blacklist = [ 'fp_feed', 'subscription' ];
		return array_diff( get_post_types(), $blacklist );
	}

	/**
	 * Register custom capability for admins and editors.
	 *
	 * @return void
	 */
	public function register_custom_cap() {
		$roles = [ 'administrator' ];

		foreach ( $roles as $role ) {
			$role_obj = get_role( $role );

			if ( is_a( $role_obj, \WP_Role::class ) ) {
				$role_obj->add_cap( EDIT_SPONSORSHIP, false );
			}
		}
	}

	/**
	 * Register the sponsorship metabox.
	 *
	 * @return void
	 */
	public function register_meta_box() {
		if (! current_user_can( EDIT_SPONSORSHIP )) {
			return;
		}

		$location = array();
		foreach ( $this->get_post_types_whitelist() as $type ) {
			$location[] =
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => $type,
				),
			);
		}

		acf_add_local_field_group( array(
			'key'                   => 'group_sponsorship_settings',
			'title'                 => 'Sponsorship Settings',
			'fields'                => array(
												array(
													'key'               => 'field_sponsored_by_label_content',
													'label'             => 'Sponsored By Text',
													'name'              => 'sponsored_by_label',
													'type'              => 'text',
													'instructions'      => 'Label to precede Sponsor Name.',
													'required'          => 0,
													'conditional_logic' => 0,
													'message'           => '',
													'default_value'     => 'Sponsored by ',
													'ui'                => 1,
												),
                                       			array(
                                       				'key'               => 'field_sponsor_name_content',
                                       				'label'             => 'Sponsor Name',
                                       				'name'              => 'sponsor_name',
                                       				'type'              => 'text',
                                       				'instructions'      => 'Please enter name only. "Sponsored By" will precede Sponsor Name.',
                                       				'required'          => 0,
                                       				'conditional_logic' => 0,
                                       				'message'           => '',
                                       				'default_value'     => '',
                                       				'ui'                => 1,
                                       			),
                                       			array(
                                       				'key'               => 'field_sponsor_url_content',
                                       				'label'             => 'Sponsor Url',
                                       				'name'              => 'sponsor_url',
                                       				'type'              => 'text',
                                       				'instructions'      => 'Optional URL to load Landing page when user clicks on Sponsor link.',
                                       				'required'          => 0,
                                       				'conditional_logic' => 0,
                                       				'message'           => '',
                                       				'default_value'     => '',
                                       				'ui'                => 1,
                                       			)
											),
			'location'              => $location,
			'menu_order'            => 0,
			'position'              => 'side',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => 1,
			'description'           => '',
		) );
	}

}

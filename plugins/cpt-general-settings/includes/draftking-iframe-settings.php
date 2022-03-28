<?php
/**
 * Class DraftKingIframeSettings
 */
class DraftKingIframeSettings {
	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'settings_cpt_init' ), 0 );
	}

	/**
	 * Returns array of post type.
	 *
	 * @return array
	 */
	public static function get_draft_king_posttype_list() {
		$result = array();

		if(current_user_can('manage_draft_kings_onoff_setting')){
			$result	= (array) apply_filters( 'draft-king-iframe-post-types', array( 'post', 'gmr_gallery', 'show', 'gmr_album', 'listicle_cpt', 'affiliate_marketing', 'tribe_events', 'contest', 'page' )  );
		}
		return $result;
	}

	/**
	 * Add the Draft King iFrame Settings
	 */
	public static function settings_cpt_init() {
		$location = array();
		foreach ( DraftKingIframeSettings::get_draft_king_posttype_list() as $type ) {
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
		* Create draft king iframe metabox in right side
		*/
		acf_add_local_field_group( array(
			'key'                   => 'draftking_iframe_settings',
			'title'                 => 'Draft King Settings',
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
					'key'           => 'field_hide_draftking_iframe',
					'label'         => 'Show iFrame',
					'name'          => 'hide_draftking_iframe',
					'type'          => 'true_false',
					'instructions'  => 'Whether or not to display iframe on the page.',
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

DraftKingIframeSettings::init();

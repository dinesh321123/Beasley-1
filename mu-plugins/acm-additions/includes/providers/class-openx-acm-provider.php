<?php

namespace GreaterMedia\AdCodeManager;

class OpenX_ACM_Provider extends \ACM_Provider {

	public function __construct() {
		$this->ad_tag_ids = array();

		// These are the fields that show up in the left side of the ACM config screen
		$this->ad_code_args = array(
			array(
				'key' => 'tag',
				'label' => __( 'Tag', 'ad-code-manager' ),
				'editable' => true,
				'required' => true,
				'type' => 'select',
				'options' => array(
					// This is added later, through 'acm_ad_code_args' filter
				),
			),
			array(
				'key' => 'openx_id',
				'label' => 'OpenX ID',
				'editable' => true,
				'required' => true,
			),
		);

		add_filter( 'acm_ad_code_args', array( $this, 'filter_ad_code_args' ) );

		add_filter( 'acm_display_ad_codes_without_conditionals', '__return_true' );

		parent::__construct();
	}

	public function filter_ad_code_args( $ad_code_args ) {
		global $ad_code_manager;

		foreach( $ad_code_args as $tag => $ad_code_arg ) {
			if ( 'tag' == $ad_code_arg['key'] ) {
				// Get all of the tags that are registered, and provide them as options
				foreach ( (array)$ad_code_manager->ad_tag_ids as $ad_tag ) {
					if ( isset( $ad_tag['enable_ui_mapping'] ) && $ad_tag['enable_ui_mapping'] )
						$ad_code_args[$tag]['options'][$ad_tag['tag']] = $ad_tag['tag'];
				}
			}
		}

		return $ad_code_args;
	}
}

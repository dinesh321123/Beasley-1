<?php
/**
 * Created by Eduard
 * Date: 30.10.2014
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class ContestsCategorizations {

	public static function init() {
		add_action( 'admin_menu' , array( __CLASS__, 'remove_contest_type_meta' ));
	}

	/**
	 * Remove default metabox for contest_type
	 */
	public static function remove_contest_type_meta() {
		remove_meta_box( 'contest_typediv', 'contest', 'side' );
	}

}

ContestsCategorizations::init();

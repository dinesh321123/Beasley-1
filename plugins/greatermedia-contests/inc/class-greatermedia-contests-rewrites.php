<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaContestsRewrites {

	function __construct() {

		add_action( 'init', array( __CLASS__, 'add_contest_rewrites' ) );

	}

	/**
	 * Retrieve a list of rewrite rules this class implements
	 * @return array
	 */
	public static function rewrite_rules() {

		static $rewrite_rules;

		if ( ! isset( $rewrite_rules ) ) {

			$rewrite_rules = array(
				'^contest/type/([^/]*)/?' => 'index.php?post_type=contest&contest_type=$matches[1]',
			);

		}

		return $rewrite_rules;

	}

	public static function add_contest_rewrites() {

		global $wp_rewrite;

		$rewrite_rules = self::rewrite_rules();

		foreach ( $rewrite_rules as $rewrite_regex => $rewrite_target ) {
			add_rewrite_rule( $rewrite_regex, $rewrite_target, 'top' );
		}

		// flush rewrite rules only if our rules is not registered
		$all_registered_rules = $wp_rewrite->wp_rewrite_rules();
		$registered_rules     = array_intersect( $rewrite_rules, $all_registered_rules );

		if ( count( $registered_rules ) !== count( $rewrite_rules ) ) {
			flush_rewrite_rules( true );
		}

	}
}

$GreaterMediaContestsRewrites = new GreaterMediaContestsRewrites();

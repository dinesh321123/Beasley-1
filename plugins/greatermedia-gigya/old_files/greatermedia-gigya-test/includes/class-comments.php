<?php
class GMI_Gigya_Comments {

	/**
	 * WordPress Hooks
	 */
	public static function hooks() {
		add_filter( 'comments_template', array( __CLASS__, 'use_plugin_comments_form' ) );
	}

	public static function use_plugin_comments_form( $template ) {
		return GMGIGYA_PATH . '/templates/comments-template.php';
	}

	/**
	 * Category for comments. Provides centralized management, user permissions, etc.
	 * Must be created in the admin panel for comments to appear
	 * @return string
	 */
	public static function category_id() {
		$site = get_current_site();
		return $site->domain;
	}

	/**
	 * Multiple streams per category. If category + stream are the same, shows the same comments
	 * streamID is limited to 150 characters and is case-sensitive.
	 * Gigya recommends using the permalink
	 */
	public static function stream_id() {
		return get_the_permalink();
	}

}

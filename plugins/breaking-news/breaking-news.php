<?php
/**
 * Plugin Name: Breaking News
 * Plugin URI:  http://wordpress.org/plugins
 * Description: Add breaking news support for posts.
 * Version:     0.1.0
 * Author:      10up
 * Author URI:
 * License:     GPLv2+
 * Text Domain: breaking_news
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2014 10up (email : michael.phillips@10up.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 */

// Useful global constants
define( 'BREAKING_NEWS_VERSION', '0.1.0' );
define( 'BREAKING_NEWS_URL',     plugin_dir_url( __FILE__ ) );
define( 'BREAKING_NEWS_PATH',    dirname( __FILE__ ) . '/' );

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

include __DIR__ . '/includes/class-breaking-news.php';

/**
 * Determine if we currently have a breaking news item. Use this function to show/hide elements in templates.
 *
 * @return bool
 */
function breaking_news_item_is_available() {
	$breaking_news = Breaking_News::get_latest_breaking_news_item();
	if ( ! empty( $breaking_news ) ) {
		return true;
	}

	return false;
}

/**
 * Return an array of breaking news items. Use this with post__not_in to remove breaking news posts from other queries.
 *
 * @return array
 */
function breaking_news_get_latest_post_ids() {
	$post_ids = array();
	$post = Breaking_News::get_latest_breaking_news_item();

	if ( ! empty( $post ) ) {
		$post_ids[] = $post->ID;
	}

	return $post_ids;
}


/**
 * Default initialization for the plugin:
 * - Registers the default textdomain.
 */
function breaking_news_init() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'breaking_news' );
	load_textdomain( 'breaking_news', WP_LANG_DIR . '/breaking_news/breaking_news-' . $locale . '.mo' );
	load_plugin_textdomain( 'breaking_news', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * Activate the plugin
 */
function breaking_news_activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	breaking_news_init();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'breaking_news_activate' );

/**
 * Deactivate the plugin
 * Uninstall routines should be in uninstall.php
 */
function breaking_news_deactivate() {

}
register_deactivation_hook( __FILE__, 'breaking_news_deactivate' );

// Wireup actions
add_action( 'init', 'breaking_news_init' );

// Wireup filters

// Wireup shortcodes

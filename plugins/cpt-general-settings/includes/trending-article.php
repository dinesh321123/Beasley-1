<?php
/**
 * Description: This file implements a shortcode for displaying trending articless in WordPress.
 * The shortcode [trending-article] generates an HTML <div> element with data attributes representing
 * the current post's ID, title, post type, categories, and URL. It can be used to create a trending
 * articles section or perform actions based on the current post.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

/**
 * TrendingArticle Class
 *
 * Implements the shortcode functionality for displaying trending articless.
 */
class TrendingArticle {
    
    /**
     * Constructor method.
     *
     * Initializes the TrendingArticle class.
     * Hooks into the 'init' action to register the shortcode.
     */
    function __construct() {
        add_action( 'init', array( $this, 'wp_init_trending_article' ), 1 );
    }
    
    /**
     * Initialize the trending articles functionality.
     *
     * Registers the 'trending-article' shortcode.
     */
    public function wp_init_trending_article() {
        add_shortcode( 'trending-article', array( $this, 'trending_article_function' ) );
    }

    /**
     * Trending articles Shortcode Function.
     *
     * Handles the 'trending-article' shortcode and generates the trending articles HTML.
     *
     * @return string Generated HTML for displaying trending articless.
     */
    public function trending_article_function() {

        return ee_render_trending_articles('embed_custom');

    }

}

// Create an instance of the TrendingArticle class to initialize the shortcode functionality.
new TrendingArticle();
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
        add_action( 'bbgi_register_settings', array( $this, 'trending_article_register_settings' ) , 10, 2 );
        add_action( 'wp_loaded', array( $this, 'trending_article_metabox' ), 0 );
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

    public function trending_article_register_settings( $group, $page ) {

        $after_content_args = array(
			'name'    => 'trending_article_after_content',
			'default' => 'off',
			'class'   => '',
			'options' => array(
				'on' => 'On',
				'off'  => 'Off',
			),
		);
        $section_id = 'trending_article_section';

        add_settings_section( $section_id, 'Trending Articles', '__return_false', $page );
		add_settings_field( 'trending_article_title', 'Title Text', 'bbgi_input_field', $page, $section_id, 'name=trending_article_title&default=Trending Articles' );
        
        add_settings_field('trending_article_gallery_items', 'Gallery Items', 'bbgi_input_field', $page, $section_id, 'name=trending_article_gallery_items&default=5');
        add_settings_field('trending_article_AM_items', 'AM Items', 'bbgi_input_field', $page, $section_id, 'name=trending_article_AM_items&default=5');
        add_settings_field('trending_article_listicle_items', 'Listicle Items', 'bbgi_input_field', $page, $section_id, 'name=trending_article_listicle_items&default=5');
        add_settings_field( 'trending_article_after_content', 'After Content', 'bbgi_select_field', $page, $section_id, $after_content_args );

        register_setting( $group, 'trending_article_title', 'sanitize_text_field');
        register_setting( $group, 'trending_article_gallery_items', 'sanitize_text_field');
        register_setting( $group, 'trending_article_AM_items', 'sanitize_text_field');
        register_setting( $group, 'trending_article_listicle_items', 'sanitize_text_field');
        register_setting( $group, 'trending_article_after_content', 'sanitize_text_field' );

    }

    public function get_trending_article_posttype_list() {
		$result = array();

		if(current_user_can('manage_trending_article')){
			$result	= (array) apply_filters( 'trending-article-post-types', array( 'post', 'gmr_gallery', 'listicle_cpt', 'affiliate_marketing')  );
		}
		return $result;
	}

    public function trending_article_metabox() {

        $roles = [ 'administrator' ];
        $location = array();

		foreach ( $roles as $role ) {
			$role_obj = get_role( $role );
			if ( is_a( $role_obj, \WP_Role::class ) ) {
				$role_obj->add_cap( 'manage_trending_article', false );
			}
		}

		foreach ( $this->get_trending_article_posttype_list() as $type ) {
			$location[] = array(
                            array(
                                'param'    => 'post_type',
                                'operator' => '==',
                                'value'    => $type,
                            ),
                        );
		}

        acf_add_local_field_group( array(
			'key'                   => 'trending_article_after_content_settings',
			'title'                 => 'Trending Article',
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
					'key'           => 'field_trending_article_after_content_page',
					'label'         => 'Show Trending Article',
					'name'          => 'trending_article_after_content_page',
					'type'          => 'true_false',
					'instructions'  => 'Whether or not to display Trending Article on the page after content.',
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

// Create an instance of the TrendingArticle class to initialize the shortcode functionality.
new TrendingArticle();
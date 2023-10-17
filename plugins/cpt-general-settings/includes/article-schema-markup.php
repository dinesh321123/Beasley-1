<?php

if (!defined('ABSPATH')) {
    die("Please don't try to access this file directly.");
}

/**
 * Class articleSchemaMarkup
 */
class articleSchemaMarkup
{
    /**
     * articleSchemaMarkup constructor.
     * Adds the action to insert article schema markup in the post header.
     */
    function __construct()
    {
        add_action('wp_head', array($this, 'add_article_schema_markup'));
    }

    /**
     * Adds JSON-LD structured data markup for the current article.
     */
    public function add_article_schema_markup()
    {
        $allowed_post_types = $this->restrict_schema_markup_posttype_list();

        if (is_single() && in_array(get_post_type(), $allowed_post_types)) {
            // Apply structured data only on single post pages
            $post_id = get_the_ID();
            $post_title = get_the_title();
            $post_url = get_permalink();
            $post_date = get_the_date('c');
            $post_date_modified = get_the_modified_date('c');
            $post_author = get_the_author();
            $post_image = get_the_post_thumbnail_url($post_id, 'full');
            $post_description = get_the_excerpt(); // You can use get_the_excerpt() to get the post excerpt or get_the_content() for full content.

            // Check if any of the essential fields are empty
            if (empty($post_title) || empty($post_url) || empty($post_date) || empty($post_date_modified) || empty($post_author) || empty($post_image) || empty($post_description)) {
                return; // Exit if any essential data is empty
            }

            $markup = array(
                '@context' => 'http://schema.org',
                '@type' => 'Article',
                'mainEntityOfPage' => array(
                    '@type' => 'WebPage',
                    '@id' => esc_url($post_url)
                ),
                'headline' => esc_html($post_title),
                'image' => array(
                    esc_url($post_image)
                ),
                'datePublished' => esc_html($post_date),
                'dateModified' => esc_html($post_date_modified),
                'author' => array(
                    '@type' => 'Person',
                    'name' => esc_html($post_author)
                ),
                'publisher' => array(
                    '@type' => 'Organization',
                    'name' => get_bloginfo('name')
                ),
                'description' => esc_html($post_description)
            );

            // Encode the structured data as JSON-LD
            $markup_json = json_encode($markup);

            // Output the JSON-LD script in the post header
            echo '<script type="application/ld+json">' . $markup_json . '</script>';
        }
    }

    public function restrict_schema_markup_posttype_list() {
		return (array) apply_filters( 'restrict-schema-markup-for-posttypes', array( 'post', 'affiliate_marketing', 'gmr_gallery', 'contest', 'tribe_events', 'listicle_cpt' ) );
	}
}

// Instantiate the articleSchemaMarkup class
new articleSchemaMarkup();
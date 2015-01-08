<?php
/**
 * Greater Media functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package Greater Media
 * @since   0.1.3
 */

// Useful global constants
/**
 *
 */
define( 'GREATERMEDIA_VERSION', '0.1.3' );

add_theme_support( 'homepage-curation' );

require_once( __DIR__ . '/includes/liveplayer/loader.php' );
require_once( __DIR__ . '/includes/layout-chooser/class-choose-layout.php' );
require_once( __DIR__ . '/includes/site-options/loader.php' );
require_once( __DIR__ . '/includes/mega-menu/mega-menu-admin.php' );
require_once( __DIR__ . '/includes/mega-menu/mega-menu-walker.php' );
require_once( __DIR__ . '/includes/mega-menu/mega-menu-mobile-walker.php' );

/**
 * Required files
 */
require_once( __DIR__ . '/includes/gm-tinymce/loader.php');

/**
 * Set up theme defaults and register supported WordPress features.
 *
 * @uses  load_theme_textdomain() For translation/localization support.
 *
 * @since 0.1.0
 */
function greatermedia_setup() {
	/**
	 * Makes Greater Media available for translation.
	 *
	 * Translations can be added to the /lang directory.
	 * If you're building a theme based on Greater Media, use a find and replace
	 * to change 'greatermedia' to the name of your theme in all template files.
	 */
	load_theme_textdomain( 'greatermedia', get_template_directory() . '/languages' );

	// Add theme support for post thumbnails
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'gm-article-thumbnail',     		1580,   9999,   false   ); // thumbnails used for articles
	add_image_size( 'gm-entry-thumbnail-1-1' ,          500,    500,    true    );
	add_image_size( 'gm-entry-thumbnail-4-3' ,          500,    375,    true    );
	add_image_size( 'gmr-gallery',              		800,    534,    true    ); // large images for the gallery
	add_image_size( 'gmr-gallery-thumbnail',    		100,    100             ); // thumbnails for the gallery
	add_image_size( 'gmr-featured-primary',     		1600,   572,    true    ); // image for primary featured post on front page
	add_image_size( 'gmr-featured-secondary',   		336,    224,    true    ); // thumbnails for secondary featured posts on front page
	add_image_size( 'gmr-show-featured-primary',   		708,    389,    true    ); // thumbnails for secondary featured posts on front page
	add_image_size( 'gmr-show-featured-secondary',   	322,    141,    true    ); // thumbnails for secondary featured posts on front page

	// Update this as appropriate content types are created and we want this functionality
	add_post_type_support( 'post', 'timed-content' );
	add_post_type_support( 'post', 'login-restricted-content' );
	add_post_type_support( 'post', 'age-restricted-content' );

	// Add theme support for post-formats
	$formats = array( 'gallery', 'link', 'image', 'video', 'audio' );
	add_theme_support( 'post-formats', $formats );

}

add_action( 'after_setup_theme', 'greatermedia_setup' );

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since 0.1.0
 */
function greatermedia_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
	$baseurl = untrailingslashit( get_template_directory_uri() );

	wp_register_style(
		'open-sans',
		'http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,800italic,400,300,700,800',
		array(),
		null
	);
	wp_register_style(
		'droid-sans',
		'http://fonts.googleapis.com/css?family=Droid+Sans:400,700',
		array(),
		null
	);
	wp_register_style(
		'font-awesome',
		'//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css',
		array(),
		null
	);
	wp_register_style(
		'greatermedia',
		"{$baseurl}/assets/css/greater_media{$postfix}.css",
		array(
			'dashicons',
			'open-sans',
			'droid-sans',
			'font-awesome'
		),
		GREATERMEDIA_VERSION
	);
	wp_enqueue_script(
		'greatermedia',
		"{$baseurl}/assets/js/greater_media{$postfix}.js",
		array(
			'underscore',
			'classlist-polyfill'
		),
		GREATERMEDIA_VERSION,
		true
	);
	wp_enqueue_script(
		'respond.js',
		"{$baseurl}/assets/js/vendor/respond.min.js",
		array(),
		'1.4.2',
		false
	);
	wp_enqueue_script(
		'html5shiv',
		"{$baseurl}/assets/js/vendor/html5shiv-printshiv.js",
		array(),
		'3.7.2',
		false
	);
	wp_enqueue_script(
		'greatermedia-load-more',
		"{$baseurl}/assets/js/greater_media_load_more{$postfix}.js",
		array( 'jquery' ),
		GREATERMEDIA_VERSION,
		true
	);
	wp_enqueue_style(
		'greatermedia'
	);

}

add_action( 'wp_enqueue_scripts', 'greatermedia_scripts_styles');

/**
 * Add humans.txt to the <head> element.
 */
function greatermedia_header_meta() {
	$humans = '<link type="text/plain" rel="author" href="' . get_template_directory_uri() . '/humans.txt" />';

	echo apply_filters( 'greatermedia_humans', $humans );
}

add_action( 'wp_head', 'greatermedia_header_meta' );

/**
 * Register Navigation Menus
 */
function greatermedia_nav_menus() {
	$locations = array(
		'main-nav' => __( 'Main Navigation', 'greatermedia' ),
		'secondary-nav' => __( 'Seconadary Navigation', 'greatermedia' ),
		'footer-nav' => __( 'Footer Navigation', 'greatermedia' )
	);
	register_nav_menus( $locations );
}

add_action( 'init', 'greatermedia_nav_menus' );

/**
 * Add Post Formats
 */
function greatermedia_post_formats() {

	global $post;
	$post_id = $post->ID;

	if ( has_post_format( 'gallery', $post_id ) ) {
		$format = 'entry__thumbnail--gallery';
	} elseif ( has_post_format( 'link', $post_id ) ) {
		$format = 'entry__thumbnail--link';
	} elseif ( has_post_format( 'image', $post_id ) ) {
		$format = 'entry__thumbnail--image';
	} elseif ( has_post_format( 'video', $post_id ) ) {
		$format = 'entry__thumbnail--video';
	} elseif ( has_post_format( 'audio', $post_id ) ) {
		$format = 'entry__thumbnail--audio';
	} else {
		$format = 'entry__thumbnail--standard';
	}

	echo $format;

}

/**
 * Add Widget Areas
 */
function greatermedia_widgets_init() {

	register_sidebar( array(
		'name'          => 'Live Player Sidebar',
		'id'            => 'liveplayer_sidebar',
		'before_widget' => '<div id="%1$s" class="widget--live-player %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget--live-player__title">',
		'after_title'   => '</h3>',
	) );

}

add_action( 'widgets_init', 'greatermedia_widgets_init' );

/**
 * Helper function to get the post id from options or transient cache
 *
 * @param $query_arg
 *
 * @return int post id if found
 */
function get_post_with_keyword( $query_arg ) {
	$query_arg = strtolower( $query_arg );
	if( class_exists('GreaterMedia_Keyword_Admin') ) {
		$saved_keyword = GreaterMedia_Keyword_Admin::get_keyword_options( GreaterMedia_Keyword_Admin::$plugin_slug . '_option_name' );
		$saved_keyword = GreaterMedia_Keyword_Admin::array_map_r( 'sanitize_text_field', $saved_keyword );

		if( $query_arg != '' && array_key_exists( $query_arg, $saved_keyword ) ) {
			return $saved_keyword[$query_arg]['post_id'];
		}
	}
	return 0;
}

/**
 * Get the URL of a post's thumbnail.  
 * 
 * @param string|array Thumbnail size.
 * @param int Post ID. Defaults to current post. 
 */
function gm_get_post_thumbnail_url( $size = 'thumbnail', $post_id = null ) {
	$thumbnail_id = get_post_thumbnail_id( $post_id );

	if ( $thumbnail_id ) {
		return gm_get_thumbnail_url( $thumbnail_id, $size );
	}
}

/**
 * Output the escaped URL of a post's thumbnail.  
 * 
 * @param string|array Thumbnail size.
 * @param int Post ID. Defaults to current post. 
 */
function gm_post_thumbnail_url( $size = 'thumbnail', $post_id = null ) {
	echo esc_url( gm_get_post_thumbnail_url( $size, $post_id ) );
}

/**
 * Get the URL of an attachment thumbnail. 
 * 
 * @param id $attachment_id
 * @return null|string URL if found, null otherwise. 
 */
function gm_get_thumbnail_url( $attachment_id, $size ) {
	$src = wp_get_attachment_image_src( $attachment_id, $size );
	if ( $src ) {
		return $src[0]; 
	}	
}

/**
 * Custom action to add keyword search results
 */
function get_results_for_keyword() {
	if( is_search() ) {
		$search_query_arg = sanitize_text_field( get_search_query() );
		$custom_post_id = intval( get_post_with_keyword( $search_query_arg ) );

		if( $custom_post_id != 0 ) {
			global $post;
			$post = get_post( $custom_post_id );
			setup_postdata( $post );
			$title = get_the_title();
			$keys= explode(" ",$search_query_arg);
			$title = preg_replace('/('.implode('|', $keys) .')/iu', '<span class="search__result--term">\0</span>', $title);
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'search__result' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

				<time datetime="<?php the_time( 'c' ); ?>" class="search__result--date"><?php the_time( 'M j, Y' ); ?></time>

				<h3 class="search__result--title"><a href="<?php the_permalink(); ?>"><?php echo $title ?></a></h3>

			</article>
			<?php
			wp_reset_postdata();
			wp_reset_query();
		}
	}
}

add_action( 'keyword_search_result', 'get_results_for_keyword' );

/**
 * Alter search results on search page
 * 
 * @param  WP_Query $query [description]
 */
function greatermedia_alter_search_query( $query ) {
	if( $query->is_search && $query->is_main_query() ) {
		$search_query_arg = sanitize_text_field( $query->query_vars['s'] );
		$custom_post_id = intval( get_post_with_keyword( $search_query_arg ) );
		if( $custom_post_id != 0 ) {
			$query->set( 'post__not_in', array( $custom_post_id ) );
		}
	}
}
add_action( 'pre_get_posts', 'greatermedia_alter_search_query' );

/**
 * Alters the main query on the front page to include additional post types
 *
 * @param WP_Query $query
 */
function greatermedia_alter_front_page_query( $query ) {
	if ( $query->is_main_query() && $query->is_front_page() ) {
		// Need to really think about how to include events here, and if it really makes sense. By default,
		// we would have all published events, in reverse cron - so like we'd have "posts" looking things dated for the future
		// that would end up hiding the actual posts, potentially for pages before getting to any real content.
		//
		// ADDITIONALLY - There is a checkbox for this on the events setting page, so we don't need to do that here :)
		$post_types = array( 'post' );
		if ( class_exists( 'GMP_CPT' ) ) {
			$post_types[] = GMP_CPT::EPISODE_POST_TYPE;
		}

		$query->set( 'post_type', $post_types );
	}
}
add_action( 'pre_get_posts', 'greatermedia_alter_front_page_query' );

/**
 * This will keep Jetpack Sharing from auto adding to the end of a post.
 * We want to add this manually to the proper theme locations
 *
 * Hooked into loop_end
 */
function greatermedia_remove_jetpack_share() {
	remove_filter( 'the_content', 'sharing_display', 19 );
	remove_filter( 'the_excerpt', 'sharing_display', 19 );
}

add_action( 'wp_head', 'greatermedia_remove_jetpack_share' );

/**
 * Removes the `[...]` from the excerpt.
 *
 * @param $more
 *
 * @return string
 */
function greatermedia_excerpt_more( $more ) {
	return '';
}
add_filter( 'excerpt_more', 'greatermedia_excerpt_more' );

if ( ! function_exists( 'greatermedia_load_more_template' ) ) :
	/**
	 * Processes load more requrests.
	 */
	function greatermedia_load_more_template() {
		// do nothing if it is not an ajax request or it is not paged
		if ( ! filter_input( INPUT_GET, 'ajax', FILTER_VALIDATE_BOOLEAN ) || ! is_paged() ) {
			return;
		}
		
		$partial_slug = sanitize_text_field( $_REQUEST['partial_slug'] );
		if ( ! $partial_slug ) {
			$partial_slug = 'partials/loop'; 
		}

		$partial_name = sanitize_text_field( $_REQUEST['partial_name'] );
		
		get_template_part( $partial_slug, $partial_name ); 
		exit;
	}
	
endif;
add_action( 'template_redirect', 'greatermedia_load_more_template' );

function greatermedia_set_ajax_loop_partial( $partial_slug, $partial_name = '' ) {
	global $greatermedia_load_more_partial; 
	
	$greatermedia_load_more_partial = array( 
		'slug' => $partial_slug, 
		'name' => $partial_name,
	); 
}

add_action( 'wp_enqueue_scripts', function () {
	global $greatermedia_load_more_partial; 
	
	if ( empty( $greatermedia_load_more_partial ) ) {
		$greatermedia_load_more_partial = array( 'slug' => '', 'name' => '' ); 
	}
	
	wp_localize_script( 'greatermedia-load-more', 'greatermedia_load_more_partial', $greatermedia_load_more_partial );
}, 100 );


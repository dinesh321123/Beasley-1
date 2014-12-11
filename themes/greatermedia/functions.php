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
 * @since   0.1.0
 */

// Useful global constants
/**
 *
 */
define( 'GREATERMEDIA_VERSION', '0.1.0' );

require_once( __DIR__ . '/includes/liveplayer/loader.php' );
require_once( __DIR__ . '/includes/layout-chooser/class-choose-layout.php' );
require_once( __DIR__ . '/includes/site-options/loader.php');

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

	/**
	 * Add theme support for post thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'gm-article-thumbnail', 1580, 9999, false ); // thumbnails used for articles

	// Update this as appropriate content types are created and we want this functionality
	add_post_type_support( 'post', 'timed-content' );
	add_post_type_support( 'post', 'login-restricted-content' );

	/**
	 * Add theme support for post-formats
	 */
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

	wp_register_style(
		'open-sans',
		'http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,400,300,700',
		array(),
		GREATERMEDIA_VERSION
	);
	wp_register_style(
		'droid-sans',
		'http://fonts.googleapis.com/css?family=Droid+Sans:400,700',
		array(),
		GREATERMEDIA_VERSION
	);
	wp_register_style(
		'font-awesome',
		'//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css',
		array(),
		'4.2'
	);
	wp_enqueue_script(
		'greatermedia',
		get_template_directory_uri() . "/assets/js/greater_media{$postfix}.js",
		array(
			'underscore'
		),
		GREATERMEDIA_VERSION,
		true
	);
	wp_enqueue_script(
		'respond.js',
		get_template_directory_uri() . '/assets/js/vendor/respond.min.js',
		array(),
		'1.4.2',
		false
	);
	wp_enqueue_script(
		'html5shiv',
		get_template_directory_uri() . '/assets/js/vendor/html5shiv-printshiv.js',
		array(),
		'3.7.2',
		false
	);
	wp_enqueue_style(
		'greatermedia',
		get_template_directory_uri() . "/assets/css/greater_media{$postfix}.css",
		array(
			'dashicons',
			'open-sans',
			'droid-sans',
			'font-awesome'
		),
		GREATERMEDIA_VERSION
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
 * @param  [type] $query [description]
 * @return [type]        [description]
 */
function alter_search_query( $query ) {
	if( $query->is_search && $query->is_main_query() ) {
		$search_query_arg = sanitize_text_field( $query->query_vars['s'] );
		$custom_post_id = intval( get_post_with_keyword( $search_query_arg ) );
		if( $custom_post_id != 0 ) {
			$query->set( 'post__not_in', array( $custom_post_id ) );
		}
	}
}
add_action( 'pre_get_posts', 'alter_search_query' );


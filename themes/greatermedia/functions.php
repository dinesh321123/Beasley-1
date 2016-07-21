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
/*
 * Add this constant to wp-config and set value to "dev" to trigger time() as the cache buster on css/js that use this,
 * instead of the version - useful for dev, especially when cloudflare or other cdn's are involved
 */
if ( defined( 'GMR_PARENT_ENV' ) && 'dev' == GMR_PARENT_ENV ) {
	define( 'GREATERMEDIA_VERSION', time() );
} else {
	define( 'GREATERMEDIA_VERSION', '1.3.4' ); /* Version bump by Steve 6/24/2016 @ 11:00 a.m. EST */
}

add_theme_support( 'homepage-curation' );
add_theme_support( 'homepage-countdown-clock' );

require_once( __DIR__ . '/includes/liveplayer/class-liveplayer.php' );
require_once( __DIR__ . '/includes/site-options/loader.php' );
require_once( __DIR__ . '/includes/mega-menu/mega-menu-admin.php' );
require_once( __DIR__ . '/includes/mega-menu/mega-menu-walker.php' );
require_once( __DIR__ . '/includes/mega-menu/mega-menu-mobile-walker.php' );
require_once( __DIR__ . '/includes/image-attributes/loader.php');
require_once( __DIR__ . '/includes/posts-screen-thumbnails/loader.php' );
require_once( __DIR__ . '/includes/category-options.php' );
require_once( __DIR__ . '/includes/class-favicon.php' );
require_once( __DIR__ . '/includes/iframe-embed.php' );
require_once( __DIR__ . '/includes/flexible-feature-images/gmr-flexible-feature-images.php' );
require_once( __DIR__ . '/includes/auction-nudge/gmr-auction-nudge.php' );

/**
 * Required files
 */
require_once( __DIR__ . '/includes/gm-tinymce/loader.php');

/**
 * WP-CLI commands.
 */
if ( defined( 'WP_CLI' ) && WP_CLI && file_exists( __DIR__ . '/includes/gmr-db-cli.php' ) ) {
	include __DIR__ . '/includes/gmr-db-cli.php';
}

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
	add_image_size( 'gm-article-thumbnail',     		1400,   9999,   false   ); // thumbnails used for articles
	add_image_size( 'gm-entry-thumbnail-1-1' ,          500,    500,    true    );
	add_image_size( 'gm-entry-thumbnail-4-3' ,          500,    375,    true    );
	add_image_size( 'gmr-gallery',              		800,    534,    true    ); // large images for the gallery
	add_image_size( 'gmr-gallery-thumbnail',    		100,    100             ); // thumbnails for the gallery
	add_image_size( 'gmr-featured-primary',     		1600,   572,    true    ); // image for primary featured post on front page
	add_image_size( 'gmr-featured-secondary',   		336,    224,    true    ); // thumbnails for secondary featured posts on front page
	add_image_size( 'gmr-show-featured-primary',   		708,    389,    true    ); // thumbnails for secondary featured posts on front page
	add_image_size( 'gmr-show-featured-secondary',   	322,    141,    true    ); // thumbnails for secondary featured posts on front page
	add_image_size( 'gm-related-post',   				300,    200,    true    );
	add_image_size( 'gmr-advertiser',                   400,    9999,   false   );

	/* Images for the Gallery Grid ---- DO NOT DELETE ---- */
	add_image_size( 'gmr-gallery-grid-featured',        1200,   800,    true    );
	add_image_size( 'gmr-gallery-grid-secondary',       560,    300,    true    );
	add_image_size( 'gmr-gallery-grid-thumb',           500,    368,    true    ); // thumbnail for gallery grid areas
	add_image_size( 'gmr-album-thumbnail',              1876,   576,    true    ); // thumbnail for albums

	// Update this as appropriate content types are created and we want this functionality
	add_post_type_support( 'post', 'timed-content' );
	add_post_type_support( 'post', 'login-restricted-content' );
	add_post_type_support( 'post', 'age-restricted-content' );
	add_post_type_support( 'post', 'flexible-feature-image' );

	// Pages should also support same restrictions as posts
	add_post_type_support( 'page', 'timed-content' );
	add_post_type_support( 'page', 'login-restricted-content' );
	add_post_type_support( 'page', 'age-restricted-content' );
	add_post_type_support( 'page', 'flexible-feature-image' );

	// Restrictions for galleries
	add_post_type_support( 'gmr_gallery', 'timed-content' );
	add_post_type_support( 'gmr_gallery', 'login-restricted-content' );
	add_post_type_support( 'gmr_gallery', 'age-restricted-content' );

	// Restrictions for albums
	add_post_type_support( 'gmr_album', 'timed-content' );
	add_post_type_support( 'gmr_album', 'login-restricted-content' );
	add_post_type_support( 'gmr_album', 'age-restricted-content' );

	// Restrictions for podcasts episodes
	add_post_type_support( 'episode', 'timed-content' );
	add_post_type_support( 'episode', 'login-restricted-content' );
	add_post_type_support( 'episode', 'age-restricted-content' );

	// Restrictions for events
	add_post_type_support( 'tribe_events', 'timed-content' );
	add_post_type_support( 'tribe_events', 'login-restricted-content' );
	add_post_type_support( 'tribe_events', 'age-restricted-content' );
	add_post_type_support( 'tribe_events', 'flexible-feature-image' );

	// Restrictions for contests
	add_post_type_support( 'contest', 'timed-content' );
	add_post_type_support( 'contest', 'login-restricted-content' );
	add_post_type_support( 'contest', 'age-restricted-content' );
	add_post_type_support( 'contest', 'flexible-feature-image' );

	// Restrictions for surveys
	add_post_type_support( 'survey', 'flexible-feature-image' );

	// Add theme support for post-formats
	$formats = array( 'gallery', 'link', 'image', 'video', 'audio' );
	add_theme_support( 'post-formats', $formats );

	// Embed providers
	wp_embed_register_handler( 'pinterest', '~https?\:\/\/\w+\.pinterest\.com\/pin\/(\d+)\/?~i', 'greatermedia_pinterest_handler' );
	wp_embed_register_handler( 'facebook', '~https?\:\/\/\w+\.facebook\.com\/\w+\/posts\/(\d+)\/?~i', 'greatermedia_facebook_handler' );
}

add_action( 'after_setup_theme', 'greatermedia_setup' );

/**
 * Hooks in when script tags are being output to <head>
 * This is the only way to use IE conditionals to load scripts.
 * Also useful for inline script snippets that don't deserve the http request.
 *
 * Note: IE conditional CSS can use the enqueue system and snippets should use the wp_print_styles hook
 */
function greatermedia_print_scripts() {
?>
	<!--[if lte IE 8]>
	<script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendor/respond.min.js?ver=1.4.2"></script>
	<![endif]-->
	<?php
}

add_action( 'wp_print_scripts', 'greatermedia_print_scripts' );

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since 0.1.0
 */
function greatermedia_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
	$baseurl = untrailingslashit( get_template_directory_uri() );

	wp_register_style(
		'google-fonts',
		'//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,800italic,400,300,700,800',
		array(),
		null
	);
	wp_register_style(
		'greatermedia',
		"{$baseurl}/assets/css/greater_media{$postfix}.css",
		array(
			'google-fonts'
		),
		GREATERMEDIA_VERSION
	);
	wp_enqueue_script(
		'greatermedia',
		"{$baseurl}/assets/js/greater_media{$postfix}.js",
		array(
			'jquery',
			'underscore',
			'classlist-polyfill'
		),
		GREATERMEDIA_VERSION,
		true
	);
	wp_enqueue_script(
		'greatermedia-load-more',
		"{$baseurl}/assets/js/greater_media_load_more{$postfix}.js",
		array(
			'jquery',
			'jquery-waypoints'
		),
		GREATERMEDIA_VERSION,
		true
	);
	wp_enqueue_style(
		'greatermedia'
	);

	/**
	 * YARPP styles are not being used, so let's get rid of them!
	 */
	wp_dequeue_style(
		'yarppWidgetCss'
	);

	/**
	 * this is a fix to resolve conflicts with styles and javascript for The Events Calendar plugin that will not
	 * load once pjax has been activated. We are checking to see if the `Tribe_Template_Factory` class exists and if
	 * the function `asset_package` exists within `Tribe_Template_Factory`. If the class and function exists, we then
	 * call the javascript and css necessary on the front end.
	 *
	 * @see `wp_content/plugins/the-events-calendar/lib/the-events-calendar.class.php` lines 2235 - 2244
	 */
	if ( class_exists( 'Tribe_Template_Factory' ) && method_exists( 'Tribe_Template_Factory', 'asset_package' ) ) {
		// jquery-resize
		Tribe_Template_Factory::asset_package( 'jquery-resize' );

		// smoothness
		Tribe_Template_Factory::asset_package( 'smoothness' );

		// Tribe Calendar JS
		Tribe_Template_Factory::asset_package( 'calendar-script' );

		Tribe_Template_Factory::asset_package( 'events-css' );
	}
}

add_action( 'wp_enqueue_scripts', 'greatermedia_scripts_styles');

/**
 * Add custom admin stylesheet.
 */
function greatermedia_admin_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
	$baseurl = untrailingslashit( get_template_directory_uri() );

	wp_register_style(
		'gmr-admin-styles',
		"{$baseurl}/assets/css/gm_admin{$postfix}.css",
		array(),
		GREATERMEDIA_VERSION
	);

	wp_enqueue_style( 'gmr-admin-styles' );
}

add_action( 'admin_enqueue_scripts', 'greatermedia_admin_styles' );

/**
 * Unload YARPP stylesheets.
 */
add_action( 'get_footer', function () {
 	wp_dequeue_style( 'yarppRelatedCss' );
 	wp_dequeue_style( 'yarpp-thumbnails-yarpp-thumbnail' );
} );

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
		'secondary-nav' => __( 'Secondary Navigation', 'greatermedia' ),
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
 * @param string|array $size Thumbnail size.
 * @param int $post_id Post ID. Defaults to current post.
 * @param bool $use_fallback Determines whether to use fallback image if thumbnmail doesn't exist.
 * @return string Thumbnail URL on success, otherwise NULL.
 */
function gm_get_post_thumbnail_url( $size = 'thumbnail', $post_id = null, $use_fallback = false ) {
	$thumbnail_id = get_post_thumbnail_id( $post_id );
	if ( $thumbnail_id && ( $url = gm_get_thumbnail_url( $thumbnail_id, $size ) ) ) {
		return $url;
	}

	if ( $use_fallback ) {
		$thumbnail_id = greatermedia_get_fallback_thumbnail_id( $post_id );
		if ( $thumbnail_id ) {
			return gm_get_thumbnail_url( $thumbnail_id, $size );
		}
	}

	return null;
}

/**
 * Output the escaped URL of a post's thumbnail.
 *
 * @param string|array $size Thumbnail size.
 * @param int $post_id Post ID. Defaults to current post.
 * @param bool $use_fallback Determines whether to use fallback image if thumbnmail doesn't exist.
 */
function gm_post_thumbnail_url( $size = 'thumbnail', $post_id = null, $use_fallback = false ) {
	echo esc_url( gm_get_post_thumbnail_url( $size, $post_id, $use_fallback ) );
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

	return null;
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
	if( ! is_admin() && $query->is_search() && $query->is_main_query() ) {
		$search_query_arg = sanitize_text_field( $query->query_vars['s'] );
		$custom_post_id = intval( get_post_with_keyword( $search_query_arg ) );
		if( $custom_post_id != 0 ) {
			$query->set( 'post__not_in', array( $custom_post_id ) );
		}
	}
}
add_action( 'pre_get_posts', 'greatermedia_alter_search_query' );

/**
 * Alter search score to favor more recent posts first
 *
 * @param $request_args
 */
function ep_search_request_args( $request_args, $args = '', $scope = '' ) {
	$gauss = new \stdClass();
	$gauss->post_date = array( 'scale' => '52w', 'offset' => '12w', 'decay' => 0.3 );

	$function_score = array(
		'function_score' => array(
			'functions' => array(
				array( 'gauss' => $gauss )
			),
			'score_mode' => 'multiply',
			'query' => $request_args['query']
		)
	);
	$request_args['query'] = $function_score;

	return $request_args;
}
add_filter( 'ep_formatted_args', 'ep_search_request_args' );

/**
 * Alter query to show custom post types in category pages.
 *
 * @param  WP_Query $query [description]
 */
function greatermedia_alter_taxonomy_archive_query( $query ) {
	if ( greatermedia_is_taxonomy_archive( $query ) ) {
		$query->set( 'post_type', get_post_types() );
	}
}

function greatermedia_is_taxonomy_archive( $query ) {
	if ( $query->is_main_query() && ! is_admin() ) {
		return $query->is_category() || $query->is_tag();
	} else {
		return false;
	}
}

add_action( 'pre_get_posts', 'greatermedia_alter_taxonomy_archive_query' );

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
	return '&hellip;';
}
add_filter( 'excerpt_more', 'greatermedia_excerpt_more' );

if ( ! function_exists( 'greatermedia_load_more_template' ) ) :
	/**
	 * Processes load more requrests.
	 */
	function greatermedia_load_more_template() {
		// Do nothing if it is not an ajax request. We no longer need to check
		// if it's paged because it functions the same regardless.
		if ( ! filter_input( INPUT_GET, 'ajax', FILTER_VALIDATE_BOOLEAN ) ) {
			return;
		}

		$partial_slug = isset( $_REQUEST['partial_slug'] ) ? sanitize_text_field( $_REQUEST['partial_slug'] ) : '';
		$partial_name = isset( $_REQUEST['partial_name'] ) ? sanitize_text_field( $_REQUEST['partial_name'] ) : '';

		if ( ! $partial_slug ) {
			$partial_slug = 'partials/loop';
		}

		global $wp_query, $gmr_loadmore_num_pages, $gmr_loadmore_post_count, $gmr_loadmore_paged;

		ob_start();

		get_template_part( $partial_slug, $partial_name );

		$html = ob_get_clean();

		wp_send_json( array(
			'paged'         => $gmr_loadmore_paged ?: $wp_query->query_vars['paged'],
			'max_num_pages' => $gmr_loadmore_num_pages ?: $wp_query->max_num_pages,
			'post_count'    => $gmr_loadmore_post_count ?: $wp_query->post_count,
			'html'          => apply_filters( 'dynamic_cdn_content', $html ), // Apply dynamic cdn filter so images aren't broken.
		) );

		exit;
	}

endif;
add_action( 'template_redirect', 'greatermedia_load_more_template' );

function greatermedia_load_more_button( $args = array() ) {

	global $wp_query;

	// $partial_slug = null, $partial_name = null, $query_or_page_link_template = null, $next_page = null
	$args = wp_parse_args( $args, array(
		'partial_slug' => '',
		'partial_name' => '',
		'query' => null,
		'page_link_template' => null,
		'next_page' => null,
		'auto_load' => false,
	) );

	if ( ! $args['query'] && ! $args['page_link_template'] ) {
		$args['query'] = $wp_query;
	}

	if ( $args['query'] && $args['query'] instanceof WP_Query ) {
		$temp_wp_query = $wp_query;

		$wp_query = $args['query'];
		$args['page_link_template'] = str_replace( PHP_INT_MAX, '%d', get_pagenum_link( PHP_INT_MAX ) );

		if ( ! $args['next_page'] ) {
			$args['next_page'] = max( 2, $wp_query->query_vars['paged'] + 1);
		}

		$wp_query = $temp_wp_query;
	}

	// Bail if we're basing this off a query and we can see there are no more
	// posts to load.
	if ( $args['query'] && $args['next_page'] > $args['query']->max_num_pages ) {
		return;
	}

	if ( empty( $args['next_page'] ) || !is_numeric( $args['next_page'] ) ) {
		$args['next_page'] = 2;
	}

	$default_page_link = sprintf( $args['page_link_template'], $args['next_page'] );
	?>
	<div class="posts-pagination">
		<a
			class="button posts-pagination--load-more is-loaded"
			href="<?php echo esc_url( $default_page_link ); ?>"
			data-page-link-template="<?php echo esc_url( $args['page_link_template'] ); ?>"
			data-page="<?php echo esc_attr( $args['next_page'] ); ?>"
			data-partial-slug='<?php echo esc_attr( $args['partial_slug'] ); ?>'
			data-partial-name='<?php echo esc_attr( $args['partial_name'] ); ?>'
			data-auto-load='<?php echo intval( $args['auto_load'] ); ?>'
			>
			<i class="gmr-icon icon-spin icon-loading"></i> Load More
		</a>
	</div>
<?php
}

add_action( 'current_screen', 'hide_seo_columns' );
function hide_seo_columns() {

    $currentScreen = get_current_screen();
    $current_user = wp_get_current_user();

    $hidden = array( 'wpseo-score',  'wpseo-title', 'wpseo-metadesc', 'wpseo-focuskw' );
    $first = get_user_meta( $current_user->ID, "screen-defaults-{$currentScreen->id}", true );

    if( !$first ) {
    	update_user_meta( $current_user->ID, 'manage' . $currentScreen->id . 'columnshidden', $hidden );
    	update_user_meta( $current_user->ID, "screen-defaults-{$currentScreen->id}", true );
    }
}

/**
 * function to globally remove post type support for custom fields for all post types
 */
function greatermedia_remove_custom_fields() {

	$post_types = array(
		'post',
		'page',
		'tribe_events'
	);

	/**
	 * go through each post type, check if the post type supports custom fields, if the post types does support
	 * custom fields, remove support
	 */
	foreach ( $post_types as $post_type ) {
		if (post_type_supports( $post_type, 'custom-fields' ) ) {
			remove_post_type_support( $post_type, 'custom-fields' );
		}
	}

}
add_action( 'init' , 'greatermedia_remove_custom_fields', 10 );

/**
 * Returns fallback image id for a post.
 *
 * @param int|WP_Post|null $post_id The post id or object to return fallback for.
 * @return int The fallback image id.
 */
function greatermedia_get_fallback_thumbnail_id( $post_id = null ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return null;
	}

	return intval( get_option( $post->post_type . '_fallback' ) );
}

/**
 * Deactivates Tribe Events filter at dashboard drafts widget.
 */
function greatermedia_deactivate_tribe_warning_on_dashboard( $option_value ) {
	remove_filter( 'get_post_time', array( 'TribeEventsTemplates', 'event_date_to_pubDate' ), 10, 3 );
	return $option_value;
}
add_filter( 'get_user_option_dashboard_quick_press_last_post_id', 'greatermedia_deactivate_tribe_warning_on_dashboard' );

function add_google_analytics() {
	global $post;
	$google_analytics = get_option( 'gmr_google_analytics', '' );
	$google_uid_dimension = absint( get_option( 'gmr_google_uid_dimension', '' ) );

	if ( empty( $google_analytics ) ) {
		return;
	}
	if ( is_singular() ) {
		$args     = array( 'orderby' => 'name', 'order' => 'ASC', 'fields' => 'slugs' );
		$shows    = implode( ', ', wp_get_post_terms( $post->ID, '_shows', $args ) );
		$category = implode( ', ', wp_get_post_terms($post->ID, 'category', $args ) );
	}
	?>
	<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', '<?php echo esc_js( $google_analytics ); ?>', 'auto');

	var googleUidDimension = '<?php echo esc_js( $google_uid_dimension ); ?>';

	if( window.is_gigya_user_logged_in && is_gigya_user_logged_in() ) {
		ga( 'set', '&uid', get_gigya_user_id() );

		if ( googleUidDimension !== '0' ) {
			googleUidDimension = 'dimension' + googleUidDimension;
			ga( 'set', googleUidDimension, get_gigya_user_id() );
		}

	}

	jQuery(document).on('pjax:end', function() {
		ga('set', 'location', window.location.href);
		ga('send', 'pageview');
	});
	ga('require', 'displayfeatures');
	<?php if ( is_singular() ) : ?>
		<?php if ( ! empty( $shows ) ) : ?>
			ga( 'set', 'contentGroup1', <?php echo json_encode( $shows ); ?> );
		<?php endif; ?>
		<?php if ( ! empty( $category ) ): ?>
			ga( 'set', 'contentGroup2', <?php echo json_encode( $category ); ?> );
		<?php endif; ?>
	<?php endif ?>

	ga('send', 'pageview');

	jQuery(document).ready(function() {
		var $body = jQuery('body');

		$body.on('inlineAudioPlaying.gmr', function() {
			ga('send', 'event', 'audio', 'Inline audio playing');
		});

		$body.on('liveStreamPlaying.gmr', function () {
			ga('send', 'event', 'audio', 'Live stream playing');
		});
	});

	</script>
	<?php
}
add_action( 'wp_head' , 'add_google_analytics' );

/**
 * Adds Embedly global script to each page to ensure no broken embeds.
 */

function add_embedly_global_script() {
	if ( class_exists( 'WP_Embedly' ) ) {
		?>

		<script>
		  (function(w, d){
		   var id='embedly-platform', n = 'script';
		   if (!d.getElementById(id)){
		     w.embedly = w.embedly || function() {(w.embedly.q = w.embedly.q || []).push(arguments);};
		     var e = d.createElement(n); e.id = id; e.async=1;
		     e.src = ('https:' === document.location.protocol ? 'https' : 'http') + '://cdn.embedly.com/widgets/platform.js';
		     var s = d.getElementsByTagName(n)[0];
		     s.parentNode.insertBefore(e, s);
		   }
		  })(window, document);
		</script>

		<?php
	}
}
add_action( 'wp_head' , 'add_embedly_global_script' );

/**
 * adds an additional body class if a user is authenticated with gigya
 *
 * @param $classes
 *
 * @return array
 */
function greatermedia_add_gigya_body_class( $classes ) {

	$classes[] = '';

	if ( is_gigya_user_logged_in() ) {
		$classes[] = 'gmr-user';
	}

	return $classes;

}
add_filter( 'body_class', 'greatermedia_add_gigya_body_class' );

/**
 * Show more posts that usual for gmr_closure archives.
 */
add_action( 'parse_query', function ( WP_Query $query ) {
	if ( $query->is_main_query() && $query->is_post_type_archive( 'gmr_closure' ) ) {
		$query->query_vars['posts_per_page'] = 30;
	}
} );


function add_ie_stylesheet() {
	?>
	<!--[if lt IE 10]>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/assets/css/ie9.css"/>
	<![endif]-->
	<!--[if lt IE 9]>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/assets/css/ie8.css"/>
	<![endif]-->
	<?php
}
add_action( 'wp_head', 'add_ie_stylesheet' );

/**
 * Hide live player sidebar
 */
add_action( 'gmlp_player_popup_template', 'greatermedia_popup_payer_hide_livesidebar' );
function greatermedia_popup_payer_hide_livesidebar(){
	add_filter( 'load_greatermedia_livepress_sidebar', '__return_false' );
}

/**
 * Create a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @global int $paged WordPress archive pagination page count.
 * @global int $page  WordPress paginated post page count.
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function greatermedia_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() ) {
		return $title;
	}

	// Add the site name.
	$title .= get_bloginfo( 'name', 'display' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title = "$title $sep $site_description";
	}

	// Add a page number if necessary.
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		$title = "$title $sep Page " . max( $paged, $page );
	}

	return $title;
}
add_filter( 'wp_title', 'greatermedia_wp_title', 10, 2 );

/**
 * Updates tribe events archive title.
 *
 * @global WP_Query $wp_query The main query.
 * @param string $title The initial title.
 * @return string Updated title.
 */
function greatermedia_events_title( $title ) {
	global $wp_query;

	// If there's a date selected in the tribe bar, show the date range of the currently showing events
	if ( ! tribe_is_month() && isset( $_REQUEST['tribe-bar-date'] ) && $wp_query->have_posts() ) {
		if ( $wp_query->get( 'paged' ) > 1 ) {
			// if we're on page 1, show the selected tribe-bar-date as the first date in the range
			$first_event_date = tribe_get_start_date( $wp_query->posts[0], false );
		} else {
			//otherwise show the start date of the first event in the results
			$first_event_date =  tribe_event_format_date( $_REQUEST['tribe-bar-date'], false );
		}

		$title = 'Events from ' . $first_event_date;
	}

	// day view title
	if ( tribe_is_day() ) {
		$title = 'Events for ' . date_i18n( tribe_get_date_format( true ), strtotime( $wp_query->get( 'start_date' ) ) );
	}

	return $title;
}
add_filter( 'tribe_get_events_title', 'greatermedia_events_title' );

/**
 * Overrides the default [caption] shortcode so we can use max-width instead of width.
 * Still takes the 'width' shortcode attribute, just modifies it at output.
 *
 * @param $empty string comes in as an empty string. Fill it up to override the caption
 * @param $attr array of attributes for the shortcode
 * @param $content string The image, possibly wrapped in an anchor — or technically any other content.
 * @return string shortcode HTML output
 */

function greatermedia_image_caption_override( $empty, $attr, $content ) {

	$atts = shortcode_atts( array(
		'id'      => '',
		'align'   => 'alignnone',
		'width'   => '',
		'caption' => '',
		'class'   => '',
	), $attr, 'caption' );

	$atts['width'] = (int) $atts['width'];

	if ( $atts['width'] < 1 || empty( $atts['caption'] ) ) {
		return $content;
	}

	if ( ! empty( $atts['id'] ) ) {
		$atts['id'] = 'id="' . esc_attr( $atts['id'] ) . '" ';
	}

	$class = trim( 'wp-caption ' . $atts['align'] . ' ' . $atts['class'] );

		return '<figure ' . $atts['id'] . 'style="max-width: ' . (int) $atts['width'] . 'px;" class="' . esc_attr( $class ) . '">'
		       . do_shortcode( $content ) . '<figcaption class="wp-caption-text">' . $atts['caption'] . '</figcaption></figure>';

}
add_filter( 'img_caption_shortcode', 'greatermedia_image_caption_override', null, 3 );

/**
 * Returns menu hash based on its arguments.
 *
 * @param array $args The array of menu arguments.
 * @return string The menu hash.
 */
function _greatermedia_get_menu_hash( $args ) {
	$_args = (array) $args;

	// let's unset walker, because it might affect hash results.
	unset( $_args['walker'] );
	// let's sort args by key
	ksort( $_args );

	return sha1( serialize( $_args ) );
}

/**
 * Returns cached menu if available.
 *
 * @param mixed $menu The default menu.
 * @param array $args The array of menu arguments.
 * @return mixed Cached menu if available, otherwise default menu.
 */
function greatermedia_get_cached_mega_menu( $menu, $args ) {
	$hash = _greatermedia_get_menu_hash( $args );
	$cached_menus = get_transient( 'gmr_mega_menus' );
	if ( ! is_array( $cached_menus ) ) {
		return $menu;
	}

	return ! empty( $cached_menus[ $hash ] ) ? $cached_menus[ $hash ] : $menu;
}
add_filter( 'pre_wp_nav_menu', 'greatermedia_get_cached_mega_menu', 10, 2 );

/**
 * Caches newly generated menu.
 *
 * @param string $menu The menu markup to cache.
 * @param array $args The array of menu arguments.
 * @return string The menu markup.
 */
function greatermedia_cache_mega_menu( $menu, $args ) {
	$hash = _greatermedia_get_menu_hash( $args );
	$cached_menus = get_transient( 'gmr_mega_menus' );
	if ( ! is_array( $cached_menus ) ) {
		$cached_menus = array();
	}

	$cached_menus[ $hash ] = $menu;
	set_transient( 'gmr_mega_menus', $cached_menus );

	return $menu;
}
add_filter( 'wp_nav_menu', 'greatermedia_cache_mega_menu', 100, 2 );

/**
 * Clears cached menus.
 */
function greatermedia_clear_mega_menu_cache() {
	delete_transient( 'gmr_mega_menus' );
}
add_action( 'wp_create_nav_menu', 'greatermedia_clear_mega_menu_cache' );
add_action( 'wp_update_nav_menu', 'greatermedia_clear_mega_menu_cache' );
add_action( 'wp_delete_nav_menu', 'greatermedia_clear_mega_menu_cache' );
add_action( 'wp_update_nav_menu_item', 'greatermedia_clear_mega_menu_cache' );
add_action( 'save_post_nav_menu_item', 'greatermedia_clear_mega_menu_cache' );

/**
 * Clears cached menus when locations have been changed.
 */
function greatermedia_clear_mega_menu_cache_on_locations_change( $value ) {
	greatermedia_clear_mega_menu_cache();
	return $value;
}
add_filter( 'pre_set_theme_mod_nav_menu_locations', 'greatermedia_clear_mega_menu_cache_on_locations_change' );

/**
 * Clears cached menus.
 */
function greatermedia_clear_mega_menu_cache_on_menu_item_delete( $post_id = false ) {
	if ( 'nav_menu_item' == get_post_type( $post_id ) ) {
		greatermedia_clear_mega_menu_cache();
	}
}
add_action( 'delete_post', 'greatermedia_clear_mega_menu_cache_on_menu_item_delete' );

/**
 * Function to add titles to category or tag archives
 */
function greatermedia_archive_title() {
	$current_category = single_cat_title( '', false );

	echo '<h2 class="content__heading">';

	if ( is_category() ) :
		echo 'Browsing articles labeled "' . $current_category . '"';
	elseif ( is_tag() ) :
		echo 'Browsing articles tagged "';
		single_tag_title();
		echo  '"';
	endif;

	echo '</h2>';
}

/**
 * Adds a class to the body if a checkbox for News/Sports sites, has been checked in the site settings
 *
 * @action body_class
 * @access public
 *
 * @param $classes
 *
 * @return array
 */
function greatermedia_newssite_class( $classes ) {

	if ( is_news_site() ) {
		$classes[] = 'news-site';
	}

	return $classes;
}
add_filter( 'body_class', 'greatermedia_newssite_class' );

/**
 * Adds a class to the body if a checkbox, that disables the live player, has been checked in the site settings
 *
 * @action body_class
 * @access public
 *
 * @param $classes
 *
 * @return array
 */
function greatermedia_liveplayer_disabled( $classes ) {
	$liveplayer_disabled = get_option( 'gmr_liveplayer_disabled' );

	if ( $liveplayer_disabled == 1 ) {
		$classes[] = 'liveplayer-disabled';
	}

	return $classes;
}
add_filter( 'body_class', 'greatermedia_liveplayer_disabled' );

/**
 * Extends the homepage featured curation limit
 *
 * @action gmr-homepage-featured-limit
 * @access public
 *
 * @param $limit
 *
 * @return int
 */
function greatermedia_extend_featured_curation_limit( $limit ) {

	if ( is_news_site() ) {
		$limit = 6;
	} else {
		$limit = 4;
	}

	return $limit;

}
add_filter( 'gmr-homepage-featured-limit', 'greatermedia_extend_featured_curation_limit' );

/**
 * Extends the homepage community curation limit
 *
 * @action gmr-homepage-community-limit
 * @access public
 *
 * @param $limit
 *
 * @return int
 */
function greatermedia_extend_community_curation_limit( $limit ) {

	if ( is_news_site() ) {
		$limit = 4;
	} else {
		$limit = 3;
	}

	return $limit;

}
add_filter( 'gmr-homepage-community-limit', 'greatermedia_extend_community_curation_limit' );

function greatermedia_podcasts_in_loop( $query ) {

	if ( is_home() && $query->is_main_query() )
		$query->set( 'post_type', array( 'post', 'episode' ) );

	return $query;
}
add_action( 'pre_get_posts', 'greatermedia_podcasts_in_loop' );

function greatermedia_pinterest_handler( $matches, $attr, $url, $rawattr ) {
	return sprintf(
		'<a data-pin-do="embedPin" href="%s"></a>' .
		'<script type="text/javascript" async defer src="//assets.pinterest.com/js/pinit.js"></script>',
		esc_url( $url )
	);
}

function greatermedia_facebook_handler( $matches, $attr, $url, $rawattr ) {
	return '<script>!function(e,n,t){var o,c=e.getElementsByTagName(n)[0];e.getElementById(t)||(o=e.createElement(n),o.id=t,o.src="//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.2",c.parentNode.insertBefore(o,c))}(document,"script","facebook-jssdk");</script>
			<div class="fb-post" data-href="' . esc_url( $url ) . '"></div>';
}

/**
 * Disables wptexturize for compatibility with Embed.ly
 */
add_filter( 'run_wptexturize', '__return_false' );

/**
 * Adds default styles to Embedly cards added by the Embedly Wordpress plug-in as defined at:
 * http://embed.ly/docs/products/cards
 *
 * @param $content
 */

function stylize_embedly_embeds( $content ) {
	return preg_replace( '/(<a class=\\\\"embedly-card\\\\" )(href=\\\\"[^"]*\\\\">)/', ' ${1}data-card-width=\"100%\" data-card-chrome=\"0\" data-card-controls=\"0\" $2', $content );
}

add_filter( 'content_save_pre', 'stylize_embedly_embeds', 30, 1 );

/**
 * Enables Video Thumbnails to work with Embedly on first post save.
 */
function urldecode_markup_for_video_thumbnails( $markup, $post_id ) {
	return urldecode($markup);
}

add_filter( 'video_thumbnail_markup', 'urldecode_markup_for_video_thumbnails', 10, 2 );

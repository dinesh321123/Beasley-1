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
	define( 'GREATERMEDIA_VERSION', '0.1.4' );
}

add_theme_support( 'homepage-curation' );

require_once( __DIR__ . '/includes/liveplayer/class-liveplayer.php' );
require_once( __DIR__ . '/includes/site-options/loader.php' );
require_once( __DIR__ . '/includes/mega-menu/mega-menu-admin.php' );
require_once( __DIR__ . '/includes/mega-menu/mega-menu-walker.php' );
require_once( __DIR__ . '/includes/mega-menu/mega-menu-mobile-walker.php' );
require_once( __DIR__ . '/includes/gallery-post-thumbnails/loader.php' );
require_once( __DIR__ . '/includes/image-attributes/loader.php');
require_once( __DIR__ . '/includes/posts-screen-thumbnails/loader.php' );

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
		array( 'jquery', 'jquery-waypoints' ),
		GREATERMEDIA_VERSION,
		true
	);
	wp_enqueue_style(
		'greatermedia'
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
		
		global $wp_query; 
		
		ob_start(); 
		
		get_template_part( $partial_slug, $partial_name );
		
		$html = ob_get_clean();
		
		wp_send_json( array( 
			'paged' => $wp_query->query_vars['paged'], 
			'max_num_pages' => $wp_query->max_num_pages,
			'post_count' => $wp_query->post_count,
			'html' => $html,
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
	
	
	if ( ! $args['next_page'] ) {
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
			<i class="fa fa-spin fa-refresh"></i> Load More
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
	?>
	<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-804109-43', 'auto');
	
	if( is_gigya_user_logged_in() ) {
		ga( 'set', '&uid', get_gigya_user_id() );
	}

	jQuery(document).on('pjax:end', function() {
		ga('set', 'location', window.location.href);
		ga('send', 'pageview');
	});
	ga('send', 'pageview');
	</script>
	<?php
}
add_action( 'wp_head' , 'add_google_analytics' );

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
 * Create a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @global int $paged WordPress archive pagination page count.
 * @global int $page  WordPress paginated post page count.
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function twentyfourteen_wp_title( $title, $sep ) {
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
add_filter( 'wp_title', 'twentyfourteen_wp_title', 10, 2 );

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

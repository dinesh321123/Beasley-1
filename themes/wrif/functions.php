<?php
/**
 * WRIF functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * @package WRIF
 * @since 0.1.0
 */

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since 0.1.0
 */
function wrif_scripts_styles() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
	
	wp_register_style( 'google-fonts-wrif', '//fonts.googleapis.com/css?family=Oswald:400,300,700', array(), null );
	wp_dequeue_style( 'greatermedia' );
	wp_deregister_style( 'greatermedia' );
	wp_enqueue_style( 'wrif', get_stylesheet_directory_uri() . "/assets/css/wrif{$postfix}.css", array( 'google-fonts-wrif' ), GREATERMEDIA_VERSION );

	wp_enqueue_script( 'livefyre', '//cdn.livefyre.com/Livefyre.js', null, null, true );
	wp_enqueue_script( 'wrif', get_stylesheet_directory_uri() . "/assets/js/wrif{$postfix}.js", array( 'livefyre' ), GREATERMEDIA_VERSION, true	);
}
add_action( 'wp_enqueue_scripts', 'wrif_scripts_styles', 20 );

/**
 * Add Chartbeat to site.
 */
function wrif_chartbeat_header() {
	$content = '<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>';

	echo apply_filters( 'wrif_chartbeat_header', $content );
}
add_action( 'wp_head', 'wrif_chartbeat_header' );

/**
 * Add Dave & Chuck Geo Redirect to site
 * only when the post content is in the category-options
 * 'dave-chuck-the-freak'
 */
function wrif_dave_and_chuck_geo_redirect() {
	if ( in_category( 'dave-chuck-the-freak' ) || ( 'dave-and-chuck' === get_page_uri() ) ) {
		?><script>
			var geolifygeoredirect = document.createElement('script')
			geolifygeoredirect.setAttribute('type', 'text/javascript')
			geolifygeoredirect.setAttribute('src', '//www.geolify.com/georedirectv2.php?id=29964&refurl=' + document.referrer)
			document.getElementsByTagName('head')[0].appendChild(geolifygeoredirect)
		</script><?php
	}
}
add_action( 'wp_head', 'wrif_dave_and_chuck_geo_redirect', 0 );

function wrif_chartbeat_footer() {
	$content = '<script type="text/javascript">var cbjspath = "static.chartbeat.com/js/chartbeat.js?uid=2332&domain=wrif.com";var cbjsprotocol = (("https:" == document.location.protocol) ? "https://" : "http://"); document.write(unescape("%3Cscript src=\'"+cbjsprotocol+cbjspath+"\' type=\'text/javascript\'%3E%3C/script%3E"))</script>';

	echo apply_filters( 'wrif_chartbeat_footer', $content );
}
add_action( 'wp_footer', 'wrif_chartbeat_footer' );

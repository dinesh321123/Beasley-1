<?php

// action hooks
add_action( 'init', 'gmr_blogroll_register_endpoint' );
add_action( 'widgets_init', 'gmr_blogroll_register_widgets' );
add_action( 'wp_enqueue_scripts', 'gmr_blogroll_enqueue_widget_scripts' );
add_action( 'template_redirect', 'gmr_blogroll_render_widget_html' );

/**
 * Registers blogroll endpoint.
 *
 * @action init
 * @global WP $wp The WP object.
 * @global WP_Rewrite $wp_rewrite The WP_Rewrite object.
 */
function gmr_blogroll_register_endpoint() {
	global $wp, $wp_rewrite;

	// register blogroll query vars
	$wp->add_query_var( 'blogroll' );

	// register rewrite rule
	$regex = '^blogroll/stream/([^/]+)/?$';
	$wp_rewrite->add_rule( $regex, 'index.php?blogroll=yes&' . GMR_LIVE_STREAM_CPT . '=$matches[1]', 'top' );

	// flush rewrite rules if it doesn't contain blogroll endpoint
	$rules = $wp_rewrite->wp_rewrite_rules();
	if ( ! isset( $rules[ $regex ] ) ) {
		$wp_rewrite->flush_rules();
	}
}

/**
 * Registers live link widgets.
 *
 * @action widgets_init
 */
function gmr_blogroll_register_widgets() {
	register_widget( 'Blogroll_Widget' );
}

/**
 * Registers blogroll widget scripts.
 *
 * @access wp_enqueue_scripts
 */
function gmr_blogroll_enqueue_widget_scripts() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
	wp_register_script( 'gmr-blogroll-widget', GMEDIA_LIVE_STREAM_URL . "/assets/js/blogroll-widget{$postfix}.js", array( 'jquery' ), GMEDIA_LIVE_STREAM_VERSION, true );
	wp_localize_script( 'gmr-blogroll-widget', 'gmr_blogroll_widget', array(
		'ajaxurl'  => home_url( '/blogroll/stream/' ),
		'interval' => MINUTE_IN_SECONDS * 1000, // 60 000 milliseconds
	) );
}

/**
 * Renders blogroll widget.
 *
 * @action template_redirect
 */
function gmr_blogroll_render_widget_html() {
	// do nothing if it is not a blogroll request
	if ( ! filter_var( get_query_var( 'blogroll' ), FILTER_VALIDATE_BOOLEAN ) ) {
		return;
	}

	// send cache headers
	if ( ! headers_sent() ) {
		$max_age = MINUTE_IN_SECONDS;
		$now = current_time( 'timestamp', 1 );
		$actual_date = gmdate( DATE_COOKIE, $now );
		$expire_date = gmdate( DATE_COOKIE, $now + $max_age );

		header( "Date: {$actual_date}" );
		header( "Expires: {$expire_date}" );
		header( "Pragma: cache" );
		header( "Cache-Control: max-age={$max_age}" );
		header( "User-Cache-Control: max-age={$max_age}" );
	}

	// send widget html
	echo gmr_blogroll_get_widget_html();
	exit;
}

/**
 * Adjusts blogroll widget transient name to make unique transients per stream.
 *
 * @param string $name The initial transient name.
 * @return string Adjusted transient name.
 */
function gmr_blogroll_get_widget_transient_name( $name ) {
	$stream = null;
	$sign = get_query_var( GMR_LIVE_STREAM_CPT );
	if ( ! empty( $sign ) ) {
		$stream = gmr_streams_get_stream_by_sign( $sign );
	}

	if ( ! $stream ) {
		$stream = gmr_streams_get_primary_stream();
	}

	return $stream ? "{$name}_{$stream->ID}" : $name;
}

/**
 * Builds and returns blogroll widget html.
 *
 * @return string The widget html.
 */
function gmr_blogroll_get_widget_html() {
	// check transient first and if it exists, then return cached version of the widget
	$transient = gmr_blogroll_get_widget_transient_name( 'gmr_blogroll_blogroll_widget' );
	$html = get_transient( $transient );
	if ( ! empty( $html ) ) {
		return $html;
	}

	// transient doesn't exists, so let's build new widget and cache it
	// start from enabling outbut buffering
	ob_start();

	// get widget item ids
	$posts__in = apply_filters( 'gmr_blogroll_widget_item_ids', array() );
	$posts__in = array_unique( array_filter( array_map( 'intval', $posts__in ) ) );

	// build quiery and render widget's html
	$query = new WP_Query();
	if ( ! empty( $posts__in ) ) {
		$query->query(  array(
			'post_type'           => apply_filters( 'gmr_blogroll_widget_item_post_types', array() ),
			'post__in'            => $posts__in,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'posts_per_page'      => 20,
		) );
	}

	echo '<ul>';
		while ( $query->have_posts() ) :
			$query->the_post();

			$item = apply_filters( 'gmr_blogroll_widget_item', false );
			if ( ! empty( $item ) ) :
				echo '<li>', $item, '</li>';
			endif;
		endwhile;
	echo '</ul>';

	// grab html from output buffer
	$html = ob_get_clean();
	// create new transient with a minute TTL
	set_transient( $transient, $html, MINUTE_IN_SECONDS );
	// reset global post data
	wp_reset_postdata();

	return $html;
}

/**
 * Blogroll widget class.
 */
class Blogroll_Widget extends WP_Widget {

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		parent::__construct( 'gmr_blogroll_widget', 'GMR - Blogroll' );
	}

	/**
	 * Renders widget.
	 *
	 * @access public
	 * @param array $args The widget settings array.
	 * @param array $instance The widget instance arguments.
	 */
	public function widget( $args, $instance ) {
		$widget = gmr_blogroll_get_widget_html();
		if ( empty( $widget ) ) {
			return;
		}

		wp_enqueue_script( 'gmr-blogroll-widget' );
		
		echo $args['before_widget'], $widget, $args['after_widget'];
	}

}
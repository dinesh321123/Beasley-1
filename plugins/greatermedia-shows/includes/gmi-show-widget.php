<?php

// action hooks
add_action( 'widgets_init', 'gmrs_register_widgets' );
add_action( 'wp_enqueue_scripts', 'gmrs_enqueue_widget_scripts' );
add_action( 'wp_ajax_gmrs_widget', 'gmrs_render_shows_widget_html' );
add_action( 'wp_ajax_nopriv_gmrs_widget', 'gmrs_render_shows_widget_html' );

/**
 * Registers live link widgets.
 *
 * @action widgets_init
 */
function gmrs_register_widgets() {
	register_widget( 'Shows_Widget' );
}

/**
 * Registers shows widget scripts.
 *
 * @access wp_enqueue_scripts
 */
function gmrs_enqueue_widget_scripts() {
	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
	wp_register_script( 'gmr-show-widget', GMEDIA_SHOWS_URL . "/assets/js/shows_widget{$postfix}.js", array( 'jquery' ), GMEDIA_SHOWS_VERSION, true );
	wp_localize_script( 'gmr-show-widget', 'gmrs', array(
		'ajaxurl'  => admin_url( 'admin-ajax.php?action=gmrs_widget' ),
		'interval' => MINUTE_IN_SECONDS * 1000,
	) );
}

/**
 * Renders shows widget.
 *
 * @action wp_ajax_gmrs_widget
 * @action wp_ajax_nopriv_gmrs_widget
 */
function gmrs_render_shows_widget_html() {
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
	echo gmrs_get_shows_widget_html();
	exit;
}

/**
 * Builds and returns shows widget html.
 *
 * @return string The widget html.
 */
function gmrs_get_shows_widget_html() {
	// check transient first and if it exists, then return cached version of the widget
	$transient = 'gmrs_show_widget';
	$html = get_transient( $transient );
	if ( ! empty( $html ) ) {
		return $html;
	}

	// transient doesn't exists, so let's build new widget and cache it
	// start from enabling outbut buffering
	ob_start();

	// build quiery and render widget's html
	$show_stuff = new WP_Query( array(
		'post_type'           => apply_filters( 'gmr_show_widget_item_post_types', array() ),
		'post_status'         => 'any',
		'orderby'             => 'date',
		'order'               => 'DESC',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'posts_per_page'      => 20,
	) );

	echo '<ul>';
		while ( $show_stuff->have_posts() ) :
			$show_stuff->the_post();

			$item = apply_filters( 'gmr_show_widget_item', false );
			if ( ! empty( $item ) ) {
				echo '<li>', $item, '</li>';
			}
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
 * Shows blogroll widget class.
 */
class Shows_Widget extends WP_Widget {

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		parent::__construct( 'gmr_shows_widget', 'GMR - Shows' );
	}

	/**
	 * Renders widget.
	 *
	 * @access public
	 * @param array $args The widget settings array.
	 * @param array $instance The widget instance settings array.
	 */
	public function widget( $args, $instance ) {
		$widget = gmrs_get_shows_widget_html();
		if ( empty( $widget ) ) {
			return;
		}

		wp_enqueue_script( 'gmr-show-widget' );
		
		echo $args['before_widget'], $widget, $args['after_widget'];
	}

}
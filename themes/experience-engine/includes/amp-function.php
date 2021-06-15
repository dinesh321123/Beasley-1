<?php
add_action( 'amp_post_template_css', 'ee_enqueue_front_css');

add_filter( 'amp_post_article_footer_meta', 'amp_remove_meta_parts');

if ( ! function_exists( 'ee_enqueue_front_css' ) ) :
	function ee_enqueue_front_css() {
		$site_colors = ee_get_css_colors();
	?>
		html {
			background: white;
		}
		header.amp-wp-header {
			background-color: white;
		}
		header.amp-wp-header a {
		}
		header.amp-wp-header .amp-wp-site-icon {
			background-color: transparent;
			position: relative;
			width: 140px;
			height: 80px;
			border-radius: 0px;
			border: none;
			right: unset;
			top: unset;
			text-align: left;
			display: block;
		}
	<?php
	}
endif;

if ( ! function_exists( 'amp_remove_meta_parts' ) ) :
	function amp_remove_meta_parts( $meta_parts ) {
		foreach ( array_keys( $meta_parts, 'meta-taxonomy', true ) as $key ) {
			// unset( $meta_parts[ $key ] );
		} exit;
		foreach ( array_keys( $meta_parts, 'meta-comments-link', true ) as $key ) {
			// unset( $meta_parts[ $key ] );
		}
		return $meta_parts;
	}
endif;

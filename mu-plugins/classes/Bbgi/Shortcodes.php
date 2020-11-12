<?php
/**
 * Registers multiple shortcodes
 * * Age Restricted
 * * Login Restricted
 * * LiveFyre Well
 * * LiveFyre Poll
 * * LiveFyre App
 * * Iframe
 * * BBGI Contest
 */

namespace Bbgi;

use WP_Query;

class Shortcodes extends \Bbgi\Module {

	public function register() {
		$suppress = $this( 'suppress_shortcode' );

		add_shortcode( 'age-restricted', $suppress );
		add_shortcode( 'login-restricted', $suppress );
		add_shortcode( 'livefyre-wall', $suppress );
		add_shortcode( 'livefyre-poll', $suppress );
		add_shortcode( 'livefyre-app', $suppress );

		add_shortcode( 'iframe', $this( 'handle_iframe_shortcode' ) );
		add_shortcode( 'bbgi-contest', $this( 'handle_national_contest_shortcode' ) );
		add_shortcode( 'inlink', $this( 'handle_inlink_shortcode' ) );
	}

	public function suppress_shortcode( $atts, $content = null ) {
		return $content;
	}

	public function get_inlink_url( $syndication_old_name ) {
		$url = '';

		$query = new WP_Query(
				array(
						'post_status' => 'publish',
						'meta-key' => 'syndication_old_name',
						'meta_value' => $atts['id']
				)
		);

		$query->set( 'no_found_rows', true );

		while ( $query->have_posts()) {
			$query->the_post();
			$url = get_the_permalink();
			break;
		}

		wp_reset_postdata();

		return $url;
	}


	public function handle_inlink_shortcode( $atts ) {

		$atts = shortcode_atts( array(
				'id'	=> '',
				'text'	=> '',
				'hideifdoesnotexist'	=>	'false'
		), $atts, 'inlink');

		$url = '';
		$result = '';

		if ($atts['id']) {

			$url = wp_cache_get( $atts['id'], 'bbgi:inlinking' );
			if ( empty( $ids ) ) {
				$url = $this->get_inlink_url( $atts['id'] );
				wp_cache_set( $atts['id'], $url, 'bbgi:inlinking', MINUTE_IN_SECONDS * 15 );
			}
		}

		if ( $atts['hideifdoesnotexist'] === 'false' && ! $url ) {
			$result = $atts['text'];
		} else if ($url) {
			$result = sprintf('<a href="%s">%s</a>', $url, $atts['text']);
		}

		return $result;
	}

	public function handle_iframe_shortcode( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'src'       => '',
			'height'    => '',
			'scrolling' => 'auto'
		), $atts, 'iframe' );

		$uniqid = uniqid();

		$style = '';
		if ( ! empty( $atts['height'] ) ) {
			$style = ' style="height: ' . esc_attr( $atts['height'] ) . 'px"';
		}

		$class = empty( $atts['height'] )
			? 'intrinsic-container-16x9'
			: 'intrinsic-container-fixed-height';

		ob_start();

		?><div id="iframe-<?php echo esc_attr( $uniqid ); ?>" class="intrinsic-container <?php echo sanitize_html_class( $class ); ?> iframe-embed" <?php echo $style; ?>>
			<iframe frameborder="0" src="<?php echo esc_attr( $atts['src'] ) ?>" scrolling="<?php echo esc_attr( $atts['scrolling'] ) ?>" seamless="seamless"<?php echo $style; ?>></iframe>
		</div><?php

		return ob_get_clean();
	}

	public function handle_national_contest_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'contest' => 'national-contest',
			'brand'   => 'WMMR',
		), $atts, 'bbgi-contest' );

		$contest = urlencode( $atts['contest'] );
		$brand = urlencode( $atts['brand'] );

		$embed = <<<EOL
<style>#contestframe {width: 100%;}</style>
<iframe id="contestframe" src="https://contests.bbgi.com/landing?contest={$contest}&brand={$brand}" frameborder="0" scrolling="no" onload="iFrameResize({log:true, autoResize: true})"></iframe>
EOL;

		return $embed;
	}

}

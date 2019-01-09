<?php

namespace Bbgi;

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
	}

	public function suppress_shortcode( $atts, $content = null ) {
		return $content;
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

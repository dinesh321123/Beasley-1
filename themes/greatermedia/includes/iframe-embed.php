<?php
/**
 * Add iframe shortcode to allow iframes to be embeded safely
 */
class GMRIframe {

	public static function init()
	{
		add_shortcode( 'iframe', array( __CLASS__, 'handle_shortcode' ) );
	}

	public static function handle_shortcode( $atts, $content = null )
	{
		$atts = shortcode_atts( array(
			'src' => '',
			'height' => '300',
			'scrolling' => 'auto'
		), $atts );

		$uniqid = uniqid();

		ob_start();

		?>
		<div id="playerwrapper-<?php echo $uniqid ?>" class="iframe-embed">
			<iframe height="<?php echo esc_attr( $atts['height'] ) ?>px" frameborder="0" src="<?php echo esc_attr( $atts['src'] ) ?>" scrolling="<?php echo esc_attr( $atts['scrolling'] ) ?>" seamless="seamless"></iframe>
		</div>
		<?php

		return ob_get_clean();
	}

}

GMRIframe::init();

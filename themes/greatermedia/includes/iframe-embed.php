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
			'height' => '',
			'scrolling' => 'auto'
		), $atts );

		$uniqid = uniqid();

		ob_start();

		?>
		<div id="iframe-<?php echo $uniqid ?>" class="intrinsic-container <?php if ( empty( $atts['height'] ) ) { ?>intrinsic-container-16x9<?php } else { ?>intrinsic-container-fixed-height<?php } ?> iframe-embed" <?php if ( !empty( $atts['height'] ) ) { ?>style="height: <?php echo esc_attr( $atts['height'] ) ?>px;" <?php } ?>>
			<iframe <?php if ( !empty( $atts['height'] ) ) { ?>style="height: <?php echo esc_attr( $atts['height'] ) ?>px;" <?php } ?>frameborder="0" src="<?php echo esc_attr( $atts['src'] ) ?>" scrolling="<?php echo esc_attr( $atts['scrolling'] ) ?>" seamless="seamless"></iframe>
		</div>
		<?php

		return ob_get_clean();
	}

}

GMRIframe::init();

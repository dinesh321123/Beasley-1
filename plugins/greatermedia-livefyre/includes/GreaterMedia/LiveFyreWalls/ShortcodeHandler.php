<?php

namespace GreaterMedia\LiveFyreWalls;

class ShortcodeHandler {

	public function __construct() {
		add_shortcode( 'livefyre-wall', array( $this, 'handle_shortcode' ) );
	}

	public function handle_shortcode( $atts, $content = null ) {
		$uniqid = uniqid();
		$atts = shortcode_atts( array(
			'network_id' => '',
			'site_id'    => '',
			'article_id' => '',
			'initial'    => 10,
			'columns'    => 2,
		), $atts );

		ob_start();
		
		?><div id="wall-<?php echo esc_attr( $uniqid ); ?>"></div>
		<script src="//cdn.livefyre.com/Livefyre.js"></script>
		<script>
			Livefyre.require(['streamhub-wall#3', 'streamhub-sdk#2'], function(LiveMediaWall, SDK) {
				window.wall_<?php echo esc_attr( $uniqid ); ?> = new LiveMediaWall({
					el: document.getElementById("wall-<?php echo esc_attr( $uniqid ); ?>"),
					initial: <?php echo esc_js( $atts['initial'] ); ?>,
					columns: <?php echo esc_js( $atts['columns'] ); ?>,
					collection: new (SDK.Collection)({
						"network": "<?php echo esc_js( $atts['network_id'] ); ?>",
						"siteId": "<?php echo esc_js( $atts['site_id'] ); ?>",
						"articleId": "<?php echo esc_js( $atts['article_id'] ); ?>"
					})
				});
			});
		</script><?php
		
		return ob_get_clean();
	}

}

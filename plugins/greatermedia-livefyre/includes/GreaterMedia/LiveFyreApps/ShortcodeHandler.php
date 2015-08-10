<?php

namespace GreaterMedia\LiveFyreApps;

class ShortcodeHandler
{
	public function __construct()
	{
		add_shortcode( 'livefyre-app', array( $this, 'handle_shortcode' ) );
	}

	public function handle_shortcode( $atts, $content = null )
	{
		$atts = shortcode_atts( array(
			'data_lf_app' => '',
			'data_lf_env' => ''
		), $atts );

		$code_atts = array(
			'data_lf_app' => $atts['data_lf_app'],
			'data_lf_env' => $atts['data_lf_env']
		);

		$uniqid = uniqid();

		ob_start();

		?>

		<script src="//cdn.livefyre.com/Livefyre.js"></script>
		<div class="lf-app-embed" data-lf-app="<?php echo htmlspecialchars($code_atts['data_lf_app']) ?>" data-lf-env="<?php echo htmlspecialchars($code_atts['data_lf_env']) ?>"></div>
		<script>Livefyre.require(['//livefyre-cdn-staging.s3.amazonaws.com/libs/app-embed/v0.6.5/app-embed.min.js'], function (appEmbed) {appEmbed.loadAll().done(function(embed) {embed = embed[0];if(embed.el.onload && embed.getConfig){embed.el.onload(embed.getConfig());}});});</script>

		<?php

		return ob_get_clean();
	}
}

<?php

namespace GreaterMedia\LiveFyrePolls;

class ShortcodeHandler
{
	public function __construct()
	{
		add_shortcode( 'livefyre-poll', array( $this, 'handle_shortcode' ) );
	}
	
	public function handle_shortcode( $atts, $content = null )
	{
		$atts = shortcode_atts( array( 
			'network_id' => '',
			'site_id' => '', 
			'poll_id' => '', 
			'env' => '', 
		), $atts );
			
		$code_atts = array(
			'networkId' => $atts['network_id'],
			'siteId' => $atts['site_id'],
			'pollId' => $atts['poll_id'],
			'env' => $atts['env'],
		);
		
		$uniqid = uniqid(); 
		
		ob_start(); 
		
		?>
		<div class="livefyre-poll" id="lf-poll-<?php echo esc_attr( $uniqid ); ?>"></div>
		<script type="text/javascript" src="//cdn.livefyre.com/Livefyre.js"></script>
		<script type="text/javascript" >Livefyre.require(["poll#uat"],function(a){(new a(<?php echo json_encode( $code_atts ); ?>)).render(document.getElementById("lf-poll-<?php echo esc_js( $uniqid ); ?>"))});</script>			
		<?php
		
		return ob_get_clean(); 
	}	
}
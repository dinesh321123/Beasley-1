<?php

namespace GreaterMedia\AdCodeManager;

add_action( 'wp_footer', __NAMESPACE__ . '\load_js' );
add_filter( 'acm_output_html', __NAMESPACE__ . '\render_tag', 15, 2 );

function load_js() {
	?>
	<script type="text/javascript" src="//ox-d.greatermedia.com/w/1.0/jstag"></script>
	<?php
}

function render_tag( $output_html, $tag_id ) {
	static $random_number;

	if ( is_null( $random_number ) ) {
		$random_number =  str_pad( rand( 0, 999999999999999 ), 15, rand( 0, 9 ), STR_PAD_LEFT );
	}
	ob_start();

	?>
	<div id="%openx_id%_%tag%">
		<noscript>
			<iframe id="9ee0446165" name="9ee0446165" src="//ox-d.greatermedia.com/w/1.0/afr?auid=%openx_id%&cb=<?php echo intval( $random_number ) ?>" frameborder="0" scrolling="no">
				<a href="http://ox-d.greatermedia.com/w/1.0/rc?cs=9ee0446165&cb=<?php echo intval( $random_number ); ?>" >
					<img src="//ox-d.greatermedia.com/w/1.0/ai?auid=%openx_id%&cs=9ee0446165&cb=<?php echo intval( $random_number ); ?>" border="0" alt="">
				</a>
			</iframe>
		</noscript>
	</div>
	<script type="text/javascript">
		var OX_ads = OX_ads || [];
		OX_ads.push({
			slot_id: "%openx_id%_%tag%",
			auid: "%openx_id%"
		});
	</script>

	<?php

	$output = ob_get_clean();

	return $output;
}

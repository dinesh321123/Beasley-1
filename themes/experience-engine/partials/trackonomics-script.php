<?php
function trackonomics_script() {
	$current_queried_post_type = get_post_type( get_queried_object_id() );
	$validPostTypeArray	= (array) apply_filters( 'trackonomics-script-valid-post-types', array( 'affiliate_marketing' )  );
	if( in_array( $current_queried_post_type, $validPostTypeArray ) ) {
	/*
	?>
	<script id="funnel-relay-installer" data-property-id="PROPERTY_ID" data-customer-id="bbgi_39ea5_bbgi" src="https://cdn-magiclinks.trackonomics.net/client/static/v2/bbgi_39ea5_bbgi.js" async="async"></script>
	<?php */
	}	//End If condition
}	//End Function

add_action( 'wp_footer', 'trackonomics_script', 100 );
?>

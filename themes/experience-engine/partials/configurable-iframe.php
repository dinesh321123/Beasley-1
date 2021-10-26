<?php
$IframePostType = 1;
$current_post_object = get_queried_object();
$hide_draftking_iframe = get_field( 'hide_draftking_iframe', $current_post_object );
if ( isset( $hide_draftking_iframe ) && $hide_draftking_iframe == 0 && !is_front_page() ) :
	$IframePostType = 0;
endif;

$iframe_height =  get_option( 'configurable_iframe_height', '0' );

// Height Setting Of 0 Means "Do Not Display"
if ( empty( $iframe_height ) || empty( $IframePostType ) ) {
	echo "<script type='text/javascript'>var setIFrameVisible = Function.prototype;</script>";
	return;
}
?>

<style>
	#footer {
		margin-bottom: var(--configurable-iframe-height);
	}

	button.back-to-top {
		margin-bottom: var(--configurable-iframe-height);
	}

	div.configurable-iframe-holder {
		position: fixed;
		bottom: 90px;
		left: 0;
		right: 0;
		height: var(--configurable-iframe-height);
		z-index: 9;
		overflow: hidden;
	}

	@media only screen and (min-width: 900px) {
		div.configurable-iframe-holder {
			left: 190px;
		}
	}
</style>

<script type='text/javascript'>
	var setIFrameVisible = (isVisible) => {
		document.documentElement.style.setProperty(
    		'--configurable-iframe-height',
    		isVisible
    			? document.documentElement.style.getPropertyValue(
    					'--default-configurable-iframe-height',
    			  )
    			: '0px',
    	);
	};
</script>

<div class="configurable-iframe-holder">
	<?PHP
		echo "<iframe id='configurable-iframe-element' width='100%' height='100%' frameborder='0' scrolling='no' style='overflow: hidden' src='"
				. get_option( 'configurable_iframe_src', '0' )
				. "' ></iframe>";
	?>
</div>




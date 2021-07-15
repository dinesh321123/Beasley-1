<?php
$iframe_height =  get_option( 'configurable_iframe_height', '0' );

if ( empty( $iframe_height ) ) {
	return;
}
?>

<style>
	#footer {
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
			left: 188px;
		}
	}
</style>
<div class="configurable-iframe-holder">
	<iframe id="configurable-iframe-element" width="100%" height="100%" frameborder="0" scrolling="no" style="overflow: hidden" ></iframe>
</div>

<?PHP
	echo "<script>";
	echo "window.addEventListener('load', function() {";
	echo "document.getElementById('configurable-iframe-element').setAttribute('src', window.bbgiconfig.configurable_iframe_src);";
	echo "});";
	echo "</script>";
?>


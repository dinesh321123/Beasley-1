<?php
	$colors = ee_get_css_colors();

	if ( empty( $colors ) ) {
		$colors = [];
	}
?>

<div id="live-player" class="live-player" data-custom-colors="<?php echo esc_attr( wp_json_encode( $colors ) ); ?>"></div>

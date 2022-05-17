<?php
$colors = ee_get_css_colors();

if ( empty( $colors ) ) {
	$colors = [];
}
?>

<div id="listen-dropdown-container" class="listen-dropdown" data-custom-colors="<?php echo esc_attr( wp_json_encode( $colors ) ); ?>" >

</div>

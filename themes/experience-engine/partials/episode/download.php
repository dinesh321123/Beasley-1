<?php if ( ( $download = ee_get_episode_meta( null, 'download' ) ) ) : ?>
	<a class="btn -empty -nobor" href="<?php echo esc_url( $download ); ?>" target="_blank" rel="noopener">Download</a>
<?php endif; ?>

<div class="download">
	<h6>Download our app</h6>
	<p>Listen Live to KISS and listen to Dave & Chuck 24/7 with our app!</p>

	<?php if ( ee_has_publisher_information( 'itunes_app' ) ) : ?>
		<a href="<?php echo esc_url( ee_get_publisher_information( 'itunes_app' ) ); ?>" aria-label="Download on the App Store" target="_blank" rel="noopener">
			<img src="<?php echo get_template_directory_uri() ?>/assets/images/itunes.svg" alt="Download on the App Store">
		</a>
	<?php endif; ?>

	<?php if ( ee_has_publisher_information( 'play_app' ) ) : ?>
		<a href="<?php echo esc_url( ee_get_publisher_information( 'play_app' ) ); ?>" aria-label="Download on the Google Play" target="_blank" rel="noopener">
			<img src="<?php echo get_template_directory_uri() ?>/assets/images/googleplay.svg" alt="Download on the Google Play">
		</a>
	<?php endif; ?>
</div>

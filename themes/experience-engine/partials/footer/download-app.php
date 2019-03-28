<div class="download">
	<h6>Download our app</h6>
	<p>Download the app to LISTEN LIVE wherever you are and connect with us like never before!</p>

	<?php if ( ee_has_publisher_information( 'ios' ) ) : ?>
		<a href="<?php echo esc_url( ee_get_publisher_information( 'ios' ) ); ?>" aria-label="Download on the App Store" target="_blank" rel="noopener">
			<img src="<?php echo get_template_directory_uri() ?>/assets/images/itunes.svg" alt="Download on the App Store">
		</a>
	<?php endif; ?>

	<?php if ( ee_has_publisher_information( 'android' ) ) : ?>
		<a href="<?php echo esc_url( ee_get_publisher_information( 'android' ) ); ?>" aria-label="Download on the Google Play" target="_blank" rel="noopener">
			<img src="<?php echo get_template_directory_uri() ?>/assets/images/googleplay.svg" alt="Download on the Google Play">
		</a>
	<?php endif; ?>
</div>

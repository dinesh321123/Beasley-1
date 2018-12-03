<?php

$feed_url = ee_get_podcast_meta( null, 'feed_url' );
$itunes_url = ee_get_podcast_meta( null, 'itunes_url' );
$google_play_url = ee_get_podcast_meta( null, 'google_play_url' );

?><div>
	<?php if ( $feed_url ) : ?>
		<a href="<?php echo esc_url( $feed_url ); ?>" target="_blank" rel="noopener">Podcast Feed</a>
	<?php endif; ?>

	<?php if ( $itunes_url ) : ?>
		<a href="<?php echo esc_url( $itunes_url ); ?>" target="_blank" rel="noopener">Apple Podcasts</a>
	<?php endif; ?>

	<?php if ( $google_play_url ) : ?>
		<a href="<?php echo esc_url( $google_play_url ); ?>" target="_blank" rel="noopener">Google Podcasts</a>
	<?php endif; ?>

	<?php get_template_part( 'partials/add-to-favorite' ); ?>
</div>

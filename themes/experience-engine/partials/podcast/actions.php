<?php

$feed_url = ee_get_podcast_meta( null, 'feed_url' );
$itunes_url = ee_get_podcast_meta( null, 'itunes_url' );
$google_play_url = ee_get_podcast_meta( null, 'google_play_url' );

?><div class="podcast-actions section-head">
	<span>
		<?php if ( $feed_url ) : ?>
			<a class="btn -empty" href="<?php echo esc_url( $feed_url ); ?>" target="_blank" rel="noopener">Podcast Feed</a>
		<?php endif; ?>

		<?php if ( $itunes_url ) : ?>
			<a class="btn -empty" href="<?php echo esc_url( $itunes_url ); ?>" target="_blank" rel="noopener">Apple Podcasts</a>
		<?php endif; ?>

		<?php if ( $google_play_url ) : ?>
			<a class="btn -empty" href="<?php echo esc_url( $google_play_url ); ?>" target="_blank" rel="noopener">Google Podcasts</a>
		<?php endif; ?>

		<?php ee_add_to_favorites( get_post_field( 'post_name' ) ); ?>
	</span>
</div>

<div class="post-thumbnail">
	<a href="<?php ee_the_permalink(); ?>">
		<?php ee_the_lazy_thumbnail( null, true ); ?>
		<?php if( has_post_format( 'video', $post->ID ) ) : ?>
			<div class="post-video-overlay">
				<?php $label_id = 'label-' . uniqid(); ?>
				<svg viewBox="0 0 17 24" xmlns="http://www.w3.org/2000/svg" role="img"
					aria-labelledby="<?php echo esc_attr( $label_id ); ?> video-icon-desc">
					<title id="<?php echo esc_attr( $label_id ); ?>">Video Icon</title>
					<description id="video-icon-desc">A circular icon, with a red play button that symbolizes that the post is a video</description>
					<path d="M16.1836 12.0055L0.910156 23.124L0.910156 0.887031L16.1836 12.0055Z"></path>
				</svg>
			</div>
		<?php endif; ?>

		<?php if( stripos( esc_attr($post->feed_title), ' Miss') !== false ) : ?>
			<?php ee_the_sponsored_by_thumbnail_overlay( $post->ID ); ?>
		<?php endif; ?>
	</a>
</div>

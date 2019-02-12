<div class="post-thumbnail">
	<a href="<?php ee_the_permalink(); ?>">
		<?php ee_the_lazy_thumbnail( null, true ); ?>
		<?php if( has_post_format( 'video', $post->ID ) ) : ?>
			<div class="post-video-overlay">
				<svg viewBox="0 0 17 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="video-icon-title video-icon-desc">
					<title id="vide-icon-title">Video Icon</title>
					<description id="video-icon-desc">A circular icon, with a red play button that symbolizes that the post is a video</description>
					<path d="M16.1836 12.0055L0.910156 23.124L0.910156 0.887031L16.1836 12.0055Z"></path>
				</svg>
			</div>
		<?php endif; ?>
	</a>
</div>

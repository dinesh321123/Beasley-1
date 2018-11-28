<?php
	$is_video = in_array( 'format-video', get_post_class() ) || in_array( 'has-featured-video', get_post_class() );
?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/tile/thumbnail' ); ?>
	<?php get_template_part( 'partials/tile/title' ); ?>

	<?php if( true === $is_video ): ?>
		<p class="type">
			<svg width="7" height="12" fill="var(--brand-primary)">
				<path d="M7 6L.25 11.196V.804L7 6z"/>
			</svg>
			video
		</p>
	<?php endif; ?>
</div>

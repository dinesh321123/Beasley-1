<?php

$classes = get_post_class( 'post-tile' );
$is_video = in_array( 'format-video', $classes ) || in_array( 'has-featured-video', $classes );

?><div id="post-<?php the_ID(); ?>" class="<?php echo esc_attr( join( ' ', $classes ) ) ?>">
	<?php get_template_part( 'partials/tile/thumbnail' ); ?>
	<div class="post-details">
		<div class="post-date">
			<?php the_date(); ?>
		</div>
		<?php get_template_part( 'partials/tile/title' ); ?>
	</div>

	<?php if( true === $is_video ): ?>
		<p class="type">
			<svg width="7" height="12">
				<path d="M7 6L.25 11.196V.804L7 6z"/>
			</svg>
			video
		</p>
	<?php endif; ?>
</div>

<?php

$classes = get_post_class( 'post-tile' );
$is_video = in_array( 'format-video', $classes ) || in_array( 'has-featured-video', $classes );

?><div data-post-id="post-<?php the_ID(); ?>" class="<?php echo esc_attr( join( ' ', $classes ) ) ?>">
	<?php get_template_part( 'partials/tile/thumbnail' ); ?>
	<div class="post-details">
		<div class="post-date">
			<?php the_date(); ?>
		</div>
		<?php get_template_part( 'partials/tile/title' ); ?>
	</div>
</div>

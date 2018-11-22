<div style="display: flex">
	<div style="width: 300px">
		<?php get_template_part( 'partials/featured-media' ); ?>
	</div>
	<div>
		<div>podcast</div>
		<h1><?php the_title(); ?></h1>
		<div>
			<?php echo esc_html( ee_get_episodes_count() ); ?> episodes
		</div>
	</div>
</div>

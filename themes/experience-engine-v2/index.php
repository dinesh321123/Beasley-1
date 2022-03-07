<?php get_header(); ?>

<div>
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<?php get_template_part( 'partials/tile', get_post_type() ); ?>
	<?php endwhile; ?>

	<?php ee_load_more(); ?>
</div>

<?php get_footer(); ?>

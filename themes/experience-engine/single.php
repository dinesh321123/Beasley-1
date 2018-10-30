<?php get_header(); ?>

<?php the_post(); ?>

<div>
	<?php get_template_part( 'partials/show-information' ); ?>
	<?php get_template_part( 'partials/show-navigation' ); ?>

	<?php get_template_part( 'partials/featured-media' ); ?>
	<h1><?php the_title(); ?></h1>

	<div>
		<?php the_content(); ?>
	</div>
</div>

<?php get_footer(); ?>
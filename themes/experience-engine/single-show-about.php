<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/show/header' ); ?>

	<?php ee_the_subtitle( 'About' ); ?>
	<div>
		<?php the_content(); ?>
	</div>
</div>

<?php get_footer(); ?>

<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/show/header' ); ?>
	<?php get_template_part( 'partials/show/featured' ); ?>
	<?php get_template_part( 'partials/show/favorites' ); ?>
	<?php get_template_part( 'partials/show/recent' ); ?>
</div>

<?php get_footer(); ?>
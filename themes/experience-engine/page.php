<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="content-wrap">
		<h1><?php the_title(); ?></h1>
	</div>

	<div class="content-wrap">
		<?php get_template_part( 'partials/content/meta' ); ?>
		<?php the_content(); ?>
	</div>
</div>

<?php get_footer(); ?>
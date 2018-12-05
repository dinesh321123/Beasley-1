<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/show/header' ); ?>

	<div class="content-wrap">
		<h1><?php the_title(); ?></h1>
	</div>

	<div class="content-wrap">
		<div>
			<?php get_template_part( 'partials/content/meta' ); ?>
			<?php get_template_part( 'partials/featured-media' ); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>

<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/show/header' ); ?>

	<div class="content-wrap">
		<?php ee_the_subtitle( 'About' ); ?>
		<?php the_content(); ?>
	</div>
</div>

<?php get_footer(); ?>

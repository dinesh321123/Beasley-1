<?php
/**
 * Template Name: Full Bleed
 */
?>

<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/page/header' ); ?>

	<div class="content-wrap">
		<?php get_template_part( 'partials/page/description' ); ?>
	</div>
</div>

<?php get_footer(); ?>

<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/page/header' ); ?>

	<div class="entry-content content-wrap">
		<?php get_template_part( 'partials/page/description' ); ?>
		<?php get_template_part( 'partials/footer/common', 'description' ); ?>
		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
	</div>
</div>

<?php get_footer(); ?>

<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/show/header' ); ?>
	<h1><?php the_title(); ?></h1>

	<div class="content-wrap">
		<div>
			<?php get_template_part( 'partials/content/meta' ); ?>
			<?php get_template_part( 'partials/featured-media' ); ?>

			<?php ee_the_content_with_ads(); ?>

			<?php get_template_part( 'partials/content/categories' ); ?>
			<?php get_template_part( 'partials/content/tags' ); ?>
		</div>

		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
	</div>

	<?php get_template_part( 'partials/related-articles' ); ?>
</div>

<?php get_footer(); ?>
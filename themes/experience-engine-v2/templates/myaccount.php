<?php
/**
 * Template Name: My Account
 */
?>

<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="post-info">
		<?php if ( bbgi_featured_image_layout_is( null, 'top' ) ) : ?>
			<?php get_template_part( 'partials/featured-media' ); ?>
		<?php endif; ?>

		<h1><?php the_title(); ?></h1>
	</header>

	<div class="entry-content content-wrap">
		<?php get_template_part( 'partials/page/description' ); ?>
		<?php echo do_shortcode('[cancel_account]'); ?>
		<?php get_template_part( 'partials/footer/common', 'description' ); ?>
		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
	</div>
</div>

<?php get_footer(); ?>

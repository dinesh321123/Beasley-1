<?php
/**
 * Template Name: My Account
 */
?>

<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="post-info">
		<h1><?php the_title(); ?></h1>
	</header>

	<div class="entry-content content-wrap">
		<div class="description">
			<div class="cancel_account">
				<?php echo do_shortcode('[cancel_account]'); ?>
			</div>
			<?php the_content(); ?>
		</div>

		<?php get_template_part( 'partials/footer/common', 'description' ); ?>
		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
	</div>
</div>

<?php get_footer(); ?>

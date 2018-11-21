<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'partials/show/header' ); ?>
	<?php the_category(); ?>
	<h1><?php the_title(); ?></h1>

	<div>
		<div>
			<div>
				<span><?php the_author_meta( 'display_name' ); ?></span>
				<span><?php ee_the_date(); ?></span>
				<?php ee_the_share_buttons( get_permalink(), get_the_title() ); ?>
			</div>

			<?php get_template_part( 'partials/featured-media' ); ?>

			<?php the_content(); ?>

			<div>
				<?php the_tags( '<span>Tags</span>' ); ?>
			</div>
		</div>

		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
	</div>

	<?php get_template_part( 'partials/related-articles' ); ?>
</div>

<?php get_footer(); ?>
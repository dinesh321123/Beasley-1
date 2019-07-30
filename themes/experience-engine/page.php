<?php get_header(); ?>

<?php the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="post-info">
	    <?php if ( bbgi_featured_image_layout_is( null, 'top' ) ) : ?>
            <?php get_template_part( 'partials/featured-media' ); ?>
        <?php endif; ?>

		<h1><?php the_title(); ?></h1>
		<div class="post-meta">
			<?php get_template_part( 'partials/content/meta', 'page' ); ?>
		</div>
	</header>

	<div class="entry-content content-wrap">
		<div class="description">
		    <?php if ( bbgi_featured_image_layout_is( null, 'inline' ) ) : ?>
                <?php get_template_part( 'partials/featured-media' ); ?>
            <?php endif; ?>
			<?php the_content(); ?>
		</div>

		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
	</div>
</div>

<?php get_footer(); ?>

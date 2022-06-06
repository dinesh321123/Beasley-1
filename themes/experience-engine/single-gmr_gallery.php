<?php

ee_setup_gallery_view_metadata(); // must be called before get_header();
get_header();

ee_switch_to_article_blog();
the_post();

?><div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( bbgi_featured_image_layout_is( null, 'top' ) ) : ?>
		<header class="post-info">
			<?php get_template_part( 'partials/featured-media', 'autoheight' ); ?>
		</header>
	<?php endif; ?>

	<?php if ( bbgi_featured_image_layout_is( null, 'top' ) ) : ?>
		<div class="content-wrap">
			<?php get_template_part( 'partials/show/header' ); ?>
		</div>
	<?php else : ?>
		<?php get_template_part( 'partials/show/header' ); ?>
	<?php endif; ?>

	<header class="post-info">
		<h1>
			<?php the_title(); ?>
		</h1>

		<div class="post-meta">
			<?php get_template_part( 'partials/content/meta' ); ?>
		</div>
	</header>

	<div class="entry-content content-wrap">
		<div class="description">
			<?php if ( bbgi_featured_image_layout_is( null, 'inline' ) ) : ?>
				<?php get_template_part( 'partials/featured-media', 'autoheight' ); ?>
			<?php endif; ?>

			<?php the_content(); ?>
			<?php get_template_part( 'partials/gallery/listicle' ); ?>

			<?php get_template_part( 'partials/footer/common', 'description' ); ?>
			<?php get_template_part( 'partials/content/categories' ); ?>
			<?php get_template_part( 'partials/content/tags' ); ?>
		</div>

		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
	</div>
</div><?php

restore_current_blog();
get_footer();

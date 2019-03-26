<?php 

ee_setup_gallery_view_metadata(); // must be called before get_header(); 
get_header();

ee_switch_to_article_blog();
the_post(); 

?><div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
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
			<?php get_template_part( 'partials/featured-media', 'autoheight' ); ?>

			<?php the_content(); ?>
			<?php get_template_part( 'partials/gallery/listicle' ); ?>

			<?php get_template_part( 'partials/content/categories' ); ?>
			<?php get_template_part( 'partials/content/tags' ); ?>
		</div>

		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
	</div>
</div><?php

restore_current_blog();
get_footer();

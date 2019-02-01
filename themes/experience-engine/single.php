<?php

get_header();

ee_switch_to_article_blog();
the_post(); 

?><div id="post-<?php the_ID(); ?>" <?php post_class( 'single' ); ?>>
	<?php get_template_part( 'partials/show/header' ); ?>

	<header class="post-info">
		<h1>
			<?php the_title(); ?>
		</h1>

		<?php if( is_singular( 'post' ) ) : ?>
			<div class="post-meta">
				<?php get_template_part( 'partials/content/meta' ); ?>
			</div>
		<?php endif; ?>
	</header>

	<div class="entry-content content-wrap">
		<div class="description">
			<?php get_template_part( 'partials/featured-media' ); ?>
			<?php ee_the_content_with_ads(); ?>

			<?php if( is_singular( 'post' ) ) : ?>
				<div class="profile">
					<?php echo get_the_author_meta( 'description' ); ?>
				</div>
			<?php endif; ?>
				
			<?php get_template_part( 'partials/content/categories' ); ?>
			<?php get_template_part( 'partials/content/tags' ); ?>
		</div>

		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
	</div>
	
	<?php get_template_part( 'partials/related-articles' );	?>
</div><?php

restore_current_blog();
get_footer();

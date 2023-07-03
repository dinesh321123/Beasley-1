<?php
get_header();

ee_switch_to_article_blog();
the_post();

?><div id="post-<?php the_ID(); ?>" <?php post_class( 'single' ); ?>>
	<div class="ads-image" style="text-align:center; margin-bottom:30px;">
		<img src="<?php echo get_template_directory_uri().'/assets/images/ad-static-1.jpg'; ?>" alt="" style="width:50%;" />
	</div>
	<div class="container">
		<div class="content-wrap">
			<div class="section-head-container">
				<h2 class="section-head">
					<span class="bigger">
					<?php
						$primary_category = ee_get_primary_category(get_the_ID());

						if (!empty($primary_category)) {
							echo $primary_category->name;
						} else {
							$categories = get_the_category();
							if (!empty($categories)) {
								echo $categories[0]->name;
							}
						}
					?>
					</span>
				</h2>
			</div>
		</div>
	</div>

	<div class="entry-content content-wrap">
		<div class="description">

		<h1> <?php the_title(); ?> </h1>

		<?php if (is_singular("post")): ?>
			<div class="post-meta">
				<?php get_template_part("partials/content/articles/meta"); ?>
			</div>
		<?php endif; ?>

			<?php if ( bbgi_featured_image_layout_is( null, 'inline' ) ) : ?>
				<?php get_template_part( 'partials/featured-media' ); ?>
			<?php endif; ?>

			<?php ee_the_content_with_ads(); ?>

			<?php if ( is_singular( 'post' ) ) : ?>
				<div class="profile">
					<?php echo get_the_author_meta( 'description' ); ?>
				</div>
				<?php echo ee_render_trending_articles('embed_bottom'); ?>
			<?php endif; ?>

			<?php get_template_part( 'partials/footer/common', 'description' ); ?>
			
			<?php get_template_part( 'partials/footer/newsletterSignupForm', 'nsf' ); ?>
			<?php get_template_part( 'partials/content/categories' ); ?>
			<?php if (!is_singular("post")): ?>
			<?php get_template_part( 'partials/content/tags' ); ?>
			<?php endif; ?>
			<?php if (is_singular("post")): ?>
				<div class="post-meta footer-post-meta-container">
					<?php get_template_part("partials/content/articles/footer-meta"); ?>
				</div>
			<?php endif; ?>
		</div>

		<aside class="ad -sticky">
			<?php //get_template_part("partials/content/articles/ads"); ?>
			<div class="ads-image" style="text-align:center;">
				<img src="<?php echo get_template_directory_uri().'/assets/images/ad-static-2.jpg'; ?>" alt="" />
			</div>
		</aside>
	</div>

	<?php get_template_part( 'partials/related-articles' );	?>
</div>
<?php

restore_current_blog();
get_footer();

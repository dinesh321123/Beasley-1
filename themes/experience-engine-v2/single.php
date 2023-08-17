<?php
get_header();

ee_switch_to_article_blog();
the_post();
?>
<div id="post-<?php the_ID(); ?>" <?php post_class( 'single' ); ?>>
	<?php if ( ee_get_current_show() ) : ?>
		<?php if ( bbgi_featured_image_layout_is( null, 'top' ) ) : ?>
			<header class="post-info">
				<?php get_template_part( 'partials/featured-media' ); ?>
			</header>
		<?php endif; ?>
		<div class="content-wrap">
			<?php get_template_part( 'partials/show/custom-header' ); ?>
		</div>
	<?php endif; ?>
	<header class="post-info">
		<?php if ( ee_get_current_show() ) : ?>
			<?php if ( bbgi_featured_image_layout_is( null, 'poster' ) ) : ?>
				<?php get_template_part( 'partials/featured-media' ); ?>
			<?php endif; ?>
		<?php elseif ( bbgi_featured_image_layout_is( null, 'top' ) || bbgi_featured_image_layout_is( null, 'poster' ) ) : ?>
			<?php get_template_part( 'partials/featured-media' ); ?>
		<?php endif; ?>
	</header>

	<div class="article-inner-container">
		<div class="entry-content content-wrap">
			<div class="description">
				<h1> <?php the_title(); ?> </h1>
				<?php if (is_singular("post")): ?>
					<div class="post-meta">
						<?php get_template_part("partials/content/articles/meta", null,array('show'=>array('date'))); ?>
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
				<div class="post-meta footer-post-meta-container">
					<?php get_template_part("partials/content/articles/meta",null,array('show'=>array('tags','category'))); ?>
				</div>
			</div>
			<?php get_template_part("partials/content/articles/ads"); ?>
		</div>
		<?php get_template_part( 'partials/related-articles' );	?>
	</div>
</div>
<?php
restore_current_blog();
get_footer();

<?php
get_header();

ee_switch_to_article_blog();
the_post();

?><div id="post-<?php the_ID(); ?>" <?php post_class( 'single' ); ?>>

	<?php if ( ee_get_current_show() ) : ?>
		<?php if ( bbgi_featured_image_layout_is( null, 'top' ) ) : ?>
			<header class="post-info">
				<?php get_template_part( 'partials/featured-media' ); ?>
			</header>
		<?php endif; ?>

		<div class="content-wrap">
			<?php get_template_part( 'partials/show/header' ); ?>
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
			<?php get_template_part( 'partials/content/tags' ); ?>
		</div>

		<aside class="ad -sticky">
			<?php get_template_part("partials/content/articles/trending-articles"); ?>
			<?php get_template_part("partials/content/articles/ads"); ?>
		</aside>
	</div>

	<?php get_template_part( 'partials/related-articles' );	?>
</div>
<style>
	aside.ad.-sticky ul.article-sidebar-listing {
		margin-bottom: 30px;
	}
	aside.ad.-sticky .content-wrap {
		padding: 0;
	}
	aside.ad.-sticky .content-wrap .section-head {
		font-size: 14px;
	}
	aside.ad.-sticky ul.article-sidebar-listing li a {
		font-size: 15px;
		line-height: 20px;
	}


	.post-trending-articles .post-tile.post {
		text-align: center;
		padding: 0px;
		margin: 10px;
		border-bottom: 1px solid #ccc;
		justify-content: center;
	}

	.post-trending-articles .post-tile.post p {
		margin-bottom: 10px;
	}
	aside.ad.-sticky .post-trending-articles .section-head:after{
		display:block;
	}
	aside.ad.-sticky .post-trending-articles .section-head{
		text-align:left;
	}

	.post-trending-articles .section-head:after{
		display: none;
	}

	.post-trending-articles .section-head{
		text-align:center;
	}

	.post-trending-articles.content-wrap.mobile-article {
		padding: 40px 0px;
		margin: 20px 0px 20px 0px;
		border-top: 2px solid var(--brand-primary);
		border-bottom: 2px solid var(--brand-primary);
	}

	.post-tile.post:first-child,
	.post-tile.post:last-child{
		border-bottom: none;
	}
	.mobile-article .post-tile.post img{
		display:none;
	}

	.entry-content .description .post-trending-articles .post-tile.post img{
		display:none;
	}

</style>
<?php

restore_current_blog();
get_footer();

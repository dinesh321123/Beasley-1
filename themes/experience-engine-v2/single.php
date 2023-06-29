<?php
get_header();

ee_switch_to_article_blog();
the_post();

?><div id="post-<?php the_ID(); ?>" <?php post_class( 'single' ); ?>>
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
			<?php if (is_singular("post")): ?>
				<div class="post-meta">
					<?php get_template_part("partials/content/articles/meta"); ?>
				</div>
			<?php endif; ?>
			<?php get_template_part( 'partials/footer/newsletterSignupForm', 'nsf' ); ?>
			<?php get_template_part( 'partials/content/categories' ); ?>
			<?php get_template_part( 'partials/content/tags' ); ?>
		</div>

		<aside class="ad -sticky">
			<?php get_template_part("partials/content/articles/ads"); ?>
		</aside>
	</div>

	<?php get_template_part( 'partials/related-articles' );	?>
</div>
<style>

	.img-box img {
		width: 100%;
	}
	.img-box {
		margin-bottom: 20px;
	}
	aside.ad.-sticky ul.article-sidebar-listing li a {
		color: #000;
		font-size: 16px;
		line-height: 18px;
		display: block;
		text-decoration: none;
	}
	aside.ad.-sticky ul.article-sidebar-listing li,ul.articleiinline-listing li {
		border-bottom: 1px solid #c1c0c0;
		padding: 15px 0;
		list-style-type: none;
	}
	aside.ad.-sticky ul.article-sidebar-listing {
		margin: 0;
		padding: 0;
	}
	ul.articleiinline-listing {
		margin: 0;
		padding: 0;
		list-style-type: none;
		display: flex;
		flex-wrap: wrap;
	}
	ul.articleiinline-listing li {
		width: calc(50% - 30px);
	}
	ul.articleiinline-listing .article-img img {
		width: 100%;
	}
	ul.articleiinline-listing .article-img {
		margin-bottom: 20px;
	}
	ul.articleiinline-listing li a {
		color: #000;
		font-size: 16px;
		line-height: 18px;
		display: block;
		text-decoration: none;
	}

	.section-head:after {
		background-color: #e12e21;
		height: 2px;
	}
	.articles-meta {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 30px;
	}
	p.author-meta-name {
		margin: 0;
		font-size: 14px;
		line-height: 18px !important;
	}
	p.author-meta-name strong {
		font-size: 14px;
		line-height: 18px;
	}
	.author-meta time {
		font-size: 14px;
		font-weight: 700;
		line-height: 18px;
	}
	.description p {
		font-size: 16px;
		font-weight: 400;
	}
	.description p:empty {
		display: none !important;
	}
	ul.articleiinline-listing ul {
		margin: 0;
		padding: 0;
	}
	ul.articleiinline-listing ul li {
		width: 100%;
	}
	ul.articleiinline-listing > li {
		margin-right: 30px;
		border: none;
	}
	ul.articleiinline-listing li:last-child {
		border: none;
	}
	ul.articleiinline-listing ul li:last-child {
		border: none;
	}

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

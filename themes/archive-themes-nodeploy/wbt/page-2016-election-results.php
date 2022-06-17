<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header();
?>

<div class="container">
	<section class="content">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article" itemscope="" itemtype="http://schema.org/BlogPosting">
			<header class="article__header">
				<h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
				<?php get_template_part( 'partials/social-share' ); ?>
			</header>
			<section class="article__content" itemprop="articleBody">
				<?php the_content(); ?>

				<iframe src="https://interactives.ap.org/2016/general-election/?SITE=WBTAMELN" class="ap-embed" width="100%" height="900" style="border: 1px solid #eee;">
					Your browser does not support the <code>iframe</code> HTML tag. Try viewing this in a modern browser like Chrome, Safari, Firefox or Internet Explorer 9 or later.
				</iframe>

			</section>
		</article>
	</section>

	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>

<?php the_post(); ?>

<div class="container">

	<?php if ( has_post_thumbnail() ): ?>
		<div class="article__thumbnail" style='background-image: url(<?php gm_post_thumbnail_url( 'gm-article-thumbnail' ); ?>)'>
			<?php image_attribution(); ?>
		</div>
	<?php endif; ?>

	<section class="content">

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'article cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

			<header class="article__header">
				<h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
				<?php get_template_part( 'partials/social-share' ); ?>
			</header>

			<section class="article__content" itemprop="articleBody">

				<?php the_content(); ?>

			</section>

		</article>			

	</section>

</div>

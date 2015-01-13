<?php the_post(); ?>

<div class="container">

	<?php if ( has_post_thumbnail() ): ?>
		<div class="article__thumbnail" style='background-image: url(<?php gm_post_thumbnail_url( 'full' ); ?>)'></div>
	<?php endif; ?>

	<section class="content">

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'article cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

			<header class="article__header">
				<h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
				<a class="icon-facebook social__link popup" href="http://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>&title=<?php echo urlencode( get_the_title() ); ?>"></a>
				<a class="icon-twitter social__link popup" href="http://twitter.com/home?status=<?php echo urlencode( get_the_title() ); ?>+<?php echo urlencode( get_permalink() ); ?>"></a>
				<a class="icon-google-plus social__link popup" href="https://plus.google.com/share?url=<?php echo urlencode( get_permalink() ); ?>"></a>
			</header>

			<section class="article__content" itemprop="articleBody">

				<?php the_content(); ?>

			</section>

		</article>			

	</section>

</div>

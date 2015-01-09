<?php the_post(); ?>

<div class="container">

	<?php if ( has_post_thumbnail() ): ?>
		<div class="article__thumbnail" style='background-image: url(<?php gm_post_thumbnail_url( 'full' ); ?>)'></div>
	<?php endif; ?>

	<section class="content">

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'article cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

			<header class="article__header">
				<h2 class="article__title" itemprop="headline"><?php the_title(); ?></h2>
				<a class="icon-facebook social__link" href="http://www.facebook.com/sharer/sharer.php?u=[URL]&title=[TITLE]"></a>
				<a class="icon-twitter social__link" href="http://twitter.com/home?status=[TITLE]+[URL]"></a>
				<a class="icon-google-plus social__link" href="https://plus.google.com/share?url=[URL]"></a>
			</header>

			<section class="article__content" itemprop="articleBody">

				<?php the_content(); ?>

			</section>

		</article>			

	</section>

</div>

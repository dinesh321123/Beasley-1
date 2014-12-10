<div class="container">

	<?php if ( has_post_thumbnail() ) {

			the_post_thumbnail( 'full', array( 'class' => 'single__featured-img' ) );

		} else {
			echo 'boop';
		}
	?>

	<?php while ( have_posts() ) : the_post(); ?>

			<section class="content">

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

					<div class="ad__inline--right">
						<img src="http://placehold.it/300x250&amp;text=inline ad">
					</div>

					<header class="entry__header">

						<time class="entry__date" datetime="<?php echo get_the_time(); ?>"><?php the_date('F j'); ?></time>
						<h2 class="entry__title" itemprop="headline"><?php the_title(); ?></h2>
						<a class="icon-facebook social-share-link" href="http://www.facebook.com/sharer/sharer.php?u=[URL]&title=[TITLE]"></a>
						<a class="icon-twitter social-share-link" href="http://twitter.com/home?status=[TITLE]+[URL]"></a>
						<a class="icon-google-plus social-share-link" href="https://plus.google.com/share?url=[URL]"></a>

					</header>

					<section class="entry-content" itemprop="articleBody">

						<?php the_content(); ?>

					</section>

					<footer class="entry__footer">
						<?php

							// If comments are open or we have at least one comment, load up the comment template.
							if ( comments_open() || get_comments_number() ) {
								comments_template();
							}

						?>

					</footer>

				</article>

			</section>

	<?php endwhile; ?>

</div>

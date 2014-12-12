<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<section class="content">

				<?php
					while ( have_posts() ) : the_post(); ?>

						<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

							<header class="entry-header">

								<h2 class="entry-title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

							</header>

							<section class="entry-content" itemprop="articleBody">

								<?php the_content(); ?>

							</section>

							<footer class="entry-footer">

								<div class="entry-author">
									<div class="entry-author--img">
										<img src="http://placecreature.com/40/40">
									</div>
									<div class="entry-author--meta">
										<div class="entry-author--name"><?php the_author_posts_link(); ?></div>
										<time datetime="<?php the_time( 'c' ); ?>" class="entry-date"><?php the_time( 'M. j, Y' ); ?></time>
									</div>
								</div>

								<div class="entry-type">

									<div class="entry-type--<?php greatermedia_post_formats(); ?>"><?php greatermedia_post_formats(); ?></div>

								</div>

								<div class="entry-comments">

									<div class="entry-comments--count">

										<a href="<?php the_permalink(); ?>#comments"><?php comments_number( '0', '1', '%' ); ?></a>

									</div>

								</div>

							</footer>

						</article>

					<?php endwhile; ?>

			</section>

		</div>

	</main>

<?php get_footer();
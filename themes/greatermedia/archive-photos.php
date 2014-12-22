<?php
/**
 * Show post type archive template
 *
 */

get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<section class="content">

				<?php if ( have_posts() ) : ?>

					<div class="gallery-grid">

						<?php while ( have_posts() ) : the_post(); ?>

							<div id="post-<?php the_ID(); ?>" class="gallery-grid__column" >

								<div class="gallery-grid__thumbnail">
									<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( array( 254, 186 ) ); // todo Image Size 254x186 ?></a>
								</div>

								<div class="gallery-grid__meta">
									<h3 class="gallery-grid__title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h3>
								</div>

							</div>

						<?php endwhile; ?>

					</div>

					<div class="posts-pagination">

						<div class="posts-pagination--previous"><?php next_posts_link( '<i class="fa fa-angle-double-left"></i>Previous' ); ?></div>
						<div class="posts-pagination--next"><?php previous_posts_link( 'Next<i class="fa fa-angle-double-right"></i>' ); ?></div>

					</div>

				<?php else : ?>

					<article id="post-not-found" class="hentry cf">

						<header class="article-header">

							<h1><?php _e( 'Oops, Nothing Found!', 'greatermedia' ); ?></h1>

						</header>

						<section class="entry-content">

							<p>We can't find the photos you are looking for. Try double checking things.</p>

						</section>

					</article>

				<?php endif; ?>

			</section>

		</div>

	</main>

<?php get_footer(); ?>
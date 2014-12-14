<?php
/**
 * The main template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<section class="content">

				<h2 class="content__heading">Latest from WMMR</h2>

				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

						<header class="entry-header">

							<time datetime="<?php the_time( 'c' ); ?>" class="entry__date"><?php the_time( 'j F' ); ?></time>

							<h2 class="entry__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

						</header>

						<section class="entry__content" itemprop="articleBody">

							<?php the_content(); ?>

						</section>

						<footer class="entry__footer">

							<?php
								$category = get_the_category();

								if( isset( $category[0] ) ){
									echo '<a href="' . esc_url( get_category_link($category[0]->term_id ) ) . '" class="entry__footer--category">' . esc_html( $category[0]->cat_name ) . '</a>';
								}
							?>

						</footer>

					</article>

				<?php endwhile; ?>

					<div class="posts-pagination">

						<div class="posts-pagination--previous"><?php next_posts_link( '<i class="fa fa-angle-double-left"></i>Previous' ); ?></div>
						<div class="posts-pagination--next"><?php previous_posts_link( 'Next<i class="fa fa-angle-double-right"></i>' ); ?></div>

					</div>

				<?php else : ?>

					<article id="post-not-found" class="hentry cf">

						<header class="entry__header">

							<h2 class="entry__title"><?php _e( 'Oops, Post Not Found!', 'greatermedia' ); ?></h2>

						</header>

						<section class="entry__content">

							<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'greatermedia' ); ?></p>

						</section>

					</article>

				<?php endif; ?>

			</section>

		</div>

	</main>

<?php get_footer(); ?>
<?php
/**
 * The front page template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header();

get_template_part( 'partials/frontpage', 'featured' );
// get_template_part( 'partials/frontpage', 'highlights' );
?>

	<main class="main" role="main">

		<div class="container">

			<section class="content">

				<h2 class="content__heading">Latest from WMMR</h2>

				<?php

				$args = array(
					'post_type' => array(
						'post', 'episode', 'tribe_events'
					),
				);

				$query = new WP_Query( $args );

				if ( $query->have_posts() ) : while (  $query->have_posts() ) :  $query->the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

						<?php
						if ( has_post_thumbnail() ) {
							if ( 'tribe_events' === get_post_type() ) { ?>
								<section class="entry__meta">

									<time datetime="<?php the_time( 'c' ); ?>" class="entry__date"><?php the_time( 'j F' ); ?></time>

									<h2 class="entry__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

									<div class="entry__excerpt">
										<?php the_excerpt(); ?>
									</div>

									<ul class="entry__event--details">
										<li class="entry__event--item"><?php echo tribe_get_start_time(); ?></li>
										<li class="entry__event--item"><?php echo tribe_get_venue(); ?></li>
										<li class="entry__event--item"><?php _e( '$', 'greatermedia' ); ?><?php echo tribe_get_cost(); ?></li>
									</ul>

								</section>

								<section class="entry__thumbnail entry__thumbnail--events">

									<a href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail( 'gm-article-thumbnail' ); ?>
									</a>

								</section>

							<?php } else { ?>
								<section class="entry__meta">

									<time datetime="<?php the_time( 'c' ); ?>" class="entry__date"><?php the_time( 'j F' ); ?></time>

									<h2 class="entry__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

								</section>

								<section class="entry__thumbnail <?php greatermedia_post_formats(); ?>">

									<a href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail( 'gm-article-thumbnail' ); ?>
									</a>

								</section>

							<?php }
						} else { ?>
							<section class="entry__meta--fullwidth">

								<time datetime="<?php the_time( 'c' ); ?>" class="entry__date"><?php the_time( 'j F' ); ?></time>

								<h2 class="entry__title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

							</section>
						<?php } ?>

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

						<header class="article-header">

							<h1><?php _e( 'Oops, Post Not Found!', 'greatermedia' ); ?></h1>

						</header>

						<section class="entry-content">

							<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'greatermedia' ); ?></p>

						</section>

					</article>

				<?php endif; ?>

			</section>

		</div>

	</main>

<?php get_footer(); ?>
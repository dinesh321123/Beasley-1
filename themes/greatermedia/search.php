<?php
/**
 * Search Results template file
 *
 * @package Greater Media
 * @since   0.1.0
 *
 * @todo this template file still needs to be layed out according to the design
 */

get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<section class="content">

				<?php
					$search_count = new WP_Query( 's=' . $s . '&posts_per_page=-1' );
					$key = esc_html( $s, 1 );
					$count = $search_count->post_count;
					
					$search_query = sanitize_text_field( get_search_query() );
					$keyword_post_id = intval( get_post_with_keyword( $search_query ) );
					if( $count == 0 && $keyword_post_id != 0 ) {
						$count = 1;
					}

					echo '<h2 class="search__results--count">' . $count . ' ';
					_e( 'Results Found', 'greatermedia' );
					echo '</h2>';

					wp_reset_postdata();

				?>
				<?php if( $keyword_post_id != 0 ): ?>
				<h3 class="search__keyword"><?php printf( __( 'Keyword: %s', 'greatermedia' ), '<span class="search__keyword--term">' . get_search_query() . '</span>' ); ?></h3>

				<div class="keyword__search--results">

					<?php do_action( 'keyword_search_result' ); ?>

				</div>

				<?php endif; ?>
				
				<?php if( $count != 1 || $keyword_post_id == 0 ): ?>
				<h2 class="search__title"><?php _e( 'Relevant Search Results', 'greatermedia' ); ?></h2>
				<?php endif; ?>

				<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
					$title = get_the_title();
					$keys= explode(" ",$s);
					$title = preg_replace('/('.implode('|', $keys) .')/iu', '<span class="search__result--term">\0</span>', $title);
					?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'search__result' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

					<time datetime="<?php the_time( 'c' ); ?>" class="search__result--date"><?php the_time( 'M j, Y' ); ?></time>

					<h3 class="search__result--title"><a href="<?php the_permalink(); ?>"><?php echo $title ?></a></h3>

				</article>
				<?php endwhile; ?>

					<div class="posts-pagination">

						<div class="posts-pagination--previous"><?php next_posts_link( '<i class="fa fa-angle-double-left"></i>Previous' ); ?></div>
						<div class="posts-pagination--next"><?php previous_posts_link( 'Next<i class="fa fa-angle-double-right"></i>' ); ?></div>

					</div>

				<?php else : ?>
					<?php if( $keyword_post_id == 0 ): ?>
					<article id="post-not-found" class="hentry cf">

						<header class="article-header">

							<h1><?php _e( 'Oops, Post Not Found!', 'greatermedia' ); ?></h1>

						</header>

						<section class="entry-content">

							<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'greatermedia' ); ?></p>

						</section>

					</article>
					<?php endif; ?>

				<?php endif; ?>

			</section>

		</div>

	</main>

<?php get_footer();
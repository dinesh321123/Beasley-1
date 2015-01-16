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
				$count = $wp_query->found_posts;
				$search_query = sanitize_text_field( get_search_query() );
				$keyword_post_id = intval( get_post_with_keyword( $search_query ) );
				if( $keyword_post_id != 0 ) {
					$count += 1;
				}

				// echo '<h2 class="search__results--count">' . intval( $count ) . ' ';
				// _e( 'Results Found', 'greatermedia' );
				// echo '</h2>';

				$term_label = 'Keyword:';
			?>


			<?php if( $keyword_post_id != 0 ): ?>
				<h3 class="search__keyword"><?php printf( __( '%s %s', 'greatermedia' ), $term_label, '<span class="search__keyword--term">' . get_search_query() . '</span>' ); ?></h3>

				<div class="keyword__search--results">
					<?php do_action( 'keyword_search_result' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( have_posts() ) :

				$term_label = intval($count) . ' Search Results for:'; ?>

				<h3 class="search__keyword"><?php printf( __( '%s %s', 'greatermedia' ), $term_label, '<span class="search__keyword--term">' . get_search_query() . '</span>' ); ?></h3>

				<?php get_template_part( 'partials/loop', 'search' ); ?>
				<?php greatermedia_load_more_button( array( 'partial_slug' => 'partials/loop-search', 'auto_load' => true ) ); ?>
				<?php get_template_part( 'partials/pagination' ); ?>

			<?php else : ?>
				<?php if( $keyword_post_id == 0 ): ?>
					<article id="post-not-found" class="hentry cf">

						<header class="article-header">

							<h1><?php _e( 'No Results Found!', 'greatermedia' ); ?></h1>

						</header>

						<section class="entry-content">

							<p><?php _e( 'Try searching for something else, or click one of the links above.', 'greatermedia' ); ?></p>

						</section>

					</article>
				<?php endif; ?>

			<?php endif; ?>

		</section>

	</div>

</main>

<?php get_footer();
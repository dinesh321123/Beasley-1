<?php
/**
 * Archive template file
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

				<h2 class="sponsors__heading"><?php _e( 'Advertisers', 'greatermedia' ); ?></h2>

				<div class="sponsors">

					<?php

					$advertiser_args = array(
						'post_type'         => GMR_ADVERTISER_CPT,
						'orderby'           => 'menu_order date',
						'order'             => 'ASC',
						'posts_per_page'    => 30
					);

					$advertiser_query = new WP_Query( $advertiser_args );
					$i = 1;

					echo '<div class="sponsors__row">';
					if ( $advertiser_query->have_posts() ) : while ( $advertiser_query->have_posts() ) : $advertiser_query->the_post();

						?>

						<?php get_template_part( 'partials/loop', 'advertiser' );

						if ( $i % 2 == 0 ) {
							echo '</div><div class="sponsors__row">';
						}

						$i++;

						?>

					<?php endwhile; ?>

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
					</div>

				</div>

			</section>

		</div>

	</main>

<?php get_footer(); ?>
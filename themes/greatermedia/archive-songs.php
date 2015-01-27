<?php
/**
 * Songs archive template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<section class="content">

				<h2 class="content__heading">
					Recently played
					<?php $call_sign = get_query_var( GMR_LIVE_STREAM_CPT ); ?>
					<?php if ( ! empty( $call_sign ) && ! is_numeric( $call_sign ) ) : ?>
						on <?php echo esc_html( $call_sign ); ?>
					<?php endif; ?>
				</h2><?php

				if ( have_posts() ) : ?>

					<?php get_template_part( 'partials/loop', 'songs' ); ?>
					<?php greatermedia_load_more_button( array( 'partial_slug' => 'partials/loop-songs', 'auto_load' => false ) ); ?>

				<?php else : ?>

					<article id="post-not-found" class="hentry cf">

						<header class="article-header">
							<h1><?php _e( 'Oops, Songs Not Found!', 'greatermedia' ); ?></h1>
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
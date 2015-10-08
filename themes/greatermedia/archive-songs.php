<?php
/**
 * Songs archive template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

	<div class="container">

		<section class="content">

			<h2 class="content__heading">
				Recently Played 
				<?php $call_sign = get_query_var( GMR_LIVE_STREAM_CPT ); ?>
				<?php if ( ! empty( $call_sign ) && ! is_numeric( $call_sign ) ) : ?>
					<?php
					$stream_query = new WP_Query( array(
						'post_type'           => GMR_LIVE_STREAM_CPT,
						'meta_key'            => 'call_sign',
						'meta_value'          => $call_sign,
						'posts_per_page'      => 1,
						'ignore_sticky_posts' => 1,
						'no_found_rows'       => true,
						'fields'              => 'ids',
					) );
					if ( $stream_query->have_posts() ) : $stream_query->the_post(); 
						$description = get_post_meta( get_the_ID(), 'description', true );
						if ( !empty( $description ) ){
							echo 'on ' . esc_html( $description );
						}else{
							echo 'on ' . esc_html( $call_sign );
						}
					endif;
					wp_reset_postdata();
					?>
				<?php endif; ?>
			</h2><?php

			if ( have_posts() ) : ?>

				<?php get_template_part( 'partials/loop', 'songs' ); ?>
				<?php greatermedia_load_more_button( array(
					'partial_slug' => 'partials/loop-songs',
					'auto_load'    => true
				) ); ?>

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

<?php get_footer(); ?>
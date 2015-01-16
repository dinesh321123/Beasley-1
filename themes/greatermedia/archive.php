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

				<div class="ad__inline--right desktop">
					<?php // 'desktop' is a variant, can call a 'mobile' variant elsewhere if we need it, but never the same variant twice ?>
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'desktop' ); ?>
				</div>

				<h2 class="content__heading">
					<?php $object = get_queried_object(); ?>
					Latest <?php echo ! empty( $object->labels->name ) ? esc_html( strtolower( $object->labels->name ) ) : ''; ?> from <?php bloginfo( 'name' ); ?>
				</h2>

				<?php if ( have_posts() ) :  ?>

					<?php if ( is_post_type_archive( 'songs' ) ) : ?>

					<ul class="song__archive">

					<?php

						while( have_posts() ) : the_post();

						$link = get_post_meta($post->ID, 'purchase_link', true);
						$artist = get_post_meta($post->ID, 'artist', true);

						echo '<li class="song__item icon-music">';
						if ( $link ) {
							echo '<a href="' . esc_url( $link ) . '">';
						}
						echo '<span class="song__title">' . get_the_title() . '</span>';
						if ( $link ) {
							echo '</a>';
						}
						echo '<span class="song__artist">' . esc_html( $artist ) . '</span>';
						echo '</li>';

						endwhile; ?>

					</ul>

					<?php else : ?>

						<?php get_template_part( 'partials/loop' ); ?>
						<?php greatermedia_load_more_button( array( 'partial_slug' => 'partials/loop', 'auto_load' => true ) ); ?>
						<?php get_template_part( 'partials/pagination' ); ?>

					<?php endif; ?>

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

				<div class="ad__inline--right mobile">
					<?php do_action( 'acm_tag_gmr_variant', 'mrec-body', 'mobile' ); ?>
				</div>

			</section>

		</div>

	</main>

<?php get_footer(); ?>
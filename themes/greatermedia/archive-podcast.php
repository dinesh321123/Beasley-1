<?php
/**
 * Closure post type archive template
 *
 */

get_header(); ?>

<div class="container">

	<section class="content">
		<header class="podcast__header">
			<h1 itemprop="headline">Podcasts</h1>
		</header>
		<?php if ( have_posts() ) : while ( have_posts() ): the_post(); ?>

			<?php get_template_part( 'partials/loop', 'podcast_archive' ); ?>

			<?php greatermedia_load_more_button( array(
				'partial_slug' => 'partials/loop',
				'partial_name' => 'podcast_archive',
				'auto_load'    => true
			) ); ?>

		<?php endwhile; ?>
		<?php else : ?>

			<article id="post-not-found" class="hentry cf">

				<header class="article-header">

					<h1><?php _e( 'No Podcasts Found!', 'greatermedia' ); ?></h1>

				</header>

			</article>

		<?php endif; ?>

	</section>

</div>

<?php get_footer(); ?>

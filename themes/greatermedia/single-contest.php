<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

<main class="main" role="main">

	<div class="container">

		<section class="content">

			<?php  if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'partials/contest', get_post_meta( $post->ID, 'contest_type', true ) ); ?>
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

		</section>

	</div>

</main>

<?php get_footer();

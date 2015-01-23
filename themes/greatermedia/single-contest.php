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

		<?php  if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>
		
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="contest__thumbnail" style="background-image:url(<?php gm_post_thumbnail_url( 'gmr-contest-thumbnail' ); ?>)">
						<?php image_attribution(); ?>
					</div>
				<?php endif; ?>

				<section class="content">
					<?php get_template_part( 'partials/contest', get_post_meta( $post->ID, 'contest_type', true ) ); ?>
				</section>

			<?php endwhile; ?>
		
		<?php else : ?>

			<section class="content">

				<article id="post-not-found" class="hentry cf">

					<header class="article-header">
						<h1><?php _e( 'Oops, Post Not Found!', 'greatermedia' ); ?></h1>
					</header>

					<section class="entry-content">
						<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'greatermedia' ); ?></p>
					</section>

				</article>

			</section>

		<?php endif; ?>

	</div>

</main>

<?php get_footer();

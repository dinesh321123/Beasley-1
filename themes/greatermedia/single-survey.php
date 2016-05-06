<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

	<div class="container">

		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php Greater_Media\Flexible_Feature_Images\feature_image_preference_is( get_the_ID(), 'poster' ) ? get_template_part( 'partials/feature-image-contest' ) : ''; ?>

				<section class="content">
					<?php get_template_part( 'partials/survey' ); ?>
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

<?php get_footer();

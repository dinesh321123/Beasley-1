<?php
/**
 * The front page template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header();

?>

	<div class="container">

		<?php get_template_part( 'partials/frontpage', 'featured' ); ?>
		<?php get_template_part( 'partials/frontpage', 'highlights' ); ?>

		<section class="entries">
			<?php get_template_part( 'partials/ad-in-loop' ); ?>

			<h2 class="content__heading">Latest from <?php bloginfo( 'name' ); ?></h2>

			<?php if ( have_posts() ) : ?>

				<?php get_template_part( 'partials/loop', 'front-page' ); ?>
				<?php greatermedia_load_more_button( array( 'partial_slug' => 'partials/loop', 'auto_load' => true ) ); ?>

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

<?php get_footer(); ?>

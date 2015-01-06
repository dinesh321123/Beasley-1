<?php
/**
 * The front page template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header();

?>

	<main class="main" role="main">

		<div class="container">

			<?php get_template_part( 'partials/frontpage', 'featured' ); ?>
			<?php get_template_part( 'partials/frontpage', 'highlights' ); ?>

			<section class="entries">				
				<div class="ad__leaderboard desktop">
					<img src='http://placehold.it/728x90'>
					<?php // do_action( 'acm_tag', 'leaderboard-body' ); ?>
				</div>

				<h2 class="content__heading">Latest from WMGK</h2>

				<?php if ( have_posts() ) : ?>

					<?php get_template_part( 'partials/loop/front-page' ); ?>
					<?php get_template_part( 'partials/pagination' ); ?>

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

<?php get_footer(); ?>

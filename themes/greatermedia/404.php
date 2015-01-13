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

			<div class="error__background"></div>

			<section class="content">

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'error' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

					<header class="error__header">

						<h2 class="error__title"><?php _e( 'Whoops ', 'greatermedia' ); ?><span class="error__title--span"><?php _e( '404', 'greatermedia' ); ?></span><?php _e( ' Channel doesn\'t exist', 'greatermedia' ); ?></h2>

					</header>

					<section class="error__content" itemprop="articleBody">

						<?php _e( 'Something went wrong. The page you are looking for could not be found. Try checking the URL for errors, then hit the refresh button on your browser.', 'greatermedia' ); ?>

					</section>

				</article>

			</section>

			</div>

	</main>

<?php get_footer();
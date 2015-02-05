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

				<?php if ( is_category() || is_tag() ) {
					greatermedia_archive_title();
				} else { ?>

					<h2 class="content__heading">
						<?php $object = get_queried_object(); ?>
						<?php echo ! empty( $object->labels->name ) ? esc_html( $object->labels->name) : ''; ?>
					</h2>
				<?php } ?>

				<?php if ( have_posts() ) :  ?>

					<?php get_template_part( 'partials/loop' ); ?>
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

	</main>

<?php get_footer(); ?>
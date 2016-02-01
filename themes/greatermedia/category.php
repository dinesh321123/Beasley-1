<?php
/**
 * Category template file
 *
 * @package Greater Media
 * @since   0.1.0
 *
 * @todo this template file still needs to be layed out according to the design
 */

$layout = sprintf( 'gmr_category_%d_layout', get_queried_object_id() );
$layout = get_option( $layout );
if ( ! empty( $layout ) ) {
	$layout = 'category-' . $layout;
}

get_header(); ?>

	<div class="container">

		<section class="content">

			<?php greatermedia_archive_title(); ?>
			
			<?php echo category_description(); ?>

			<?php if ( have_posts() ) :  ?>

				<?php
				if ( ! empty( $layout ) ) {
					get_template_part( 'partials/loop', $layout );
				} else {
					get_template_part( 'partials/loop' );
					greatermedia_load_more_button( array( 'partial_slug' => 'partials/loop', 'auto_load' => true ) );
				} ?>

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

<?php
/**
 * Closure post type archive template
 *
 */

get_header(); ?>

<div class="container">

	<section class="content">
		<h1 class="content__heading" itemprop="headline">Closures</h1>
		<?php
			$args = array(
				'numberposts' => 1,
				'offset' => 0,
				'category' => 0,
				'orderby' => 'post_date',
				'order' => 'DESC',
				'post_type' => GreaterMediaClosuresCPT::CLOSURE_CPT_SLUG,
				'post_status' => 'publish'
			);
			$recent_posts = wp_get_recent_posts( $args, ARRAY_A );
			$last_updated = strtotime( $recent_posts[0]['post_modified'] );
			$published_posts = wp_count_posts( GreaterMediaClosuresCPT::CLOSURE_CPT_SLUG );
			if( isset( $published_posts->publish ) ) {
				?>
				<div class="closure-entry-title" >
					<?php echo intval( $published_posts->publish ) . ' reported closures'; ?>
				</div>
				<div class="closure-attr--date">
					<p>Last updated: <?php echo date('m/d/Y \a\t G:i', $last_updated); ?></p>
				</div>
				<?php
			}
			?>

		<?php if ( have_posts() ) : ?>

		<section class="closures">

			<div class="closures_header">
				<div class="closures_header_name">Name</div>
				<div class="closures_header_location">Location</div>
				<div class="closures_header_reported">Reported</div>
			</div>

			<?php get_template_part( 'partials/loop', 'gmr_closure' ); ?>

		</section>

			<?php greatermedia_load_more_button( array( 'partial_slug' => 'partials/loop', 'partial_name' => 'gmr_closure', 'auto_load' => true ) ); ?>

		<?php else : ?>

			<article id="post-not-found" class="hentry cf">

				<header class="article-header">

					<h1><?php _e( 'No Closures Found!', 'greatermedia' ); ?></h1>

				</header>

			</article>

		<?php endif; ?>

	</section>

	<?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>
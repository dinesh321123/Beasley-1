<?php
/**
 * Closure post type archive template
 *
 */

get_header(); ?>

<main class="main" role="main">

	<div class="container">

		<section class="content">
			<h1 itemprop="headline">CLOSURES</h1>
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
					<div class="closure-entry-title" ><?php echo intval( $published_posts->publish ) . ' reported closures'; ?></div>
					<div class="closure-attr--date">
						<p>Last updated: <?php echo date('m/d/Y \a\t G:i', $last_updated); ?></p>
					</div>
					<?php
				}
				?>
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
				$closure_type = get_post_meta( get_the_ID(), 'gmedia_closure_type', true );
				$closure_entity_type = get_post_meta( get_the_ID(), 'gmedia_closure_entity_type', true );
				$closure_general_location = get_post_meta( get_the_ID(), 'gmedia_closure_general_location', true );
			?>
				<div class="closure cf">
					<div class="closure-attr--entity">
						<p><?php the_title(); ?></p>
						<div class="closure-attr--entity_name">
							<p><?php echo esc_html( $closure_entity_type ); ?></p>
						</div>
					</div>
					<div class="closure-attr--entity_location">
						<p><?php echo esc_html( $closure_general_location ); ?></p>
					</div>
					<div class="closure-attr--type">
						<p><?php echo esc_html( $closure_type ); ?></p>
					</div>
				</div>
		<?php endwhile; ?>
			<div class="posts-pagination">
				<div class="posts-pagination--previous"><?php next_posts_link( '<i class="fa fa-angle-double-left"></i>Previous' ); ?></div>
				<div class="posts-pagination--next"><?php previous_posts_link( 'Next<i class="fa fa-angle-double-right"></i>' ); ?></div>
			</div>
	<?php else : ?>

	<article id="post-not-found" class="hentry cf">

		<header class="article-header">

			<h1><?php _e( 'No Closures Found!', 'greatermedia' ); ?></h1>

		</header>

	</article>

<?php endif; ?>

</section>

</div>

</main>

<?php get_footer(); ?>
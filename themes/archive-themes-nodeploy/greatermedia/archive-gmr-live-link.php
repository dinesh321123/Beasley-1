<?php
/**
 * Live Links archive template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

	<div class="container">

		<section class="content">

			<section class="show__live-links cf">

				<h2 class="content__heading"><?php do_action( 'gmr_livelinks_title' ); ?> <?php _e( 'Archive', 'greatermedia'); ?></h2>

				<?php

				$live_links_args = array(
					'post_type' => 'gmr-live-link',
				);

				$live_links_query = new WP_Query( $live_links_args );

				if ( $live_links_query->have_posts() ) :
					// get the first post's date
					$live_links_query->the_post();
					$current_date = get_the_time( 'M j, Y' );
					$live_links_query->rewind_posts();
					?>
					<div class="live-links-group">
					<div class="group-date">
						<?php echo esc_html( $current_date ); ?>
					</div>

					<ul class="ll-archive-list">
					<?php while ( $live_links_query->have_posts() ) : $live_links_query->the_post(); ?>
					<?php
					$date = get_the_time( 'M j, Y' );
					if ( $date != $current_date ) {
						$current_date = $date;
						// New Date - Close the UL, Render Date, Reopen UL
						?>
						</ul>
						</div>
						<div class="live-links-group">
						<div class="group-date">
							<?php echo esc_html( $current_date ); ?>
						</div>
						<ul class="ll-archive-list">
					<?php
					}
					?>
					<li class="live-link__type--<?php echo ( $format = get_post_format() ) ? $format : 'standard'; ?>">
						<div class="live-link__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</div>
					</li>
				<?php endwhile; ?>
					<?php wp_reset_query(); ?>
					</ul>
					</div>

				<?php endif; ?>

			</section>

			<div class="show__paging"><?php echo \GreaterMedia\Shows\get_show_endpoint_pagination_links( $live_links_query ); ?></div>

		</section>

		<?php get_sidebar(); ?>

	</div>

<?php get_footer(); ?>
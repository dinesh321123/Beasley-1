<?php get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<?php the_post(); ?>

			<?php get_template_part( 'show-header' ); ?>

			<section class="content">

				<section class="show__live-links cf">

					<h2>Live Links Archive</h2>

					<?php
					$live_links_query = \GreaterMedia\Shows\get_show_live_links_archive_query();

					if ( $live_links_query->have_posts() ) :
						// get the first post's date
						$live_links_query->the_post();
						$current_date = get_the_time( 'M j' );
						$live_links_query->rewind_posts();
						?>
						<div class="live-links-group">
							<div class="group-date">
								<?php echo esc_html( $current_date ); ?>
							</div>

							<ul class="ll-archive-list">
								<?php while ( $live_links_query->have_posts() ) : $live_links_query->the_post(); ?>
									<?php
									$date = get_the_time( 'M j' );
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

				<div class="live-link-paging"><?php echo \GreaterMedia\Shows\get_show_endpoint_pagination_links( $live_links_query ); ?></div>

			</section>

		</div>

	</main>

<?php get_footer();
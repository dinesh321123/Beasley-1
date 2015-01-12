<?php
/**
 * Show schedule archive template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<section class="content">

				<h2 class="content__heading">
					Show schedule from <?php bloginfo( 'name' ); ?>
				</h2>

				<?php 

				$offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
				
				$now = current_time( 'timestamp' );
				$today = $now - $now % DAY_IN_SECONDS - $offset; // remote time fraction and offset from current time
				$from = date( DATE_ISO8601, $today );
				$to = date( DATE_ISO8601, $today + 7 * DAY_IN_SECONDS - 1 ); // -1 second to exclude next week show

				$episodes = gmrs_get_scheduled_episodes( $from, $to );

				$days = array();
				$start = $today + $offset;

				?>

				<div class="shows__schedule">
					<div class="shows__schedule--days">

						<?php for ( $i = 0; $i < 7; $i++, $start += DAY_IN_SECONDS ) : ?>
							<div class="shows__schedule--day">

								<div class="shows__schedule--dayofweek">
									<?php echo date( 'D, M jS', $start ); ?>
								</div>

								<div class="shows__schedule--episodes">
									<div class="shows__schedule--now" style="top:<?php echo ( $now % DAY_IN_SECONDS ) * 60 / HOUR_IN_SECONDS; ?>px"></div>

									<?php $day = date( 'N', $start ); ?>
									<?php if ( ! empty( $episodes[ $day ] ) ) : ?>
										<?php for ( $j = 0, $len = count( $episodes[ $day ] ); $j < $len; $j++ ) :
											$episode = $episodes[ $day ][ $j ];
											$show = get_post( $episode->post_parent );
											if ( ! $show || ShowsCPT::SHOW_CPT != $show->post_type ) :
												continue;
											endif;
											
											$styles = array(
												'top:' . ( ( strtotime( $episode->post_date ) % DAY_IN_SECONDS ) * 60 / HOUR_IN_SECONDS ) . 'px',
												'height:' . ( $episode->menu_order * 60 / HOUR_IN_SECONDS ) . 'px',
											);

											?><div class="shows__schedule--episode"
												 style="<?php echo implode( ';', $styles ) ?>"
												 data-hover-color="<?php echo gmrs_show_color( $episode->post_parent, 0.4 ) ?>">

												<div class="shows__schedule--episode-title" title="<?php echo esc_attr( $show->post_title ); ?>">
													<?php if ( \GreaterMedia\Shows\supports_homepage( $show->ID ) ) : ?>
														<a href="<?php echo esc_url( get_permalink( $show->ID ) ); ?>">
															<?php echo esc_html( $show->post_title ); ?>
														</a>
													<?php else : ?>
														<a href="javascript:void(0);" class="not-link">
															<?php echo esc_html( $show->post_title ); ?>
														</a>
													<?php endif; ?>
												</div>

												<div class="shows__schedule--episode-time">
													<?php echo date( 'h:i A', strtotime( $episode->post_date_gmt ) + $offset ); ?> -
													<?php echo date( 'h:i A', strtotime( $episode->post_date_gmt ) + $episode->menu_order + $offset ); ?>
												</div>
											</div>
										<?php endfor; ?>
									<?php endif; ?>
								</div>
								
							</div>
						<?php endfor; ?>

					</div>
				</div>

			</section>

		</div>

	</main>

<?php get_footer(); ?>
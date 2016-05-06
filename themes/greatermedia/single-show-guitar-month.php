<?php get_header(); ?>

	<div class="container guitar-month-show">

		<?php the_post(); ?>

		<?php get_template_part( 'show-header' ); ?>

		<section class="content">

			<?php
			$featured_query = \GreaterMedia\Shows\get_show_featured_query();
			if ( $featured_query->have_posts() ): $featured_query->the_post(); ?>
				<section class="show__features">
					<div class="show__feature--primary">
						<a href="<?php the_permalink(); ?>">
							<div class="show__feature">
								<div class='show-feature__thumbnail'>
									<?php if ( has_post_thumbnail() ) : ?>
										<div class='thumbnail'
										     style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-show-featured-primary', null, true ); ?>)'></div>
									<?php else: ?>
										<div class='thumbnail thumbnail-placeholder' style=''></div>
									<?php endif; ?>
								</div>
								<div class="show__feature--desc">
									<div class='inner-wrap'>
										<h3 class="show__feature--title"><?php the_title(); ?></h3>
										<time class="show__feature--date"
										      datetime="<?php the_time( 'c' ); ?>"><?php the_time( 'M j, Y' ); ?></time>
									</div>
								</div>
							</div>
						</a>
					</div>
					<?php if ( $featured_query->have_posts() ): ?>
						<div class="show__feature--secondary">
							<?php while ( $featured_query->have_posts() ): $featured_query->the_post(); ?>
								<a href="<?php the_permalink(); ?>">
									<div class="show__feature">
										<div class='show-feature__thumbnail'>
											<?php if ( has_post_thumbnail() ) : ?>
												<div class='thumbnail'
												     style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-show-featured-primary', null, true ); ?>)'></div>
											<?php else: ?>
												<div class='thumbnail thumbnail-placeholder' style=''></div>
											<?php endif; ?>
										</div>
										<div class="show__feature--desc">
											<div class='inner-wrap'>
												<h3 class="show__feature--title"><?php the_title(); ?></h3>
												<time class="show__feature--date"
												      datetime="<?php the_time( 'c' ); ?>"><?php the_time( 'M j, Y' ); ?></time>
											</div>
										</div>
									</div>
								</a>
							<?php endwhile; ?>
						</div>
					<?php endif; ?>
				</section>
				<?php wp_reset_query(); ?>
			<?php endif; ?>


			<?php get_template_part( 'partials/show-highlights-guitar-month' ); ?>


			<div class="row">

				<aside class="inner-right-col">

					<div class="show__ad">
						<?php do_action( 'acm_tag', 'mrec-lists' ); ?>
					</div>

					<?php
					$live_links_query = \GreaterMedia\Shows\get_show_live_links_query();
					if ( $live_links_query->have_posts() ) :
						$live_link_archive = \GreaterMedia\Shows\get_live_links_permalink( get_the_ID() );
						?>
						<section class="show__live-links cf">
							<h2 class="section-header"><?php do_action( 'gmr_livelinks_title' ); ?></h2>
							<ul>
								<?php while ( $live_links_query->have_posts() ) : ?>
									<?php $live_links_query->the_post(); ?>
									<li class="live-link__type--<?php echo ( $format = get_post_format() ) ? $format : 'standard'; ?>">
										<div class="live-link__title">
											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
										</div>
									</li>
								<?php endwhile; ?>
								<?php wp_reset_query(); ?>
							</ul>

						</section>

						<div class="show__live-links--more">
							<a class="more-btn" href="<?php echo $live_link_archive; ?>">more</a>
						</div>

					<?php endif; ?>

				</aside>

				<section class="show__blogroll inner-left-col">
					<h2 class="section-header">Stories</h2>
					<?php get_template_part( 'partials/loop', 'show' ); ?>
				</section>

			</div>

		</section>

	</div>

<?php get_footer();

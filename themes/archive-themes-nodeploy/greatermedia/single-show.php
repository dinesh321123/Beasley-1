<?php get_header(); ?>

<div class="container">

	<?php the_post(); ?>
	<?php get_template_part( 'show-header' ); ?>

	<section class="content"><?php

		$featured_query = \GreaterMedia\Shows\get_show_featured_query();
		if ( $featured_query->have_posts() ): $featured_query->the_post(); ?>
			<section class="show__features">
				<div class="show__feature--primary">
					<a href="<?php the_permalink(); ?>">
						<div class="show__feature">
							<div class="show-feature__thumbnail">
								<?php if ( has_post_thumbnail() ) : ?>
									<div class="thumbnail" style="background-image: url(<?php bbgi_post_thumbnail_url( null, true, 385, 255 ); ?>)"></div>
								<?php else: ?>
									<div class="thumbnail thumbnail-placeholder"></div>
								<?php endif; ?>
							</div>

							<div class="show__feature--desc">
								<div class='inner-wrap'>
									<h3 class="show__feature--title"><?php the_title(); ?></h3>

									<time class="show__feature--date" datetime="<?php the_time( 'c' ); ?>">
										<?php the_time( 'M j, Y' ); ?>
									</time>
								</div>
							</div>
						</div>
					</a>
				</div>

				<?php if ( $featured_query->have_posts() ): ?>
					<div class="show__feature--secondary">
						<?php while ( $featured_query->have_posts() ) : $featured_query->the_post(); ?>
							<a href="<?php the_permalink(); ?>">
								<div class="show__feature">
									<div class='show-feature__thumbnail'>
										<?php if ( has_post_thumbnail() ) : ?>
											<div class="thumbnail" style="background-image: url(<?php bbgi_post_thumbnail_url( null, true, 185, 125 ); ?>)"></div>
										<?php else: ?>
											<div class="thumbnail thumbnail-placeholder"></div>
										<?php endif; ?>
									</div>

									<div class="show__feature--desc">
										<div class="inner-wrap">
											<h3 class="show__feature--title"><?php the_title(); ?></h3>

											<time class="show__feature--date" datetime="<?php the_time( 'c' ); ?>">
												<?php the_time( 'M j, Y' ); ?>
											</time>
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

		<?php get_template_part( 'partials/show-highlights' ); ?>

		<div class="row">
			<section class="show__blogroll">
				<h2 class="section-header">Blog</h2>
				<?php get_template_part( 'partials/loop', 'show' ); ?>
			</section>
		</div>

	</section>

	<?php get_sidebar(); ?>

</div>

<?php get_footer();

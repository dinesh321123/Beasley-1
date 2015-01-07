<?php

 get_header();

?>

	<main class="main" role="main">

		<div class="container">

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
												<div class='thumbnail' style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-show-featured-primary' ); ?>)'></div>
											<?php else: ?>
												<div class='thumbnail thumbnail-placeholder' style=''></div>
											<?php endif; ?>
										</div>
										<div class="show__feature--desc">
											<div class='inner-wrap'>
												<h3><?php the_title(); ?></h3>
												<time class="show__feature--date" datetime="<?php the_time( 'c' ); ?>"><?php the_time( 'M j' ); ?></time>
											</div>
										</div>
									</div>
								</a>
							</div>
							<?php if ( $featured_query->have_posts() ): ?>
							<div class="show__feature--secondary">
								<?php while( $featured_query->have_posts() ): $featured_query->the_post(); ?>
									<a href="<?php the_permalink(); ?>">
										<div class="show__feature">
											<div class='show-feature__thumbnail'>
												<?php if ( has_post_thumbnail() ) : ?>
													<div class='thumbnail' style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-show-featured-primary' ); ?>)'></div>
												<?php else: ?>
													<div class='thumbnail thumbnail-placeholder' style=''></div>
												<?php endif; ?>
											</div>
											<div class="show__feature--desc">
												<div class='inner-wrap'>
													<h3><?php the_title(); ?></h3>
												<time class="show__feature--date" datetime="<?php the_time( 'c' ); ?>"><?php the_time( 'M j' ); ?></time>
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

				        <aside class="inner-right-col">
					        <?php do_action( 'acm_tag', 'mrec-lists' ); ?>

							<?php
							$live_links_query = \GreaterMedia\Shows\get_show_live_links_query();
							if ( $live_links_query->have_posts() ) :
							?>
							<section class="show__live-links cf">
					        	<h2 class="section-header">Live Links</h2>
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
								
								<a id="show__live-links--more" class="more-btn" href="#">
									<i class="fa fa-spinner fa-spin" style="display:none"></i> more
								</a>
					        </section>
							<?php endif; ?>

				        </aside>

				        <section class="show__blogroll inner-left-col">
				        	<h2 class="section-header">Blog</h2>

					        <?php
					        $main_query = \GreaterMedia\Shows\get_show_main_query();
					        while( $main_query->have_posts() ): $main_query->the_post(); ?>
						        
								<?php get_template_part('partials/entry'); ?>

					        <?php endwhile; ?>
					        <?php wp_reset_query(); ?>

<!-- 					        <div class="show-main-paging">
					        <?php echo \GreaterMedia\Shows\get_show_endpoint_pagination_links( $main_query ); ?>
					        </div>
 -->					        
				        </section>

			        </div>

			</section>

		</div>

	</main>

<?php get_footer();
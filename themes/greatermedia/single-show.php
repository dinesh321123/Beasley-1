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
										<?php if ( has_post_thumbnail() ) : ?>
											<?php the_post_thumbnail( 'gmr-show-featured-primary' ); ?>
										<?php endif; ?>
										<div class="show__feature--desc">
											<h3><?php the_title(); ?></h3>
											<time class="show__feature--date" datetime="<?php the_time( 'c' ); ?>"><?php the_time( 'd M' ); ?></time>
										</div>
									</div>
								</a>
							</div>
							<?php if ( $featured_query->have_posts() ): ?>
							<div class="show__feature--secondary">
								<?php while( $featured_query->have_posts() ): $featured_query->the_post(); ?>
									<a href="<?php the_permalink(); ?>">
										<div class="show__feature">
											<?php if ( has_post_thumbnail() ) : ?>
												<?php the_post_thumbnail( 'gmr-show-featured-secondary' ); ?>
											<?php endif; ?>
											<div class="show__feature--desc">
												<h3><?php the_title(); ?></h3>
												<time class="show__feature--date" datetime="<?php the_time( 'c' ); ?>"><?php the_time( 'd M' ); ?></time>
											</div>
										</div>
									</a>
								<?php endwhile; ?>
							</div>
							<?php endif; ?>
						</section>
						<?php wp_reset_query(); ?>
					<?php endif; ?>

				
					<?php
					global $post;
					$events = \GreaterMedia\Shows\get_show_events();
					if ( $events ) { ?>
						<div class="highlights__events">
							<h2 class="section-header">Upcoming Events</h2>
							<?php foreach( $events as $post ): setup_postdata( $post ); ?>
							<div class="highlights__event--item">
								<a href="<?php the_permalink(); ?>">
								<?php if ( has_post_thumbnail() ) : ?>
									<div class="highlights__event--thumb">
										<?php the_post_thumbnail( 'thumbnail' ); ?>
									</div>
								<?php endif; ?>
								<div class="highlights__event--meta">
									<h3 class="highlights__event--title"><?php the_title(); ?></h3>
									<span class="highlights__event--date"><time datetime="2014-12-28T08:00:00+00:00">Dec 28</time></span> <!-- TODO add dynamic time -->
								</div>
								</a>
							</div>
						<?php endforeach; ?>
						<?php wp_reset_query(); ?>
			        	</div>
		        	<?php } ?>
				    

			        <div class="row">

				        <aside class="inner-right-col">

					        <?php
					        $fav_query = \GreaterMedia\Shows\get_show_favorites_query();
					        if ( $fav_query->have_posts() ) :
				        	?>

					        <section class="show__favorites cf">
					        	<h2 class="section-header">Our Favorites</h2>

							<?php while( $fav_query->have_posts() ): $fav_query->the_post();
					        ?>
							<div class="featured__content--block">
				                <?php if ( has_post_thumbnail() ): ?>
					                <div class="featured__content--image">
						                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( array( 400, 400 ) ); ?></a>
					                </div>
								<?php endif; ?>
				                <div class="featured__content--meta">
				                    <h3 class="featured__content--title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				                </div>
				            </div>
							<?php endwhile; endif; ?>
					        <?php wp_reset_query(); ?>
					        </section>

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
						        <article <?php post_class( 'cf' ); ?>>
							        <section class="entry__meta<?php if ( !has_post_thumbnail() ) echo '--fullwidth'; ?>">
								        <time class="entry__date" datetime="<?php the_time( 'c' ); ?>"><?php the_time( 'd F' ); ?></time>

								        <h2 class="entry__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							        
										<?php the_excerpt(); ?>

							        </section>

							        <?php if ( has_post_thumbnail() ) : ?>
							        <section class="entry__thumbnail entry__thumbnail--standard">
								        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( array( 300, 580 ) ); // todo probably define an image size for this? ?></a>
							        </section>
							        <?php endif; ?>

									<footer class="entry__footer">

										<?php
										$category = get_the_category();

										if( isset( $category[0] ) ){
											echo '<a href="' . esc_url( get_category_link($category[0]->term_id ) ) . '" class="entry__footer--category">' . esc_html( $category[0]->cat_name ) . '</a>';
										}
										?>

									</footer>
						        </article>
					        <?php endwhile; ?>
					        <?php wp_reset_query(); ?>

					        <div class="show-main-paging"><?php echo \GreaterMedia\Shows\get_show_endpoint_pagination_links( $main_query ); ?></div>
				        </section>

			        </div>

			</section>

		</div>

	</main>

<?php get_footer();
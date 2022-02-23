<?php
/**
 * Partial for the Front Page Featured Content for the Music Theme
 *
 * @package Greater Media
 * @since   0.1.0
 */
$hp_featured_query = \GreaterMedia\HomepageCuration\get_featured_query();

// if we still have more posts (we almost always will), render the 3 below the main section
if ( $hp_featured_query->have_posts() ) :
	?><div class="slideshow">
		<?php while ( $hp_featured_query->have_posts() ) : $hp_featured_query->the_post(); ?>
			<div class="feature-post-slide" style="display:none">
				<a href="<?php the_permalink(); ?>">
					<div class="slide-content">
						<div class="featured__article--image" style="background-image: url(<?php bbgi_post_thumbnail_url( null, true, 610, 308, 'crop', array( 'width' => false, 'height' => true ) ); ?>)">
						</div>
						<div class="featured__article--content">
							<div class="featured__article--heading">
								<?php the_title(); ?>
							</div>
							<?php bbgi_the_image_attribution(); ?>
						</div>
					</div>
				</a>
			</div>
		<?php endwhile; ?>
		<div class="slick-arrow">
			<a href=# class="slick-prev">Prev</a>
			<a href=# class="slick-next">Next</a>
		</div>
	</div>
	<div class="slick-dots"></div><?php

	wp_reset_query();
endif;

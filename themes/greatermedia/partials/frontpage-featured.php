<?php
/**
 * Partial for the Front Page Featured Content
 *
 * @package Greater Media
 * @since   0.1.0
 */
?>
<section id="featured" class="home__featured">
		<?php
		$hp_featured_query = \GreaterMedia\HomepageCuration\get_featured_query();

		if ( $hp_featured_query->have_posts() ) : $hp_featured_query->the_post(); ?>
			<div class="featured__article">
				<div class="featured__article--image">
					<?php the_post_thumbnail( array( 2800, 1000 ) ); // todo Image Size: 2800x1000 ?>
				</div>
				<div class="featured__article--content">
					<div class="featured__article--heading">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</div>
					<div class="featured__article--bio"><?php the_excerpt(); ?></div>
				</div>
			</div>
		<?php endif; ?>
		<?php // if we still have more posts (we almost always will), render the 3 below the main section ?>
		<?php if ( $hp_featured_query->have_posts() ) : ?>
			<div class="featured__content">
				<?php while ( $hp_featured_query->have_posts() ) : $hp_featured_query->the_post(); ?>
					<div class="featured__content--block">
						<div class="featured__content--image">
							<?php the_post_thumbnail( array( 400, 400 ) ); // todo Image Size: 400x400 ?>
						</div>
						<div class="featured__content--meta">
							<h2 class="featured__content--title"><?php the_title(); ?></h2>
							<div class="featured__content--link">
								<a href="<?php the_permalink(); ?>" class="featured__content--btn">Read More</a>
							</div>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
		<?php wp_reset_query(); ?>
</section>
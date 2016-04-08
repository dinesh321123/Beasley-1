<?php
/**
 * Partial for the Front Page Featured Content for the News/Sports Theme
 *
 * @package Greater Media
 * @since   0.1.0
 */

$hp_featured_query = \GreaterMedia\HomepageCuration\get_featured_query();

if ( $hp_featured_query->have_posts() ) : ?>
<div class="featured__articles">
		<div class="featured__article--primary">
			<?php if ( $hp_featured_query->have_posts() ) : $hp_featured_query->the_post(); ?>
				<div class="featured__article">
					<a href="<?php the_permalink(); ?>" class="featured__article--link">
						<div class="featured__article--image" style='background-image: url(<?php gm_post_thumbnail_url( 'gm-article-thumbnail', null, true ); ?>);'>
						</div>
						<div class="featured__article--content">
							<div class="featured__article--heading">
								<?php the_title(); ?>
							</div>
						</div>
						<?php if ( has_post_format( 'video' ) ) { ?>
							<div class="featured__video"><i class="gmr-icon icon-play-circle"></i></div>
						<?php } ?>
					</a>
				</div>
			<?php endif; ?>
		</div>

	<?php
	// if we still have more posts (we almost always will), render the 3 below the main section

	$count = 0;
	if ( $hp_featured_query->have_posts() ) :
	?>
		<div class="featured__article--secondary">
			<?php
				while ( $hp_featured_query->have_posts() && $count < 2 ) : $hp_featured_query->the_post();
				$count++; ?>
				<div class="featured__article">
					<a href="<?php the_permalink(); ?>" class="featured__article--link">
						<div class="featured__article--image" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-secondary', null, true ); ?>);'>
						</div>
						<div class="featured__article--content">
							<div class="featured__article--heading">
								<?php the_title(); ?>
							</div>
						</div>
						<?php if ( has_post_format( 'video' ) ) { ?>
							<div class="featured__video"><i class="gmr-icon icon-play-circle"></i></div>
						<?php } ?>
					</a>
				</div>
			<?php
				endwhile;

			endif;

			?>
		</div>
		<div class="featured__content">
			<?php
			if ( $hp_featured_query->have_posts() ) :
				while ( $hp_featured_query->have_posts() ) : $hp_featured_query->the_post();
			?>
					<div class="featured__content--block">
						<a href="<?php the_permalink(); ?>" class="featured__content--link">
							<div class="featured__content--image" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-secondary', null, true ); ?>);'></div>
							<div class="featured__content--meta">
								<h2 class="featured__content--title"><?php the_title(); ?></h2>
							</div>
							<?php if ( has_post_format( 'video' ) ) { ?>
								<div class="featured__video"><i class="gmr-icon icon-play-circle"></i></div>
							<?php } ?>
						</a>
					</div>
			<?php
				endwhile;
			endif;
			?>
		</div>
	<?php wp_reset_query(); ?>
</div>
<?php
else :
endif;

// If the home page countdown clock plugin is enabled, render the next available clock.
if ( function_exists( 'GreaterMedia\HomepageCountdownClock\render_homepage_countdown_clock' ) ) {
	GreaterMedia\HomepageCountdownClock\render_homepage_countdown_clock();
}

?>

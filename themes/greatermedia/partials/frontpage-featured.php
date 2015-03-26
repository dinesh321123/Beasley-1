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
	$news_site = get_option( 'gmr_newssite' );

	// If this is a News/Sports site, our query will change
	if ( $news_site ) {

		$hp_featured_query = \GreaterMedia\HomepageCuration\get_featured_query();

		if ( $hp_featured_query->have_posts() ) : ?>
		<div class="featured__articles">
				<div class="featured__article--primary">
					<?php if ( $hp_featured_query->have_posts() ) : $hp_featured_query->the_post(); ?>
						<div class="featured__article">
							<a href="<?php the_permalink(); ?>" class="featured__article--link">
								<div class="featured__article--image" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-primary', null, true ); ?>);'>
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
	} else {
		$hp_featured_query = \GreaterMedia\HomepageCuration\get_featured_query();

		if ( $hp_featured_query->have_posts() ) : $hp_featured_query->the_post(); ?>
			<div class="featured__article">
				<a href="<?php the_permalink(); ?>">
					<div class="featured__article--image" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-primary', null, true ); ?>);'>
						<?php image_attribution(); ?>
					</div>
					<div class="featured__article--content">
						<div class="featured__article--heading">
							<?php the_title(); ?>
						</div>
					</div>
				</a>
			</div>
		<?php endif; ?>
		<?php // if we still have more posts (we almost always will), render the 3 below the main section ?>
		<?php if ( $hp_featured_query->have_posts() ) : ?>
			<div class="featured__content">
				<?php while ( $hp_featured_query->have_posts() ) : $hp_featured_query->the_post(); ?>
					<div class="featured__content--block">
						<a href="<?php the_permalink(); ?>">
							<div class="featured__content--image" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-secondary', null, true ); ?>);'></div>
							<div class="featured__content--meta">
								<h2 class="featured__content--title"><?php the_title(); ?></h2>
								<div class="featured__content--link">
									<span class="featured__content--btn">Read More</span>
								</div>
							</div>
						</a>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
		<?php wp_reset_query();
	} ?>
</section>
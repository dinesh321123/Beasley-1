<?php
/**
 * Partial for the Front Page Highlights for the News/Sports theme
 *
 * @package Greater Media
 * @since   0.1.0
 */
?>
<div class="highlights__community">

	<h2 class="highlights__heading"><?php _e( 'Don\'t Miss', 'greatermedia' ); ?></h2>

	<?php
	$hp_comm_query = \GreaterMedia\HomepageCuration\get_community_query();

	if ( $hp_comm_query->have_posts() ) :

		$count = 0;
		if ( $hp_comm_query->have_posts() ) : ?>
		<div class="highlights__community--column">
			<?php
			while ( $hp_comm_query->have_posts() && $count < 2 ) : $hp_comm_query->the_post();
				$count++; ?>
				<div class="highlights__community--item">
					<a href="<?php the_permalink(); ?>">

						<div class="highlights__community--thumb" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-secondary', null, true ) ?>)'></div>

						<h3 class="highlights__community--title">
							<?php the_title(); ?>
						</h3>

					</a>
				</div>
			<?php endwhile; ?>
		</div>
			<div class="highlights__community--column">
			<?php while( $hp_comm_query->have_posts() && $count >= 2 ) : $hp_comm_query->the_post(); ?>
				<div class="highlights__community--item">
					<a href="<?php the_permalink(); ?>">

						<div class="highlights__community--thumb" style='background-image: url(<?php gm_post_thumbnail_url( 'gmr-featured-secondary', null, true ) ?>)'></div>

						<h3 class="highlights__community--title">
							<?php the_title(); ?>
						</h3>

					</a>
				</div>
			<?php endwhile; ?>
		</div>
		<?php endif;
	endif;
	wp_reset_query(); ?>
</div>

<div class="highlights__ad">

	<div class="highlights__ad--desktop">
		<?php do_action( 'acm_tag_gmr_variant', 'mrec-lists', 'desktop', array( 'min_width' => 1024 ) ); ?>
	</div>
	<div class="highlights__ad--mobile">
		<?php do_action( 'acm_tag_gmr_variant', 'mrec-lists', 'mobile', array( 'max_width' => 1023 ) ); ?>
	</div>

</div>
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
			while ( $hp_comm_query->have_posts() && $count < 3 ) : $hp_comm_query->the_post();
				$count++; ?>
				<div class="highlights__community--item">
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
						<div class="highlights__community--thumb" style="background-image: url(<?php bbgi_post_thumbnail_url( null, true, 180, 180 ); ?>)"></div>

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

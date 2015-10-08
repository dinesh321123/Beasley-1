<?php
/**
 * Episode entry partial
 */

$episode = GMP_Player::get_podcast_episode();

if ( ! empty( $episode ) ) :
	?><article id="post-<?php the_ID(); ?>" <?php post_class( 'cf episode' ); ?> role="article" itemscope itemtype="http://schema.org/OnDemandEvent">
		<?php echo $episode; ?>
	</article><?php
endif;
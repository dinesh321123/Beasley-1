<?php
/**
 * Episode entry partial
 */

?><article id="post-<?php the_ID(); ?>" <?php post_class( 'cf episode' ); ?> role="article" itemscope itemtype="http://schema.org/OnDemandEvent">
	<?php GMP_Player::render_podcast_episode(); ?>
</article>
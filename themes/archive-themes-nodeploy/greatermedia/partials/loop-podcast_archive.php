<?php

global $podcast_episodes_query;

$podcast_episodes_query =  new WP_Query( array(
	'posts_per_page' => 2,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'post_type'      => GMP_CPT::EPISODE_POST_TYPE,
	'post_parent'    => get_the_ID(),
) );

if ( ! $podcast_episodes_query->have_posts() ) {
	return;
}

while ( $podcast_episodes_query->have_posts() ) {
	$podcast_episodes_query->the_post();
	$content = GMP_Player::get_podcast_episode();
	if ( ! empty( $content ) ) {
		break;
	}
}

wp_reset_postdata();

if ( ! empty( $content ) ) {
	?><article id="post-<?php the_ID(); ?>" <?php post_class( 'cf podcast podcast__archive' ); ?> role="article" itemscope itemtype="http://schema.org/OnDemandEvent">
		<?php echo $content; ?>
	</article><?php
}

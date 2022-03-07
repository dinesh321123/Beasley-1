<?php

$episode = get_post();
$query = new \WP_Query( array(
	'no_found_rows'  => true,
	'post_type'      => 'podcast',
	'posts_per_page' => 5,
	'post__not_in'   => array( $episode->post_parent ),
) );

if ( ! $query->have_posts() ) :
	return;
endif;

?><div class="podcast-tiles">
	<?php ee_the_subtitle( 'Podcasts you may like' ); ?>
	<?php ee_the_query_tiles( $query ); ?>
</div>

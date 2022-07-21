<?php

$episode = get_post();
$args = array(
	'no_found_rows'  => true,
	'posts_per_page' => 5,
	'post__not_in'   => array( $episode->ID ),
);

$query = ee_get_episodes_query( $episode->post_parent, $args );
if ( ! $query->have_posts() ) :
	return;
endif;

?><div class="episodes">
	<?php ee_the_subtitle( 'Next Episodes' ); ?>

	<div class="archive-tiles">
		<?php ee_the_query_tiles( $query ); ?>
	</div>
</div>

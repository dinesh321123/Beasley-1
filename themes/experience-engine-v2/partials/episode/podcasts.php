<?php

$episode = get_post();

$app_only_meta_args = array();
if ( ! ee_is_whiz() ) {
	$app_only_meta_args["meta_query"] = array(
		'relation' => 'OR',
		array(
			'key'     => '_is_app_only',
			'value'   => 1,
			'compare' => '!=',
		),
		array(
			'key'     => '_is_app_only',
			'compare' => 'NOT EXISTS',
		),
	);
}
$query = new \WP_Query( array_merge( $app_only_meta_args, array(
	'no_found_rows'  => true,
	'post_type'      => 'podcast',
	'posts_per_page' => 5,
	'post__not_in'   => array( $episode->post_parent ),
) ));

if ( ! $query->have_posts() ) :
	return;
endif;

?><div class="podcast-tiles">
	<?php ee_the_subtitle( 'Podcasts you may like' ); ?>
	<?php ee_the_query_tiles( $query ); ?>
</div>

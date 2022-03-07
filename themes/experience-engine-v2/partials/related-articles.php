<?php

if ( ms_is_switched() ) :
	return;
endif;

$categories = array_map(
	function( \WP_Term $category ) {
		return $category->slug;
	},
	get_the_category()
);

echo sprintf(
	'<div class="related-articles content-wrap"
		  data-postid="%d"
		  data-posttitle="%s"
		  data-posttype="%s"
		  data-categories="%s"
		  data-url="%s"></div>',
	get_the_ID(),
	get_the_title(),
	get_post_type(),
	implode( ',', $categories ),
	get_the_permalink()
);

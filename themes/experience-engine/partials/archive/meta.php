<?php

$slug = false;
$object = get_queried_object();

if ( ! empty( $object->rewrite['slug'] ) ) :
	$slug = $object->rewrite['slug'];
elseif ( ! empty( $object->slug ) ) :
	$slug = $object->slug;
endif;

if ( ! empty( $slug ) ) :
	echo '<div class="content-wrap">';
		ee_add_to_favorites( $slug );
	echo '</div>';
endif;

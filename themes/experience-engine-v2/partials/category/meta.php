<?php

$slug = false;
$object = get_queried_object();

if ( ! empty( $object->rewrite['slug'] ) ) :
	$slug = $object->rewrite['slug'];
elseif ( ! empty( $object->slug ) ) :
	$slug = $object->slug;
endif;

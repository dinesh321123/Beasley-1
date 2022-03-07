<?php

if ( ! class_exists( 'GreaterMediaGallery' ) ) :
	return;
endif;

$gallery = get_queried_object();
$ids = \GreaterMediaGallery::get_attachment_ids_for_post( $gallery );
if ( ! is_array( $ids ) ) :
	$ids = array();
endif;

echo ee_get_gallery_html( $gallery, $ids );

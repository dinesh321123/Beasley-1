<?php
if ( ! class_exists( 'GeneralSettingsFrontRendering' ) ) :
	return;
endif;
$post_object = get_queried_object();
	$footer_description	= apply_filters('the_content', \GeneralSettingsFrontRendering::get_post_metadata_from_post( 'common_footer_description', $post_object ) );

echo '<div class="am_footer_description">', $footer_description, '</div>';

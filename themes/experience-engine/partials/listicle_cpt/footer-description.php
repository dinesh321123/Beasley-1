<?php
if ( ! class_exists( 'ListicleCPTFrontRendering' ) ) :
	return;
endif;
$cpt_post_object = get_queried_object();
	$cpt_footer_description	=	\ListicleCPTFrontRendering::get_post_metadata_from_post( 'listicle_cpt_footer_description', $cpt_post_object );

echo '<div class="am_footer_description">', $cpt_footer_description, '</div>';

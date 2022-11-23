<?php
if ( ! class_exists( 'SecondStreetWidget' ) ) :
	return;
endif;
$sswPostType = 1;
$current_post_object = get_queried_object();
$hide_ssw = get_field( 'hide_ssw', $current_post_object );

if ( isset( $hide_ssw ) && $hide_ssw == 0 ) :
	$sswPostType = 0;
endif;

if ( ! in_array( $current_post_object->post_type, \SecondStreetWidget::get_ssw_posttype_list() ) ) {
	return;
	exit;
}

// Second street email opt-in widget settings 0 Means "Do Not Display"
if ( empty( $sswPostType ) ) {
	return;
}

$common_footer_description	= get_post_meta( $current_post_object->ID, 'common_footer_description', true );
$am_footer_description		= get_post_meta( $current_post_object->ID, 'am_footer_description', true );
$listicle_footer_description= get_post_meta( $current_post_object->ID, 'listicle_cpt_footer_description', true );


$tempContent				= get_the_content();
$tempCheck					= '[ss-promo';
$tempVerify					= strpos($tempContent,$tempCheck);
$sswContent					= "";
$get_ss_op_id				= get_option( 'secondstreet_op_id' ) ? get_option( 'secondstreet_op_id' ) : '' ;
$get_ss_op_guid				= get_option( 'secondstreet_op_guid' ) ? get_option( 'secondstreet_op_guid' ) : '' ;
if( strpos($common_footer_description,$tempCheck) === false && strpos($am_footer_description,$tempCheck) === false && strpos($listicle_footer_description,$tempCheck) === false && $tempVerify === false && isset($get_ss_op_id) && isset($get_ss_op_guid) ) {
	$sswContent = do_shortcode('[ss-promo op_id="' . $get_ss_op_id . '" op_guid="' . $get_ss_op_guid . '" routing="hash"]');
}

echo '<div class="ssw_content">', $sswContent, '</div>';

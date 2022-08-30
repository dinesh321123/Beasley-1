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

$tempContent	= get_the_content();
$tempCheck		= '[ss-promo';
$tempVerify		= strpos($tempContent,$tempCheck);
$sswContent		= "";
if($tempVerify === false) {
	$get_op_id = get_option( 'secondstreet_op_id' ) ? get_option( 'secondstreet_op_id' ) : '435162' ;
	$sswContent = do_shortcode('[ss-promo op_id="' . $get_op_id . '" op_guid="f7638862-0779-48d5-8725-c4ede5cdc6a9" routing="hash"]');
}

echo '<div class="ssw_content">', $sswContent, '</div>';

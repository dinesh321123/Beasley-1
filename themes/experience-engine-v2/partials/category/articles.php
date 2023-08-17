<?php
$ca_articles_data = get_query_var( 'ca_articles_data' );
$ca_obj = $ca_articles_data['ca_obj'] ?? null;
$ca_posts = $ca_articles_data['ca_posts'] ?? array();
$outer_call = $ca_articles_data['outer_call'] ?? false;
$default_order = $ca_articles_data['default_order'] ?? true;
$ca_mobile_ad = $ca_articles_data['ca_mobile_ad'] ?? 0;
$station_mobile_ad = $ca_articles_data['station_mobile_ad'] ?? 6;
$ca_stn_video_barker_id = $ca_articles_data['ca_stn_video_barker_id'] ?? "";

$ca_current_page = 1;
while(count($ca_posts) > 0) {
	if(( $ca_current_page == 2 && ee_is_first_page() && !$outer_call ) || ( $ca_current_page == 1 && ee_is_first_page() && $outer_call ) ) {
		if( !$outer_call ) {
			set_query_var( 'ca_barker_data', array(
				'ca_obj'					=> $ca_obj,
				'ca_stn_video_barker_id'	=> $ca_stn_video_barker_id,
			) );
			get_template_part( 'partials/category/barker' );
		} 
		?>
		<div class="content-wrap<?php if(!empty($ca_stn_video_barker_id)) { echo " pt-100"; } ?>">
			<div class="ca-section-head-container">
				<h2 class="section-head ca-section-head">
					<span class="bigger">More <?php echo $ca_obj->name; ?></span>
				</h2>
			</div>
		</div>
	<?php }

	if( ( $ca_current_page == 1 && !ee_is_first_page() ) || ( $ca_current_page == 2 && ee_is_first_page() ) ) { ?>
		<div id="autoload-category-archive" ></div>
	<?php }

	$have_ad = null;
	$posts_fetch = 12;
	if( ( $ca_current_page % 2 == 0 && $default_order ) || ( $ca_current_page % 2 !== 0 && !$default_order ) ) {
		$have_ad = "advertise";
	}
	$display_ca_archive_posts = array_slice($ca_posts, 0, $posts_fetch);
	$ca_posts = array_slice($ca_posts, $posts_fetch);
	$display_ca_archive_data = array(
		'display_ca_archive_posts' 		=> $display_ca_archive_posts,
		'station_mobile_ad_occurance' 	=> $station_mobile_ad,
		'ca_mobile_ad_occurrence' 		=> $ca_mobile_ad
	);

	set_query_var( 'display_ca_archive_data', $display_ca_archive_data );
	get_template_part( 'partials/category/archive', $have_ad );
	$ca_current_page++;
	
}
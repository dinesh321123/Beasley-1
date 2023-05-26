<?php
$display_ca_archive_data = get_query_var( 'display_ca_archive_data' );
$display_ca_archive_posts = isset($display_ca_archive_data['display_ca_archive_posts']) ? $display_ca_archive_data['display_ca_archive_posts'] : array();
$station_mobile_ad_occurance = isset($display_ca_archive_data['station_mobile_ad_occurance']) ? $display_ca_archive_data['station_mobile_ad_occurance'] : 0;
$ca_mobile_ad_occurrence = isset($display_ca_archive_data['ca_mobile_ad_occurrence']) ? $display_ca_archive_data['ca_mobile_ad_occurrence'] : 0;
$ca_mobile_ad_occurrence = $ca_mobile_ad_occurrence ? $ca_mobile_ad_occurrence : $station_mobile_ad_occurance;

if( !empty($display_ca_archive_posts) && ( count($display_ca_archive_posts) > 0 ) ) { ?>
<div class="content-wrap">
	<div class="d-flex">
		<div class="w-67">
			<div class="archive-tiles content-wrap -grid -large p-0">
				<section class="list-grid-section card-2">
					<?php
						foreach ($display_ca_archive_posts as $key=>$archive_ca_post) {
							$cap_sponsored_by = ee_get_sponsored_by($archive_ca_post);
							$cap_is_sponsored = ( $cap_sponsored_by !== '' ) ? true : false;
							$category_archive_data = array(
								'category_archive_post' => $archive_ca_post,
								'cap_is_sponsored' 		=> $cap_is_sponsored,
								'cap_show_icon' 		=> true
							); ?>
							<?php if( $ca_mobile_ad_occurrence > 0 && $key > 0 ) {
								if( $key % $ca_mobile_ad_occurrence == 0 ) { ?>
								<article class="in-grid-ad -mobile-in-ad">
									<div class="ad -footer -centered">
										<?php do_action( 'dfp_tag', 'bottom-leaderboard', false, array( array( 'pos', 2 ) ) ); ?>
									</div>
								</article>
								<?php }
							} ?>
							<article <?php if($cap_is_sponsored) { echo 'class="bg-red bg-red-thumbnail"'; }?>>
								<?php
									set_query_var( 'category_archive_data', $category_archive_data );
									get_template_part( 'partials/tile/thumbnail', 'category' );
									get_template_part( 'partials/tile/title', 'category' );
								?>
							</article>
					<?php } ?>
				</section>
			</div>
		</div>
		<div class="w-33">
			<div class="two-column-sticky-ad">
				<?php get_template_part( 'partials/ads/short-sidebar-sticky' ); ?>
			</div>
		</div>
	</div>
</div>
<?php } ?>

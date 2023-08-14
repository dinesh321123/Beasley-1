<?php
$ca_barker_data = get_query_var( 'ca_barker_data' );
$ca_obj = $ca_barker_data['ca_obj'] ?? null;
$ca_stn_video_barker_id = $ca_barker_data['ca_stn_video_barker_id'] ?? "";
$ca_stn_cid = get_option( 'stn_cid') ? get_option( 'stn_cid') : '10462';
if ( !empty($ca_stn_video_barker_id) ) { ?>
	<div class="pre-load-cont">
		<div class="content-wrap">
			<div class="ca-section-head-container">
				<h2 class="section-head ca-section-head"><span class="bigger"><?php echo $ca_obj->name; ?> Videos</span></h2>
			</div>
			<div class="d-flex">
				<div class="w-67">
					<div class="stnbarker" data-fk="<?php echo $ca_stn_video_barker_id; ?>" data-cid="<?php echo $ca_stn_cid; ?>"></div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
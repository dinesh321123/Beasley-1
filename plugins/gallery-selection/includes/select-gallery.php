<?php
/**
 * Class SegmentPermissionsMetaboxes
 */
class ExistingGallerySelection {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'admin_footer', array( __CLASS__, 'gallery_print_media_templates' ) );
		add_action( 'wp_footer', array( __CLASS__, 'gallery_print_media_templates' ) );
		add_action( 'customize_controls_print_footer_scripts', array( __CLASS__, 'gallery_print_media_templates' ) );
		add_action( 'wp_ajax_get_gmr_gallery_data', array( __CLASS__, 'get_gmr_gallery_data' ) );
		add_action( 'wp_ajax_load_more_gmr_gallery_data', array( __CLASS__, 'load_more_gmr_gallery_data' ) );
		add_filter('media_view_strings', array( __CLASS__, 'custom_media_string'), 10, 2);
	}

	public static function enqueue_scripts(){
		global $typenow, $pagenow;
		$post_types = array( 'listicle_cpt', 'gmr_gallery' );
		if ( !in_array( $typenow, $post_types ) ) {
			wp_register_style('existing-gallery-selection-admin', GALLERY_SELECTION_URL . "assets/css/gallery_selection.css", array(), GALLERY_SELECTION_VERSION, 'all');
			wp_enqueue_style('existing-gallery-selection-admin');
			wp_enqueue_script('custom', GALLERY_SELECTION_URL . "assets/js/gallery_selection.js", array('media-views'), GALLERY_SELECTION_VERSION, true);
			wp_enqueue_media();
			wp_enqueue_editor();
		}
	}
	
	public static function custom_media_string($strings,  $post){
		$strings['customMenuTitle'] = __('Existing Gallery', 'custom');
		$strings['customButton'] = __('Add Existing Gallery', 'custom');
		return $strings;
	}
	
	public static function get_modified_gallery_date( $post = null ) {
		$post = get_post( $post );
		if ( is_a( $post, '\WP_Post' ) ) {
			$modified = mysql2date( 'G', $post->post_modified_gmt );
			return self::format_gallery_date( $modified, 1 );
		}
		return;
	}
	
	public static function format_gallery_date( $timestamp, $gmt = 0 ) {
		return date( "m / d / Y", $gmt ? $timestamp + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS : $timestamp );
	}
	
	public static function get_gmr_galleries_data( $s_value = null, $paged_value ) {
		global $wpdb;
		$images = array();
		$query_images_args = array(
			'post_type'      => 'gmr_gallery',
			'post_status'    => 'publish',
			'posts_per_page' => 14,
			'paged'			 => $paged_value
		);
		
		if( isset( $s_value ) && $s_value !="" ) {
			$sql = "SELECT DISTINCT $wpdb->posts.id FROM $wpdb->posts LEFT JOIN $wpdb->term_relationships ON ( $wpdb->posts.id = $wpdb->term_relationships.object_id ) LEFT JOIN $wpdb->term_relationships AS tt1 ON ( $wpdb->posts.id = tt1.object_id ) WHERE  1 = 1 AND (  $wpdb->posts.post_title LIKE %s";
			
			// Search By Category Name
			if(get_cat_ID($s_value)) { 
				$sql .= " OR tt1.term_taxonomy_id IN ( ".get_cat_ID($s_value)." )";
			}
			
			// Search By Tag
			if(term_exists($s_value, "post_tag")) {
				$sql .= " OR $wpdb->term_relationships.term_taxonomy_id IN ( ".term_exists($s_value, "post_tag")['term_id']." )";
			}

			$sql .= " ) AND $wpdb->posts.post_type = 'gmr_gallery' AND $wpdb->posts.post_status = 'publish' GROUP  BY $wpdb->posts.id ORDER  BY $wpdb->posts.post_date DESC";
			
			$search = $wpdb->get_col( $wpdb->prepare( $sql, '%' . $wpdb->esc_like($s_value) . '%' ) );

			// Search Query Result
			if(count($search)) {
				$query_images_args['post__in'] = $search;
			}
	
			// If not found any result, Then don't show results
			if( !count($search) ) {
				$query_images_args['post__in'] = Array(0);
			}
			return new WP_Query( $query_images_args );
		} else {
			return new WP_Query( $query_images_args );
		}
		$result = array( "imgs" => $images, "imgs_array" => $query_images->posts );
		return $result;
	}
	public static function get_gmr_gallery_data() {
		$searchMediaImage = filter_input( INPUT_GET, 's_mediaimage', FILTER_SANITIZE_SPECIAL_CHARS );
		$paged_mediaimage = filter_input( INPUT_GET, 'paged_mediaimage', FILTER_SANITIZE_SPECIAL_CHARS );
	
		$searchMediaImage_val = $searchMediaImage ? $searchMediaImage : '';
		$paged_mediaimage_val = $paged_mediaimage ? $paged_mediaimage : '1';
	
		$gallery_data = self::get_gmr_galleries_data( $searchMediaImage_val, $paged_mediaimage_val );
		
		$html = '
			<div id="main-container-mediaimg">
				<input type="hidden" name="paged_mediaimage" id="paged_mediaimage" class="paged_mediaimage" value="1" />
				<div class="media-search">
					<span class="spinner" id="s_spinner"></span>
					<input type="text" name="s_mediaimage" id="s_mediaimage" class="s_mediaimage" placeholder="Search gallery..." value="'. $searchMediaImage_val .'" />
					<button type="button" class="s_btn_mediaimage button" >Search</button>
				</div>';
	
		if( $gallery_data->found_posts > 0 ) {
			$html .= '<ul class="select-gallery-ul">';
	
			while ( $gallery_data->have_posts() ) : $gallery_data->the_post();
				$jqueryEventSelectedClass = "'selected-gallery-thumbnail'";
				$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), array(200, 150));
	
				$html .= '
					<li class="select-exist-gallery-li" gallery-id="'.get_the_ID().'" onclick=" jQuery(\'.select-gallery-ul li\').removeClass(' . $jqueryEventSelectedClass .'); jQuery(\'.select-gallery-ul li\').css(\'box-shadow\', \'0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.19)\'); jQuery(this).addClass(' . $jqueryEventSelectedClass .'); " >
						<div style="width: 200px; height: 150px; display: flex;">
							<img
								class="img-attachment"
								src="' . $image_src[0] . '" image-id="' . get_post_thumbnail_id() . '" />
						</div>
						<div class="desc-main-container">
						<div class="desc-upper-container">'.get_the_title().'</div>
						<div class="desc-lower-container">
							<div class="desc-lower-title">Author:</div> <div class="desc-lower-text"> '.get_the_author_meta( 'display_name' ).'</div>
						</div>
						<div class="desc-lower-container" style="padding-bottom:10px;"> 
							<div class="desc-lower-title">Date:</div> <div> '.self::get_modified_gallery_date($post).'</div>
						</div>
					</li>';
			endwhile;
			$html .= '
				</ul>
				<div style="text-align: center;"><span class="spinner" id="loadmore_spinner"></span></div>
				<div style="text-align: center;"><button type="button" id="media_loadmore" class="media_loadmore button button-secondary button-hero">Load more galleries</button></div>';
		} else {
			$html .= '<div class="no-existing-gallery-data"><h2 class="">No existing gallery found.</h2></div>';
		}
		$html .= '</div>';
	
		$resutl = array( "html" => $html, "imgs_array" => json_encode( $imgs ) );
		wp_send_json_success( $resutl );
	}
	
	public static function load_more_gmr_gallery_data() {
		$html = '';
		$searchMediaImage = filter_input( INPUT_GET, 's_mediaimage', FILTER_SANITIZE_SPECIAL_CHARS );
		$paged_mediaimage = filter_input( INPUT_GET, 'paged_mediaimage', FILTER_SANITIZE_SPECIAL_CHARS );
	
		$searchMediaImage_val = $searchMediaImage ? $searchMediaImage : '';
		$paged_mediaimage_val = $paged_mediaimage ? $paged_mediaimage+1 : '1';
		
		$gallery_data = self::get_gmr_galleries_data( $searchMediaImage, $paged_mediaimage_val );
		if( $gallery_data->found_posts > 0 ) {
			while ( $gallery_data->have_posts() ) : $gallery_data->the_post();
				$jqueryEventSelectedClass = "'selected-gallery-thumbnail'";
				$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), array(200, 150));
	
				$html .= '
					<li class="select-exist-gallery-li" gallery-id="'.get_the_ID().'" onclick=" jQuery(\'.select-gallery-ul li\').removeClass(' . $jqueryEventSelectedClass .'); jQuery(\'.select-gallery-ul li\').css(\'box-shadow\', \'0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.19)\'); jQuery(this).addClass(' . $jqueryEventSelectedClass .'); " >
						<div style="width: 200px; height: 150px; display: flex;">
							<img
								class="img-attachment"
								src="' . $image_src[0] . '" image-id="' . get_post_thumbnail_id() . '" />
						</div>
						<div class="desc-main-container">
						<div class="desc-upper-container">'.get_the_title().'</div>
						<div class="desc-lower-container">
							<div class="desc-lower-title">Author:</div> <div class="desc-lower-text"> '.get_the_author_meta( 'display_name' ).'</div>
						</div>
						<div class="desc-lower-container" style=" padding-bottom:10px;">
							<div class="desc-lower-title">Date:</div> <div> '.self::get_modified_gallery_date($post).'</div>
						</div>
					</li>';
			endwhile;
		}
		
		wp_send_json_success( array( "media_image_list" => $html, "paged_mediaimage" => $paged_mediaimage_val, "searchMediaImage_val" => $searchMediaImage_val, "imgs_array" => json_encode( $imgs ) ) );
	}
	
	public static function gallery_print_media_templates() {
			?><script type="text/html" id="tmpl-gallery-selector">
			<input type="hidden" name="gallery_selected_id" id="gallery_selected_id" />
			<div class="selectgallery__preview">
				<?php	
					// Query to fetch galleries
					$gallery_data = self::get_gmr_galleries_data(null, 1);
					
					$html = '
					<div id="main-container-mediaimg">
						<input type="hidden" name="paged_mediaimage" id="paged_mediaimage" class="paged_mediaimage" value="1" />
						<div class="media-search">
							<span class="spinner" id="s_spinner"></span>
							<input type="text" name="s_mediaimage" id="s_mediaimage" class="s_mediaimage" placeholder="Search gallery..." value="'. $searchMediaImage_val .'" />
							<button type="button" class="s_btn_mediaimage button" >Search</button>
						</div>';
				
					if( $gallery_data->found_posts > 0 ) {
						$html .= '<ul class="select-gallery-ul">';
	
						while ( $gallery_data->have_posts() ) : $gallery_data->the_post();
							$jqueryEventSelectedClass = "'selected-gallery-thumbnail'";
							$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), array(200, 150));
	
							$html .= '
								<li class="select-exist-gallery-li" gallery-id="'.get_the_ID().'" onclick=" jQuery(\'.select-gallery-ul li\').removeClass(' . $jqueryEventSelectedClass .'); jQuery(\'.select-gallery-ul li\').css(\'box-shadow\', \'0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.19)\'); jQuery(this).addClass(' . $jqueryEventSelectedClass .'); " >
									<div style="width: 200px; height: 150px; display: flex;">
										<img
											class="img-attachment"
											src="' . $image_src[0] . '" image-id="' . get_post_thumbnail_id() . '" />
									</div>
									<div class="desc-main-container">
									<div class="desc-upper-container">'.get_the_title().'</div>
									<div class="desc-lower-container">
										<div class="desc-lower-title">Author:</div> <div class="desc-lower-text"> '.get_the_author_meta( 'display_name' ).'</div>
									</div>
									<div class="desc-lower-container" style="padding-bottom:10px;"> 
										<div class="desc-lower-title">Date:</div> <div> '.self::get_modified_gallery_date($post).'</div>
									</div>
								</li>';
						endwhile;
						$html .= '
							</ul>
							<div style="text-align: center;"><span class="spinner" id="loadmore_spinner"></span></div>
							<div style="text-align: center;"><button type="button" id="media_loadmore" class="media_loadmore button button-secondary button-hero">Load more galleries</button></div>';
					} else {
						$html .= '<div class="no-existing-gallery-data"><h2 class="">No existing gallery found.</h2></div>';
					}
					$html .= '</div>';
	
					echo $html;
				?>
	
				</div>
			</script><?php
	}
}

ExistingGallerySelection::init();

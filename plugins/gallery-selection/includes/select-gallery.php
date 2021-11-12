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
	
	public static function get_gmr_galleries_data( $paged_value, $s_value = null, $s_category = null, $s_tag = null ) {
		global $wpdb;
		$images = array();
		$query_images_args = array(
			'post_type'      => 'gmr_gallery',
			'post_status'    => 'publish',
			'posts_per_page' => 14,
			'paged'			 => $paged_value
		);
		
		if( ( isset( $s_value ) && $s_value !="" ) || ( isset( $s_category ) && $s_category !="" ) || ( isset( $s_tag ) && $s_tag !="" ) ) {
			$sql = "SELECT DISTINCT $wpdb->posts.id FROM $wpdb->posts LEFT JOIN $wpdb->term_relationships ON ( $wpdb->posts.id = $wpdb->term_relationships.object_id ) LEFT JOIN $wpdb->term_relationships AS tt1 ON ( $wpdb->posts.id = tt1.object_id ) WHERE  1 = 1 AND $wpdb->posts.post_title LIKE %s";
			
			// Search By Category Name
			if($s_category !== "" && $s_category !== null) {
				$sql .= " AND tt1.term_taxonomy_id = ".$s_category." ";
			}
			
			// Search By Tag
			if($s_tag !== "" && $s_tag !== null) {
				$sql .= " AND $wpdb->term_relationships.term_taxonomy_id = ".$s_tag." ";
			}

			$sql .= " AND $wpdb->posts.post_type = 'gmr_gallery' AND $wpdb->posts.post_status = 'publish' GROUP  BY $wpdb->posts.id ORDER  BY $wpdb->posts.post_date DESC";
			
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
	}

	public static function get_gmr_gallery_data() {
		$SearchTitle = filter_input( INPUT_GET, 's_title', FILTER_SANITIZE_SPECIAL_CHARS );
		$PagedData = filter_input( INPUT_GET, 'page_number', FILTER_SANITIZE_SPECIAL_CHARS );
		$SearchCat = filter_input( INPUT_GET, 's_category', FILTER_SANITIZE_SPECIAL_CHARS );
		$SearchTag = filter_input( INPUT_GET, 's_tag', FILTER_SANITIZE_SPECIAL_CHARS );
		
		$SearchTitle_val = $SearchTitle ? $SearchTitle : '';
		$PagedData_val = $PagedData ? $PagedData : '1';
		$SearchCat_val = $SearchCat ? $SearchCat : '';
		$SearchTag_val = $SearchTag ? $SearchTag : '';
	
		$gallery_data = self::get_gmr_galleries_data( $PagedData_val, $SearchTitle_val, $SearchCat_val, $SearchTag_val );
		$html = self::prepare_html($gallery_data, $SearchTitle_val, $SearchCat_val, $SearchTag_val);
		
		$resutl = array( "html" => $html );
		wp_send_json_success( $resutl );
	}
	
	public static function load_more_gmr_gallery_data() {
		global $post;
		$html = '';
		$SearchTitle = filter_input( INPUT_GET, 's_title', FILTER_SANITIZE_SPECIAL_CHARS );
		$PagedData = filter_input( INPUT_GET, 'page_number', FILTER_SANITIZE_SPECIAL_CHARS );
		$SearchCat = filter_input( INPUT_GET, 's_category', FILTER_SANITIZE_SPECIAL_CHARS );
		$SearchTag = filter_input( INPUT_GET, 's_tag', FILTER_SANITIZE_SPECIAL_CHARS );
	
		$SearchTitle_val = $SearchTitle ? $SearchTitle : '';
		$PagedData_val = $PagedData ? $PagedData+1 : '1';
		$SearchCat_val = $SearchCat ? $SearchCat : '';
		$SearchTag_val = $SearchTag ? $SearchTag : '';
		
		$gallery_data = self::get_gmr_galleries_data( $PagedData_val, $SearchTitle_val, $SearchCat_val, $SearchTag_val );

		if( $gallery_data->found_posts > 0 ) {
			while ( $gallery_data->have_posts() ) : $gallery_data->the_post();
				$jqueryEventSelectedClass = "'selected-gallery-thumbnail'";
				$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), array(200, 150));
	
				$html .= '
					<li class="select-exist-gallery-li" gallery-id="'.get_the_ID().'" slug-name="'.$post->post_name.'" onclick=" jQuery(\'.select-gallery-ul li\').removeClass(' . $jqueryEventSelectedClass .'); jQuery(\'.select-gallery-ul li\').css(\'box-shadow\', \'0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.19)\'); jQuery(this).addClass(' . $jqueryEventSelectedClass .'); " >
						<div style="width: 200px; height: 150px; display: flex;">
							<img
								class="img-attachment"
								src="' . $image_src[0] . '" image-id="' . get_post_thumbnail_id() . '" />
						</div>
						<div class="desc-main-container">
						<div class="desc-upper-container">'.get_the_title().'</div>
						<div class="desc-lower-container">
							<div class="desc-lower-title">Author:</div> <div class="desc-lower-text"> '.(get_the_author_meta( 'display_name' ) ? get_the_author_meta( 'display_name' ) : "-").'</div>
						</div>
						<div class="desc-lower-container" style="padding-bottom:10px;">
							<div class="desc-lower-title">Date:</div> <div> '.self::get_modified_gallery_date($post).'</div>
						</div>
					</li>';
			endwhile;
		}
		
		wp_send_json_success( array( "media_image_list" => $html, "page_number" => $PagedData_val, "searchMediaImage_val" => $SearchTitle_val ) );
	}
	
	public static function gallery_print_media_templates() {
			?><script type="text/html" id="tmpl-gallery-selector">
			<input type="hidden" name="gallery_selected_id" id="gallery_selected_id" />
			<input type="hidden" name="gallery_selected_slug" id="gallery_selected_slug" />
			<div class="selectgallery__preview">
				<?php	
					// Query to fetch galleries
					$gallery_data = self::get_gmr_galleries_data(1, null, null, null);
					
					$html = self::prepare_html($gallery_data, null, null, null);
					echo $html;
				?>
	
				</div>
			</script><?php
	}

	public static function prepare_html($gallery_data, $searchval = null, $selected_cat = null, $selected_tag = null) {
		global $post;
		$html = '
		<div id="main-container-mediaimg">
			<input type="hidden" name="page_number" id="page_number" class="page_number" value="1" />
			<div class="media-search"> Category:&nbsp; <select name="s_category" id="s_category" class="searchinputs">';

		// Search by category
		$categories = get_categories();
		$html .= '<option value=""> Any </option>';
		foreach ( $categories as $category ) :
			$selected = ($category->term_id == $selected_cat) ? 'selected' : '';
			$html .= '<option value="'.$category->term_id.'" '.$selected.'> '.$category->name.' </option>';
		endforeach;

		// Search by tag
		$html .= '</select> &nbsp;&nbsp; Tag:&nbsp; <select name="s_tag" id="s_tag" class="searchinputs">';
		$tags = get_tags();
		$html .= '<option value=""> Any </option>';
		foreach ( $tags as $tag ) :
			$selected = ($tag->term_id == $selected_tag) ? 'selected' : '';
			$html .= '<option value="'.$tag->term_id.'"  '.$selected.'> '.$tag->name.' </option>';
		endforeach;

		$html .= '
			</select> &nbsp;&nbsp;
			<input type="text" name="s_title" id="s_title" class="searchinputs" placeholder="Search by title..." value="'. $searchval.'" /> &nbsp;&nbsp;
			<button type="button" class="s_btn_mediaimage button" >Search</button> &nbsp;
			<span class="spinner" id="s_spinner"></span>
		</div>';
	
		if( $gallery_data->found_posts > 0 ) {
			$html .= '<ul class="select-gallery-ul">';

			while ( $gallery_data->have_posts() ) : $gallery_data->the_post();
				$jqueryEventSelectedClass = "'selected-gallery-thumbnail'";
				$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), array(200, 150));

				$html .= '
					<li class="select-exist-gallery-li" gallery-id="'.get_the_ID().'" slug-name="'.$post->post_name.'" onclick=" jQuery(\'.select-gallery-ul li\').removeClass(' . $jqueryEventSelectedClass .'); jQuery(\'.select-gallery-ul li\').css(\'box-shadow\', \'0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.19)\'); jQuery(this).addClass(' . $jqueryEventSelectedClass .'); " >
						<div style="width: 200px; height: 150px; display: flex;">
							<img
								class="img-attachment"
								src="' . $image_src[0] . '" image-id="' . get_post_thumbnail_id() . '" />
						</div>
						<div class="desc-main-container">
						<div class="desc-upper-container">'.get_the_title().'</div>
						<div class="desc-lower-container">
							<div class="desc-lower-title">Author:</div> <div class="desc-lower-text"> '.(get_the_author_meta( 'display_name' ) ? get_the_author_meta( 'display_name' ) : "-").'</div>
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
		return $html;
	}
}

ExistingGallerySelection::init();

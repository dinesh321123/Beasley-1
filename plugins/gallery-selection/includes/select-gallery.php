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
		// $post_types = array( 'listicle_cpt', 'gmr_gallery' );
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

	public static function select_gallery_title_filter( $where, $wp_query ){
		global $wpdb;
		if ( $search_term = $wp_query->get( 'search_prod_title' ) ) {
			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $search_term ) ) . '%\'';
		}
		return $where;
	}
	
	public static function get_gmr_galleries_data( $paged_value, $s_value = null, $s_category = null, $s_tag = null ) {
		global $wpdb;
		$return_result = array(); 
		$images = array();
		$query_images_args = array(
			'post_type'      => 'gmr_gallery',
			'post_status'    => 'publish',
			'posts_per_page' => 14,
			'paged'			 => $paged_value
		);
		
		$title_condition = isset( $s_value ) && $s_value !="" ? true : false;
		$category_condition = isset( $s_category ) && $s_category !="" ? true : false;
		$tag_condition = isset( $s_tag ) && $s_tag !="";
		$detected_flag = 0;

		if( $title_condition || $category_condition || $tag_condition ) {
			$wp_query_args = array(
				'posts_per_page' => -1,
				'post_type' => 'gmr_gallery',
				'post_status' => 'publish'
			);
			if(term_exists($s_value, 'category')) {
				if(term_exists($s_value, 'category')['term_id']) {
					$detected_flag = 1;
					$wp_query_args['cat'] = term_exists($s_value, 'category')['term_id'];
				}
			}
			if(term_exists($s_value, "post_tag")) {
				if(term_exists($s_value, "post_tag")['term_id']) {
					$detected_flag = 1;
					$wp_query_args['tag_id'] = term_exists($s_value, "post_tag")['term_id'];
				}
			}
			if($title_condition && $detected_flag === 0) {
				$wp_query_args['search_prod_title'] = $s_value;
				add_filter( 'posts_where', array( __CLASS__, 'select_gallery_title_filter'), 10, 2 );
				$gallery_filter_result = new WP_Query($wp_query_args);
				remove_filter( 'posts_where', array( __CLASS__, 'select_gallery_title_filter'), 10, 2 );
			} else {
				$gallery_filter_result = new WP_Query($wp_query_args);
			}
			$search = wp_list_pluck( $gallery_filter_result->posts, 'ID' );

			// Search Query Result
			if(count($search)) {
				$search = array_unique($search);
				$query_images_args['post__in'] = $search;
			}
	
			// If not found any result, Then don't show results
			if( !count($search) ) {
				$query_images_args['post__in'] = Array(0);
			}
			$return_result['searchids'] = $search;
			$return_result['data'] = new WP_Query( $query_images_args );
		} else {
			$return_result['data'] = new WP_Query( $query_images_args );
			$return_result['searchids'] = '';
		}
		return $return_result;
	}

	public static function get_gmr_gallery_data() {
		global $wpdb;
		$SearchTitle = filter_input( INPUT_GET, 's_title', FILTER_SANITIZE_SPECIAL_CHARS );
		$PagedData = filter_input( INPUT_GET, 'page_number', FILTER_SANITIZE_SPECIAL_CHARS );
		$SearchCat = filter_input( INPUT_GET, 's_category', FILTER_SANITIZE_SPECIAL_CHARS );
		$SearchTag = filter_input( INPUT_GET, 's_tag', FILTER_SANITIZE_SPECIAL_CHARS );
		
		$SearchTitle_val = $SearchTitle ? $SearchTitle : '';
		$PagedData_val = $PagedData ? $PagedData : '1';
		$SearchCat_val = $SearchCat ? $SearchCat : '';
		$SearchTag_val = $SearchTag ? $SearchTag : '';
	
		$gallery_data = self::get_gmr_galleries_data( $PagedData_val, $SearchTitle_val, $SearchCat_val, $SearchTag_val );
		$html = self::prepare_html($gallery_data['data'], $SearchTitle_val, $SearchCat_val, $SearchTag_val);
		
		$resutl = array( "html" => $html, "searchids" => $gallery_data['searchids'],"searchtitle" => $SearchTitle_val, "pageddata" => $PagedData_val, "searchcat" => $SearchCat_val, "searchtag" => $SearchTag_val );
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
		
		$gallery_data = self::get_gmr_galleries_data( $PagedData_val, $SearchTitle_val, $SearchCat_val, $SearchTag_val )['data'];

		if( $gallery_data->found_posts > 0 ) {
			while ( $gallery_data->have_posts() ) : $gallery_data->the_post();
				$jqueryEventSelectedClass = "'selected-gallery-thumbnail'";
				$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), array(200, 150));
	
				$html .= '
					<li class="select-exist-gallery-li" gallery-id="'.get_the_ID().'" slug-name="'.$post->post_name.'" onclick=" jQuery(\'.select-gallery-ul li\').removeClass(' . $jqueryEventSelectedClass .'); jQuery(\'.select-gallery-ul li\').css(\'box-shadow\', \'0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.19)\'); jQuery(this).addClass(' . $jqueryEventSelectedClass .'); " >
						<div style="width: 200px; height: 150px; display: flex;">
							<img
								class="img-attachment"
								src="' . ($image_src ? $image_src[0] : 'https://2.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=200&d=mm&r=g') . '" image-id="' . get_post_thumbnail_id() . '" />
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
					
					$html = self::prepare_html($gallery_data['data'], null, null, null);
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
			<div class="media-search">
				<input type="text" name="s_title" id="s_title" class="searchinputs" placeholder="Search here" value="'. $searchval.'" /> &nbsp;&nbsp;
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
								src="' . ($image_src ? $image_src[0] : 'https://2.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=200&d=mm&r=g') . '" image-id="' . get_post_thumbnail_id() . '" />
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

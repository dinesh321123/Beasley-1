<?php
/**
 * Class SegmentPermissionsMetaboxes
 */
class ExistingListicleSelection {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'admin_footer', array( __CLASS__, 'listicle_print_media_templates' ) );
		add_action( 'wp_footer', array( __CLASS__, 'listicle_print_media_templates' ) );
		add_action( 'customize_controls_print_footer_scripts', array( __CLASS__, 'listicle_print_media_templates' ) );
		add_action( 'wp_ajax_get_listicle_cpt_data', array( __CLASS__, 'get_listicle_cpt_data' ) );
		add_action( 'wp_ajax_load_more_listicle_cpt_data', array( __CLASS__, 'load_more_listicle_cpt_data' ) );
		add_filter('media_view_strings', array( __CLASS__, 'custom_media_string'), 10, 2);
	}

	public static function enqueue_scripts(){
		global $typenow, $pagenow;
		$post_types = array( 'listicle_cpt', 'gmr_gallery' );
		if ( !in_array( $typenow, $post_types ) ) {
			wp_register_style('existing-listicle-selection-admin', LISTICLE_SELECTION_URL . "assets/css/listicle_selection.css", array(), LISTICLE_SELECTION_VERSION, 'all');
			wp_enqueue_style('existing-listicle-selection-admin');
			wp_enqueue_script('existinglisticle', LISTICLE_SELECTION_URL . "assets/js/listicle_selection.js", array('media-views'), LISTICLE_SELECTION_VERSION, true);
			wp_enqueue_media();
			wp_enqueue_editor();
		}
	}

	public static function custom_media_string($strings,  $post){
		$strings['customMenuTitleListicle'] = __('Existing Listicle', 'existinglisticle');
		$strings['customButtonListicle'] = __('Add Existing Listicle', 'existinglisticle');
		return $strings;
	}

	public static function get_modified_listicle_date( $post = null ) {
		$post = get_post( $post );
		if ( is_a( $post, '\WP_Post' ) ) {
			$modified = mysql2date( 'G', $post->post_modified_gmt );
			return self::format_listicle_date( $modified, 1 );
		}
		return;
	}

	public static function format_listicle_date( $timestamp, $gmt = 0 ) {
		return date( "m / d / Y", $gmt ? $timestamp + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS : $timestamp );
	}

	public static function select_listicle_title_filter( $where, $wp_query ){
		global $wpdb;
		if ( $search_term = $wp_query->get( 'search_prod_title' ) ) {
			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $search_term ) ) . '%\'';
		}
		return $where;
	}

	public static function get_listicles_cpt_data( $paged_value, $s_value = null, $s_category = null, $s_tag = null ) {
		global $wpdb;
		$return_result = array();
		$images = array();
		$query_images_args = array(
			'post_type'      => 'listicle_cpt',
			'post_status'    => 'publish',
			'posts_per_page' => 14,
			'paged'			 => $paged_value
		);

		$title_condition = isset( $s_value ) && $s_value !="" ? true : false;
		$category_condition = isset( $s_category ) && $s_category !="" ? true : false;
		$tag_condition = isset( $s_tag ) && $s_tag !="";

		if( $title_condition || $category_condition || $tag_condition ) {
			$wp_query_args = array(
				'posts_per_page' => -1,
				'post_type' => 'listicle_cpt',
				'post_status' => 'publish'
			);
			if($category_condition) {
				$wp_query_args['cat'] = $s_category;
			}
			if($tag_condition) {
				$wp_query_args['tag_id'] = $s_tag;
			}
			if($title_condition) {
				$wp_query_args['search_prod_title'] = $s_value;
				add_filter( 'posts_where', array( __CLASS__, 'select_listicle_title_filter'), 10, 2 );
				$listicle_filter_result = new WP_Query($wp_query_args);
				remove_filter( 'posts_where', array( __CLASS__, 'select_listicle_title_filter'), 10, 2 );
			} else {
				$listicle_filter_result = new WP_Query($wp_query_args);
			}
			$search = wp_list_pluck( $listicle_filter_result->posts, 'ID' );

			// Search Query Result
			if(count($search)) {
				$search = array_unique($search);
				$query_images_args['post__in'] = $search;
			}

			// If not found any result, Then don't show results
			if( !count($search) ) {
				$query_images_args['post__in'] = Array(0);
			}
			$return_result['data'] = new WP_Query( $query_images_args );
			$return_result['searchids'] = $search;
		} else {
			$return_result['data'] = new WP_Query( $query_images_args );
			$return_result['searchids'] = '';
		}
		return $return_result;
	}

	public static function get_listicle_cpt_data() {
		global $wpdb;
		$SearchTitle = filter_input( INPUT_GET, 's_title', FILTER_SANITIZE_SPECIAL_CHARS );
		$PagedData = filter_input( INPUT_GET, 'page_number', FILTER_SANITIZE_SPECIAL_CHARS );
		$SearchCat = filter_input( INPUT_GET, 's_category', FILTER_SANITIZE_SPECIAL_CHARS );
		$SearchTag = filter_input( INPUT_GET, 's_tag', FILTER_SANITIZE_SPECIAL_CHARS );

		$SearchTitle_val = $SearchTitle ? $SearchTitle : '';
		$PagedData_val = $PagedData ? $PagedData : '1';
		$SearchCat_val = $SearchCat ? $SearchCat : '';
		$SearchTag_val = $SearchTag ? $SearchTag : '';

		$listicle_data = self::get_listicles_cpt_data( $PagedData_val, $SearchTitle_val, $SearchCat_val, $SearchTag_val );
		$html = self::prepare_html($listicle_data['data'], $SearchTitle_val, $SearchCat_val, $SearchTag_val);

		$resutl = array( "html" => $html, "searchids" => $listicle_data['searchids'], "searchtitle" => $SearchTitle_val, "pageddata" => $PagedData_val, "searchcat" => $SearchCat_val, "searchtag" => $SearchTag_val );
		wp_send_json_success( $resutl );
	}

	public static function load_more_listicle_cpt_data() {
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

		$listicle_data = self::get_listicles_cpt_data( $PagedData_val, $SearchTitle_val, $SearchCat_val, $SearchTag_val )['data'];

		if( $listicle_data->found_posts > 0 ) {
			while ( $listicle_data->have_posts() ) : $listicle_data->the_post();
				$jqueryEventSelectedClass = "'selected-listicle-thumbnail'";
				$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), array(200, 150));

				$html .= '
					<li class="select-exist-listicle-li" listicle-id="'.get_the_ID().'" slug-name="'.$post->post_name.'" onclick=" jQuery(\'.select-listicle-ul li\').removeClass(' . $jqueryEventSelectedClass .'); jQuery(\'.select-listicle-ul li\').css(\'box-shadow\', \'0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.19)\'); jQuery(this).addClass(' . $jqueryEventSelectedClass .'); " >
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
							<div class="desc-lower-title">Date:</div> <div> '.self::get_modified_listicle_date($post).'</div>
						</div>
					</li>';
			endwhile;
		}

		wp_send_json_success( array( "media_image_list" => $html, "page_number" => $PagedData_val, "searchMediaImage_val" => $SearchTitle_val ) );
	}

	public static function listicle_print_media_templates() {
			?><script type="text/html" id="tmpl-listicle-selector">
			<input type="hidden" name="listicle_selected_id" id="listicle_selected_id" />
			<input type="hidden" name="listicle_selected_slug" id="listicle_selected_slug" />
			<div class="selectlisticle__preview">
				<?php
					// Query to fetch listicles
					$listicle_data = self::get_listicles_cpt_data(1, null, null, null);

					$html = self::prepare_html($listicle_data['data'], null, null, null);
					echo $html;
				?>

				</div>
			</script><?php
	}

	public static function prepare_html($listicle_data, $searchval = null, $selected_cat = null, $selected_tag = null) {
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

		if( $listicle_data->found_posts > 0 ) {
			$html .= '<ul class="select-listicle-ul">';

			while ( $listicle_data->have_posts() ) : $listicle_data->the_post();
				$jqueryEventSelectedClass = "'selected-listicle-thumbnail'";
				$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), array(200, 150));

				$html .= '
					<li class="select-exist-listicle-li" listicle-id="'.get_the_ID().'" slug-name="'.$post->post_name.'" onclick=" jQuery(\'.select-listicle-ul li\').removeClass(' . $jqueryEventSelectedClass .'); jQuery(\'.select-listicle-ul li\').css(\'box-shadow\', \'0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.19)\'); jQuery(this).addClass(' . $jqueryEventSelectedClass .'); " >
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
							<div class="desc-lower-title">Date:</div> <div> '.self::get_modified_listicle_date($post).'</div>
						</div>
					</li>';
			endwhile;
			$html .= '
				</ul>
				<div style="text-align: center;"><span class="spinner" id="loadmore_spinner"></span></div>
				<div style="text-align: center;"><button type="button" id="media_loadmore" class="media_loadmore button button-secondary button-hero">Load more listicles</button></div>';
		} else {
			$html .= '<div class="no-existing-listicle-data"><h2 class="">No existing listicle found.</h2></div>';
		}
		$html .= '</div>';
		return $html;
	}
}

ExistingListicleSelection::init();

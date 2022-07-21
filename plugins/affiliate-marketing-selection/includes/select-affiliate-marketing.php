<?php
/**
 * Class SegmentPermissionsMetaboxes
 */
class ExistingAffiliateMarketingSelection {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		global $pagenow;
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'admin_footer', array( __CLASS__, 'am_print_media_templates' ) );
		add_action( 'customize_controls_print_footer_scripts', array( __CLASS__, 'am_print_media_templates' ) );
		add_action( 'wp_ajax_get_am_cpt_data', array( __CLASS__, 'get_am_cpt_data' ) );
		add_action( 'wp_ajax_load_more_am_cpt_data', array( __CLASS__, 'load_more_am_cpt_data' ) );
		add_filter('media_view_strings', array( __CLASS__, 'custom_media_string'), 10, 2);
		if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' || $pagenow == 'admin-ajax.php' ) && is_admin() ) {
			remove_filter('the_title', 'wptexturize');
		}
	}

	public static function enqueue_scripts() {
		wp_register_style('existing-am-selection-admin', AFFILIATE_MARKETING_SELECTION_URL . "assets/css/affiliate_marketing_selection.css", array(), AFFILIATE_MARKETING_SELECTION_VERSION, 'all');
		wp_enqueue_style('existing-am-selection-admin');
		wp_enqueue_script('existingaffiliatemarketing', AFFILIATE_MARKETING_SELECTION_URL . "assets/js/affiliate_marketing_selection.js", array('media-views'), AFFILIATE_MARKETING_SELECTION_VERSION, true);
		wp_enqueue_media();
		wp_enqueue_editor();
	}

	public static function custom_media_string($strings,  $post) {
		$strings['customMenuTitleAffiliateMarketing'] = __('Existing Affiliate Marketing Post', 'existingaffiliatemarketing');
		$strings['customButtonAffiliateMarketing'] = __('Add Existing Affiliate Marketing Post', 'existingaffiliatemarketing');
		return $strings;
	}

	public static function get_modified_am_date( $post = null ) {
		$post = get_post( $post );
		if ( is_a( $post, '\WP_Post' ) ) {
			$modified = mysql2date( 'G', $post->post_modified_gmt );
			return self::format_am_date( $modified, 1 );
		}
		return;
	}

	public static function format_am_date( $timestamp, $gmt = 0 ) {
		return date( "m / d / Y", $gmt ? $timestamp + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS : $timestamp );
	}

	public static function select_am_title_filter( $where, $wp_query ) {
		global $wpdb;
		if ( $search_term = $wp_query->get( 'search_prod_title' ) ) {
			$where .= ' AND LOWER(' . $wpdb->posts . '.post_title) LIKE \'%' . esc_sql( like_escape( $search_term ) ) . '%\'';
		}
		return $where;
	}

	public static function get_ams_cpt_data( $paged_value, $s_value = null, $s_category = null, $s_tag = null, $s_title_wo_filter = null ) {
		global $wpdb;
		$search = array();
		$title_search = array();
		$return_result = array();
		$images = array();
		$query_images_args = array(
			'post_type'      => 'affiliate_marketing',
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
				'post_type' => 'affiliate_marketing',
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
			if($title_condition) {
				$wp_title_query = array(
					'posts_per_page' => -1,
					'post_type' => 'affiliate_marketing',
					'post_status' => 'publish',
					'search_prod_title' => $s_title_wo_filter
				);
				add_filter( 'posts_where', array( __CLASS__, 'select_am_title_filter'), 10, 2 );
				$title_filter_am = new WP_Query($wp_title_query);
				remove_filter( 'posts_where', array( __CLASS__, 'select_am_title_filter'), 10, 2 );

				if(count($title_filter_am->posts)) {
					$title_search = wp_list_pluck( $title_filter_am->posts, 'ID' );
				}	
			}
			if($detected_flag == 1) {
				$am_filter_result = new WP_Query($wp_query_args);
				$search = wp_list_pluck( $am_filter_result->posts, 'ID' );
			}

			if(count($title_search)) {
				$search = array_unique( array_merge( $search, $title_search ) );
			}

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

	public static function get_am_cpt_data() {
		global $wpdb;
		$SearchTitle = filter_input( INPUT_GET, 's_title', FILTER_SANITIZE_SPECIAL_CHARS );
		$PagedData = filter_input( INPUT_GET, 'page_number', FILTER_SANITIZE_SPECIAL_CHARS );
		$SearchCat = filter_input( INPUT_GET, 's_category', FILTER_SANITIZE_SPECIAL_CHARS );
		$SearchTag = filter_input( INPUT_GET, 's_tag', FILTER_SANITIZE_SPECIAL_CHARS );

		$SearchTitle_val = $SearchTitle ? $SearchTitle : '';
		$PagedData_val = $PagedData ? $PagedData : '1';
		$SearchCat_val = $SearchCat ? $SearchCat : '';
		$SearchTag_val = $SearchTag ? $SearchTag : '';

		$am_data = self::get_ams_cpt_data( $PagedData_val, $SearchTitle_val, $SearchCat_val, $SearchTag_val, $_GET['s_title'] );
		$html = self::prepare_html($am_data['data'], $SearchTitle_val, $SearchCat_val, $SearchTag_val);

		$resutl = array( "html" => $html, "searchids" => $am_data['searchids'], "searchtitle" => $_GET['s_title'], "pageddata" => $PagedData_val, "searchcat" => $SearchCat_val, "searchtag" => $SearchTag_val );
		wp_send_json_success( $resutl );
	}

	public static function load_more_am_cpt_data() {
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

		$am_data = self::get_ams_cpt_data( $PagedData_val, $SearchTitle_val, $SearchCat_val, $SearchTag_val, $_GET['s_title'] )['data'];

		if( $am_data->found_posts > 0 ) {
			while ( $am_data->have_posts() ) : $am_data->the_post();
				$jqueryEventSelectedClass = "'selected-am-thumbnail'";
				$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), array(200, 150));

				$html .= '
					<li class="select-exist-am-li" am-id="'.get_the_ID().'" slug-name="'.$post->post_name.'" onclick=" jQuery(\'.select-am-ul li\').removeClass(' . $jqueryEventSelectedClass .'); jQuery(\'.select-am-ul li\').css(\'box-shadow\', \'0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.19)\'); jQuery(this).addClass(' . $jqueryEventSelectedClass .'); " >
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
							<div class="desc-lower-title">Date:</div> <div> '.self::get_modified_am_date($post).'</div>
						</div>
					</li>';
			endwhile;
		}

		wp_send_json_success( array( "media_image_list" => $html, "page_number" => $PagedData_val, "searchMediaImage_val" => $SearchTitle_val ) );
	}

	public static function am_print_media_templates() {
			?><script type="text/html" id="tmpl-am-selector">			
			<input type="hidden" name="am_selected_id" id="am_selected_id" value="{{{data.name}}}" />
			<input type="hidden" name="am_selected_slug" id="am_selected_slug" />
			<div class="selectam__preview">
				<?php
					// Query to fetch ams
					$am_data = self::get_ams_cpt_data(1, null, null, null, null);

					$html = self::prepare_html($am_data['data'], null, null, null);
					echo $html;
				?>

				</div>
			</script><?php
	}

	public static function prepare_html($am_data, $searchval = null, $selected_cat = null, $selected_tag = null) {
		global $post;
		$html = '
		<div id="main-container-mediaimg">
			<input type="hidden" name="page_number" id="page_number" class="page_number" value="1" />
			<div class="media-search">
				<input type="text" name="s_title" id="s_title" class="searchinputs" placeholder="Search here" value="'. $searchval.'" /> &nbsp;&nbsp;
				<button type="button" class="s_btn_mediaimage button" >Search</button> &nbsp;
				<span class="spinner" id="s_spinner"></span>
			</div>';

		if( $am_data->found_posts > 0 ) {
			$html .= '<ul class="select-am-ul">';

			while ( $am_data->have_posts() ) : $am_data->the_post();
				$jqueryEventSelectedClass = "'selected-am-thumbnail'";
				$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), array(200, 150));

				$html .= '
					<li class="select-exist-am-li" am-id="'.get_the_ID().'" slug-name="'.$post->post_name.'" onclick=" jQuery(\'.select-am-ul li\').removeClass(' . $jqueryEventSelectedClass .'); jQuery(\'.select-am-ul li\').css(\'box-shadow\', \'0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.19)\'); jQuery(this).addClass(' . $jqueryEventSelectedClass .'); " >
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
							<div class="desc-lower-title">Date:</div> <div> '.self::get_modified_am_date($post).'</div>
						</div>
					</li>';
			endwhile;
			$html .= '
				</ul>
				<div style="text-align: center;"><span class="spinner" id="loadmore_spinner"></span></div>
				<div style="text-align: center;"><button type="button" id="media_loadmore" class="media_loadmore button button-secondary button-hero">Load more affiliate marketing posts</button></div>';
		} else {
			$html .= '<div class="no-existing-am-data"><h2 class="">No existing affiliate marketing post found.</h2></div>';
		}
		$html .= '</div>';
		return $html;
	}
}

ExistingAffiliateMarketingSelection::init();

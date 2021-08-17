<?php
/**
 * Plugin Name: Featured Videos
 * Plugin URI:
 * Description: Import Youtube or Vimeo videos into media library and use it as featured image for articles.
 * Version: 1.0.0
 * Author: 10up
 * Author URI: http://10up.com/
 * License: BSD 2-Clause
 * License URI: http://www.opensource.org/licenses/bsd-license.php
 * Text Domain: fvideos
 * Domain Path: /languages
 */

define( 'FVIDEOS_VERSION', '1.0.0' );

add_action( 'wp_enqueue_media', 'fvideos_enqueue_scripts' );
add_action( 'admin_footer', 'fvideos_print_media_templates' );
add_action( 'wp_footer', 'fvideos_print_media_templates' );
add_action( 'customize_controls_print_footer_scripts', 'fvideos_print_media_templates' );
add_action( 'wp_ajax_fvideos_get_embed', 'fvideos_discover_oembed' );
add_action( 'wp_ajax_fvideos_import_embed', 'fvideos_import_oembed' );
add_action( 'wp_ajax_fvideos_get_media_image', 'fvideos_get_media_image' );
add_action( 'wp_ajax_fvideos_load_more_media_image', 'fvideos_load_more_media_image' );
add_action( 'wp_ajax_get_selected_media_image', 'get_selected_media_image' );
add_action( 'plugins_loaded', 'fvideos_load_textdomain' );

add_filter( 'post_thumbnail_html', 'fvideos_post_thumbnail_video', 10, 3 );
add_filter( 'post_class', 'fvideos_update_post_classes', 10, 3 );

function fvideos_load_textdomain() {
	load_plugin_textdomain( 'fvideos', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

function fvideos_enqueue_scripts() {
	$min = defined( 'SCRIPT_DEBUG' ) && filter_var( SCRIPT_DEBUG, FILTER_VALIDATE_BOOLEAN ) ? '' : '.min';
	
	wp_enqueue_script( 'fvideo', plugins_url( "/assets/dist/media{$min}.js", __FILE__ ), array( 'wp-util' ), FVIDEOS_VERSION, true );
	wp_localize_script( 'fvideo', 'fvideo', array(
		'wrongUrl'    => esc_html__( 'Please, enter valid URL', 'fvideos' ),
		'cannotEmbed' => esc_html__( 'Unexpected error happened during import', 'fvideos' ),
		'missingImage' => esc_html__( 'Please, select thumbnail image', 'fvideos' ),
		'missingMediaImage' => esc_html__( 'Please, select media thumbnail image', 'fvideos' ),
		'cannotEmbedImage' => esc_html__( 'Unexpected error happened during image select', 'fvideos' ),
	) );
	wp_register_style('fvideostyle',plugins_url( "/assets/dist/media{$min}.css", __FILE__ ), array(), FVIDEOS_VERSION, 'all');
	wp_enqueue_style('fvideostyle');
}

function fvideos_post_thumbnail_video( $html, $post_id, $thumbnail_id ) {
	$show_video = is_singular();
	$show_video = apply_filters( 'fvideos_show_video', $show_video, $post_id, $thumbnail_id );
	if ( $show_video ) {
		$embed = get_post_meta( $thumbnail_id, 'embed', true );
		if ( ! empty( $embed ) && ! empty( $embed['html'] ) ) {
			$html = sprintf( '<div class="fvideos">%s</div>', $embed['html'] );
			$html = apply_filters( 'fvideos_video_html', $html, $embed, $post_id, $thumbnail_id );
		}
	}

	return $html;
}

function fvideos_update_post_classes( $classes, $class, $post_id ) {
	$thumbnail_id = get_post_thumbnail_id( $post_id );
	$thumbnail_id = apply_filters( 'fvideos_post_thumbnail_id', $thumbnail_id, $post_id );
	if ( ! empty( $thumbnail_id ) ) {
		$embed = get_post_meta( $thumbnail_id, 'embed', true );
		if ( ! empty( $embed ) && ! empty( $embed['html'] ) ) {
			$classes[] = 'has-featured-video';
		}
	}

	return $classes;
}

function fvideos_print_media_templates() {
	?><script type="text/html" id="tmpl-video-embed-import">
		<div class="video__embed embed-url cus_video__embed">
			<label> Video URL </label>
			<div class="video_input">
				<input type="url" class="video__url" placeholder="https://...">
				<button type="button" class="video__submit button button-primary button-hero" disabled>&rarr;</button>
				<span class="spinner" id="video__submit_spinner"></span>
			</div>
			<div class="video__preview"></div>
		</div>
		<div class="image__embed embed-image">
			<div class="radio__img__options">
				<label>Thumbnail Image</label>
				<div> 
					<input type="radio" id="upload_image" name="image_option" value="upload_image"> Upload Image
					<input type="radio" id="select_media_library" name="image_option" value="select_media_library"> Media library
				</div>
			</div>
			
			<!-- Open Media Library -->
			<div class="media__img__option" style="display: none;">
				<input type="button" class="button video__mediaimg" id="video__mediaimg" value="Open Media Library" />
				<input type="hidden" name="media_image_id" id="media_image_id" />
				<span class="spinner" id="image__preview_spinner"></span>
				<div class="image__preview"></div>
			</div>
			
			<div class="upload__img__option" style="display: none;">
				<!-- Select file from local system -->
				<input type="file" name="custom_featured_img" id="custom_featured_img" />
			</div>
			<div class="mediaimage__preview"></div>
		</div>
	</script><?php
}

function fvideos_get_oembed( $url ) {
	$found = false;
	$key = 'fvideos-embed-' . $url;
	$embed = wp_cache_get( $key, 'fvideos', false, $found );
	if ( ! $found ) {
		$embed = _wp_oembed_get_object()->get_data( $url );
		wp_cache_set( $key, $embed, 'fvideos', 5 * MINUTE_IN_SECONDS );
	}
	if ( $embed->provider_name == 'YouTube' && false !== stripos( $embed->thumbnail_url, 'hqdefault.jpg' ) ) {
		$embed->thumbnail_url = str_ireplace(
			'hqdefault.jpg',
			'mqdefault.jpg',
			$embed->thumbnail_url
		);
	}

	return $embed;
}

function fvideos_discover_oembed() {
	$url = filter_input( INPUT_GET, 'url', FILTER_VALIDATE_URL );
	if ( ! $url ) {
		wp_send_json_error( $_GET['url'] );
	}

	$embed = fvideos_get_oembed( $url );
	if ( ! empty( $embed->html ) ) {
		wp_send_json_success( $embed->html );
	} else {
		wp_send_json_error();
	}
}

function get_images_from_media_library( $s_value = null, $paged_value ) {
	global $wpdb;
	$images = array();
	$query_images_args = array(
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'post_status'    => 'inherit',
		'posts_per_page' => 16,
		'paged'			 => $paged_value
	);
	
	if( isset( $s_value ) && $s_value !="" ) {
		$search = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_title LIKE %s AND post_type = 'attachment'", '%' . $wpdb->esc_like($s_value) . '%' ) );
		// echo $wpdb->last_query;
		$query_images_args['post__in'] = $search;
		// print_r($query_images_args);
		$query_images = new WP_Query( $query_images_args );
		if( count($query_images_args['post__in']) > 0 && !empty( $query_images_args['post__in'] ) )
		{
			foreach ( $query_images->posts as $image) {
				$image_guid = wp_get_attachment_url( $image->ID );
				$images[ $image->ID ]= $image_guid;
			}
		}
	} else {
		$query_images = new WP_Query( $query_images_args );
		// print_r( $query_images->posts );
		// echo '--- Page '.$paged_value.' --- ';
		foreach ( $query_images->posts as $image) {
			$image_guid = wp_get_attachment_url( $image->ID );
			// $file = get_post_meta( $post->ID, '_wp_attached_file', true );
			$images[ $image->ID ]= $image_guid;
		}
	}
	$result = array( "imgs" => $images, "imgs_array" => $query_images->posts );
    return $result;
}

function fvideos_load_more_media_image() {
	$html = '';
	$searchMediaImage = filter_input( INPUT_GET, 's_mediaimage', FILTER_SANITIZE_SPECIAL_CHARS );
	$paged_mediaimage = filter_input( INPUT_GET, 'paged_mediaimage', FILTER_SANITIZE_SPECIAL_CHARS );

	$searchMediaImage_val = $searchMediaImage ? $searchMediaImage : '';
	$paged_mediaimage_val = $paged_mediaimage ? $paged_mediaimage+1 : '1';
	// echo "-- Passed value".$paged_mediaimage_val.' --- ';

	$imgs = get_images_from_media_library( $searchMediaImage, $paged_mediaimage_val );
	//print_r(count($imgs));
	// exit;
	if( !empty( $imgs['imgs'] ) && count( $imgs['imgs'] ) > 0 ) {
		foreach( $imgs['imgs'] as $imgid => $img ) {
			$jqueryEventSelectedClass = "'selected-media-img'";
			$html .= '<li class="mediaimg-li" >';
				$html .= '<img class="img-attachment" src="' . $img . '" alt="" image-id="' . $imgid . '" onclick="jQuery(this).addClass(' . $jqueryEventSelectedClass .')" />';
			$html .= '</li>';
		}
	}
	
	wp_send_json_success( array( "media_image_list" => $html, "paged_mediaimage" => $paged_mediaimage_val, "searchMediaImage_val" => $searchMediaImage_val, "imgs_array" => json_encode( $imgs ) ) );
}

function fvideos_get_media_image() {
	$searchMediaImage = filter_input( INPUT_GET, 's_mediaimage', FILTER_SANITIZE_SPECIAL_CHARS );
	$paged_mediaimage = filter_input( INPUT_GET, 'paged_mediaimage', FILTER_SANITIZE_SPECIAL_CHARS );

	$searchMediaImage_val = $searchMediaImage ? $searchMediaImage : '';
	$paged_mediaimage_val = $paged_mediaimage ? $paged_mediaimage : '1';

	$imgs = get_images_from_media_library( $searchMediaImage_val, $paged_mediaimage_val );
	// print_r( $imgs['imgs'] );
	// exit;
	$html = '<div id="main-container-mediaimg">';
	$html .= '<input type="hidden" name="paged_mediaimage" id="paged_mediaimage" class="paged_mediaimage" value="'. $paged_mediaimage_val .'" />';
	$html .= '<div class="media-search"> <span class="spinner" id="s_spinner"></span> <input type="text" name="s_mediaimage" id="s_mediaimage" class="s_mediaimage" placeholder="Search media items..." value="'. $searchMediaImage_val .'" /> <button type="button" class="s_btn_mediaimage button" >Search</button>
	</div>' ;
	if( !empty( $imgs['imgs'] ) && count( $imgs['imgs'] ) > 0 ) {
		$html .= '<ul class="mediaimg-ul">';
		foreach( $imgs['imgs'] as $imgid => $img ) {
			$jqueryEventSelectedClass = "'selected-media-img'";
			/* $html .= '<li class="" >';
				$html .= '<img class="img-attachment" src="' . $img . '" alt="" image-id="' . $imgid . '" onclick="$(this).addClass(' . $jqueryEventSelectedClass .')" />';
			$html .= '</li>'; */
			$html .= '<li class="mediaimg-li" >';
				$html .= '<img class="img-attachment" src="' . $img . '" alt="" image-id="' . $imgid . '" onclick="jQuery(this).addClass(' . $jqueryEventSelectedClass .')" />';
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= '<div style="text-align: center;"><span class="spinner" id="loadmore_spinner"></span></div>';		
		$html .= '<div style="text-align: center;"><button type="button" id="media_loadmore" class="media_loadmore button button-secondary button-hero">Load more media images</button></div>';	
	} else {
		$html .= '<div class=""><h2 class="">No items found.</h2></div>';
	}
	$html .= '</div>';

	$resutl = array( "html" => $html, "imgs_array" => json_encode( $imgs ) );
	wp_send_json_success( $resutl );
}

function get_images_li_view(){

}

function get_selected_media_image() {
	$imageAttrId = filter_input( INPUT_GET, 'imageAttrId', FILTER_VALIDATE_INT );
	$imageSrc = "";
	if ( ! $imageAttrId ) {
		wp_send_json_error( $_GET['imageAttrId'] );
	}
	$imageAttributes = wp_get_attachment_image_src( $imageAttrId );
	
	$html = '<div id="img-main-container">';
	if ( $imageAttributes ) : 
		$imageSrc = $imageAttributes[0];
		$html .= '<img class="" src="' . $imageAttributes[0] . '" width="100" height="100"/>';
		// $html .= '<img class="" src="' . $imageAttributes[0] . '" width="' . $imageAttributes[1] . '" height="' . $imageAttributes[2] . '"/>';
	endif;
	$html .= '</div>';
	$result = array( "single_image_div" => $html, "imageAttrId" => $imageAttrId, "imageSrc" => $imageSrc );
	
	wp_send_json_success( $result );
}

function fvideos_import_oembed() {
	/* print_r( $_REQUEST );
	print_r( $_FILES['imagearr']['name'] );
	wp_send_json_success( "RJ working here" );
	wp_die(); */
	$url = filter_input( INPUT_POST, 'url', FILTER_VALIDATE_URL );
	
	if ( ! $url ) {
		wp_send_json_error( $_POST['url'] );
	}

	$embed = fvideos_get_oembed( $url );
	if ( ! empty( $embed->html ) ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$title			= strip_tags( $embed->title );
		$file_array		= array();
		$image_option	= $_POST['image_option'];

		if( isset( $image_option ) && $image_option == 'select_media_library' ){
			$img_atts = wp_get_attachment_image_src($_POST['mediaImageId'], 'full');
			
			if( !empty($img_atts) ) {
				$file_array['name'] 	= str_replace( ' ', '-', mb_strtolower( $title ) ) . '.jpg';
				$file_array['tmp_name'] = download_url( $img_atts[0] );
			}
		} else {
			// removing white space
				// $imageName = preg_replace('/\s+/', '-', $_FILES['imagearr']['name'] );
			// removing special character but keep . character because . seprate to extantion of file
				// $imageName = preg_replace('/[^A-Za-z0-9.\-]/', '', $imageName);
			// rename file using time
			// $imageName = time().'-'.$_FILES['imagearr']['name'];
			$imageName = $_FILES['imagearr']['name'];

			// $file_array['name'] = str_replace( ' ', '-', mb_strtolower( $title ) ) . '.jpg';
			// $file_array['tmp_name'] = download_url( $embed->thumbnail_url );
			$file_array['name']			= $imageName;
			// $file_array['tmp_name']		= $_FILES['imagearr']['tmp_name'];	// /tmp/1629112272-celtics400icon-0oJi4I.tmp
			$file_array['tmp_name']		= '/tmp/pip-a-short-animated-film-by-southeastern-guide-dogs-5lnsSK.tmp';
		}
		
		// print_r( $_FILES );
		$isWpError = is_wp_error( $file_array['tmp_name'] );
		/* echo $isWpError;
		echo $file_array['tmp_name'];
		print_r( $file_array );
		exit; */
		if ( ! $isWpError ) {
			echo "IN isWpError condition----- ";
			print_r($isWpError);
			print_r($file_array);
			$post_id = filter_input( INPUT_POST, 'post_id', FILTER_VALIDATE_INT );
			$image_id = media_handle_sideload( $file_array, $post_id, $title );
			
			print_r($image_id);
			if ( is_int( $image_id ) ) {
				// $embed_array = json_decode( json_encode( $embed ), true );
				// update_post_meta( $image_id, 'embed', $embed_array );

				// wp_send_json_success( $image_id );
			}
		}
	}

	$error = array( "File_Array" => $file_array, "Embed_Array" => $embed, "isWpError" => $isWpError, "post_value" => $_POST, "file_value" => $_FILES );
	wp_send_json_error( $error );
}

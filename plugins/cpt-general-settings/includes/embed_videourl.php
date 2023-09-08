<?php
/**
 * Class EmbedVideoURL to manage embed field in media 
 */
class EmbedVideoURL {
	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_filter( 'attachment_fields_to_edit', array( __CLASS__, 'videourl_field_credit' ), 10, 2 );
		
		add_action( 'wp_ajax_validate_embed_videourl', array( __CLASS__, 'validate_embed_videourl_action' ) );
		add_action( 'wp_ajax_nopriv_validate_embed_videourl', array( __CLASS__, 'validate_embed_videourl_action' ) );
	}

	public static function validate_embed_videourl_action () {
		$embed_url	=	filter_input(INPUT_POST, "getEmbedVideoUrl", FILTER_VALIDATE_URL);
		$getPostid	=	filter_input(INPUT_POST, "getPostid", FILTER_VALIDATE_INT);
		
		if ( ! $embed_url ) {
			$error = array( "message" => 'URL not valid', "post_value" => $embed_url );
			wp_send_json_error( $error );
		}
		
		
		$embed = _wp_oembed_get_object()->get_data( $embed_url );
		if ( $embed->type != 'error' && isset($embed->html) && !empty( $embed->html ) ) {
			delete_post_meta( $getPostid, 'embed' );
			
			$embed_array = json_decode( json_encode( $embed ), true );
			update_post_meta( $getPostid, 'embed', $embed_array );
			
			$result = array( "message" => 'URL update successfully...', "embed_data" => array( $embed_array, $embed_url ) );
			wp_send_json_success( $result );
		}

		$error = array( "message" => 'URL not valid', "embed_data" => $embed );
		wp_send_json_error( $error );
	}

	public static function get_video_url($embed) {
		$url	=	"";
		if( isset($embed['provider_name']) && $embed['provider_name'] == 'YouTube' ) {
			$matchUrl ="";
			preg_match( '/src="([^"]+)"/', $embed['html'], $matchUrl );
			$youtubeMatchUrl	= isset($matchUrl[1]) && $matchUrl[1] != "" ? $matchUrl[1] : "";
			$url = isset($embed['url']) && $embed['url'] != "" ? $embed['url'] : $youtubeMatchUrl;
			
		} else if ( isset($embed['type']) && $embed['type'] == 'video' ) {
			$url = $embed['provider_url'].''.$embed['video_id'];
		}
		return $url;
		exit;
		
	}

	public static function videourl_field_credit( $form_fields, $post ) {
		$embed	=	get_post_meta($post->ID, 'embed', true);
		$url	=	EmbedVideoURL::get_video_url($embed);

		$form_fields['embed_field'] = array(
			'label' => 'Video URL',
			'html' => "<input type='text' class='embed_field_url' name='embed_field' value='". $url ."'><input type='hidden' class='embed_field_mediaid' value='". $post->ID."' /><span class='spinner' id='embed_field_spinner'></span><textarea id='w3review' name='w3review' rows='4' cols='50' style='display: none;'>'". json_encode($embed) ."'</textarea>",
			'input' => 'html'
		);
		return $form_fields;
	}
	
	
	


	/**
	 * Enqueues admin scripts and styles.
	 *
	 * @global string $typenow The current type.
	 * @global string $pagenow The current page.
	 */
	public static function enqueue_scripts() {
		global $typenow, $pagenow;
		// Condition for any edit, add or media edit page
		if ( in_array( $pagenow, array( 'post.php', 'post-new.php', 'upload.php' ) ) ) {
			$min = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_script( 'embed_videourl_admin', GENERAL_SETTINGS_CPT_URL . "assets/js/embed_videourl{$min}.js", array('jquery'), GENERAL_SETTINGS_CPT_VERSION, true );
			wp_localize_script( 'embed_videourl_admin', 'my_ajax_object', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
		}
	}
}

EmbedVideoURL::init();

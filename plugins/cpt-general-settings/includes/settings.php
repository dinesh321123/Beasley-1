<?php
/**
 * Class CommonSettings
 */
class CommonSettings {
	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'settings_cpt_init' ), 0 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'admin_head', array( __CLASS__, 'required_alt_text' ) );	// Script for validate Alt text from Add media button
		add_action('admin_footer', array( __CLASS__, 'sticky_publish_box' ) );	// Script to sticky/floating publish box
		add_action( 'save_post',array( __CLASS__, 'select_embed_data_save') );
	}

	/**
	 * Code to sticky publish box in admin side
	 */
	public static function sticky_publish_box() {
		global $typenow, $pagenow;
		if ( in_array( $typenow, CommonSettings::allow_sticky_posttype_list() ) && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			jQuery(window).scroll(function() {
			    var scroll = jQuery(window).scrollTop();
			    if (scroll >= 200) {
			        jQuery("div#side-sortables").addClass("sticky_publish");
			    }
			    else{
			    	jQuery("div#side-sortables").removeClass("sticky_publish");
			    }
			}); //missing );
		});
		</script>
		<style type="text/css">
			.sticky_publish div#submitdiv {
				position: fixed;
				min-width: 277px;
				z-index: 999;
				top:35px;
			}
			.sticky_publish div#submitdiv.closed {
				min-width: 277px;
			}
		</style>
	<?php } //Close if condition
	}
	/**
	 * Returns array of post type.
	 */
	public static function allow_sticky_posttype_list() {
		return (array) apply_filters( 'allow-sticky-for-posttypes', array( 'post', 'page', 'tribe_events', 'listicle_cpt', 'affiliate_marketing', 'gmr_gallery', 'gmr_album', 'contest', 'show', 'podcast', 'episode', 'content-kit' ) );
	}

	public static function required_alt_text() {
		global $typenow, $pagenow;
		if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) { ?>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					jQuery(document).on('click','button.button.insert-media.add_media',function(e){
						setTimeout(function(e){
							var buttonInsertContent = jQuery('.supports-drag-drop[style="position: relative;"] .media-button-insert');
							clickListenerContent = jQuery._data(buttonInsertContent[0], 'events').click[0];
							buttonInsertContent.off('click');
							jQuery('.supports-drag-drop[style="position: relative;"] .media-button-insert').click(function(e){
								var altTextContent = jQuery('input#attachment-details-alt-text').val();
								if ( altTextContent  || jQuery('input#attachment-details-alt-text').length == 0 )
								{
									jQuery('.supports-drag-drop[style="position: relative;"] .media-button-insert').unbind("click");
									buttonInsertContent.click(clickListenerContent.handler);
									buttonInsertContent.triggerHandler('click');
								} else {
									alert( 'ERROR: Please fill the Alt text.' );
									jQuery('input#attachment-details-alt-text').focus();
									return false;
								}
							});
						},1000);
					});
					jQuery('a#set-post-thumbnail').click(function(e){
						setTimeout(function(e){
							var buttonInsertFeature = jQuery('.supports-drag-drop[style="position: relative;"] .media-button-select');
							clickListenerFeature = jQuery._data(buttonInsertFeature[0], 'events').click[0];
							buttonInsertFeature.off('click');
							jQuery('.supports-drag-drop[style="position: relative;"] .media-button-select').click(function(e){
								var altTextFeature = jQuery('input#attachment-details-alt-text').val();
								if ( altTextFeature  || jQuery('input#attachment-details-alt-text').length == 0 )
								{
									jQuery('.supports-drag-drop[style="position: relative;"] .media-button-select').unbind("click");
									buttonInsertFeature.click(clickListenerFeature.handler);
									buttonInsertFeature.triggerHandler('click');
								} else {
									alert( 'ERROR: Please fill the Alt text.' );
									jQuery('input#attachment-details-alt-text').focus();
									return false;
								}
							});
						},500);
					});
				});
				jQuery( document ).ajaxComplete(function(event, xhr, settings) {
					var params = {}, queries, temp, i, l;
					// Split into key/value pairs
					queries = settings.data.split("&");
					// Convert the array of strings into an object
					for ( i = 0, l = queries.length; i < l; i++ ) {
						temp = queries[i].split('=');
						params[temp[0]] = temp[1];
					}
					var data= params;
					if(data.action == 'get-post-thumbnail-html'){
						setTimeout(function(e){
							jQuery('a#set-post-thumbnail').click(function(e){
								setTimeout(function(e){
									var buttonInsertFeatureAjax = jQuery('.supports-drag-drop[style="position: relative;"] .media-button-select');
									clickListenerFeatureAjax = jQuery._data(buttonInsertFeatureAjax[0], 'events').click[0];
									buttonInsertFeatureAjax.off('click');
									jQuery('.supports-drag-drop[style="position: relative;"] .media-button-select').click(function(e){
										var altTextFeatureAjax = jQuery('input#attachment-details-alt-text').val();
										if ( altTextFeatureAjax  || jQuery('input#attachment-details-alt-text').length == 0 )
										{
											jQuery('.supports-drag-drop[style="position: relative;"] .media-button-select').unbind("click");
											buttonInsertFeatureAjax.click(clickListenerFeatureAjax.handler);
											buttonInsertFeatureAjax.triggerHandler('click');
										} else {
											alert( 'ERROR: Please fill the Alt text.' );
											jQuery('input#attachment-details-alt-text').focus();
											return false;
										}
									});
								},500);
							});
						},500);
					}
				});
			</script>
		<?php }
	}

	public static function settings_cpt_init() {
		// Register custom capability for Draft Kings On/Off Setting and Max mega menu
		$roles = [ 'administrator' ];

		foreach ( $roles as $role ) {
			$role_obj = get_role($role);

			if (is_a($role_obj, \WP_Role::class)) {
				$role_obj->add_cap('manage_draft_kings_onoff_setting', false);
				$role_obj->add_cap('manage_max_mega_menu', false);
			}
		}

		add_filter( 'megamenu_options_capability', array( __CLASS__, 'megamenu_options_capability_callback' ) );
	}

	public static function megamenu_options_capability_callback() {
		return 'manage_max_mega_menu';
	}
	/**
	 * Returns array of post type.
	 *
	 * @return array
	 */
	public static function allow_fontawesome_posttype_list() {
		return (array) apply_filters( 'allow-font-awesome-for-posttypes', array( 'listicle_cpt', 'affiliate_marketing' )  );
	}
	public static function allow_require_feature_img_posttype_list() {
		return (array) apply_filters( 'allow-font-awesome-for-posttypes', array( 'post', 'page', 'listicle_cpt', 'affiliate_marketing' )  );
	}

	/**
	 * Enqueues admin scripts and styles.
	 *
	 * @global string $typenow The current type.
	 * @global string $pagenow The current page.
	 */
	public static function enqueue_scripts() {
		global $typenow, $pagenow;
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

		if ( in_array( $typenow, CommonSettings::allow_fontawesome_posttype_list() ) && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			wp_register_style('general-font-awesome',GENERAL_SETTINGS_CPT_URL . "assets/css/general-font-awesome". $postfix .".css", array(), GENERAL_SETTINGS_CPT_VERSION, 'all');
			wp_enqueue_style('general-font-awesome');
		}

		if ( in_array( $typenow, CommonSettings::allow_require_feature_img_posttype_list() ) && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			wp_register_script(
					'required-feature-img-admin-js',
					GENERAL_SETTINGS_CPT_URL . "assets/js/require-featured-image-onedit". $postfix .".js",
					array( 'jquery' ), '0.1' );
			// wp_register_script( 'required-feature-img-admin-js', GENERAL_SETTINGS_CPT_URL . "assets/js/require-featured-image-onedit.js", array( 'jquery' ) );
			wp_enqueue_script( 'required-feature-img-admin-js' );

			wp_localize_script(
					'required-feature-img-admin-js',
					'passedFromServer',
					array(
							'jsWarningHtml' => __( '<strong>This entry has no featured image.</strong> Please set one. You need to set a featured image before publishing.', 'require-featured-image' ),
					)
			);
		}
	}

	public static function select_embed_verify_post($attributes) {
		if( empty($attributes['post_id']) || empty($attributes['post_type']) ) {
			return '';
		}

		$post_id = '';

		if ($attributes['syndication_name']) {
			$meta_query_args = [
				'meta_key'    => 'syndication_old_name',
				'meta_value'  => $attributes['syndication_name'],
				'post_status' => 'any',
				'post_type'   => $attributes['post_type']
			];

			$existing = get_posts($meta_query_args);

			if ($existing) {
				$existing_post = current($existing);
				$post_id = intval($existing_post->ID);
			}
		}

		$post_id = $post_id ? $post_id : $attributes['post_id'];
		$post = get_post($post_id);
		if ($post->post_type !== $attributes['post_type'] || $post->post_name !== $attributes['syndication_name']) {
			return '';
		}

		return $post_id;
	}

	public static function select_embed_data_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! current_user_can( 'edit_post' ) ) return;

		$pattern = get_shortcode_regex();
		$select_embed_post_id = '';
		$select_embed_post_types = array( 'post', 'listicle_cpt', 'affiliate_marketing', 'gmr_gallery'  );

		if ( $_POST['post_type'] && in_array( $_POST['post_type'], $select_embed_post_types ) ) {
			if (   preg_match_all( '/'. $pattern .'/s', $_POST['post_content'].$_POST['common_footer_description'], $matches )
				&& array_key_exists( 2, $matches )
				&& (in_array( 'select-listicle', $matches[2] ) || in_array( 'select-gallery', $matches[2] ) || in_array( 'select-am', $matches[2] ))
			) {
				$select_embed_type = (count($matches[2]) > 0) ? $matches[2][0] : '';
				$attributes =  (count($matches[0]) > 0) ? self::eri_shortcode_parse_atts(str_replace('\"', '"', $matches[0][0])) : '';

				if ($select_embed_type == 'select-gallery') {
					$attributes['post_id'] = $attributes['gallery_id'];
					$attributes['post_type'] = 'gmr_gallery';
				} else if ($select_embed_type == 'select-listicle') {
					$attributes['post_id'] = $attributes['listicle_id'];
					$attributes['post_type'] = 'listicle_cpt';
				} else if ($select_embed_type == 'select-am') {
					$attributes['post_id'] = $attributes['am_id'];
					$attributes['post_type'] = 'affiliate_marketing';
				}

				$select_embed_post_id = self::select_embed_verify_post($attributes);
			}

			update_post_meta( $post_id, 'select_embed_post_id', !empty($select_embed_post_id) ? $select_embed_post_id : '' );
		}
	}

	public static function eri_shortcode_parse_atts( $shortcode ) {
		// Store the shortcode attributes in an array here
		$attributes = [];

		// Get all attributes
		if (preg_match_all('/\w+\=\".*?\"/', $shortcode, $key_value_pairs)) {

			// Now split up the key value pairs
			foreach($key_value_pairs[0] as $kvp) {
				$kvp = str_replace('"', '', $kvp);
				$pair = explode('=', $kvp);
				$attributes[$pair[0]] = $pair[1];
			}
		}

		// Return the array
		return $attributes;
	}
}

CommonSettings::init();

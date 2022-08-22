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
		add_action('admin_footer', array( __CLASS__, 'my_admin_footer_function' ) );
	}
	function my_admin_footer_function() {
		$id			= get_the_ID();
		$user_id	= wp_check_post_lock( $id );
		if ( $user_id ) {
			$user	= get_userdata( $user_id );
			$name	= $user->display_name;
		}
		$type		=  get_post_type( $id );
		$message	= "has taken over and is currently editing";
		$lock_url	= esc_url( add_query_arg( 'get-post-lock', '1', wp_nonce_url( get_edit_post_link( $id, 'url' ), 'lock-post_' . $id ) ) );
		$post		= get_post();
		$admin_link	= admin_url( 'edit.php' );
		$edit_link	= esc_url(add_query_arg( 'post_type', $post->post_type, $edit_link ));


		if ( $user_id !='' AND $type !='') {
			?>
			<script type="text/javascript">
				jQuery( document ).ready(function() {

					var name		= "<?php echo $name; ?>";
					var type		= "<?php echo $type; ?>";
					var lock_url	= "<?php echo $lock_url; ?>";
					var message		= "<?php echo $message; ?>";
					var edit_link	= "<?php echo $edit_link; ?>";
					var admin_url	= "<?php echo $admin_link; ?>";
					console.log("Custom post type: " + type);
					console.log("Custom Name: " + name);
					console.log("Custom Lock URL: " + lock_url);
					console.log("Custom Message: " + message);
					console.log("Custom Edit link: " + edit_link);
					console.log("Custom Message: " + admin_url);

					if (lock_url) {
						jQuery(".notification-dialog p:nth-child(3)").append('<a class="button" href="'+lock_url+'">Tack Over</a>');
					}
					jQuery("a.button.button-primary.wp-tab-last").css("margin-right","10px");
					if(jQuery('.post-locked-message').length > 0){
						if (name !='' && message !='') {
							jQuery("p.currently-editing.wp-tab-first").text(name+' '+message);
						}
					}
					function explode(){
						jQuery(".notification-dialog p a.button:nth-child(1)").text('All '+type);
						jQuery(".notification-dialog p a.button:nth-child(1)").attr("href",admin_url+edit_link)
					}
					setTimeout(explode, 2000);
				});
			</script>
		<?php }
	}

	public function required_alt_text() {
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

	public function megamenu_options_capability_callback() {
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

	/**
	 * Enqueues admin scripts and styles.
	 *
	 * @global string $typenow The current type.
	 * @global string $pagenow The current page.
	 */
	public static function enqueue_scripts() {
		global $typenow, $pagenow;

		if ( in_array( $typenow, CommonSettings::allow_fontawesome_posttype_list() ) && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
			wp_register_style('general-font-awesome',GENERAL_SETTINGS_CPT_URL . "assets/css/general-font-awesome". $postfix .".css", array(), GENERAL_SETTINGS_CPT_VERSION, 'all');
			wp_enqueue_style('general-font-awesome');
		}
	}
}

CommonSettings::init();

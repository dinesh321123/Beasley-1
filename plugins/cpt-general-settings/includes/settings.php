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
		add_action( 'admin_init', array( __CLASS__, 'settings_cpt_admin_init' ) );
	}
	public function settings_cpt_admin_init() {
		add_filter( 'dashboard_recent_posts_query_args', array( __CLASS__, 'dashboard_recent_posts_query_args_callback' ), 10, 1 ) ;
	}
	public static function dashboard_recent_posts_query_args_callback( $query_args ) {
		$query_args['post_type'] = CommonSettings::allow_recent_posts_posttype_list();
		$query_args['posts_per_page'] = 10;
		return $query_args;
	}
	/**
	 * Returns array of post type for recent activity.
	 */
	public static function allow_recent_posts_posttype_list() {
		return (array) apply_filters( 'allow-dashboard-recent-posts-for-posttypes', array( 'post', 'gmr_gallery', 'listicle_cpt', 'affiliate_marketing' )  );
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
		// Register custom capability for Draft Kings On/Off Setting.
		$roles = [ 'administrator' ];

		foreach ( $roles as $role ) {
			$role_obj = get_role( $role );

			if ( is_a( $role_obj, \WP_Role::class ) ) {
				$role_obj->add_cap( 'manage_draft_kings_onoff_setting', false );
			}
		}
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

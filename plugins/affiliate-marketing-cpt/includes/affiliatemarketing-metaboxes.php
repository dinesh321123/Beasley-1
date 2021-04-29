<?php
/**
 * Class AffiliateMarketingCPTMetaboxes
 */
class AffiliateMarketingCPTMetaboxes {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post',array( __CLASS__, 'affiliate_marketing_save') );
		add_action( 'save_post',array( __CLASS__, 'affiliate_marketing_footer_description_save') );
	}

	/**
	 * Enqueues admin scripts and styles.
	 *
	 * @global string $typenow The current type.
	 * @global string $pagenow The current page.
	 */
	public static function enqueue_scripts() {
		global $typenow, $pagenow;
		if ( AffiliateMarketingCPT::AFFILIATE_MARKETING_POST_TYPE == $typenow && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			// $postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
			wp_register_style('affiliate-marketing-admin',AFFILIATE_MARKETING_CPT_URL . "assets/css/am_admin.css", array(), AFFILIATE_MARKETING_CPT_VERSION, 'all');
			wp_enqueue_style('affiliate-marketing-admin');
			wp_enqueue_script( 'affiliate-marketing-admin', AFFILIATE_MARKETING_CPT_URL . "assets/js/am_admin.js", array('jquery'), AFFILIATE_MARKETING_CPT_VERSION, true);
			wp_enqueue_media();
		}
	}

	/**
	 * Adds the meta box container for Episodes.
	 *
	 * @param $post_type
	 */
	public static function add_meta_box( $post_type ) {
		$post_types = array( AffiliateMarketingCPT::AFFILIATE_MARKETING_POST_TYPE );
		if ( in_array( $post_type, $post_types ) ) {
			add_meta_box( 'am_meta_box', 'Items', array( __CLASS__, 'render_items_metabox' ), $post_type, 'normal', 'low' );
			add_meta_box( 'am_footer_meta_box', 'Footer Description', array( __CLASS__, 'render_footer_metabox' ), $post_type, 'normal', 'low' );
		}
	}

	public static function render_footer_metabox( \WP_Post $post ) {
		wp_nonce_field( '_am_footer_description_nonce', '_am_footer_description_nonce' );
		$am_footer_description = self::am_get_metavalue( 'am_footer_description' );
		$am_footer_description = !empty($am_footer_description) ? $am_footer_description : '';
		?>
		<div class="am-form-group">
			<label class="ammetafooterdescription" for="am_footer_description"><?php _e( 'Description', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ); ?></label>
			<textarea name="am_footer_description" class="tinytext tiny-editor" id="am_footer_description" rows="10">
					<?php echo $am_footer_description; ?>
				</textarea>
				<script type="text/javascript">
					jQuery(document).ready(function($) {
						tinymce.init({ selector: '#am_footer_description', branding: false });
					});
				</script>
		</div>
		<?php 
	}

	/**
	 * @param $post
	 */
	public static function render_items_metabox( \WP_Post $post ) {
		wp_nonce_field( '_repeatable_editor_repeatable_editor_nonce', 'repeatable_editor_repeatable_editor_nonce' );
		$contents = self::am_get_metavalue( 'am_item_description' );
		$am_item_name = self::am_get_metavalue( 'am_item_name' );
		$am_item_photo = self::am_get_metavalue( 'am_item_photo' );
		$am_item_buttontext = self::am_get_metavalue( 'am_item_buttontext' );
		$am_item_buttonurl = self::am_get_metavalue( 'am_item_buttonurl' );
		$am_item_getitnowfromname = self::am_get_metavalue( 'am_item_getitnowfromname' );
		$am_item_getitnowfromurl = self::am_get_metavalue( 'am_item_getitnowfromurl' );

		if ($contents && !empty($contents)) {
			$contents = $contents;
		} else {
			$contents = array('');
		}
		if ($am_item_name && !empty($am_item_name)) {
			$am_item_name = $am_item_name ;
		} else {
			$am_item_name = array('');
		}
		if ($am_item_buttontext && !empty($am_item_buttontext)) {
			$am_item_buttontext =  $am_item_buttontext ;
		} else {
			$am_item_buttontext = array('');
		}
		if ($am_item_buttonurl && !empty($am_item_buttonurl)) {
			$am_item_buttonurl =  $am_item_buttonurl ;
		} else {
			$am_item_buttonurl = array('');
		}
		if ($am_item_getitnowfromname && !empty($am_item_getitnowfromname)) {
			$am_item_getitnowfromname =  $am_item_getitnowfromname;
		} else {
			$am_item_getitnowfromname = array('');
		}
		if ($am_item_getitnowfromurl && !empty($am_item_getitnowfromurl)) {
			$am_item_getitnowfromurl =  $am_item_getitnowfromurl;
		} else {
			$am_item_getitnowfromurl = array('');
		}

			for ($i = 0; $i < count($contents); $i++) {
			?>
			<div class="content-row am-content-row">
				<?php 
					if( $i !== 0 && $i > 0 ){ 
				?>
					<a class="content-delete" href="#" style="color:#a00;float:right;margin-top: 3px;text-decoration:none;font-size:20px;"><i class="fa fa-trash" aria-hidden="true"></i></a>
				<?php } ?>				
				<h3 class="am-item-title">Item</h3>
				<div class="am-form-group">
					<label class="ammetatitle" for="am_item_name_<?php echo $i; ?>"><?php _e( 'Name', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ); ?> </label>
					<input  name="am_item_name[]" type="text" value="<?php echo $am_item_name[$i]; ?>">
				</div>
				<div  class="am-form-group">
					<label class="ammetatitle" for="am_item_photo_<?php echo $i; ?>"><?php _e( 'Photo', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ); ?> </label>
					<?php 
						$img    = wp_get_attachment_image_src($am_item_photo[$i], 'thumbnail');
						?>
						<input type="hidden" value="<?php echo $am_item_photo[$i]; ?>" class="regular-text process_custom_images" id="process_custom_images<?php echo $i;?>" name="am_item_photo[]" max="" min="1" step="1">
						<button class="set_custom_images button">Upload Image</button>
						
						<?php
						if($img != "") {
						?>
							<img class="upload-preview" src="<?= $img[0]; ?>" width="100px" /><br />
						<?php 
						}else{
							?>
							<img class="upload-preview" src="<?= $img[0]; ?>" width="100px" /><br />
						<?php 
						}
						?>
				</div>
				<div class="am-form-group">
					<label class="ammetatitle" for="am_item_description_<?php echo $i; ?>"><?php _e( 'Description', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ); ?></label>
					<textarea name="am_item_description[]" class="tinytext" id="tiny-editor-<?php echo $i; ?>" class="tiny-editor" rows="10">
							<?php echo $contents[$i]; ?>
						</textarea>
						<script type="text/javascript">
							jQuery(document).ready(function($) {
								var startingContent = <?php echo $i; ?>;
								var contentID = 'tiny-editor-' + startingContent;
								tinymce.init({ selector: '#' + contentID, branding: false });
							});
							
						</script>
				</div>
				<div class="am-form-group">
					<label class="ammetatitle" for="am_item_buttontext_<?php echo $i; ?>"><?php _e( 'Button Text', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ); ?> </label>
					<input name="am_item_buttontext[]" type="text" value="<?php echo $am_item_buttontext[$i] ? htmlentities( $am_item_buttontext[$i] ) : 'Shop This' ; ?>" class="am_item_buttontext">
				</div>
				<div class="am-form-group">
					<label class="ammetatitle" for="am_item_buttonurl_<?php echo $i; ?>"><?php _e( 'Button URL', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ); ?> </label>
					<input name="am_item_buttonurl[]" type="text" value="<?php echo htmlentities($am_item_buttonurl[$i]); ?>" class="am_item_buttonurl">
				</div>
				<div class="am-form-group">
					<label class="ammetatitle" for="am_item_getitnowfromname_<?php echo $i; ?>"><?php _e( 'Get it now from name', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ); ?> </label>
					<input name="am_item_getitnowfromname[]" type="text" value="<?php echo htmlentities($am_item_getitnowfromname[$i]); ?>" class="am_item_getitnowfromname">
				</div>
				<div class="am-form-group">
					<label class="ammetatitle" for="am_item_getitnowfromurl_<?php echo $i; ?>"><?php _e( 'Get it now from URL', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ); ?> </label>
					<input name="am_item_getitnowfromurl[]" type="text" value="<?php echo htmlentities($am_item_getitnowfromurl[$i]); ?>" class="am_item_getitnowfromurl">
				</div>
				<br style="clear:both">
			</div>
			<?php
			}
			?>
		<p><a class="button" href="#" id="add_content">Add new item</a></p>
		<script>
			var startingContent = <?php echo count($contents) - 1; ?>;
			jQuery('#add_content').click(function(e) {
				e.preventDefault();
				startingContent++;
				var contentID = 'am_item_description_' + startingContent;
				var am_item_name = 'am_item_name_' + startingContent;
				var am_item_photo = 'am_item_photo_' + startingContent;
				var am_item_buttontext = 'am_item_buttontext_' + startingContent;
				var am_item_buttonurl = 'am_item_buttonurl_' + startingContent;
				var am_item_getitnowfromname = 'am_item_getitnowfromname_' + startingContent;
				var am_item_getitnowfromurl = 'am_item_getitnowfromurl_' + startingContent;
				
					contentRow = '<div class="content-row am-content-row"><a class="content-delete" href="#" style="color:#a00;float:right;margin-top: 3px;text-decoration:none;font-size:20px;"><i class="fa fa-trash" aria-hidden="true"></i></a><h3 class="am-item-title">Item</h3><div class="am-form-group"><label  class="ammetatitle" for="' + am_item_name + '"><?php _e( 'Name', 'affiliate_marketing_textdomain' ); ?></label><input name="am_item_name[]" type="text" id="' + am_item_name + '" ></div><div class="am-form-group"><label  class="ammetatitle" for="' + am_item_photo + '"><?php _e( 'Photo', 'affiliate_marketing_textdomain' ); ?></label><input type="hidden" value="" class="regular-text process_custom_images" id="process_custom_images" name="am_item_photo[]" max="" min="1" step="1"><button class="set_custom_images button">Upload Image</button><img class="upload-preview" src="" width="100"></div><div class="am-form-group"><label  class="ammetatitle" for="' + contentID + '"><?php _e( 'Description', 'affiliate_marketing_textdomain' ); ?></label><textarea name="am_item_description[]" class="tinytext" id="' + contentID + '" rows="10"></textarea></div><div class="am-form-group"><label class="ammetatitle" for="' + am_item_buttontext + '"><?php _e( 'Button Text', 'affiliate_marketing_textdomain' ); ?></label><input name="am_item_buttontext[]" type="text" value="Shop This" id="' + am_item_buttontext + '" ></div><div class="am-form-group"><label class="ammetatitle" for="' + am_item_buttonurl + '"><?php _e( 'Button URL', 'affiliate_marketing_textdomain' ); ?></label><input name="am_item_buttonurl[]" type="text" id="' + am_item_buttonurl + '" ></div><div class="am-form-group"><label class="ammetatitle" for="' + am_item_getitnowfromname + '"><?php _e( 'Get it now from name', 'affiliate_marketing_textdomain' ); ?></label><input name="am_item_getitnowfromname[]" type="text" id="' + am_item_getitnowfromname + '" ></div><div class="am-form-group"><label class="ammetatitle" for="' + am_item_getitnowfromurl + '"><?php _e( 'Get it now from URL', 'affiliate_marketing_textdomain' ); ?></label><input name="am_item_getitnowfromurl[]" type="text" id="' + am_item_getitnowfromurl + '" ></div></div>';
					
					jQuery('.content-row').eq(jQuery('.content-row').length - 1).after(contentRow);
					tinymce.init({ selector: '#' + contentID , branding: false });
			});
			jQuery(document).on('click', '.content-delete', function(e) {
				e.preventDefault();
				if (
					jQuery('.content-row').length > 1 &&
					confirm('Are you sure you want to delete this task?')
				) {
					jQuery(this).parents('.content-row').remove();
				}
			});
		</script>
		<?php
	}
	function affiliate_marketing_footer_description_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! isset( $_POST['_am_footer_description_nonce'] ) || ! wp_verify_nonce( $_POST['_am_footer_description_nonce'], '_am_footer_description_nonce' ) ) return;
		if ( ! current_user_can( 'edit_post' ) ) return;

		if ( isset( $_POST['am_footer_description'] ) ) {
			$am_footer_description = $_POST['am_footer_description'];
			update_post_meta( $post_id, 'am_footer_description', $am_footer_description );
		}
	}

	function affiliate_marketing_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! isset( $_POST['repeatable_editor_repeatable_editor_nonce'] ) || ! wp_verify_nonce( $_POST['repeatable_editor_repeatable_editor_nonce'], '_repeatable_editor_repeatable_editor_nonce' ) ) return;
		if ( ! current_user_can( 'edit_post' ) ) return;

		if ( isset( $_POST['am_item_name'] ) ) {
			$am_item_name = $_POST['am_item_name'];
			update_post_meta( $post_id, 'am_item_name', $am_item_name );
		}
		if ( isset( $_POST['am_item_description'] ) ) {
			$am_item_description =  $_POST['am_item_description'] ;
			update_post_meta( $post_id, 'am_item_description', $am_item_description );
		}
		if ( isset( $_POST['am_item_photo'] ) ) {
			$filecontents =  $_POST['am_item_photo'] ;
			var_dump($filecontents);
			update_post_meta( $post_id, 'am_item_photo', $filecontents );
		}
		if ( isset( $_POST['am_item_buttontext'] ) ) {
			$am_item_buttontext =  $_POST['am_item_buttontext'] ;
			update_post_meta( $post_id, 'am_item_buttontext', $am_item_buttontext );
		}
		if ( isset( $_POST['am_item_buttonurl'] ) ) {
			$am_item_buttonurl =  $_POST['am_item_buttonurl'] ;
			update_post_meta( $post_id, 'am_item_buttonurl', $am_item_buttonurl );
		}
		if ( isset( $_POST['am_item_getitnowfromname'] ) ) {
			$am_item_getitnowfromname =  $_POST['am_item_getitnowfromname'] ;
			update_post_meta( $post_id, 'am_item_getitnowfromname', $am_item_getitnowfromname );
		}
		if ( isset( $_POST['am_item_getitnowfromurl'] ) ) {
			$am_item_getitnowfromurl =  $_POST['am_item_getitnowfromurl'] ;
			update_post_meta( $post_id, 'am_item_getitnowfromurl', $am_item_getitnowfromurl );
		}
	}

	public static function am_get_metavalue( $value ) {
		global $post;
		$field = get_post_meta( $post->ID, $value, true );
		if ( ! empty( $field ) ) {
			return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
		} else {
			return false;
		}
	}
}

AffiliateMarketingCPTMetaboxes::init();

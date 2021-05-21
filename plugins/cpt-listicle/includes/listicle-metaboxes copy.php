<?php
/**
 * Class ListicleCPTMetaboxes
 */
class ListicleCPTMetaboxes {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post',array( __CLASS__, 'listicle_cpt_save') );
		add_action( 'save_post',array( __CLASS__, 'listicle_cpt_footer_description_save') );
	}

	/**
	 * Enqueues admin scripts and styles.
	 *
	 * @global string $typenow The current type.
	 * @global string $pagenow The current page.
	 */
	public static function enqueue_scripts() {
		global $typenow, $pagenow;
		if ( ListicleCPT::LISTICLE_POST_TYPE == $typenow && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			// $postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
			wp_register_style('listicle-admin',LISTICLE_CPT_URL . "assets/css/listicle_admin.css", array(), LISTICLE_CPT_VERSION, 'all');
			wp_enqueue_style('listicle-admin');
			wp_enqueue_script( 'listicle-admin', LISTICLE_CPT_URL . "assets/js/listicle_admin.js", array('jquery'), LISTICLE_CPT_VERSION, true);
			wp_enqueue_media();
		}
	}

	/**
	 * Adds the meta box container for Episodes.
	 *
	 * @param $post_type
	 */
	public static function add_meta_box( $post_type ) {
		$post_types = array( ListicleCPT::LISTICLE_POST_TYPE );
		if ( in_array( $post_type, $post_types ) ) {
			add_meta_box( 'listicle_meta_box', 'Items', array( __CLASS__, 'render_items_metabox' ), $post_type, 'normal', 'low' );
			add_meta_box( 'listicle_footer_meta_box', 'Footer Description', array( __CLASS__, 'render_footer_metabox' ), $post_type, 'normal', 'low' );
		}
	}

	public static function render_footer_metabox( \WP_Post $post ) {
		wp_nonce_field( '_listicle_cpt_footer_description_nonce', '_listicle_cpt_footer_description_nonce' );
		$listicle_cpt_footer_description = self::get_custom_metavalue( 'listicle_cpt_footer_description' );
		$listicle_cpt_footer_description = !empty($listicle_cpt_footer_description) ? $listicle_cpt_footer_description : '';
		?>
		<div class="cpt-form-group">
			<label class="ammetafooterdescription" for="listicle_cpt_footer_description"><?php _e( 'Description', LISTICLE_CPT_TEXT_DOMAIN ); ?></label>
			<textarea name="listicle_cpt_footer_description" class="tinytext tiny-editor" id="listicle_cpt_footer_description" rows="10">
					<?php echo $listicle_cpt_footer_description; ?>
				</textarea>
				<script type="text/javascript">
					jQuery(document).ready(function($) {
						tinymce.init({ selector: '#listicle_cpt_footer_description', branding: false });
					});
				</script>
		</div>
		<?php 
	}

	/**
	 * @param $post
	 */
	public static function render_items_metabox( \WP_Post $post ) {
		wp_nonce_field( '_repeatable_editor_nonce', 'repeatable_editor_nonce' );
		$contents = self::get_custom_metavalue( 'cpt_item_description' );
		$cpt_item_name = self::get_custom_metavalue( 'cpt_item_name' );
		
		$am_item_order = self::get_custom_metavalue( 'am_item_order' );

		/*Remove*/
		$am_item_unique_order = self::get_custom_metavalue( 'am_item_unique_order' );
		$am_item_photo = self::get_custom_metavalue( 'am_item_photo' );
		$am_item_imagetype = self::get_custom_metavalue( 'am_item_imagetype' );
		$am_item_imagecode = self::get_custom_metavalue( 'am_item_imagecode' );
		$am_item_buttontext = self::get_custom_metavalue( 'am_item_buttontext' );
		$am_item_buttonurl = self::get_custom_metavalue( 'am_item_buttonurl' );
		$am_item_getitnowfromname = self::get_custom_metavalue( 'am_item_getitnowfromname' );
		$am_item_getitnowtext = self::get_custom_metavalue( 'am_item_getitnowtext' );
		$am_item_getitnowfromurl = self::get_custom_metavalue( 'am_item_getitnowfromurl' );
		/*Remove*/



		$contents = $contents && !empty($contents) ? $contents : array('');
		$cpt_item_name = $cpt_item_name && !empty($cpt_item_name) ? $cpt_item_name : array('');


			for ($i = 0; $i < count($contents); $i++) {
			?>
			<div class="content-row cpt-content-row">
				<?php 
					if( $i !== 0 && $i > 0 ){
				?>
					<a class="content-delete" href="#" style="color:#a00;float:right;margin-top: 3px;text-decoration:none;font-size:20px;"><svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="-64 0 512 512" width="25px"><path d="m256 80h-32v-48h-64v48h-32v-80h128zm0 0" fill="#62808c"/><path d="m304 512h-224c-26.507812 0-48-21.492188-48-48v-336h320v336c0 26.507812-21.492188 48-48 48zm0 0" fill="#e76e54"/><path d="m384 160h-384v-64c0-17.671875 14.328125-32 32-32h320c17.671875 0 32 14.328125 32 32zm0 0" fill="#77959e"/><path d="m260 260c-6.246094-6.246094-16.375-6.246094-22.625 0l-41.375 41.375-41.375-41.375c-6.25-6.246094-16.378906-6.246094-22.625 0s-6.246094 16.375 0 22.625l41.375 41.375-41.375 41.375c-6.246094 6.25-6.246094 16.378906 0 22.625s16.375 6.246094 22.625 0l41.375-41.375 41.375 41.375c6.25 6.246094 16.378906 6.246094 22.625 0s6.246094-16.375 0-22.625l-41.375-41.375 41.375-41.375c6.246094-6.25 6.246094-16.378906 0-22.625zm0 0" fill="#fff"/></svg></a>
				<?php } ?>
				<h3 class="cpt-item-title">Item</h3>
				<div class="cpt-form-group">
					<label class="ammetatitle" for="cpt_item_name_<?php echo $i; ?>"><?php _e( 'Name', LISTICLE_CPT_TEXT_DOMAIN ); ?> </label>
					<input name="cpt_item_name[]" type="text" value="<?php echo $cpt_item_name[$i]; ?>">
				</div>
				<input name="am_item_order[]" type="hidden" value="<?php echo $i; ?>" />
				
				<div class="cpt-form-group">
					<label class="ammetatitle" for="cpt_item_description_<?php echo $i; ?>"><?php _e( 'Description', LISTICLE_CPT_TEXT_DOMAIN ); ?></label>
					<textarea name="cpt_item_description[]" class="tinytext" id="tiny-editor-<?php echo $i; ?>" class="tiny-editor" rows="10">
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
				var contentID = 'cpt_item_description_' + startingContent;
				var cpt_item_name = 'cpt_item_name_' + startingContent;
				var am_item_photo = 'am_item_photo_' + startingContent;
				var am_item_imagetype = 'am_item_imagetype_' + startingContent;
				var am_item_imagetype_imageurl = 'am_item_imagetype_imageurl_' + startingContent;
				var am_item_buttontext = 'am_item_buttontext_' + startingContent;
				var am_item_buttonurl = 'am_item_buttonurl_' + startingContent;
				var am_item_getitnowfromname = 'am_item_getitnowfromname_' + startingContent;
				var am_item_getitnowtext = 'am_item_getitnowtext_' + startingContent;
				var am_item_getitnowfromurl = 'am_item_getitnowfromurl_' + startingContent;
				
					contentRow = '<div class="content-row cpt-content-row">';
					contentRow += '<a class="content-delete" href="#" style="color:#a00;float:right;margin-top: 3px;text-decoration:none;font-size:20px;"><svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="-64 0 512 512" width="25px"><path d="m256 80h-32v-48h-64v48h-32v-80h128zm0 0" fill="#62808c"/><path d="m304 512h-224c-26.507812 0-48-21.492188-48-48v-336h320v336c0 26.507812-21.492188 48-48 48zm0 0" fill="#e76e54"/><path d="m384 160h-384v-64c0-17.671875 14.328125-32 32-32h320c17.671875 0 32 14.328125 32 32zm0 0" fill="#77959e"/><path d="m260 260c-6.246094-6.246094-16.375-6.246094-22.625 0l-41.375 41.375-41.375-41.375c-6.25-6.246094-16.378906-6.246094-22.625 0s-6.246094 16.375 0 22.625l41.375 41.375-41.375 41.375c-6.246094 6.25-6.246094 16.378906 0 22.625s16.375 6.246094 22.625 0l41.375-41.375 41.375 41.375c6.25 6.246094 16.378906 6.246094 22.625 0s6.246094-16.375 0-22.625l-41.375-41.375 41.375-41.375c6.246094-6.25 6.246094-16.378906 0-22.625zm0 0" fill="#fff"/></svg></a><h3 class="cpt-item-title">Item</h3>';
					contentRow += '<div class="cpt-form-group"><label  class="ammetatitle" for="' + cpt_item_name + '"><?php _e( 'Name', 'listicle_textdomain' ); ?></label><input name="cpt_item_name[]" type="text" id="' + cpt_item_name + '" ></div><input  name="am_item_order[]" type="hidden" value="' + startingContent + '"><input  name="am_item_unique_order[]" type="hidden" value="<?php echo $post->ID.'221'.mt_rand() ; ?>">';

					contentRow += '<div class="cpt-form-group"><label class="ammetatitle" for="' + am_item_imagetype + '"><?php _e( 'Image code', LISTICLE_CPT_TEXT_DOMAIN ); ?><input name="' + am_item_imagetype + '" id="' + am_item_imagetype + '" type="radio" value="imagecode" class="am_item_imagetype" data-postid="' + startingContent + '" data-type-hide="imageurl"></label><label class="ammetatitle" for="' + am_item_imagetype + '"><?php _e( 'Photo', LISTICLE_CPT_TEXT_DOMAIN ); ?><input name="' + am_item_imagetype + '" id="' + am_item_imagetype_imageurl + '" type="radio" checked value="imageurl" class="am_item_imagetype" data-postid="' + startingContent + '" data-type-hide="imagecode"></label><div class="imageurl" id="imageurl_' + startingContent + '" style="display:none;"><textarea name="am_item_imagecode[]" class="tinytext" id="imagecode-' + startingContent + '" rows="10"></textarea></div><div class="imagecode" id="imagecode_' + startingContent + '"><input type="hidden" value="" class="regular-text process_custom_images" id="process_custom_images" name="am_item_photo[]" max="" min="1" step="1"><button class="set_custom_images button">Upload Image</button><img class="upload-preview" src="" width="100"></div></div>';
					contentRow += '<div class="cpt-form-group"><label  class="ammetatitle" for="' + contentID + '"><?php _e( 'Description', 'listicle_textdomain' ); ?></label><textarea name="cpt_item_description[]" class="tinytext" id="' + contentID + '" rows="10"></textarea></div><div class="cpt-form-group"><label class="ammetatitle" for="' + am_item_buttontext + '"><?php _e( 'Button Text', 'listicle_textdomain' ); ?></label><input name="am_item_buttontext[]" type="text" value="Shop This" id="' + am_item_buttontext + '" ></div><div class="cpt-form-group"><label class="ammetatitle" for="' + am_item_buttonurl + '"><?php _e( 'Button URL', 'listicle_textdomain' ); ?></label><input name="am_item_buttonurl[]" type="text" id="' + am_item_buttonurl + '" ></div><div class="cpt-form-group"><label class="ammetatitle" for="' + am_item_getitnowtext + '"><?php _e( 'Get it now from name', 'listicle_textdomain' ); ?></label><select name="am_item_getitnowtext[]"><option value="Get it now here">Get it now here</option><option value="Get it now from">Get it now from</option><option value="Get It Here">Get It Here</option><option value="Buy It Now">Buy It Now</option><option value="Pick It Up Here">Pick It Up Here</option><option value="Score Yours Now">Score Yours Now</option><option value="See More Here">See More Here</option><option value="Learn More Here">Learn More Here</option><option value="Snag One Here">Snag One Here</option><option value="Grab It One Here">Grab It One Here</option><option value="Get One Here">Get One Here</option><option value="Get Yours Now">Get Yours Now</option></select></div><div class="cpt-form-group"><label class="ammetatitle" for="' + am_item_getitnowfromname + '"><?php _e( 'Get it now from name', 'listicle_textdomain' ); ?></label><input name="am_item_getitnowfromname[]" type="text" id="' + am_item_getitnowfromname + '" ></div><div class="cpt-form-group"><label class="ammetatitle" for="' + am_item_getitnowfromurl + '"><?php _e( 'Get it now from URL', 'listicle_textdomain' ); ?></label><input name="am_item_getitnowfromurl[]" type="text" id="' + am_item_getitnowfromurl + '" ></div>';
					contentRow += '</div>';
					
					jQuery('.content-row').eq(jQuery('.content-row').length - 1).after(contentRow);
					tinymce.init({ selector: '#' + contentID , branding: false });
					jQuery(".am_item_imagetype").click(function() {$('#' + $(this).val() + '_' + $(this).attr('data-postid')).hide();$('#' + $(this).attr('data-type-hide') + '_' + $(this).attr('data-postid')).show();});
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
	function listicle_cpt_footer_description_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! isset( $_POST['_listicle_cpt_footer_description_nonce'] ) || ! wp_verify_nonce( $_POST['_listicle_cpt_footer_description_nonce'], '_listicle_cpt_footer_description_nonce' ) ) return;
		if ( ! current_user_can( 'edit_post' ) ) return;

		if ( isset( $_POST['listicle_cpt_footer_description'] ) ) {
			$listicle_cpt_footer_description = $_POST['listicle_cpt_footer_description'];
			update_post_meta( $post_id, 'listicle_cpt_footer_description', $listicle_cpt_footer_description );
		}
	}

	function listicle_cpt_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! isset( $_POST['repeatable_editor_nonce'] ) || ! wp_verify_nonce( $_POST['repeatable_editor_nonce'], '_repeatable_editor_nonce' ) ) return;
		if ( ! current_user_can( 'edit_post' ) ) return;

		if ( isset( $_POST['cpt_item_name'] ) ) {
			$cpt_item_name = $_POST['cpt_item_name'];
			update_post_meta( $post_id, 'cpt_item_name', $cpt_item_name );
		}
		if ( isset( $_POST['cpt_item_description'] ) ) {
			$cpt_item_description =  $_POST['cpt_item_description'] ;
			update_post_meta( $post_id, 'cpt_item_description', $cpt_item_description );
		}
		if ( isset( $_POST['am_item_order'] ) ) {
			$am_item_order =  $_POST['am_item_order'] ;
			update_post_meta( $post_id, 'am_item_order', $am_item_order );
		}
	}

	public static function get_custom_metavalue( $value ) {
		global $post;
		$field = get_post_meta( $post->ID, $value, true );
		if ( ! empty( $field ) ) {
			return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
		} else {
			return false;
		}
	}
}

ListicleCPTMetaboxes::init();

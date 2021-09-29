<?php
/**
 * Class AffiliateMarketingCPTMetaboxes
 */
class AffiliateMarketingCPTMetaboxes {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		add_filter( 'wpseo_metabox_prio', array( __CLASS__, 'affiliate_marketing_yoast_to_bottom' ) );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post',array( __CLASS__, 'affiliate_marketing_save') );
		add_action( 'save_post',array( __CLASS__, 'affiliate_marketing_footer_description_save') );
	}

	function affiliate_marketing_yoast_to_bottom() {
		global $typenow, $pagenow;
		if ( AffiliateMarketingCPT::AFFILIATE_MARKETING_POST_TYPE == $typenow && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			return 'low';
		}
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
			wp_register_style('am-awesome-font',AFFILIATE_MARKETING_CPT_URL . "assets/css/am-font-awesome.css", array(), AFFILIATE_MARKETING_CPT_VERSION, 'all');
			wp_enqueue_style('am-awesome-font');
			wp_register_style('affiliate-marketing-admin',AFFILIATE_MARKETING_CPT_URL . "assets/css/am_admin.css", array(), AFFILIATE_MARKETING_CPT_VERSION, 'all');
			wp_enqueue_style('affiliate-marketing-admin');
			wp_enqueue_script( 'affiliate-marketing-admin', AFFILIATE_MARKETING_CPT_URL . "assets/js/am_admin.js", array('jquery'), AFFILIATE_MARKETING_CPT_VERSION, true);
			wp_enqueue_media();
			wp_enqueue_editor();
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
			add_meta_box( 'am_meta_box', 'Items', array( __CLASS__, 'render_items_metabox' ), $post_type, 'normal', 'high' );
			add_meta_box( 'am_footer_meta_box', 'Footer Description', array( __CLASS__, 'render_footer_metabox' ), $post_type, 'normal', 'high' );
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
		$am_item_imagetype = self::am_get_metavalue( 'am_item_imagetype' );
		$am_item_imagecode = self::am_get_metavalue( 'am_item_imagecode' );

		$am_item_unique_order = self::am_get_metavalue( 'am_item_unique_order' );
		$am_item_order = self::am_get_metavalue( 'am_item_order' );

		$am_item_buttontext = self::am_get_metavalue( 'am_item_buttontext' );
		$am_item_buttonurl = self::am_get_metavalue( 'am_item_buttonurl' );
		$am_item_getitnowfromname = self::am_get_metavalue( 'am_item_getitnowfromname' );
		$am_item_getitnowtext = self::am_get_metavalue( 'am_item_getitnowtext' );
		$am_item_getitnowfromurl = self::am_get_metavalue( 'am_item_getitnowfromurl' );

		$contents = $contents && !empty($contents) ? $contents : array('');
		$am_item_name = $am_item_name && !empty($am_item_name) ? $am_item_name : array('');
		$am_item_buttontext = $am_item_buttontext && !empty($am_item_buttontext) ? $am_item_buttontext : array('');
		$am_item_buttonurl = $am_item_buttonurl && !empty($am_item_buttonurl) ? $am_item_buttonurl : array('');

		$am_item_getitnowtext = $am_item_getitnowtext && !empty($am_item_getitnowtext) ? $am_item_getitnowtext : array('') ;

		$am_item_getitnowfromname = $am_item_getitnowfromname && !empty($am_item_getitnowfromname) ? $am_item_getitnowfromname : array('');
		$am_item_getitnowfromurl = $am_item_getitnowfromurl && !empty($am_item_getitnowfromurl) ? $am_item_getitnowfromurl : array('');


		echo '<div>';
		for ($i = 0; $i < count($contents); $i++) {
			?>
			<div class="content-row am-content-row default-orderdiv">
				<div class="dir-btn-grp">
					<button type="button" tiny-editorid="<?php echo 'tiny-editor-'.$i; ?>" class="updownbtn movetop fa fa-angle-double-up dir-btn"></button>
					<button type="button" tiny-editorid="<?php echo 'tiny-editor-'.$i; ?>" class="updownbtn moveup fa fa-angle-up dir-btn"></button>
					<button type="button" tiny-editorid="<?php echo 'tiny-editor-'.$i; ?>" class="updownbtn movedown fa fa-angle-down dir-btn"></button>
					<button type="button" tiny-editorid="<?php echo 'tiny-editor-'.$i; ?>" class="updownbtn movebottom fa fa-angle-double-down dir-btn"></button>
					<a class="content-delete dir-btn " href="#"><i class="fa fa-trash-o"></i></a>
				</div>
				<h3 class="am-item-title">Item</h3>
				<div class="am-form-group">
					<label class="ammetatitle" for="am_item_name_<?php echo $i; ?>"><?php _e( 'Name', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ); ?> </label>
					<input name="am_item_name[]" type="text" value="<?php echo $am_item_name[$i]; ?>">
				</div>
				<input name="am_item_order[]" type="hidden" value="<?php echo $i; ?>">
				<input name="am_item_unique_order[]" type="hidden" value="<?php echo $am_item_unique_order[$i] ? $am_item_unique_order[$i] : $post->ID.'221'.mt_rand() ; ?>">

				<div  class="am-form-group">
					<label class="ammetatitle" for="am_item_imagetype_<?php echo $i; ?>">
						<?php _e( 'Image code', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ); ?>
						<input name="am_item_imagetype_<?php echo "$i";?>" id="am_item_imagetype_<?php echo "$i";?>" type="radio" <?php echo $am_item_imagetype[$i] == "imagecode" ? 'checked' : '' ;?> value="imagecode" class="am_item_imagetype" data-postid="<?php echo $i; ?>" data-type-hide="imageurl" />
					</label>
					<label class="ammetatitle" for="am_item_imagetype_<?php echo $i; ?>">
						<?php _e( 'Photo', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ); ?>
						<input name="am_item_imagetype_<?php echo "$i";?>" id="am_item_imagetype_imageurl_<?php echo "$i";?>" type="radio" <?php echo $am_item_imagetype[$i] == "imageurl" ? 'checked' : '' ;?> value="imageurl" class="am_item_imagetype" data-postid="<?php echo $i; ?>" data-type-hide="imagecode" />
					</label>
					<div class="imageurl" id="imageurl_<?php echo $i;?>" <?php echo $am_item_imagetype[$i] == "imageurl" || $am_item_imagetype[$i] == "" ? 'style="display:none"' : '' ; ?>>
						<textarea name="am_item_imagecode[]" class="tinytext" id="imagecode-<?php echo $i; ?>" rows="10"><?php echo $am_item_imagetype[$i] == "imagecode" ? $am_item_imagecode[$i] : ""; ?></textarea>
					</div>
					<div class="imagecode" id="imagecode_<?php echo $i;?>" <?php echo $am_item_imagetype[$i] == "imagecode" || $am_item_imagetype[$i] == "" ? 'style="display:none"' : '' ; ?> >
						<input type="hidden" value="<?php echo $am_item_photo[$i]; ?>" class="regular-text process_custom_images" id="process_custom_images<?php echo $i;?>" name="am_item_photo[]" max="" min="1" step="1">
						<button class="set_custom_images button">Upload Image</button>
						<?php
							$img = wp_get_attachment_image_src($am_item_photo[$i], 'thumbnail');
							echo '<img class="upload-preview" src="', $img != "" && $am_item_imagetype[$i] == "imageurl" ? $img[0] : "" , '" width="100px" /><br />';

						?>
					</div>
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
					<label class="ammetatitle" for="am_item_getitnowtext_<?php echo $i; ?>"><?php _e( 'Get it now here text', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ); ?> </label>
					<select name="am_item_getitnowtext[]">
						<option value="Get it now here" <?php echo $am_item_getitnowtext[$i]== "Get it now here" ? 'selected' : ''; ?> >Get it now here</option>
						<option value="Get it now from" <?php echo $am_item_getitnowtext[$i]== "Get it now from" ? 'selected' : ''; ?>>Get it now from</option>
						<option value="Get It Here" <?php echo $am_item_getitnowtext[$i]== "Get It Here" ? 'selected' : ''; ?>>Get It Here</option>
						<option value="Buy It Now" <?php echo $am_item_getitnowtext[$i]== "Buy It Now" ? 'selected' : ''; ?>>Buy It Now</option>
						<option value="Pick It Up Here" <?php echo $am_item_getitnowtext[$i]== "Pick It Up Here" ? 'selected' : ''; ?>>Pick It Up Here</option>
						<option value="Score Yours Now" <?php echo $am_item_getitnowtext[$i]== "Score Yours Now" ? 'selected' : ''; ?>>Score Yours Now</option>
						<option value="See More Here" <?php echo $am_item_getitnowtext[$i]== "See More Here" ? 'selected' : ''; ?>>See More Here</option>
						<option value="Learn More Here" <?php echo $am_item_getitnowtext[$i]== "Learn More Here" ? 'selected' : ''; ?>>Learn More Here</option>
						<option value="Snag One Here" <?php echo $am_item_getitnowtext[$i]== "Snag One Here" ? 'selected' : ''; ?>>Snag One Here</option>
						<option value="Grab It One Here" <?php echo $am_item_getitnowtext[$i]== "Grab It One Here" ? 'selected' : ''; ?>>Grab It One Here</option>
						<option value="Get One Here" <?php echo $am_item_getitnowtext[$i]== "Get One Here" ? 'selected' : ''; ?>>Get One Here</option>
						<option value="Get Yours Now" <?php echo $am_item_getitnowtext[$i]== "Get Yours Now" ? 'selected' : ''; ?>>Get Yours Now</option>
					</select>
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
		echo '</div>';
			?>
		<p>
			<input name="total_count_items" id="total_count_items" type="hidden" value="<?php echo count($contents);?>" />
			<a class="button" href="#" id="add_content">Add new item</a>
		</p>
		<script>
			var startingContent = <?php echo count($contents) - 1; ?>;
			jQuery('#add_content').click(function(e) {
				e.preventDefault();
				startingContent++;
				var total_count_items = startingContent+1;
				jQuery('#total_count_items').val(total_count_items);
				var contentID = 'am_item_description_' + startingContent;
				var am_item_name = 'am_item_name_' + startingContent;
				var am_item_photo = 'am_item_photo_' + startingContent;
				var am_item_imagetype = 'am_item_imagetype_' + startingContent;
				var am_item_imagetype_imageurl = 'am_item_imagetype_imageurl_' + startingContent;
				var am_item_buttontext = 'am_item_buttontext_' + startingContent;
				var am_item_buttonurl = 'am_item_buttonurl_' + startingContent;
				var am_item_getitnowfromname = 'am_item_getitnowfromname_' + startingContent;
				var am_item_getitnowtext = 'am_item_getitnowtext_' + startingContent;
				var am_item_getitnowfromurl = 'am_item_getitnowfromurl_' + startingContent;

				contentRow = '<div class="content-row am-content-row ajx-order-row-' + contentID + '">';
					contentRow += '<div class="dir-btn-grp">';
					contentRow += '<button type="button" tiny-editorid="' + contentID + '" class="updownbtn movetop fa fa-angle-double-up dir-btn"></button>';
					contentRow += '<button type="button" tiny-editorid="' + contentID + '" class="updownbtn moveup fa fa-angle-up dir-btn"></button>';
					contentRow += '<button type="button" tiny-editorid="' + contentID + '" class="updownbtn movedown fa fa-angle-down dir-btn"></button>';
					contentRow += '<button type="button" tiny-editorid="' + contentID + '" class="updownbtn movebottom fa fa-angle-double-down dir-btn"></button>';
					contentRow += '<a class="content-delete dir-btn " href="#"><i class="fa fa-trash-o"></i></a>';
					contentRow += '</div>';
					contentRow += '<h3 class="am-item-title">Item</h3>';
					contentRow += '<div class="am-form-group"><label  class="ammetatitle" for="' + am_item_name + '"><?php _e( 'Name', 'affiliate_marketing_textdomain' ); ?></label><input name="am_item_name[]" type="text" id="' + am_item_name + '" ></div><input  name="am_item_order[]" type="hidden" value="' + startingContent + '"><input  name="am_item_unique_order[]" type="hidden" value="<?php echo $post->ID.'221'.mt_rand() ; ?>"><div class="am-form-group"><label class="ammetatitle" for="' + am_item_imagetype + '"><?php _e( 'Image code', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ); ?><input name="' + am_item_imagetype + '" id="' + am_item_imagetype + '" type="radio" value="imagecode" class="am_item_imagetype" data-postid="' + startingContent + '" data-type-hide="imageurl"></label><label class="ammetatitle" for="' + am_item_imagetype + '"><?php _e( 'Photo', AFFILIATE_MARKETING_CPT_TEXT_DOMAIN ); ?><input name="' + am_item_imagetype + '" id="' + am_item_imagetype_imageurl + '" type="radio" checked value="imageurl" class="am_item_imagetype" data-postid="' + startingContent + '" data-type-hide="imagecode"></label><div class="imageurl" id="imageurl_' + startingContent + '" style="display:none;"><textarea name="am_item_imagecode[]" class="tinytext" id="imagecode-' + startingContent + '" rows="10"></textarea></div><div class="imagecode" id="imagecode_' + startingContent + '"><input type="hidden" value="" class="regular-text process_custom_images" id="process_custom_images" name="am_item_photo[]" max="" min="1" step="1"><button class="set_custom_images button">Upload Image</button><img class="upload-preview" src="" width="100"></div></div>';
					contentRow += '<div class="am-form-group"><label  class="ammetatitle" for="' + contentID + '"><?php _e( 'Description', 'affiliate_marketing_textdomain' ); ?></label><textarea name="am_item_description[]" class="tinytext" id="' + contentID + '" rows="10"></textarea></div>';
					contentRow += '<div class="am-form-group"><label class="ammetatitle" for="' + am_item_buttontext + '"><?php _e( 'Button Text', 'affiliate_marketing_textdomain' ); ?></label><input name="am_item_buttontext[]" type="text" value="Shop This" id="' + am_item_buttontext + '" ></div><div class="am-form-group"><label class="ammetatitle" for="' + am_item_buttonurl + '"><?php _e( 'Button URL', 'affiliate_marketing_textdomain' ); ?></label><input name="am_item_buttonurl[]" type="text" id="' + am_item_buttonurl + '" ></div><div class="am-form-group"><label class="ammetatitle" for="' + am_item_getitnowtext + '"><?php _e( 'Get it now from name', 'affiliate_marketing_textdomain' ); ?></label><select name="am_item_getitnowtext[]"><option value="Get it now here">Get it now here</option><option value="Get it now from">Get it now from</option><option value="Get It Here">Get It Here</option><option value="Buy It Now">Buy It Now</option><option value="Pick It Up Here">Pick It Up Here</option><option value="Score Yours Now">Score Yours Now</option><option value="See More Here">See More Here</option><option value="Learn More Here">Learn More Here</option><option value="Snag One Here">Snag One Here</option><option value="Grab It One Here">Grab It One Here</option><option value="Get One Here">Get One Here</option><option value="Get Yours Now">Get Yours Now</option></select></div><div class="am-form-group"><label class="ammetatitle" for="' + am_item_getitnowfromname + '"><?php _e( 'Get it now from name', 'affiliate_marketing_textdomain' ); ?></label><input name="am_item_getitnowfromname[]" type="text" id="' + am_item_getitnowfromname + '" ></div><div class="am-form-group"><label class="ammetatitle" for="' + am_item_getitnowfromurl + '"><?php _e( 'Get it now from URL', 'affiliate_marketing_textdomain' ); ?></label><input name="am_item_getitnowfromurl[]" type="text" id="' + am_item_getitnowfromurl + '" ></div></div>';

				jQuery('.content-row').eq(jQuery('.content-row').length - 1).after(contentRow);
				tinymce.init({ selector: '#' + contentID , branding: false });
				jQuery(".am_item_imagetype").click(function() {	jQuery('#' + jQuery(this).val() + '_' + jQuery(this).attr('data-postid')).hide(); jQuery('#' + jQuery(this).attr('data-type-hide') + '_' + jQuery(this).attr('data-postid')).show(); });
				jQuery(".moveup").on("click", function() {
					var elem	= jQuery(this).closest( "div.ajx-order-row-" + contentID );
					var editorId = jQuery(this).attr( "tiny-editorid" );
					if ( confirm('Are you sure you want to move this item?') ) {
						elem.prev().before(elem);
						wp.editor.remove( contentID );
						wp.editor.initialize( contentID, {
							tinymce: { wpautop  : true, menubar    : true, toolbar1: 'formatselect bold italic | bullist numlist | blockquote | alignleft aligncenter alignright ', wpeditimage_disable_captions: false, wpeditimage_html5_captions  : true
							}
						} );
					}
				});

				jQuery(".movedown").on("click", function() {
					var elem = jQuery(this).closest( "div.ajx-order-row-" + contentID );
					var editorId = jQuery(this).attr( "tiny-editorid" );
					if ( confirm('Are you sure you want to move this item?') ) {
						elem.next().after(elem);
						wp.editor.remove( contentID );
						wp.editor.initialize( contentID, {
							tinymce: { wpautop  : true, menubar    : true, toolbar1: 'formatselect bold italic | bullist numlist | blockquote | alignleft aligncenter alignright ', wpeditimage_disable_captions: false, wpeditimage_html5_captions  : true
							}
						} );
					}
				});

				jQuery(".movetop").on("click", function() {
					var elem = jQuery(this).closest( "div.ajx-order-row-" + contentID );
					var editorId = jQuery(this).attr( "tiny-editorid" );
					if ( confirm('Are you sure you want to move this item?') ) {
						elem.siblings().first().before(elem);
						wp.editor.remove( contentID );
						wp.editor.initialize( contentID, {
							tinymce: { wpautop  : true, menubar    : true, toolbar1: 'formatselect bold italic | bullist numlist | blockquote | alignleft aligncenter alignright ', wpeditimage_disable_captions: false, wpeditimage_html5_captions  : true
							}
						} );
					}
				});

				jQuery(".movebottom").on("click", function() {
					var elem = jQuery(this).closest( "div.ajx-order-row-" + contentID );
					var editorId = jQuery(this).attr( "tiny-editorid" );
					if ( confirm('Are you sure you want to move this item?') ) {
						elem.siblings().last().after(elem);
						wp.editor.remove( contentID );
						wp.editor.initialize( contentID, {
							tinymce: { wpautop  : true, menubar    : true, toolbar1: 'formatselect bold italic | bullist numlist | blockquote | alignleft aligncenter alignright ', wpeditimage_disable_captions: false, wpeditimage_html5_captions  : true
							}
						} );
					}
				});
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
			// var_dump($filecontents);
			update_post_meta( $post_id, 'am_item_photo', $filecontents );
		}
		$itemCount = $_POST['total_count_items'];
		$am_item_imagetype = array();
		for ($x = 0; $x < $itemCount; $x++) {
			if( isset($_POST['am_item_imagetype_'.$x]) && $_POST['am_item_imagetype_'.$x] != "" ) {
				$am_item_imagetype[] = $_POST['am_item_imagetype_'.$x];
			}
		}

		if ( !empty($am_item_imagetype) && isset( $am_item_imagetype ) ) {
			update_post_meta( $post_id, 'am_item_imagetype', $am_item_imagetype );
		}
		if ( isset( $_POST['am_item_imagecode'] ) ) {
			$am_item_imagecode =  $_POST['am_item_imagecode'] ;
			update_post_meta( $post_id, 'am_item_imagecode', $am_item_imagecode );
		}

		if ( isset( $_POST['am_item_order'] ) ) {
			$am_item_order =  $_POST['am_item_order'] ;
			update_post_meta( $post_id, 'am_item_order', $am_item_order );
		}
		if ( isset( $_POST['am_item_unique_order'] ) ) {
			$am_item_unique_order =  $_POST['am_item_unique_order'] ;
			update_post_meta( $post_id, 'am_item_unique_order', $am_item_unique_order );
		}

		if ( isset( $_POST['am_item_buttontext'] ) ) {
			$am_item_buttontext =  $_POST['am_item_buttontext'] ;
			update_post_meta( $post_id, 'am_item_buttontext', $am_item_buttontext );
		}
		if ( isset( $_POST['am_item_buttonurl'] ) ) {
			$am_item_buttonurl =  $_POST['am_item_buttonurl'] ;
			update_post_meta( $post_id, 'am_item_buttonurl', $am_item_buttonurl );
		}
		if ( isset( $_POST['am_item_getitnowtext'] ) ) {
			$am_item_getitnowtext =  $_POST['am_item_getitnowtext'] ;
			update_post_meta( $post_id, 'am_item_getitnowtext', $am_item_getitnowtext );
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

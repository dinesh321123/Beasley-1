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
			wp_enqueue_editor();
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
			add_meta_box( 'listicle_meta_box', 'Items', array( __CLASS__, 'render_items_metabox' ), $post_type, 'normal', 'high' );
			add_meta_box( 'listicle_footer_meta_box', 'Footer Description', array( __CLASS__, 'render_footer_metabox' ), $post_type, 'normal', 'high' );
		}
	}

	public static function render_footer_metabox( \WP_Post $post ) {
		wp_nonce_field( '_listicle_cpt_footer_description_nonce', '_listicle_cpt_footer_description_nonce' );
		$listicle_cpt_footer_description = self::get_custom_metavalue( 'listicle_cpt_footer_description' );
		$listicle_cpt_footer_description = !empty($listicle_cpt_footer_description) ? $listicle_cpt_footer_description : '';
		?>
		<div class="cpt-form-group">
			<label class="listicle_cpt_footer_description" for="listicle_cpt_footer_description"><?php _e( 'Description', LISTICLE_CPT_TEXT_DOMAIN ); ?></label>
			<?php
				wp_editor( $listicle_cpt_footer_description, 'listicle_cpt_footer_description', array('textarea_rows' => '5'));
			?>
		</div>
		<?php 
	}

	/**
	 * @param $post
	 */
	public static function render_items_metabox( \WP_Post $post ) {
		wp_nonce_field( '_repeatable_editor_nonce', 'repeatable_editor_nonce' );
		$cpt_item_name = self::get_custom_metavalue( 'cpt_item_name' );
		$cpt_item_order = self::get_custom_metavalue( 'cpt_item_order' );
		$contents = self::get_custom_metavalue( 'cpt_item_description' );
		
		$contents = $contents && !empty($contents) ? $contents : array('');
		$cpt_item_name = $cpt_item_name && !empty($cpt_item_name) ? $cpt_item_name : array('');

			echo '<input name="contentcount" type="hidden" value="'. count($contents) .'" class="content_count" />';
			for ($i = 0; $i < count($contents); $i++) {
				?>
				<div class="content-row cpt-content-row">
						<a class="content-delete" href="#" style="color:#a00;float:right;margin-top: 3px;text-decoration:none;font-size:20px;"><svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="-64 0 512 512" width="25px"><path d="m256 80h-32v-48h-64v48h-32v-80h128zm0 0" fill="#62808c"/><path d="m304 512h-224c-26.507812 0-48-21.492188-48-48v-336h320v336c0 26.507812-21.492188 48-48 48zm0 0" fill="#e76e54"/><path d="m384 160h-384v-64c0-17.671875 14.328125-32 32-32h320c17.671875 0 32 14.328125 32 32zm0 0" fill="#77959e"/><path d="m260 260c-6.246094-6.246094-16.375-6.246094-22.625 0l-41.375 41.375-41.375-41.375c-6.25-6.246094-16.378906-6.246094-22.625 0s-6.246094 16.375 0 22.625l41.375 41.375-41.375 41.375c-6.246094 6.25-6.246094 16.378906 0 22.625s16.375 6.246094 22.625 0l41.375-41.375 41.375 41.375c6.25 6.246094 16.378906 6.246094 22.625 0s6.246094-16.375 0-22.625l-41.375-41.375 41.375-41.375c6.246094-6.25 6.246094-16.378906 0-22.625zm0 0" fill="#fff"/></svg></a>
					<h3 class="cpt-item-title">Item</h3>
					<div class="cpt-form-group">
						<label class="cptformtitle" for="cpt_item_name_<?php echo $i; ?>"><?php _e( 'Name', LISTICLE_CPT_TEXT_DOMAIN ); ?> </label>
						<input name="cpt_item_name[]" type="text" value="<?php echo $cpt_item_name[$i]; ?>">
					</div>
					<input name="cpt_item_order[]" type="hidden" value="<?php echo $i; ?>" />
					
					<div class="cpt-form-group">
						<label class="cptformtitle" for="cpt_item_description_<?php echo $i; ?>"><?php _e( 'Description', LISTICLE_CPT_TEXT_DOMAIN ); ?></label>
						<?php
							wp_editor( $contents[$i], 'tiny-editor-'.$i, array( 'textarea_name' =>'cpt_item_description[]', 'textarea_rows' => '10' ) );
						?>
					</div>
					<br style="clear:both">
				</div>
				<?php
			}
			?>
		<p><a class="button" href="#" id="add_content">Add new item</a></p>
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
		if ( isset( $_POST['cpt_item_order'] ) ) {
			$cpt_item_order =  $_POST['cpt_item_order'] ;
			update_post_meta( $post_id, 'cpt_item_order', $cpt_item_order );
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

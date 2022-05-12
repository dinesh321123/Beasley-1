<?php
/**
 * Class FooterDescriptionSettings
 */
class FooterDescriptionSettings {
	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post',array( __CLASS__, 'common_footer_description_save') );
	}

	public static function add_meta_box( $post_type ) {
		$post_types = FooterDescriptionSettings::get_footer_description_posttype_list();
		if ( in_array( $post_type, $post_types ) ) {
			add_meta_box( 'common_footer_description_meta_box', 'Footer Description', array( __CLASS__, 'render_common_footer_description_metabox' ), $post_type, 'normal', 'high' );
		}
	}
	public static function render_common_footer_description_metabox( \WP_Post $post ) {
		wp_nonce_field( '_common_footer_description_nonce', '_common_footer_description_nonce' );
		$common_footer_description = self::get_custom_metavalue( 'common_footer_description' );
		$common_footer_description = !empty($common_footer_description) ? $common_footer_description : '';
		?>
		<div class="cpt-form-group">
			<label class="common_footer_description" for="common_footer_description"><?php _e( 'Common Footer Description', general_settings_textdomain ); ?></label>
			<?php
			wp_editor( $common_footer_description, 'common_footer_description', array('textarea_rows' => '5'));
			?>
		</div>
		<?php
	}

	function common_footer_description_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! isset( $_POST['_common_footer_description_nonce'] ) || ! wp_verify_nonce( $_POST['_common_footer_description_nonce'], '_common_footer_description_nonce' ) ) return;
		if ( ! current_user_can( 'edit_post' ) ) return;

		if ( isset( $_POST['common_footer_description'] ) ) {
			$common_footer_description = $_POST['common_footer_description'];
			update_post_meta( $post_id, 'common_footer_description', $common_footer_description );
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

	/**
	 * Returns array of post type.
	 * @return array
	 */
	public static function get_footer_description_posttype_list() {
		$result	= (array) apply_filters(
			'common-footer-description-post-types',
			array(
				'post',
				'tribe_events',
				'page',
				'gmr_gallery',
				// 'gmr_album',
				'contest',
				// 'show',
				'podcast',
				// 'episode',
				'advertiser'
			)
		);
		return $result;
	}
}

FooterDescriptionSettings::init();

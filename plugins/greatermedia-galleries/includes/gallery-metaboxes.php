<?php
/**
 * Class GMP_Meta
 *
 * This class constructs a meta box for episodes and saves data entered into the fields of the meta box.
 */
class GreaterMediaGalleryMetaboxes {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		self::add_save_post_actions();
	}

	public static function add_save_post_actions() {
		add_action( 'save_post_' . GreaterMediaGalleryCPT::GALLERY_POST_TYPE, array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	public static function remove_save_post_actions() {
		remove_action( 'save_post_' . GreaterMediaGalleryCPT::GALLERY_POST_TYPE, array( __CLASS__, 'save_meta_box' ), 10 );
	}

	/**
	 * Adds the meta box container for Episodes.
	 *
	 * @param $post_type
	 */
	public static function add_meta_box( $post_type ) {

		$post_types = array( GreaterMediaGalleryCPT::GALLERY_POST_TYPE );

		if ( in_array( $post_type, $post_types ) ) {
			add_meta_box( 'gmp_albums_meta_box', 'Album', array( __CLASS__, 'render_parent_metabox' ), $post_type, 'side' );
		}

	}

	/**
	 * Render Meta Box content for Episodes.
	 *
	 * @param $post
	 */
	public static function render_parent_metabox( \WP_Post $post ) {
		$album_args = array(
			'post_type' => GreaterMediaGalleryCPT::ALBUM_POST_TYPE,
			'posts_per_page' => 100,
			'paged' => 0,
		);

		wp_nonce_field( 'save_gallery_parent', 'gallery_parent_nonce' );

		?>
		<select name="gallery-parent" id="gallery-parent">
		<option value="0">&mdash; Select an Album &mdash;</option>
		<?php

		do {
			$album_args['paged']++;
			$album_query = new WP_Query( $album_args );
			while( $album_query->have_posts() ) {
				$album = $album_query->next_post();

				?><option value="<?php echo intval( $album->ID ); ?>" <?php selected( $album->ID, $post->post_parent ); ?>><?php echo esc_html( $album->post_title ); ?></option><?php
			}
		} while ( $album_args['paged'] < $album_query->max_num_pages );

		?></select><?php
	}

	/**
	 * Save the meta when the post is saved for Episodes.
	 *
	 * @param $post_id
	 */
	public static function save_meta_box( $post_id, \WP_Post $post ) {
		if ( ! isset( $_POST['gallery_parent_nonce'] ) || ! wp_verify_nonce( $_POST['gallery_parent_nonce'], 'save_gallery_parent' ) ) {
			return;
		}

		if ( ! isset( $_POST['gallery-parent'] ) ) {
			return;
		}

		$parent_id = intval( $_POST['gallery-parent'] );

		$post->post_parent = $parent_id;

		self::remove_save_post_actions();
		wp_update_post( $post );
		self::add_save_post_actions();
	}

}

GreaterMediaGalleryMetaboxes::init();

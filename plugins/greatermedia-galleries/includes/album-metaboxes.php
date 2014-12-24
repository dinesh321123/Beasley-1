<?php
/**
 * Class GMP_Meta
 *
 * This class constructs a meta box for episodes and saves data entered into the fields of the meta box.
 */
class GreaterMediaGalleryAlbumMetaboxes {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		self::add_save_post_actions();
	}

	public static function add_save_post_actions() {
		add_action( 'save_post_' . GreaterMediaGalleryCPT::ALBUM_POST_TYPE, array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	public static function remove_save_post_actions() {
		remove_action( 'save_post_' . GreaterMediaGalleryCPT::ALBUM_POST_TYPE, array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	/**
	 * Adds the meta box container for Episodes.
	 *
	 * @param $post_type
	 */
	public static function add_meta_box( $post_type ) {

		$post_types = array( GreaterMediaGalleryCPT::ALBUM_POST_TYPE );

		if ( in_array( $post_type, $post_types ) ) {
			add_meta_box( 'gmp_galleries_meta_box', 'Galleries', array( __CLASS__, 'render_gallery_metabox' ), $post_type, 'advanced', 'high' );
		}

	}

	/**
	 * Render Meta Box content for Episodes.
	 *
	 * @param $post
	 */
	public static function render_gallery_metabox( \WP_Post $post ) {
		wp_nonce_field( 'save_album_galleries', 'album_gallery_nonce' );

		$limit = 1000; // Arbitrary high limit, since we need something here

		$options = array(
			'args' => array(
				'post_type' => GreaterMediaGalleryCPT::GALLERY_POST_TYPE,
				'post_parent__in' => array( 0, $post->ID ), // Galleries with no parent, or with THIS parent
			),
			'limit' => $limit,
		);

		$children_ids = self::get_child_gallery_ids( $post->ID );

		pf_render( 'gmr_album_galleries', implode( ',', $children_ids ), $options );
	}

	/**
	 * Save the meta when the post is saved for Episodes.
	 *
	 * @param $post_id
	 */
	public static function save_meta_box( $post_id, \WP_Post $post ) {
		if ( ! isset( $_POST['album_gallery_nonce'] ) || ! wp_verify_nonce( $_POST['album_gallery_nonce'], 'save_album_galleries' ) ) {
			return;
		}

		if ( ! isset( $_POST['gmr_album_galleries'] ) ) {
			return;
		}

		// Deactivate this method to escape infinite loop
		self::remove_save_post_actions();

		$old_gallery_ids = self::get_child_gallery_ids( $post_id );
		$gallery_ids = array_map( 'intval', explode( ',', $_POST['gmr_album_galleries'] ) );

		// Get IDs of galleries that were deleted, by figuring out what ids are in the old array and not in the new array
		$deleted_galleries = array_diff( $old_gallery_ids, $gallery_ids );
		foreach( $deleted_galleries as $deleted_gallery_id ) {
			$deleted_gallery_post = get_post( $deleted_gallery_id );
			$deleted_gallery_post->post_parent = 0;
			wp_update_post( $deleted_gallery_post );
		}

		// Loop through the new posts, and update the menu order and post parent on all of them
		$count = 0;
		foreach( $gallery_ids as $gallery_id ) {
			$count++;
			$gallery_post = get_post( $gallery_id );
			$gallery_post->post_parent = $post_id;
			$gallery_post->menu_order = $count;
			wp_update_post( $gallery_post );
		}
	}

	public static function get_child_gallery_ids( $post_id ) {
		$children_args = array(
			'post_type' => GreaterMediaGalleryCPT::GALLERY_POST_TYPE,
			'post_parent' => $post_id,
			'fields' => 'ids',
			'limit' => 1000,
			'orderby' => 'menu_order',
			'order' => 'ASC',
		);
		$children_ids = get_posts( $children_args );

		return $children_ids;
	}

}

GreaterMediaGalleryAlbumMetaboxes::init();

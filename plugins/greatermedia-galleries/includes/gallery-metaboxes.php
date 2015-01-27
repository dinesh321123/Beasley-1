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
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'edit_form_after_title', array( __CLASS__, 'inline_instructions' ) );
		add_action( 'edit_form_after_title', array( __CLASS__, 'gallery_builder' ), 11 );

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
	 * Enqueues admin scripts and styles.
	 *
	 * @global string $typenow The current type.
	 * @global string $pagenow The current page.
	 */
	public static function enqueue_scripts() {
		global $typenow, $pagenow;

		if ( GreaterMediaGalleryCPT::GALLERY_POST_TYPE == $typenow && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

			wp_enqueue_media();
			wp_enqueue_style( 'gmr-gallery-admin', GREATER_MEDIA_GALLERIES_URL . "assets/css/gmr_gallery_admin{$postfix}.css", null, GREATER_MEDIA_GALLERIES_VERSION );
			wp_enqueue_script( 'gmr-gallery-admin', GREATER_MEDIA_GALLERIES_URL . "assets/js/gmr_admin{$postfix}.js", array( 'jquery', 'jquery-ui-sortable' ), GREATER_MEDIA_GALLERIES_VERSION, true );
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

		delete_post_meta( $post_id, 'gallery-image' );
		if ( ! empty( $_POST['gmr-gallery-items'] ) ) {
			foreach ( $_POST['gmr-gallery-items'] as $id ) {
				$attachment = wp_get_attachment_image_src( $id, 'medium' );
				if ( ! empty( $attachment ) ) {
					add_post_meta( $post_id, 'gallery-image', $id );
				}
			}
		}

		if ( isset( $_POST['gallery-parent'] ) ) {
			$parent_id = intval( $_POST['gallery-parent'] );

			$post->post_parent = $parent_id;

			self::remove_save_post_actions();
			wp_update_post( $post );
			self::add_save_post_actions();
		}
	}

	/**
	 * Output instructions on creating a gallery
	 */
	public static function inline_instructions( $post ) {
		// These instructions are applicable to adding a gallery anywhere, but unlike a post
		// it's a bit unclear where to start, so we'll only output them on the gallery post type
		if ( GreaterMediaGalleryCPT::GALLERY_POST_TYPE !== $post->post_type ) {
			return;
		}

		?>
		<h3>To add a gallery:</h3>
		<ol>
			<li>Click the <strong>Add Media</strong> button</li>
			<li>Select "Create Gallery" in the left menu</li>
			<li>Upload or select photos from the media library</li>
			<li>Click on "Create a new Gallery"</li>
			<li>Click on "Insert Gallery"</li>
		</ol>

		<p>
			All the gallery images will be extracted and displayed above the gallery title and any text.
		</p>
		<?php
	}

	/**
	 * Renders gallery builder section.
	 * 
	 * @action edit_form_after_title
	 * @param \WP_Post $post The current post object.
	 */
	public static function gallery_builder( \WP_Post $post ) {
		if ( GreaterMediaGalleryCPT::GALLERY_POST_TYPE !== $post->post_type ) {
			return;
		}

		$images = array();
		foreach ( get_post_meta( $post->ID, 'gallery-image' ) as $attachment_id ) {
			$attachment = wp_get_attachment_image_src( $attachment_id, 'medium' );
			if ( ! empty( $attachment ) ) {
				$images[] = array(
					'id'    => $attachment_id,
					'image' => $attachment[0],
				);
			}
		}

		?><div id="gallery-builder">
			<script id="gallery-item-tmpl" type="text/html">
				<li class="gallery-item gallery-image" style="background-image:url(%image%)">
					<input type="hidden" name="gmr-gallery-items[]" value="%id%">
					<a class="remove-gallery-item" href="#">
						<span class="dashicons dashicons-trash"></span>
					</a>
				</li>
			</script>
			
			<ul class="gallery-preview">
				<?php foreach ( $images as $image ) : ?>
					<li class="gallery-item gallery-image" style="background-image:url(<?php echo esc_url( $image['image'] ); ?>)">
						<input type="hidden" name="gmr-gallery-items[]" value="<?php echo esc_attr( $image['id'] ); ?>">
						<a class="remove-gallery-item" href="#">
							<span class="dashicons dashicons-trash"></span>
						</a>
					</li>
				<?php endforeach; ?>

				<li class="gallery-item">
					<a class="add-gallery-item" href="#">
						<span class="dashicons dashicons-plus-alt"></span>
						ADD IMAGE
					</a>
				</li>
			</ul>
		</div><?php
	}

}

GreaterMediaGalleryMetaboxes::init();

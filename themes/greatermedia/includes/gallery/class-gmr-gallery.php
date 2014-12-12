<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

/**
 * Gallery related functions
 *
 * Desired functionality:
 *
 * Each gallery in post_content should create a page break immediately before it, so that the gallery is the first thing on each page.
 *
 * IF there is a Greater Media Gallery, it will be used for the gallery on page 1. If there is not a gmr gallery, the first
 * gallery in the content will be used for this.
 *
 * Example Page/Gallery WITH Greater Media Gallery:
 *  - Page 1: Greater Media Gallery
 *  - Page 2: First [gallery] shortcode
 *  - Page 3: Second [gallery] shortcode
 *  - etc
 *
 * Example Page/Gallery WITHOUT Greater Media Gallery
 *  - Page 1: First [gallery] Shortcode
 *  - Page 2: Second [gallery] Shortcode
 *  - etc
 *
 * Since WordPress only splits pages using the <!--nextpage--> tag, we filter the post_content on save_post to include
 * these either immediately before or after each gallery shortcode. We will insert <!--gmr--><!--nextpage--> tags so
 * that they are unique and can be filtered and reset easily.
 *
 * They will be placed before [gallery] if we have a Harris Gallery, so that the content for the first page does not have
 * it's own [gallery]. If there is not a gmr gallery, the nextpage tags will be placed after the [gallery] shortcode
 * to make sure that each page has it's own gallery.
 */

class GreaterMediaGallery {

	/**
	 * Sets up actions and filters for the gallery class.
	 */
	public static function init() {
		add_filter( 'is_protected_meta', array( __CLASS__, 'filter_is_protected_meta' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post' ) );
		add_filter( 'the_content', array( __CLASS__, 'strip_wp_gallery_shortcode' ) );

		//		add_action( 'save_post', array( __CLASS__, 'paginate_post_content' ), 100, 2 );
		//		add_filter( 'content_edit_pre', array( __CLASS__, 'strip_gmr_pagination' ) );
	}

	// In case we need to ever remove and then re add filters/actions (for things like preventing infinite loops on post_save) this should make it easy
	public static function remove_actions() {
		remove_filter( 'is_protected_meta', array( __CLASS__, 'filter_is_protected_meta' ), 10, 2 );
		remove_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
		remove_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		remove_action( 'save_post', array( __CLASS__, 'save_post' ) );
		remove_filter( 'the_content', array( __CLASS__, 'strip_wp_gallery_shortcode' ) );

		//		remove_action( 'save_post', array( __CLASS__, 'paginate_post_content' ), 100, 2 );
		//		remove_filter( 'content_edit_pre', array( __CLASS__, 'strip_gmr_pagination' ) );
	}

	/**
	 * Marks meta keys related to the gallery functionality as protected, so they can't be messed with via custom fields.
	 *
	 * @param bool $protected Is the current key currently marked as protected
	 * @param string $meta_key The meta key to check
	 *
	 * @return bool
	 */
	public static function filter_is_protected_meta( $protected, $meta_key ) {
		if ( 'gmr-gallery-ids' === $meta_key ) {
			$protected = true;
		}

		return $protected;
	}

	public static function admin_enqueue_scripts() {
		if ( 'gallery' !== get_post_type() ) {
			return;
		}

		// todo this can eventually move to an admin or admin-post specific JS file - For now, just loading here, since this is the only custom admin JS
		wp_enqueue_script( 'gmr-gallery-admin', get_template_directory_uri() . '/assets/js/src/admin_gallery.js', array( 'jquery' ), false, true );
	}

	/**
	 * Adds the meta box for rendering the gallery preview to 'post' post types.
	 *
	 * @param string $post_type The current post type.
	 */
	public static function add_meta_boxes( $post_type ) {
		if ( 'gallery' !== $post_type ) {
			return;
		}

		add_meta_box( 'gmr-gallery-metabox', 'Gallery', array( __CLASS__, 'gallery_meta_box' ), $post_type, 'normal', 'high' );
	}

	/**
	 * Renders the gallery meta box.
	 *
	 * Currently just calls gallery_preview - If nothing else needs to be in this meta box, then we can just replace the
	 * callback in the add_meta_box function above with a straight call to gallery_preview.
	 *
	 * @param WP_Post $post The current post object.
	 */
	public static function gallery_meta_box( $post ) {
		self::gallery_preview( $post );
	}

	/**
	 * Saves information about the gallery attached to the post.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public static function save_post( $post_id ) {
		// Verify nonce - Also serves as post type check, since the nonce wont be present unless we are on a gallery page
		if ( ! isset( $_POST['gmr-gallery-nonce'] ) || ! wp_verify_nonce( $_POST['gmr-gallery-nonce'], 'save-gallery' ) ) {
			return;
		}

		if ( isset( $_POST['gmr-gallery-ids'] ) && '' !== trim( $_POST['gmr-gallery-ids'] ) ) {
			// Get an array of the ids
			$ids = explode( ',', $_POST['gmr-gallery-ids'] );

			// Sanitizes, Filters, and Saves Image IDs for the gallery.
			self::set_image_ids( $post_id, $ids );
		} else {
			self::delete_image_ids( $post_id );
		}
	}

	/**
	 * Adds <!--gmr--><!--nextpage--> tags to the post content before or after gallery shortcodes.
	 *
	 * See explanation at the top of this file for when they are placed before or after.
	 *
	 * @param $post_id
	 * @param $post
	 */
	public static function paginate_post_content( $post_id, $post ) {
		if ( self::has_gmr_gallery( $post_id ) ) {
			$before = true;
		} else {
			$before = false;
		}

		if ( $before ) {
			$post->post_content = str_ireplace( '[gallery', '<!--gmr--><!--nextpage-->[gallery', $post->post_content );
		} else {
			$post->post_content = preg_replace( '#(\[gallery[^\]]*\])#i', '$1<!--gmr--><!--nextpage-->', $post->post_content );

		}

		self::remove_actions();
		wp_update_post( $post );
		self::init(); // Add the actions back!
	}

	/**
	 * Remove the <!--gmr--><!--nextpage--> tags from the post_content when viewing / editing posts in the admin.
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public static function strip_gmr_pagination( $content ) {
		$content = str_ireplace( '<!--gmr--><!--nextpage-->', '', $content );

		return $content;
	}



	/* General Helpers */
	/**
	 * Function to determine if a given post has any gallery images attached to it. Respects pagination (Greater Media galleries would only show up on page 1)
	 *
	 * @param int $post_id The post ID of the post to check. Defaults to current global $post object's ID.
	 *
	 * @return bool True if there are gallery images.
	 */
	public static function has_gallery( $post_id = null ) {
		global $page;

		if ( is_null( $post_id ) ) {
			$post_id = get_the_ID();
		}

		// Not using gmr galleries on anything but the first page of a post
		if ( 1 == $page && self::has_gmr_gallery( $post_id ) ){
			return true;
		}

		if ( self::has_wp_gallery( $post_id ) ){
			return true;
		}

		return false;
	}

	public static function has_wp_gallery( $post_id ) {
		// Content for the current page
		$post_content = get_the_content();

		if ( ! has_shortcode( $post_content, 'gallery' ) ) {
			return false;
		}

		return true;
	}

	public static function has_gmr_gallery( $post_id ) {
		$attachment_ids = self::get_image_ids( $post_id );

		if ( empty( $attachment_ids ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns the images in the gallery, in the order they should be displayed. Does not take WP Galleries into account.
	 *
	 * @param int $post_id The current post object.
	 *
	 * @return array Array of WP_Post objects corresponding to the gallery attachments.
	 */
	public static function get_images_in_order( $post_id = null ) {
		if ( is_null( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$additional_args = array();

		$attachment_ids = self::get_image_ids( $post_id );

		if ( empty( $attachment_ids ) ) {
			// return an empty array if we don't have any gallery images for the post
			return array();
		}

		$additional_args['include'] = $attachment_ids;
		$additional_args['orderby'] = 'post__in';
		$additional_args['posts_per_page'] = count( $attachment_ids );

		$params = array(
			'post_status' => 'inherit',
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'no_found_rows' => true,
		);

		$params = array_merge( $params, $additional_args );

		$attachments = get_posts( $params );

		return $attachments;
	}

	/**
	 * Returns a new WP_Query object for the gallery associated with a specified post.
	 *
	 * Very similar to the get_images_in_order function, except it returns a WP_Query object instead of an array of attachments.
	 *
	 * @param null $post_id
	 *
	 * @return array|WP_Query
	 */
	public static function get_gallery_query( $post_id = null ) {
		global $page;
		if ( is_null( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$additional_args = array();

		$have_gmr_gallery = self::has_gmr_gallery( $post_id );

		// Determine what gallery we should be working with...
		if ( 1 == $page && $have_gmr_gallery ) {
			// If page 1 and we have a gmr gallery, we want to work with that
			$attachment_ids = self::get_image_ids( $post_id );

			$additional_args['post__in'] = $attachment_ids;
			$additional_args['orderby'] = 'post__in';
			$additional_args['posts_per_page'] = count( $attachment_ids );
		} else {
			// Otherwise, we work with the wordpress [gallery] shortcodes in the post content

			$wp_galleries = self::get_post_galleries( $post_id, false );

			$wp_gallery_data = reset( $wp_galleries );

			if ( isset( $wp_gallery_data['ids'] ) && ! empty( $wp_gallery_data['ids'] ) ) {
				$attachment_ids = array_filter( array_map( 'intval', explode( ',', $wp_gallery_data['ids'] ) ) );

				$additional_args['post__in'] = $attachment_ids;
				$additional_args['orderby'] = 'post__in';
				$additional_args['posts_per_page'] = count( $attachment_ids );
			} else {
				// This is the case where there is [gallery] without ids specified
				$additional_args['post_parent'] = $post_id;
				$additional_args['posts_per_page'] = 100; // not likely to ever have this many, but don't want unlimited
			}
		}

		$params = array(
			'post_status' => 'inherit',
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'no_found_rows' => true,
		);

		$params = array_merge( $params, $additional_args );

		return new WP_Query( $params );
	}

	/**
	 * Strips the first gallery shortcode from the content ONLY if the gallery metabox is not being used.
	 *
	 * Ripped off from here: http://wordpress.stackexchange.com/questions/121489/split-content-and-gallery
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public static function strip_wp_gallery_shortcode( $content ) {
		global $page;

		// Don't strip the first gallery from content if we aren't using it as the featured gallery.
		// We only use the first gallery in the content as featured gallery if we dont have a gmr gallery
		if ( 1 == $page && self::has_gmr_gallery( get_the_ID() ) ) {
			return $content;
		}
		preg_match_all( '/'. get_shortcode_regex() .'/s', $content, $matches, PREG_SET_ORDER );
		if ( ! empty( $matches ) ) {
			foreach ( $matches as $shortcode ) {
				if ( 'gallery' === $shortcode[2] ) {
					$pos = strpos( $content, $shortcode[0] );
					if ($pos !== false)
						return substr_replace( $content, '', $pos, strlen($shortcode[0]) );
				}
			}
		}
		return $content;
	}


	/**
	 * Gets attachment IDs for the given post.
	 *
	 * @param int $post_id The post ID to get the image ID's for
	 *
	 * @return array Array of attachment IDs
	 */
	public static function get_image_ids( $post_id ) {
		$ids = array_filter( array_map( 'intval', explode( ',', get_post_meta( $post_id, 'gmr-gallery-ids', true ) ) ) );

		return $ids;
	}

	public static function set_image_ids( $post_id, $image_ids ) {
		update_post_meta( $post_id, 'gmr-gallery-ids', implode( ',', array_filter( array_map( 'intval', $image_ids ) ) ) );
	}

	public static function delete_image_ids( $post_id ) {
		delete_post_meta( $post_id, 'gmr-gallery-ids' );
	}


	/* Admin Rendering */

	/**
	 * Renders thumbnails, to preview the gallery, and select the featured image.
	 *
	 * @param WP_Post $post The current post object.
	 */
	public static function gallery_preview( $post ) {
		$gallery_ids = get_post_meta( $post->ID, 'gmr-gallery-ids', true );
		$attachments = self::get_images_in_order( $post->ID );

		if ( empty( $gallery_ids ) ) {
			$label = 'Create Gallery';
		} else {
			$label = 'Edit Gallery';
		}
		?>

		<div id="gmr-gallery-preview">
			<div class="gmr-manage-gallery-container">
				<a href="#" id="manage-gallery-button" class="button manage_gallery" title="Manage Gallery"><span class="dashicons dashicons-admin-media"></span>&nbsp;<span id="manage-gallery-text"><?php echo esc_html( $label ); ?></span></a>
				<a href="#" id="clear-gallery-button" class="button clear_gallery" title="Clear Gallery"><span id="clear-gallery-text">Clear Images</span></a>
				<input type="hidden" id="gmr-gallery-ids" name="gmr-gallery-ids" value='<?php echo esc_attr( $gallery_ids ); ?>' />
			</div>

			<div class="gallery-preview" id="gmr-gallery-images">
				<?php
				foreach ( $attachments as $attachment ) {
					$attachment_src = wp_get_attachment_image_src( $attachment->ID, 'thumbnail' );
					?>
					<div class="gallery-item attachment" data-attachment-id="<?php echo esc_attr( $attachment->ID ); ?>">
						<img class="attachment-thumbnail" src="<?php echo esc_url( $attachment_src[0] ); ?>" alt="Gallery Image Preview"/>
					</div>
				<?php
				}
				?>
				<?php // Template used when re-rendering the thumbnails after updating the gallery in JS ?>
				<script type="text/template" id="gmr-gallery-item-template">
					<div class="gallery-item attachment" data-attachment-id="{{attachment_id}}">
						<img class="attachment-thumbnail" src="{{thumbnail_url}}" alt="Gallery Image Preview"/>
					</div>
				</script>
			</div>
		</div>

		<?php
		wp_nonce_field( 'save-gallery', 'gmr-gallery-nonce' );
	}

	/**
	 * Near clone of the core get_post_galleries function, but only uses the current set of content, to make it easier to
	 * get the correct gallery on paged posts!
	 *
	 * @return array
	 */
	public static function get_post_galleries() {
		// The current post content. Only for the current page of the post if this is a paged post, so that we can get the correct gallery
		$post_content = get_the_content();

		if ( ! has_shortcode( $post_content, 'gallery' ) )
			return array();

		$galleries = array();
		if ( preg_match_all( '/' . get_shortcode_regex() . '/s', $post_content, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $shortcode ) {
				if ( 'gallery' === $shortcode[2] ) {
					$srcs = array();

					$gallery = do_shortcode_tag( $shortcode );

					preg_match_all( '#src=([\'"])(.+?)\1#is', $gallery, $src, PREG_SET_ORDER );
					if ( ! empty( $src ) ) {
						foreach ( $src as $s )
							$srcs[] = $s[2];
					}

					$data = shortcode_parse_atts( $shortcode[3] );
					$data['src'] = array_values( array_unique( $srcs ) );
					$galleries[] = $data;
				}
			}
		}

		return $galleries;
	}



	/* Front End Rendering */

	/**
	 * Render the gallery
	 *
	 * @param int $post_id The post id for the gallery to render. Default null.
	 * @param bool $echo Echo or return the gallery html. Default true.
	 *
	 * @return mixed Returns rendered html if $echo is false, or else everything is echoed.
	 */
	public static function render( $post_id = null, $echo = true ) {
		if ( is_null( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$attachments = self::get_images_in_order( $post_id );

		$output = "<div class='gallery clearfix'>";

		// todo finish the front end rendering function (Loop over $attachments for the array of attachments for the gallery)

		$output .= '</div>';

		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}
	}

}

GreaterMediaGallery::init();

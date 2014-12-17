<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaGallery {

	public static $strip_shortcodes = false;

	public static function init() {
		// Override the core gallery shortcode with our own handler
		remove_shortcode( 'gallery' );
		add_shortcode( 'gallery', array( __CLASS__, 'render_shortcode' ) );

		// If we need to manually render somewhere, like on the top of a single-gallery template
		add_action( 'gmr_gallery', array( __CLASS__, 'do_gallery_action' ) );

		// Remove gallery shortcodes from content, since we have these at the top of single-page
		add_filter( 'the_content', array( __CLASS__, 'strip_for_single_gallery' ) );
	}

	/**
	 * Strips gallery shortcodes for content, on pages where we know we've run the action instead
	 *
	 * @param string $content
	 *
	 * @return string Final content with galleries removed
	 */
	public static function strip_for_single_gallery( $content ) {
		if ( self::$strip_shortcodes ) {
			$content = preg_replace( '/\[gallery.*?\]/', '', $content );
		}

		return $content;
	}

	/**
	 * Get an array of photos for a gallery.
	 *
	 * @param $post
	 *
	 * @return array
	 */
	public static function get_gallery_photos( $post ) {
		preg_match_all( '/\[gallery.*ids=.(.*).\]/', $post->post_content, $ids );

		$array_ids = array();
		foreach( $ids[1] as $match ) {
			$array_id = explode( ',', $match );
			$array_id = array_map( 'intval', $array_id );

			$array_ids = array_merge( $array_ids, $array_id );
		}

		$photos = array();

		foreach( $array_ids as $id ) {
			$image = wp_get_attachment_image_src( $id, 'gmr-gallery' );
			$thumb = wp_get_attachment_image_src( $id, 'gmr-gallery-thumbnail' );

			if ( ! $image ) {
				continue;
			}

			$photos[] = array(
				'url'       => $image[0],
				'title'     => get_post_field( 'post_excerpt', $id ),
				'thumbnail' => $thumb[0], // 82x46
			);
		}

		return $photos;
	}

	/**
	 * Returns a WP_Query that corresponds to the IDs provided
	 *
	 * @param array $ids Array of image IDs
	 *
	 * @return WP_Query
	 */
	public static function get_query_for_ids( $ids ) {
		$photos = new WP_Query(
			array(
				'ignore_sticky_posts' => true,
				'post__in'            => $ids,
				'post_status'         => 'inherit',
				'post_type'           => 'attachment',
				'posts_per_page'      => count( $ids ),
				'orderby'             => 'post__in',
			)
		);

		return $photos;
	}

	/**
	 * Gets a WP_Query for the attachments in the gallery
	 * @param $post
	 * @return WP_Query
	 */
	public static function get_query_for_post( $post ) {
		preg_match_all( '/\[gallery.*ids=.(.*).\]/', $post->post_content, $ids );

		$array_ids = array();
		foreach( $ids[1] as $match ) {
			$array_id = explode( ',', $match );
			$array_id = array_map( 'intval', $array_id );

			$array_ids = array_merge( $array_ids, $array_id );
		}

		$query = self::get_query_for_ids( $array_ids );

		return $query;
	}

	/**
	 * Renders a gallery for a post when do_action( 'gmr_gallery' ) is called
	 */
	public static function do_gallery_action( $strip_shortcodes ) {
		// So that we remove the gallery from content, since we're rendering it now
		self::$strip_shortcodes = true;

		$gallery_query = self::get_query_for_post( get_queried_object() );

		echo self::render_gallery_from_query( $gallery_query );
	}

	/**
	 * Renders the gallery shortcode
	 *
	 * @param $args
	 */
	public static function render_shortcode( $args ) {
		$defaults = array(
			'ids' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$ids = array_map( 'intval', explode( ',', $args['ids'] ) );

		$query = self::get_query_for_ids( $ids );

		return self::render_gallery_from_query( $query );
	}

	/**
	 * Renders the gallery html when given a WP_Query that corresponds to the images we need to use.
	 *
	 * Abstracted, so we can share with the shortcode rendering
	 *
	 * @param WP_Query $gallery
	 *
	 * @return string Rendered HTML for the gallery
	 */
	public static function render_gallery_from_query( \WP_Query $gallery ) {
		ob_start();
		if ( $gallery->have_posts() ):
			$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

			/* all js files are being concatenated into a single js file.
			 * This include all js files for cycle2, located in `assets/js/vendor/cycle2/`
			 * and `gmr_gallery.js`, located in `assets/js/src/`
			 */
			wp_register_script(
				'gmr-gallery',
				GREATER_MEDIA_GALLERIES_URL . "assets/js/gmr_gallery{$postfix}.js",
				array(
					'jquery'
				),
				GREATER_MEDIA_GALLERIES_VERSION,
				true
			);
			wp_enqueue_script(
				'gmr-gallery'
			);

			$main_post_id         = get_queried_object_id();

			$thumbnails_per_page = 8;
			$image_count_text = sprintf( __( '%s of %s', 'greatermedia' ), '{{slideNum}}', '{{slideCount}}' );
			?>
			<div class="gallery">
				<div class="container">
					<div class="gallery__slides">
						<div class="gallery__slide--images cycle-slideshow"
						     data-cycle-log="false"
						     data-slides="> .gallery__slide--image"
						     data-cycle-prev=".gallery__prev--btn"
						     data-cycle-next=".gallery__next--btn"
						     data-cycle-timeout="0"
						     data-cycle-caption="#gallery__count"
						     data-cycle-caption-template="<?php echo esc_attr( $image_count_text ); ?>"
						     data-cycle-center-horz="true"
						     data-cycle-center-vert="true"
						     data-cycle-manual-speed="200"
						     data-cycle-swipe="true"
						     data-cycle-fx="scrollHorz"
						     data-cycle-swipe-fx="scrollHorz"
						     data-cycle-manual-fx="scrollHoriz"
						     data-cycle-auto-height=container>
							<?php
							while ( $gallery->have_posts() ) {
								$gallery->the_post();
								$slide_hash = get_post_field( 'post_name', get_the_ID() );
								$slide_link = get_permalink( $main_post_id ) . '#' . $slide_hash;

								$attr = array(
									'data-cycle-hash'           => get_post_field( 'post_name', get_the_ID() ),
									'data-cycle-slide_shorturl' => $slide_link,
									'data-cycle-slide_title'    => urlencode( get_the_title() ),
								);
								$image = wp_get_attachment_image_src( get_the_ID(), array( 775, 516 ), false );
								$image_url = $image[0];
								?>
								<div class="gallery__slide--image"
									<?php
									foreach ( $attr as $attr_name => $attr_value ) {
										echo $attr_name . '="' . esc_attr( $attr_value ) . '" ';
									}
									?>
									 style="background-image: url(<?php echo esc_url( $image_url ); ?>);">
								</div>
							<?php

							}
							$gallery->rewind_posts();
							?>
						</div>
					</div>
					<div class="gallery__meta">
						<div class="gallery__content cycle-slideshow"
						     data-cycle-log="false"
						     data-cycle-slides="> div"
						     data-cycle-prev=".gallery__prev--btn"
						     data-cycle-next=".gallery__next--btn"
						     data-cycle-timeout="0"
						     data-cycle-manual-speed="1"
						     data-cycle-auto-height=container>
							<?php
							while ( $gallery->have_posts() ){
								global $post;
								$gallery->the_post();
								$slide_hash = get_post_field( 'post_name', get_the_ID() );
								$slide_link = get_permalink( $main_post_id ) . '#' . $slide_hash;
								$image_title = get_the_title( $post ); // title of the gallery image
								echo '<div class="gallery__slide--content" data-cycle-hash="' . $slide_hash . '">';
								echo '<h2 class="gallery__slide--title">';
								echo $image_title;
								echo '</h2>';
								echo '<div class="gallery__social">';
								echo '<a class="icon-facebook social-share-link" href="http://www.facebook.com/sharer/sharer.php?u=' . esc_url( $slide_link ) . '&title=' . esc_attr( $image_title ) . '"></a>';
								echo '<a class="icon-twitter social-share-link" href="http://twitter.com/home?status=' . esc_attr( $image_title ) . '+' . esc_url( $slide_link ) . '"></a>';
								echo '<a class="icon-google-plus social-share-link" href="https://plus.google.com/share?url=' . esc_url( $slide_link ) . '"></a>';
								echo '</div>';
								echo '</div>';
							}
							$gallery->rewind_posts();
							?>
						</div>
						<div class="gallery__progress">
							<div class="gallery__prev">
								<button type="button" class="gallery__prev--btn slide-overlay-control-nohide"><span class="gallery__prev--span"><?php _e( 'Prev', 'greatermedia'); ?></span></button>
							</div>
							<div id="gallery__count" class="gallery__count">
							</div>
							<div class="gallery__next">
								<button type="button" class="gallery__next--btn slide-overlay-control-nohide"><span class="gallery__next--span"><?php _e( 'Next', 'greatermedia'); ?></span></button>
							</div>
						</div>
					</div>
					<div class="gallery__thumbnails">
						<?php
						$pager_text = sprintf( _x( 'Group %s', 'noun: group number', 'greatermedia' ), '{{slideNum}}' );
						?>
						<div class="gallery__previews cycle-slideshow"
						     data-cycle-log="false"
						     data-slides=".gallery__previews--group"
						     data-cycle-prev=".gallery__paging--prev"
						     data-cycle-next=".gallery__paging--next"
						     data-cycle-pager=".slide-group-pager"
						     data-cycle-pager-template="<button class='btn btn-link indicator'><i class='dot'><?php echo esc_attr( $pager_text ); ?></i></button>"
						     data-cycle-pager-active-class="current"
						     data-cycle-timeout="0"
						     data-cycle-manual-speed="200"
						     data-cycle-swipe="true"
						     data-cycle-fx="scrollHorz"
						     data-cycle-swipe-fx="scrollHorz">

							<div class="gallery__previews--group">
								<?php
								$image_count = 0;
								while ( $gallery->have_posts() ) {
									$gallery->the_post();
									$thumb_url = wp_get_attachment_image_src( get_the_ID(), array( 100, 100 ), false );

									if ( $image_count > 0 && $image_count % $thumbnails_per_page == 0 ) {
										echo '</div><div class="gallery__previews--group">';
									}

									echo '<div id="preview-' . $image_count . '" class="gallery__slide--preview" style="background-image: url(' . esc_url( $thumb_url[0] ) . ');" data-cycle-hash="' . get_post_field( 'post_name', get_the_ID() ) . '" data-cycle-index="' . esc_attr( $image_count ) . '"></div>';
									$image_count++;
								}
								$gallery->rewind_posts();
								?>
							</div>
						</div>
						<div class="gallery__paging gallery__paging--left">
							<div class="slide-paging-arrows carousel-controls">
								<button type="button" class="gallery__paging--prev"></button>
							</div>
						</div>
						<div class="gallery__paging gallery__paging--right">
							<div class="slide-paging-arrows carousel-controls">
								<button type="button" class="gallery__paging--next"></button>
							</div>
						</div>
					</div> <!-- / gallery sidebar -->

					<?php wp_reset_postdata(); ?>
				</div>
			</div><!-- / gallery -->
		<?php
		endif;

		return ob_get_clean();
	}

}

GreaterMediaGallery::init();

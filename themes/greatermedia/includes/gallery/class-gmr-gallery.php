<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaGallery {

	public static function init() {
		add_shortcode( 'gallery', '__return_false' );
		add_action( 'init', array( __CLASS__, 'add_image_sizes' ) );
		add_action( 'gmr_gallery', array( __CLASS__, 'render_gallery' ) );
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
	 * Gets a WP_Query for the attachments in the gallery
	 * @param $post
	 * @return WP_Query
	 */
	public static function get_gallery_loop( $post ) {
		preg_match_all( '/\[gallery.*ids=.(.*).\]/', $post->post_content, $ids );

		$array_ids = array();
		foreach ( $ids[1] as $match ) {
			$array_id = explode( ',', $match );
			$array_id = array_map( 'intval', $array_id );
			$array_ids = array_merge( $array_ids, $array_id );
		}

		$photos = new WP_Query(
			array(
				'ignore_sticky_posts' => true,
				'post__in'            => $array_ids,
				'post_status'         => 'inherit',
				'post_type'           => 'attachment',
				'posts_per_page'      => - 1,
				'orderby'             => 'post__in',
			)
		);

		return $photos;
	}

	/**
	 * Add custom image sizes so WordPress generates images of the appropriate size.
	 */
	public static function add_image_sizes() {
		add_image_size( 'gmr-gallery',               1400, 1400      );
		add_image_size( 'gmr-gallery-thumbnail',     120,  120, true );
	}

	public static function render_gallery() {
		$gallery = self::get_gallery_loop( get_queried_object() );
		if ( $gallery->have_posts() ):
			wp_enqueue_script( 'cycle', get_template_directory_uri() . '/assets/js/vendor/cycle2/jquery.cycle2.min.js', array( 'jquery' ), '2.1.6', true );
			wp_enqueue_script( 'cycle-center', get_template_directory_uri() . '/assets/js/vendor/cycle2/jquery.cycle2.center.min.js', array( 'cycle' ), '20141007', true );
			wp_enqueue_script( 'cycle-swipe', get_template_directory_uri() . '/assets/js/vendor/cycle2/jquery.cycle2.swipe.min.js', array( 'cycle' ), '20141007', true );
			wp_enqueue_script( 'cycle-carousel', get_template_directory_uri() . '/assets/js/vendor/cycle2/jquery.cycle2.carousel.min.js', array( 'cycle' ), '20141007', true );
			wp_register_script( 'gmr-gallery', get_template_directory_uri() . '/assets/js/src/greater_media_gallery.js', array( 'jquery' ), GREATERMEDIA_VERSION, true );
			wp_enqueue_script( 'gmr-gallery' );

			$main_post_id         = get_queried_object_id();
			$main_post_title      = get_the_title( $main_post_id );
			$main_post_short_link = wp_get_shortlink( $main_post_id );

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
						     data-cycle-caption=".gallery__count"
						     data-cycle-caption-template="<?php echo esc_attr( $image_count_text ); ?>"
						     data-cycle-center-horz="true"
						     data-cycle-center-vert="true"
						     data-cycle-manual-speed="200"
						     data-cycle-swipe="true"
						     data-cycle-fx="scrollHorz"
						     data-cycle-swipe-fx="scrollHorz"
						     data-cycle-manual-fx="scrollHoriz">
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
								$image = wp_get_attachment_image_src( get_the_ID(), 'gmr-gallery', false );
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
						<div class="gallery__prev">
							<button type="button" class="gallery__prev--btn slide-overlay-control-nohide"><span class="gallery__prev--span"><?php _e( 'Prev', 'greatermedia'); ?></span></button>
						</div>
						<div class="gallery__content cycle-slideshow"
						     data-cycle-log="false"
						     data-cycle-slides="> div"
						     data-cycle-prev=".gallery__prev--btn"
						     data-cycle-next=".gallery__next--btn"
						     data-cycle-timeout="0"
						     data-cycle-auto-height="false"
						     data-cycle-manual-speed="1">
							<?php
							while ( $gallery->have_posts() ){
								global $post;
								$gallery->the_post();
								$slide_hash = get_post_field( 'post_name', get_the_ID() );
								$image_title = get_the_title( $post ); // title of the gallery image
								echo '<div class="gallery__slide--content" data-cycle-hash="' . $slide_hash . '">';
								echo '<h2 class="gallery__slide--title">';
								echo $image_title;
								echo '</h2>';
								if ( function_exists( 'sharing_display' ) ) {
									echo sharing_display();
								}
								echo '</div>';
							}
							$gallery->rewind_posts();
							?>
						</div>
						<div class="gallery__count">
						</div>
						<div class="gallery__next">
							<button type="button" class="gallery__next--btn slide-overlay-control-nohide"><span class="gallery__next--span"><?php _e( 'Next', 'greatermedia'); ?></span></button>
						</div>
					</div>
					<div class="gallery__thumbnails">
						<?php
						$pager_text = sprintf( _x( 'Group %s', 'noun: group number', 'greatermedia' ), '{{slideNum}}' );
						?>
						<div class="slide-paging-previews cycle-slideshow"
						     data-cycle-log="false"
						     data-slides=".slide-previews-group"
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

							<div class="slide-previews-group">
								<?php
								$image_count = 0;
								while ( $gallery->have_posts() ) {
									$gallery->the_post();
									$thumb_url = wp_get_attachment_image_src( get_the_ID(), 'gmr-gallery-thumbnail' );

									if ( $image_count > 0 && $image_count % $thumbnails_per_page == 0 ) {
										echo '</div><div class="slide-previews-group">';
									}

									echo '<div id="preview-' . $image_count . '" class="gallery__slide--preview" style="background-image: url(' . $thumb_url[0] . ');" data-cycle-hash="' . get_post_field( 'post_name', get_the_ID() ) . '" data-cycle-index="' . $image_count . '"></div>';
									$image_count++;
								}
								$gallery->rewind_posts();
								?>
							</div>
						</div>
						<div class="slide-paging gallery__paging--left">
							<div class="slide-paging-arrows carousel-controls">
								<button type="button" class="gallery__paging--prev prev-group"></button>
							</div>
						</div>
						<div class="slide-paging gallery__paging--right">
							<div class="slide-paging-arrows carousel-controls">
								<button type="button" class="gallery__paging--next next-group"></button>
							</div>
						</div>
					</div> <!-- / gallery sidebar -->

					<?php wp_reset_postdata(); ?>
				</div>
			</div><!-- / gallery -->
		<?php
		endif;
	}

}

GreaterMediaGallery::init();

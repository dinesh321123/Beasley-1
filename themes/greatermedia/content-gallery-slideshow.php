<?php

if ( ! class_exists( 'GreaterMediaGallery' ) ) :
	return;
endif;

$current_gallery = get_queried_object();
$ids = \GreaterMediaGallery::get_attachment_ids_for_post( $current_gallery );
if ( ! is_array( $ids ) ) :
	$ids = array();
endif;

$sponsored_image = get_field( 'sponsored_image', $current_gallery );
if ( ! empty( $sponsored_image ) ) :
	array_unshift( $ids, $sponsored_image );
endif;

$images = array_values( array_filter( array_map( 'get_post', array_values( $ids ) ) ) );
if ( empty( $images ) ) :
	return;
endif;

$ads_interval = filter_var( get_field( 'images_per_ad', $current_gallery ), FILTER_VALIDATE_INT, array( 'options' => array(
	'min_range' => 1,
	'max_range' => 99,
	'default'   => 3,
) ) );

$april15th = strtotime( '2018-04-15' );
$current_image_slug = get_query_var( 'view' );

$slide_index = 0;
$base_url = untrailingslashit( get_permalink( $current_gallery ) );

$galleries = get_posts( array(
	'post_type'      => 'gmr_gallery',
	'post__not_in'   => array( $current_gallery->ID ),
	'posts_per_page' => 4,
) );

add_filter( 'beasley-share-url', function() use ( $images, $current_gallery ) {
	return untrailingslashit( get_permalink( $current_gallery->ID ) ) . '/view/' . urlencode( $images[0]->post_name ) . '/';
} );

?><h1 class="slideshow-title">
	<span class="container">
		<span class="backbutton">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'gmr_gallery' ) ); ?>">View All Galleries</a>
		</span>
		<?php the_title(); ?>
	</span>
</h1>

<div class="gallery">
	<div class="gallery-thumbs loading"><?php
		foreach ( $images as $index => $image ) :
			if ( $index > 0 && $index % $ads_interval == 0 ) :
				?><div><div class="swiper-slide meta-spacer"></div></div><?php
			endif;

			?><div>
				<div class="swiper-slide" style="background-image:url(<?php echo esc_url( bbgi_get_image_url( $image, 75, 75 ) ); ?>)"></div>
			</div><?php
		endforeach;

		// Last slide thumbnail placeholder
		?><div><div class="swiper-slide meta-spacer"></div></div>
	</div>
	<!-- .gallery-thumbs -->

	<div class="swiper-container gallery-top loading" data-refresh-interval="<?php echo esc_attr( $ads_interval ); ?>" data-share-photos="<?php echo get_field( 'share_photos', $current_gallery ) ? 1 : 0; ?>">
		<div class="swiper-wrapper"><?php
			foreach ( $images as $index => $image ) :
				if ( $index > 0 && $index % $ads_interval == 0 ) :
					?><div data-index="<?php echo esc_attr( $slide_index ); ?>" class="swiper-slide meta-spacer">
						<div class="swiper-slide meta-spacer"></div>
					</div><?php
					$slide_index++;
				endif;

				$image_data = wp_get_attachment_image_src( $image->ID, 'original' );
				if ( empty( $image_data ) ) :
					continue;
				endif;

				$aspect = ! empty( $image_data[2] ) ? $image_data[1] / $image_data[2] : 1;
				$attribution = get_post_meta( $image->ID, 'gmr_image_attribution', true );

				?><div class="swiper-slide"
					 data-index="<?php echo esc_attr( $slide_index ); ?>"
					 data-slug="<?php echo esc_attr( $base_url ); ?>/view/<?php echo esc_attr( $image->post_name ); ?>/"
					 data-title="<?php echo get_post_modified_time( 'U', true, $image, false ) > $april15th ? esc_attr( get_the_title( $image ) ) : ''; ?>"
					 data-caption="<?php echo esc_attr( get_the_excerpt( $image ) ); ?>"
					 data-source="<?php echo esc_url( $image_data[0] ); ?>"
					 data-share="<?php echo esc_attr( $base_url ); ?>/view/<?php echo esc_attr( $image->post_name ); ?>/"
					 <?php echo $image->post_name == $current_image_slug ? 'data-initial="true"' : ''; ?>
					 >
					<img class="swiper-image"
						 src="<?php echo esc_url( bbgi_get_image_url( $image, ceil( $aspect * 600 ), 600, 'max', true ) ); ?>"
						 alt="<?php echo esc_attr( get_the_excerpt( $image ) ); ?>"
						 width="<?php echo esc_attr( $aspect * 600 ); ?>"
						 height="600"
						 >

					<?php if ( ! empty( $attribution ) ) : ?>
						<div class="image-attribution"><?php echo esc_html( $attribution ); ?></div>
					<?php endif; ?>
				</div><?php

				$slide_index++;
			endforeach;

			if ( is_singular( 'gmr_gallery' ) ) :
				?><div data-index="<?php echo esc_attr( $slide_index ); ?>" class="swiper-slide last-slide">
					<div class="other-galleries">
						<h2>More from <?php bloginfo( 'name' ); ?></h2>
						<div class="gallery__grid gallery__grid-album">
							<?php foreach ( $galleries as $gallery ) : ?>
								<article class="gallery__grid--column">
									<a href="<?php the_permalink( $gallery ); ?>">
										<div class="gallery__grid--thumbnail">
											<div class="thumbnail" style="background-image: url(<?php bbgi_post_thumbnail_url( $gallery, true, 345, 228 ); ?>)"></div>
										</div>
										<div class="gallery__grid--meta">
											<h3 class="gallery__grid--title">
												<?php echo esc_html( get_the_title( $gallery ) ); ?>
											</h3>
										</div>
									</a>
								</article>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<!-- .swiper-wrapper -->

		<div class="swiper-sidebar">
			<div class="swiper-sidebar-text">
				<h2 class="swiper-sidebar-title">
					<?php echo esc_html( get_the_title( $images[0] ) ); ?>
				</h2>

				<p class="swiper-sidebar-caption">
					<?php 
						$excerpt = get_the_excerpt( $images[0] );
						$trimmed = wp_trim_words( $excerpt, 30, '...' );
						echo esc_attr( $trimmed ); 
					?>
				</p>

				<button class="swiper-sidebar-expand">
					<span class="expand-label">Read more</span>
					<span class="close-label">Read less</span>
					<span class="icon-arrow-next"></span>
				</button>

				<?php if ( ! get_field( 'hide_download_link', $current_gallery ) ) : ?>
					<p>
						<a href="<?php echo esc_url( wp_get_attachment_image_url( $images[0]->ID, 'full' ) ); ?>" class="swiper-sidebar-download" download target="_blank">
							<span class="icon-download-arrow"></span> Download image
						</a>
					</p>
				<?php endif; ?>
			</div>

			<?php if ( ! get_field( 'hide_social_share', $current_gallery ) ) : ?>
				<div class="swiper-sidebar-sharing">
					<?php get_template_part( 'partials/social-share' ); ?>
				</div>
			<?php endif; ?>

			<div class="swiper-sidebar-meta">
				<?php do_action( 'dfp_tag', 'dfp_ad_sidegallery' ); ?>
			</div>

			<button class="swiper-sidebar-fullscreen">
				<svg class="enter-fullscreen" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
					<path d="M4.5 11H3v4h4v-1.5H4.5V11zM3 7h1.5V4.5H7V3H3v4zm10.5 6.5H11V15h4v-4h-1.5v2.5zM11 3v1.5h2.5V7H15V3h-4z"/>
				</svg>

				<svg class="exit-fullscreen" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
					<path d="M3 12.5h2.5V15H7v-4H3v1.5zm2.5-7H3V7h4V3H5.5v2.5zM11 15h1.5v-2.5H15V11h-4v4zm1.5-9.5V3H11v4h4V5.5h-2.5z"/>
				</svg>

				<span class="screen-reader-text">Fullscreen</span>
			</button>
		</div>
		<!-- .swiper-sidebar -->

		<div class="swiper-meta-container">
			<div class="swiper-meta-inner">
				<?php do_action( 'dfp_tag', 'dfp_ad_ingallery' ); ?>
			</div>
		</div>
		<!-- .swiper-meta-container -->
	</div>
	<!-- .gallery-top -->
</div>

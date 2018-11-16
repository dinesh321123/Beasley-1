<?php
/**
 * This runs a check to determine if the post has a thumbnail, and that it's not a gallery or video post format.
 */
if ( has_post_thumbnail() && ! bbgi_post_has_gallery() && ! has_post_format( 'video' ) && ! has_post_format( 'audio' )  ) :
	$width = 512;
	$height = 342;

	if ( \Greater_Media\Flexible_Feature_Images\feature_image_preference_is( get_the_ID(), 'poster' ) ) {
		$width = 970;
		$height = 545;
	}

	?><div class="article__thumbnail">

		<img src="<?php echo esc_url( bbgi_get_image_url( get_post_thumbnail_id(), $width, $height ) ); ?>" alt="">
		<?php bbgi_the_image_attribution(); ?>
	</div><?php
endif;

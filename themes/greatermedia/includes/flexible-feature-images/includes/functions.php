<?php

namespace Greater_Media\Flexible_Feature_Images;

function feature_image_preference_is( $post = null, $feature_image_preference = null )
{
	$post = get_post( $post );

	if ( ! $post ) {
		return;
	}

  $current_feature_image_preference = get_post_meta( $post->ID, 'post_feature_image_preference', true );

  // Match to current meta data.  Default to 'poster' if none already specified in post meta.
  return (bool) ( $current_feature_image_preference ? ( $feature_image_preference === $current_feature_image_preference ) : ( 'poster' === $feature_image_preference ) );
}

?>

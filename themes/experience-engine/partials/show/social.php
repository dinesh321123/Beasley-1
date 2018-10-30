<?php

$show = ee_get_current_show();
if ( ! $show ) :
	return;
endif;

?><div>
	<?php if ( ( $facebook_url = ee_get_show_meta( $show, 'facebook' ) ) ): ?>
		<a href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener noreferrer">Facebook</a>
	<?php endif; ?>

	<?php if ( ( $twitter_url = ee_get_show_meta( $show, 'twitter' ) ) ): ?>
		<a href="<?php echo esc_url( $twitter_url ); ?>" target="_blank" rel="noopener noreferrer">Twitter</a>
	<?php endif; ?>

	<?php if ( ( $instagram_url = ee_get_show_meta( $show, 'instagram' ) ) ): ?>
		<a href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener noreferrer">Instagram</a>
	<?php endif; ?>
</div>

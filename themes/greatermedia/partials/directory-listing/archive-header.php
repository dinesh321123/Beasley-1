<?php

$styles = '';

$image_id = get_option( 'listgin-archive-image' );
if ( ! empty( $image_id ) ) :
	$image_url = wp_get_attachment_image_url( $image_id, 'full' );
	if ( $image_url ) :
		$styles = sprintf( "background-image: url('%s');", $image_url );
	endif;
endif;

?><div class="directory-archive__hero" style="<?php echo esc_attr( $styles ); ?>">
	<div class="directory-archive__title-wrapper">
		<h1 class="directory-archive__title">
			<?php echo esc_html( get_option( 'listing-archive-title' ) ); ?>
		</h1>
	</div>
</div>

<div class="directory-archive__intro">
	<?php echo apply_filters( 'the_content', get_option( 'listgin-archive-description' ) ); ?>
</div>

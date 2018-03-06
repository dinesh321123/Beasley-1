<?php

$directory = get_post( get_query_var( 'directory_id' ) );
if ( ! is_a( $directory, '\WP_Post' ) || ! function_exists( 'get_field' ) ) :
	return;
endif;

$styles = '';

$image_id = get_field( 'hero_image', $directory->ID );
if ( ! empty( $image_id ) ) :
	$image_url = wp_get_attachment_image_url( $image_id, 'full' );
	if ( $image_url ) :
		$styles = sprintf( "background-image: url('%s');", $image_url );
	endif;
endif;

?><div class="directory-archive__hero" style="<?php echo esc_attr( $styles ); ?>">
	<div class="directory-archive__title-wrapper">
		<h1 class="directory-archive__title">
			<?php echo esc_html( get_the_title( $directory ) ); ?>
		</h1>
	</div>
</div>

<div class="directory-archive__intro">
	<?php the_field( 'description', $directory->ID ); ?>
</div>

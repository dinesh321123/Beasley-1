<?php

$show = ee_get_current_show();
if ( ! $show ) :
	return;
endif;

?><div>
	<?php if ( ( $logo = ee_get_show_meta( $show, 'logo' ) ) ) : ?>
		<div style="width: 150px">
			<?php ee_the_lazy_image( $logo ); ?>
		</div>
	<?php endif; ?>

	<div>
		<?php get_template_part( 'partials/show/social' ); ?>

		<h3><?php echo esc_html( get_the_title( $show ) ); ?></h3>
		<?php get_template_part( 'partials/add-to-favorite' ); ?>

		<?php if ( ( $showtime = ee_get_show_meta( $show, 'show-time' ) ) ) : ?>
			<div>
				<?php echo esc_html( $showtime ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>


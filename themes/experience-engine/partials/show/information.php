<?php

$show = ee_get_current_show();
if ( ! $show ) :
	return;
endif;

?><div class="information content-wrap">
	<?php if ( ( $logo = ee_get_show_meta( $show, 'logo' ) ) ) : ?>
		<div class="thumbnail">
			<?php ee_the_lazy_image( $logo ); ?>
		</div>
	<?php endif; ?>
	<div class="meta">
		<h2><?php echo esc_html( get_the_title( $show ) ); ?></h2>
		<?php get_template_part( 'partials/add-to-favorite' ); ?>
		<?php if ( ( $showtime = ee_get_show_meta( $show, 'show-time' ) ) ) : ?>
			<p>
				<?php echo esc_html( $showtime ); ?>
			</p>
		<?php endif; ?>
	</div>
	<?php get_template_part( 'partials/show/social' ); ?>
</div>


<?php

$show = ee_get_current_show();
if ( ! $show ) :
	return;
endif;

?>


<div class="information">
    <div class="information_content">
		
    <?php if ( ( $logo = ee_get_show_meta( $show, 'logo' ) ) ) : ?>
        <!-- <img src="<?php echo wp_get_attachment_image_url( $logo ,array(100,100));?>" /> -->
        <div class="thumbnail">
			<?php ee_the_lazy_image( $logo ); ?>
		</div>
     <?php endif; ?>
        <div class="title_description">
            <h2><?php echo esc_html( get_the_title( $show ) ); ?></h2>
            <div class="meta-fave-time">
				<?php if ( ( $showtime = ee_get_show_meta( $show, 'show-time' ) ) ) : ?>
               
                    <?php echo esc_html( $showtime ); ?>
                
                <?php endif; ?>
            </div>
        </div>
        <?php get_template_part( 'partials/show/social' ); ?>  
    </div>
	  
</div>

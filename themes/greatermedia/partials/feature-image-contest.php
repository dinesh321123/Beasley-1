<?php if ( has_post_thumbnail() ) : ?>

  <div class="contest__thumbnail">
       <img src="<?php gm_post_thumbnail_url( 'gmr-contest-thumbnail' ) ?>" />
       <?php

         $image_attr = image_attribution();

         if ( ! empty( $image_attr ) ) {
           echo $image_attr;
         }

       ?>
  </div>

<?php endif; ?>

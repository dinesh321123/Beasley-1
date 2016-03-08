<?php if ( has_post_thumbnail() ) : ?>

  <div class="event__thumbnail">
       <img class="single__featured-img" src="<?php gm_post_thumbnail_url( 'gmr-event-thumbnail' ) ?>" />
       <?php

         $image_attr = image_attribution();

         if ( ! empty( $image_attr ) ) {
           echo $image_attr;
         }

       ?>
  </div>

<?php endif; ?>

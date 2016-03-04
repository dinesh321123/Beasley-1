<?php

	$advertiser_link = get_post_meta( get_the_ID(), 'advertiser_link', true );

?>

<article class="sponsor cf">
	<?php if ( has_post_thumbnail() ) { ?>
		<div class="sponsor__title"><?php the_title(); ?></div>
		<div class="sponsor__logo"><?php the_post_thumbnail( 'gmr-advertiser' ); ?></div>
		<div class="sponsor_description">
			<div class="sponsor_description-text"><?php the_content(); ?></div>
		</div>
		<div class="sponsor_link"><a href="<?php echo esc_url( $advertiser_link ); ?>" target="_blank" class="contest-attr--rules-toggler">Visit <?php the_title(); ?></a></div>
	<?php } else { ?>

		

		<div class="sponsor__title"><?php the_title(); ?></div>
		<div class="sponsor_description">
			<div class="sponsor_description-text"><?php the_content(); ?></div>
		</div>
		<div class="sponsor_link"><a href="<?php echo esc_url( $advertiser_link ); ?>" target="_blank" class="contest-attr--rules-toggler">Visit <?php the_title(); ?></a></div>




	<?php } ?>
</article>

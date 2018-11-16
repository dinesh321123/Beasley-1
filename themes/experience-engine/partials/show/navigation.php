<?php

$show = ee_get_current_show();
if ( ! $show ) :
	return;
endif;

?><div class="navigation content-wrap"><?php
	if ( \GreaterMedia\Shows\uses_custom_menu( $show->ID ) ) :
		wp_nav_menu( array(
			'menu'      => \GreaterMedia\Shows\assigned_custom_menu_id( $show->ID ),
			'container' => false,
		) );
	else :
		?><ul>
			<?php \GreaterMedia\Shows\home_link_html( $show->ID ); ?>
			<?php \GreaterMedia\Shows\about_link_html( $show->ID ); ?>
			<?php \GreaterMedia\Shows\podcasts_link_html( $show->ID ); ?>
			<?php \GreaterMedia\Shows\galleries_link_html( $show->ID ); ?>
			<?php \GreaterMedia\Shows\videos_link_html( $show->ID ); ?>
		</ul><?php
	endif;
?></div>

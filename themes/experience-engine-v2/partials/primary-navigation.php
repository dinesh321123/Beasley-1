<?php

if ( ! has_nav_menu( 'primary-nav' ) ) :
	return;
endif;

wp_nav_menu( array(
	'theme_location' => 'primary-nav',
	'depth'          => 2,
	'item_spacing'   => 'discard',
	'walker'         => new \PrimaryNavWalker(),
) );

<?php

if ( has_nav_menu( 'primary-nav' ) ) :
	wp_nav_menu( array(
		'theme_location' => 'primary-nav',
		'depth'          => 2,
		'item_spacing'   => 'discard',
		'walker'         => new \PrimaryNavWalker(),
	) );
elseif ( has_nav_menu( 'ee-primary' ) ):
	wp_nav_menu( array(
		'theme_location' => 'ee-primary',
		'depth'          => 2,
		'item_spacing'   => 'discard',
		'walker'         => new \PrimaryNavWalker(),
	) );
endif;


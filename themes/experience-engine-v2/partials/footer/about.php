<div class="about">
	<h6>About</h6><?php

	if ( has_nav_menu( 'about-nav' ) ) :
		wp_nav_menu( array( 'theme_location' => 'about-nav' ) );
	endif;
?></div>

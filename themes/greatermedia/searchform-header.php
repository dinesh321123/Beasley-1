<form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="s"><?php _x( 'Search for:', 'label' ); ?></label>
	<input type="text" value="<?php echo get_search_query(); ?>" name="s" id="header-search" class="header__search--input" placeholder="Search <?php bloginfo( 'name' ); ?>" autocomplete="off">
	<button type="submit" id="searchsubmit" class="header__search--submit"></button>
</form>
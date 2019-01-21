<div class="primary-sidebar">
	<a href="#primary-menu" id="js-menu-toggle" class="site-menu-toggle">
		<span class="screen-reader-text">
			Primary Menu
		</span>
		<svg viewBox="0 0 100 100" width="50" xmlns="http://www.w3.org/2000/svg">
			<path class="line top" d="M30 33h40s9.044-.655 9.044-8.509-8.024-11.958-14.9-10.859C57.27 14.731 50.509 17.804 50.509 30v40"/>
			<path class="line middle" d="M30 50h40"/>
			<path class="line bottom" d="M30 67h40c12.796 0 15.358-11.718 15.358-26.852 0-15.133-4.787-27.274-16.668-27.274-11.88 0-18.499 6.995-18.435 17.126l.253 40"/>
		</svg>
	</a>

	<div class="logo" itemscope itemtype="http://schema.org/Organization">
		<?php ee_the_custom_logo(); ?>
		<span class="screen-reader-text"><?php wp_title(); ?></span>
	</div>

	<div class="nav-wrap" aria-hidden="true">
		<nav id="js-primary-nav" class="primary-nav" role="navigation" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement">
			<?php echo get_search_form(); ?>
			<?php get_template_part( 'partials/primary', 'navigation' ); ?>
		</nav>

		<div id="user-nav" class="user-nav"></div>
	</div>
</div>

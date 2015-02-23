(function(jQuery, window, undefined) {

	var $mobileMenu = jQuery(document.querySelectorAll('ul.js-mobile-sub-menus')),
		$menuOverlay = jQuery(document.querySelector('.menu-overlay-mask'));

	function init() {

		$mobileMenu.on('click.greaterMedia.Menus', 'a.show-subnavigation', openSubMenu);

		$mobileMenu.on('click.greaterMedia.Menus', 'a.mobile-menu-submenu-back-link', closeSubMenu);

		$menuOverlay.on('click', closeSubMenu);

	}

	function closeSubMenu(event) {
		event.preventDefault();
		jQuery(this).parents('.sub-menu').removeClass('is-visible');
	}

	function openSubMenu(event) {
		event.preventDefault();

		// collapse any other open menus before opening ours.
		$mobileMenu.find('.is-visible').removeClass('is-visible');
		jQuery(this).siblings('.sub-menu').addClass('is-visible');
	}

	init();

})(jQuery, window);
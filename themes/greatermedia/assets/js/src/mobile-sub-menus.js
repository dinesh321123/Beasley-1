/**
 * Greater Media
 *
 * Functionality specific to the the mobile menus.
 *
 * Copyright (c) 2014 10up
 * Licensed under the GPLv2+ license.
 */

(function($, window, undefined) {

	/**
	 * Global Variables
	 *
	 * @type {*|HTMLElement}
	 */
	var $mobileMenu = $(document.querySelectorAll('ul.js-mobile-sub-menus')),
		$menuOverlay = $(document.querySelector('.menu-overlay-mask'));

	/**
	 * Init Function
	 */
	function init() {

		$mobileMenu.on('click.greaterMedia.Menus', 'a.show-subnavigation', openSubMenu);

		$mobileMenu.on('click.greaterMedia.Menus', 'a.mobile-menu-submenu-back-link', closeSubMenu);

		$menuOverlay.on('click', closeSubMenu);

	}

	/**
	 * Closes the SubMenu
	 *
	 * @param event
	 */
	function closeSubMenu(event) {
		event.preventDefault();
		$(this).parents('.sub-menu').removeClass('is-visible');
	}

	/**
	 * Opens the SubMenu
	 *
	 * @param event
	 */
	function openSubMenu(event) {
		event.preventDefault();

		// collapse any other open menus before opening ours.
		$mobileMenu.find('.is-visible').removeClass('is-visible');
		$(this).siblings('.sub-menu').addClass('is-visible');
	}

	init();

})(jQuery, window);
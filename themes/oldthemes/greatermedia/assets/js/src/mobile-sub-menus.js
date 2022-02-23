/**
 * Greater Media
 *
 * Functionality specific to the the mobile menus.
 *
 * Copyright (c) 2014 10up
 * Licensed under the GPLv2+ license.
 */

(function($, document, undefined) {

	/**
	 * Global Variables
	 *
	 * @type {*|HTMLElement}
	 */
	var $mobileMenu = $(document.querySelectorAll('ul.js-mobile-sub-menus')),
		$menuOverlay = $(document.querySelector('.menu-overlay-mask'));

	/**
	 * Closes the SubMenu
	 *
	 * @param event
	 */
	function closeSubMenu(event) {
		event.preventDefault();
		$(this).removeClass('is-open').siblings('.sub-menu').removeClass('is-visible');
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
		$mobileMenu.find('.is-open').removeClass('is-open');
		$(this).addClass('is-open').siblings('.sub-menu').addClass('is-visible');
	}

	/**
	 * Init Function
	 */
	function init() {
		$mobileMenu.on('click.greaterMedia.Menus', 'a.show-subnavigation', openSubMenu);
		$mobileMenu.on('click.greaterMedia.Menus', 'a.show-subnavigation.is-open', closeSubMenu);
		$menuOverlay.on('click', closeSubMenu);
	}

	init();

})(jQuery, document);
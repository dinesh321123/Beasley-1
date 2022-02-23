/**
 * Greater Media
 *
 * Headroom.js Settings for Header and Audio Player
 *
 * Copyright (c) 2014 10up
 * Licensed under the GPLv2+ license.
 */
(function( window, document, undefined ) {

	/**
	 * Variables
	 */
	var body = document.querySelector( 'body' ),
		header = document.getElementById( 'header' ),
		header_main = (header) ? header.getElementsByClassName( 'header__main' )[0] : false,
		header_container = (header) ? header.querySelectorAll( '.header__main > .container' )[0] : false;

	if ( !header ){
		return;
	}
	if ( window.matchMedia( '(min-width: 768px)' ).matches ) {

		// Using window.onload because heights we want need to be rendered.
		window.addEventListener( 'load', function( e ) {

			// Set variables after things are loaded
			var header_container_height_full = header_container.offsetHeight,
				offset_height = header_container_height_full,
				header_height = header.offsetHeight;

			// Set header height
			header.style.height = header_height + 'px';

			// Manage the position of the header during headroom.js events
			var headroomManagePosition = function() {
				header_main.style.transform = 'translateY(-' + header_container.offsetHeight + 'px)';
			};

			// Headroom.js
			/* jshint ignore:start */
			var headroom_header = new Headroom( header_main, {
				'offset': offset_height,
				'tolerance': 5,
				onPin: function() {
					header_main.style.transform = 'translateY(0)';
				},
				onUnpin: function() {
					headroomManagePosition();
				},
				onTop: function() {
					header_main.style.transform = 'translateY(0)';
					header_main.classList.remove( 'headroom--pinned' ); // This removes the animation on first pin
				},
				onNotTop: function() {
					headroomManagePosition();
				}
			} );
			headroom_header.init();
			/* jshint ignore:end */

		} );
	} else {

		/* jshint ignore:start */
		var headroom_header = new Headroom( header_main, {
			'offset': 0,
			'tolerance': 5
		} );
		headroom_header.init();
		/* jshint ignore:end */

	}

})( window, document );
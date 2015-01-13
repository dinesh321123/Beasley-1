/**
 * WMGK
 * http://wordpress.org/themes
 *
 * Copyright (c) 2014 10up
 * Licensed under the GPLv2+ license.
 */
 
 ( function( window, undefined ) {
	'use strict';

	 jQuery( function( $ ) {
		$('.popup').on( 'click', function(event) {
			event.preventDefault();
			var x = screen.width/2 - 700/2;
			var y = screen.height/2 - 450/2;
			window.open( $(this).attr('href'), $(this).attr('href'), 'height=485,width=700,scrollbars=yes, resizable=yes,left='+x+ ',top='+y);
		});
	});
	 
 } )( this );
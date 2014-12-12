/**
 * Greater Media Live Player
 * http://wordpress.org/plugins
 *
 * Copyright (c) 2014 10up
 * Licensed under the GPLv2+ license.
 */

(function ($,window,undefined) {
	"use strict";

	// variables
	var document = window.document,
		$document = $(document),
		$window = $(window),
		body = document.querySelectorAll('body'),
		$body = $(body),
		toggleButton = document.querySelectorAll('.gmlp-nav-toggle'),
		$toggleButton = $(toggleButton),
		playButton = $('#playButton'),
		pauseButton = $('#pauseButton');


	playButton.on('click', function(event) {
		event.preventDefault();
		// add gif file for testing
		// call pjax to update container
		if( !gmlp.logged_in ) {
			$(document).pjax('a:not(.ab-item)', 'section.content', {'fragment': 'section.content', 'maxCacheLength': 500, 'timeout' : 5000});
		}

	});

	pauseButton.on('click', function(event) {
		event.preventDefault();
	});

} )(jQuery,window);
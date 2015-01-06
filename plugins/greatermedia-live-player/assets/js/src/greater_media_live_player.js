/**
 * Greater Media Live Player
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


	var enablePjax = function() {
		$(document).pjax('a:not(.ab-item)', 'section.content', {'fragment': 'section.content', 'maxCacheLength': 500, 'timeout' : 5000});
	};

	/**
	 *
	 * Pjax is running against the DOM. By default pjax detects a click event, and this case, we are targeting all `a`
	 * links in the `.main` element. This will run pjax against the first link clicked. After the initial link is
	 * clicked, pjax will stop.
	 *
	 * It is important to call pjax against the `.main` element. Initially we used `.page-wrap` but this caused elements
	 * that had click events attached to them to not function.
	 *
	 * To prevent pjax from stopping, we introduce some pjax `options`.
	 * The `fragment` allows for pjax to continue to detect clicks within the same element, in this case `.main`,
	 * that we initially are calling pjax against. This ensures that pjax continues to run.
	 * `maxCacheLength` is the maximum cache size for the previous container contents.
	 * `timeout` is the ajax timeout in milliseconds after which a full refresh is forced.
	 *
	 * If a user is logged into WordPress, pjax will not work. To resolve this, we run a check that is part of the `else
	 * if` statement that runs a localized variable from the PHP Class `GMLP_Player` in the Greater Media Live player
	 * plugin folder>includes>class-gmlp-player.php. This variable is `gmlp.logged_in` and checks if a user is logged
	 * in with WordPress. If a user is logged in with WordPress, we change the element that pjax is targeting to
	 * `.page-wrap`.
	 *
	 * @summary Detects if a user is authenticated with Gigya, then runs pjax against `a` links in `.page-wrap`
	 *
	 * @event click
	 * @fires pjax
	 *
	 * @see https://github.com/defunkt/jquery-pjax
	 */
	if (is_gigya_user_logged_in()) {
		if ($.support.pjax) {
			$(document).pjax('a:not(.ab-item)', '.main', {'fragment': '.main', 'maxCacheLength': 500, 'timeout' : 5000});
		}
	} else if (gmlp.logged_in) {
		if ($.support.pjax) {
			$(document).pjax('a:not(.ab-item)', '.page-wrap', {'fragment': '.page-wrap', 'maxCacheLength': 500, 'timeout' : 5000});
		}
	}

	playButton.on('click', function(event) {
		event.preventDefault();
		// add gif file for testing
		// call pjax to update container
		if( !gmlp.logged_in ) {
			//enablePjax();
		}
	});

	$('.live-stream').on( 'click', function(event) {
		/* Act on the event */
		if( !is_gigya_user_logged_in() ) {
			Cookies.set( "gmlp_play_button_pushed", 1 );
		}
	});

	pauseButton.on('click', function(event) {
		event.preventDefault();
	});

} )(jQuery,window);
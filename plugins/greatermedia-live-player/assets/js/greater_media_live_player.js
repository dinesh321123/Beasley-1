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
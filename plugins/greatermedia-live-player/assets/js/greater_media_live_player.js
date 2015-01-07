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
		pauseButton = $('#pauseButton'),
		resumeButton = $('#resumeButton'),
		podcastPlay = $('.podcast__btn--play');
	/**
	 * global variables for event types to use in conjunction with `addEventHandler` function
	 * @type {string}
	 */
	var elemClick = 'click',
		elemLoad = 'load',
		elemScroll = 'scroll',
		elemResize = 'resize';

	/**
	 * function to detect if the current browser can use `addEventListener`, if not, use `attachEvent`
	 * this is a specific fix for IE8
	 *
	 * @param elem
	 * @param eventType
	 * @param handler
	 */
	function addEventHandler(elem,eventType,handler) {
		if (elem.addEventListener)
			elem.addEventListener (eventType,handler,false);
		else if (elem.attachEvent)
			elem.attachEvent ('on'+eventType,handler);
	}

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
	function pjaxInit() {
		if (is_gigya_user_logged_in()) {
			if ($.support.pjax) {
				$(document).pjax('a:not(.ab-item)', '.main', {
					url: $(this).attr('href'),
					'fragment': '.main',
					'maxCacheLength': 500,
					'timeout': 5000
				});
			}
		} else if (gmlp.logged_in) {
			if ($.support.pjax) {
				$(document).pjax('a:not(.ab-item)', '.page-wrap', {
					url: $(this).attr('href'),
					'fragment': '.page-wrap',
					'maxCacheLength': 500,
					'timeout': 5000
				});
			}
		}
	}

	function pjaxStop() {
		$(document).on('pjax:click', function(event) {
			event.preventDefault()
		});
	}

	playButton.on('click', function(event) {
		event.preventDefault();
		pjaxInit();
	});

	pauseButton.on('click', function(event) {
		event.preventDefault();
		$('a').on('click', pjaxStop);
	});

	resumeButton.on('click', function(event) {
		event.preventDefault();
		pjaxInit();
	});

	$('.live-stream').on( 'click', function() {
		/* Act on the event */
		if( !is_gigya_user_logged_in() ) {
			Cookies.set( "gmlp_play_button_pushed", 1 );
		}
	});

} )(jQuery,window);
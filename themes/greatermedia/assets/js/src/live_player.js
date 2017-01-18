/**
 * Greater Media
 *
 * Functionality specific to the live player and it's interaction within the theme.
 *
 * Copyright (c) 2014 10up
 * Licensed under the GPLv2+ license.
 */
(function ($, window, document, undefined) {

	/**
	 * Global Variables
	 *
	 * @type {*|HTMLElement}
	 */
	var body = document.querySelector('body');
	var html = document.querySelector('html');
	var siteWrap = document.getElementById('site-wrap');
	var header = document.getElementById('header');
	var livePlayer = document.getElementById('live-player__sidebar');
	var wpAdminHeight = 32;
	var onAir = document.getElementById( 'on-air' );
	var upNext = document.getElementById( 'up-next');
	var nowPlaying = document.getElementById( 'nowPlaying' );
	var liveLinks = document.getElementById( 'live-links' );
	var liveLinksWidget = document.querySelector( '.widget--live-player' );
	var liveLinksWidgetTitle = document.querySelector('.widget--live-player__title');
	var liveLinksMore = document.querySelector('.live-links--more');
	var scrollObject = {};
	var livePlayerMore = document.getElementById('live-player--more');
	var footer = document.querySelector('.footer');
	var livePlayerOpenBtn = document.querySelector('.live-player--open__btn');

	/**
	 * Function to dynamically calculate the offsetHeight of an element
	 *
	 * @param elem
	 * @returns {number}
	 */
	function elemHeight(elem) {
		return elem.offsetHeight;
	}

	/**
	 * Function that will detect if the element is in the visible viewport
	 *
	 * @param elem
	 * @returns {boolean}
	 */
	function elementInViewport(elem) {
		if (elem !== null) {
			var top = elem.offsetTop;
			var left = elem.offsetLeft;
			var width = elem.offsetWidth;
			var height = elem.offsetHeight;

			while (elem.offsetParent) {
				elem = elem.offsetParent;
				top += elem.offsetTop;
				left += elem.offsetLeft;
			}

			return (
			top < (window.pageYOffset + window.innerHeight) &&
			left < (window.pageXOffset + window.innerWidth) &&
			(top + height) > window.pageYOffset &&
			(left + width) > window.pageXOffset
			);
		}
	}

	/**
	 * Function to detect if the current browser can use `addEventListener`, if not, use `attachEvent`
	 * this is a specific fix for IE8
	 *
	 * @param elem
	 * @param eventType
	 * @param handler
	 */
	function addEventHandler(elem, eventType, handler) {
		if (elem.addEventListener)
			elem.addEventListener(eventType, handler, false);
		else if (elem.attachEvent)
			elem.attachEvent('on' + eventType, handler);
	}

	/**
	 * Default height for the live player
	 */
	function lpPosDefault() {
		return; // Disabling code we may not need once the player is in the header or footer.

		if (livePlayer !== null) {
			if (body.classList.contains('logged-in')) {
				livePlayer.style.top = wpAdminHeight + elemHeight(header) + 'px';
			} else {
				livePlayer.style.top = elemHeight(header) + 'px';
			}
		}
	}

	/**
	 * Adds a height to the live player based on the height of the sitewrap element minus the height of the header
	 */
	function lpHeight() {
		return; // Disabling code we may not need once the player is in the header or footer.

		if (livePlayer !== null) {
			livePlayer.style.height = elemHeight(siteWrap) - elemHeight(header) + 'px';
		}
	}

	/**
	 * Adds a height to the live links
	 */
	function liveLinksHeight() {
		var liveLinksBlogRoll = document.getElementById('live-links__blogroll');

		if (liveLinksWidget !== null && liveLinksMore !== null && liveLinksBlogRoll !== null) {
			var liveLinksItem = liveLinksBlogRoll.getElementsByTagName('li');
			if (liveLinksItem.length <= 0) {
				liveLinksMore.classList.add('show-more--muted');
			}
		} else if (liveLinksWidget === null || liveLinksMore === null || liveLinksBlogRoll === null) {
			liveLinksMore.classList.add('show-more--muted');
		}
	}

	/**
	 * Detects various positions of the screen on scroll to deliver states of the live player
	 *
	 * y scroll position === `0`: the live player will be absolute positioned with a top location value based
	 * on the height of the header and the height of the WP Admin bar (if logged in); the height will be adjusted
	 * based on the window height - WP Admin Bar height (if logged in) - header height.
	 *
	 * y scroll position >= `1` and <= the header height: the live player height will be 100% and will still be
	 * positioned absolute as y scroll position === `0` was.
	 *
	 * y scroll position >= the header height: the live player height will be based on the height of the window - WP
	 * Admin bar height (if logged in); the live player will be fixed position at `0` or the height of the WP Admin bar
	 * if logged in.
	 *
	 * All other states will cause the live player to have a height of 100%;.
	 */
	function getScrollPosition() {
		return; // Disabling code we may not need once the player is in the header or footer.

		if (window.innerWidth >= 768) {
			scrollObject = {
				x: window.pageXOffset,
				y: window.pageYOffset
			};

			if (scrollObject.y === 0) {
				if (livePlayer.classList.contains('live-player--fixed')) {
					livePlayer.classList.remove('live-player--fixed');
				}
				lpPosDefault();
			} else if (scrollObject.y >= 1 && elementInViewport(header) && ! elementInViewport(footer)) {
				if (livePlayer.classList.contains('live-player--fixed')) {
					livePlayer.classList.remove('live-player--fixed');
				}
				lpPosDefault();
			} else if (!elementInViewport(header) && ! elementInViewport(footer)) {
				livePlayer.classList.add('live-player--fixed');
				if (livePlayer !== null) {
					livePlayer.style.removeProperty('top');
				}
			}
			lpHeight();
		}
	}

	/**
	 * Adds some styles to the live player that would be called at mobile breakpoints. This is added specifically to
	 * deal with a window being resized.
	 */
	function livePlayerMobileReset() {
		return; // Disabling code we may not need once the player is in the header or footer.

		if (livePlayer !== null) {
			if (livePlayer.classList.contains('live-player--init')) {
				livePlayer.classList.remove('live-player--init');
			}
			if (livePlayer.classList.contains('live-player--fixed')) {
				livePlayer.classList.remove('live-player--fixed');
			}
			liveLinks.style.marginTop = '0px';
			livePlayer.classList.add('live-player--mobile');
		}
	}

	/**
	 * Adds some styles to the live player that would be called at desktop breakpoints. This is added specifically to
	 * deal with a window being resized.
	 */
	function livePlayerDesktopReset() {
		return; // Disabling code we may not need once the player is in the header or footer.

		if (body.classList.contains('live-player--open')) {
			body.classList.remove('live-player--open');
		}
		if (livePlayer.classList.contains('live-player--mobile')) {
			livePlayer.classList.remove('live-player--mobile');
		}
		liveLinksMobileState();
		setTimeout(getScrollPosition, 1000);
		if (window.innerWidth >= 1385 || this.document.documentElement.clientWidth >= 1385 || this.document.body.clientWidth >= 1385) {
			livePlayer.style.right = 'calc(50% - 700px)';
		} else {
			livePlayer.style.right = '0';
		}
	}
	
	/**
	 * Function to handle stream selection through a dropdown
	 */
	function streamSelection() {
		var livePlayerStream = document.querySelector('.live-player__stream'),
			livePlayerStreamSelect = document.querySelector('.live-player__stream--current'),
			livePlayerCurrentName = document.querySelector('.live-player__stream--current-name'),
			livePlayerStreams = document.querySelectorAll('.live-player__stream--item');

		function toggleStreamSelect() {
			if (livePlayerStreamSelect !== null) {
				livePlayerStreamSelect.classList.toggle('open');
			}

			if (livePlayerStream !== null) {
				livePlayerStream.classList.toggle('open');
			}
		}

		if (livePlayerStreamSelect !== null) {
			addEventHandler(livePlayerStreamSelect, 'click', toggleStreamSelect);
		}

		/**
		 * Selects a Live Player Stream
		 */
		function selectStream() {
			var selected_stream = this.querySelector('.live-player__stream--name').textContent;

			if (livePlayerCurrentName !== null) {
				livePlayerCurrentName.textContent = selected_stream;
			}

			document.dispatchEvent(new CustomEvent('live-player-stream-changed', {'detail': selected_stream}));
		}

		if (livePlayerStreams !== null) {
			for (var i = 0; i < livePlayerStreams.length; i++) {
				addEventHandler(livePlayerStreams[i], 'click', selectStream);
			}
		}
	}

	streamSelection();

	/**
	 * Toggles a class to the body when an element is clicked on small screens.
	 */
	function openLivePlayer() {
		if (window.innerWidth <= 767) {
			body.classList.toggle('live-player--open');
			//liveLinksMobileState();
		}
	}

	/**
	 * Sets states needed for the liveplayer on mobile
	 */
	function liveLinksMobileState() {
		if ( $('body').hasClass('live-player--open')) {
			document.body.style.overflow = 'hidden';
			html.style.overflow = 'hidden';
		} else {
			document.body.style.overflow = 'initial';
			html.style.overflow = 'initial';
		}
	}

	/**
	 * Closes the live links
	 */
	function liveLinksClose() {
		if (window.innerWidth <= 767) {
			if (body.classList.contains('live-player--open')) {
				body.classList.remove('live-player--open');
			}
		}
	}

	/**
	 * Resize Window function for when a user scales down their browser window below 767px
	 */
	function resizeWindow() {
		if (window.innerWidth <= 767) {
			if (livePlayer !== null) {
				livePlayerMobileReset();
			}
		} else {
			if (livePlayer !== null) {
				livePlayerDesktopReset();
				addEventHandler(window, 'scroll', function () {
					scrollDebounce();
					scrollThrottle();
				});
			}
		}
	}

	/**
	 * variables that define debounce and throttling for window resizing and scrolling
	 */
	var scrollDebounce = _.debounce(getScrollPosition, 50),
		scrollThrottle = _.throttle(getScrollPosition, 50),
		resizeDebounce = _.debounce(resizeWindow, 50),
		resizeThrottle = _.throttle(resizeWindow, 50);

	/**
	 * functions being run at specific window widths.
	 */
	if (window.innerWidth >= 768) {
		lpPosDefault();
		lpHeight();
		liveLinksHeight();
		addEventHandler(window, 'scroll', function () {
			scrollDebounce();
			scrollThrottle();
		});
	}

	if (onAir !== null) {
		addEventHandler(onAir, 'click', openLivePlayer);
	}
	if (upNext !== null) {
		addEventHandler(upNext, 'click', openLivePlayer);
	}
	if (nowPlaying !== null) {
		addEventHandler(nowPlaying, 'click', openLivePlayer);
	}
	if (livePlayerMore !== null) {
		addEventHandler(livePlayerMore, 'click', openLivePlayer);
	}
	if (liveLinksWidget !== null) {
		addEventHandler(liveLinksWidget, 'click', liveLinksClose);
	}
	if (body.classList.contains('liveplayer-disabled')) {
		addEventHandler(liveLinksWidgetTitle, 'click', openLivePlayer);
		addEventHandler(livePlayerOpenBtn, 'click', openLivePlayer);
	}

	addEventHandler(window, 'resize', function () {
		resizeDebounce();
		resizeThrottle();
	});

})(jQuery, window, document);
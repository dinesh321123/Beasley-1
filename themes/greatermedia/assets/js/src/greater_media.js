/**
 * Greater Media
 *
 * Copyright (c) 2014 10up
 * Licensed under the GPLv2+ license.
 */
(function() {

	var _now, headroom, livePlayerFix, livePlayerInit,

		body = document.querySelector( 'body' ),
		mobileNavButton = document.querySelector( '.mobile-nav__toggle' ),
		header = document.getElementById( 'header' ),
		headerHeight = header.offsetHeight,
		livePlayer = document.getElementById( 'live-player__sidebar' ),
		livePlayerStreamSelect = document.querySelector( '.live-player__stream--current' ),
		livePlayerStreamSelectHeight = livePlayerStreamSelect.offsetHeight,
		wpAdminHeight = 32,
		onAir = document.getElementById( 'on-air' ),
		upNext = document.getElementById( 'up-next'),
		nowPlaying = document.getElementById( 'now-playing' ),
		liveLinks = document.getElementById( 'live-links' ),
		liveLinksWidget = document.querySelector( '.widget--live-player' ),
		liveLinksWidgetHeight = liveLinksWidget.offsetHeight,
		liveStream = document.getElementById( 'live-player' ),
		liveStreamHeight = liveStream.offsetHeight,
		windowHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0),
		scrollObject = {};


	/**
	 * function from underscore.js that detects the current date
	 *
	 * @type {Function|Date.now}
	 * @private
	 */
	_now = Date.now || function () {
		return new Date().getTime();
	};

	/**
	 * function from underscore.js for throttling events
	 *
	 * @param func
	 * @param wait
	 * @param options
	 * @returns {Function}
	 * @private
	 */
	function _throttle(func, wait, options) {
		var context, args, result;
		var timeout = null;
		var previous = 0;
		if (!options) options = {};
		var later = function() {
			previous = options.leading === false ? 0 : _now();
			timeout = null;
			result = func.apply(context, args);
			if (!timeout) context = args = null;
		};
		return function() {
			var now = _now();
			if (!previous && options.leading === false) previous = now;
			var remaining = wait - (now - previous);
			context = this;
			args = arguments;
			if (remaining <= 0 || remaining > wait) {
				clearTimeout(timeout);
				timeout = null;
				previous = now;
				result = func.apply(context, args);
				if (!timeout) context = args = null;
			} else if (!timeout && options.trailing !== false) {
				timeout = setTimeout(later, remaining);
			}
			return result;
		};
	}

	/**
	 * function from underscore.js for debouncing events
	 *
	 * @param func
	 * @param wait
	 * @param immediate
	 * @returns {Function}
	 * @private
	 */
	function _debounce(func, wait, immediate) {
		var timeout, args, context, timestamp, result;

		var later = function() {
			var last = _now() - timestamp;

			if (last < wait && last > 0) {
				timeout = setTimeout(later, wait - last);
			} else {
				timeout = null;
				if (!immediate) {
					result = func.apply(context, args);
					if (!timeout) context = args = null;
				}
			}
		};

		return function() {
			context = this;
			args = arguments;
			timestamp = _now();
			var callNow = immediate && !timeout;
			if (!timeout) timeout = setTimeout(later, wait);
			if (callNow) {
				result = func.apply(context, args);
				context = args = null;
			}

			return result;
		};
	}

	/**
	 * detects various positions of the screen on scroll to deliver states of the live player
	 *
	 * y scroll position === `0`: the live player will be absolute positioned with a top location value based
	 * on the height of the header and the height of the WP Admin bar (if logged in); the height will be adjusted
	 * based on the window height - WP Admin Bar height (if logged in) - header height.
	 * y scroll position >= `1` and <= the header height: the live player height will be 100% and will still be
	 * positioned absolute as y scroll position === `0` was.
	 * y scroll position >= the header height: the live player height will be based on the height of the window - WP
	 * Admin bar height (if logged in); the live player will be fixed position at `0` or the height of the WP Admin bar
	 * if logged in.
	 * all other states will cause the live player to have a height of 100%;.
	 */
	function getScrollPosition() {
		scrollObject = {
			x: window.pageXOffset,
			y: window.pageYOffset
		};

		if( scrollObject.y === 0 ) {
			if ( body.classList.contains( 'logged-in' ) ) {
				livePlayer.style.top = headerHeight + wpAdminHeight + 'px';
				livePlayer.style.height = windowHeight - wpAdminHeight - headerHeight + 'px';
				liveLinks.style.height = windowHeight - headerHeight - wpAdminHeight - livePlayerStreamSelectHeight - liveStreamHeight - 36 + 'px';
			} else {
				livePlayer.style.top = headerHeight + 'px';
				livePlayer.style.height = windowHeight - headerHeight + 'px';
				liveLinks.style.height = windowHeight - headerHeight - livePlayerStreamSelectHeight - liveStreamHeight - 36 + 'px';
			}
			livePlayer.classList.remove( 'live-player--fixed' );
			livePlayer.classList.add( 'live-player--init' );
		} else if ( scrollObject.y >= 1 && scrollObject.y <= headerHeight ){
			if ( body.classList.contains( 'logged-in' ) ) {
				livePlayer.style.top = headerHeight + wpAdminHeight + 'px';
				liveLinks.style.height = windowHeight - wpAdminHeight - livePlayerStreamSelectHeight - liveStreamHeight - 36 + 'px';
			} else {
				livePlayer.style.top = headerHeight + 'px';
				liveLinks.style.height = windowHeight - livePlayerStreamSelectHeight - liveStreamHeight - 36 + 'px';
			}
			livePlayer.style.height = '100%';
			livePlayer.classList.remove( 'live-player--fixed' );
			livePlayer.classList.add( 'live-player--init' );
		} else if ( scrollObject.y >= headerHeight ) {
			if ( body.classList.contains( 'logged-in' ) ) {
				livePlayer.style.top = wpAdminHeight + 'px';
				livePlayer.style.height = windowHeight - wpAdminHeight + 'px';
				liveLinks.style.height = windowHeight - wpAdminHeight - livePlayerStreamSelectHeight - liveStreamHeight - 36 + 'px';
			} else {
				livePlayer.style.top = '0px';
				livePlayer.style.height = windowHeight + 'px';
				liveLinks.style.height = windowHeight - livePlayerStreamSelectHeight - liveStreamHeight - 36 + 'px';
			}
			livePlayer.classList.remove( 'live-player--init' );
			livePlayer.classList.add( 'live-player--fixed' );
		} else {
			livePlayer.style.height = '100%';
		}
	}

	/**
	 * adds a class to the live player that causes it to return to it's original state while also removing the class
	 * that causes the live player to become fixed to the top of the window
	 */
	function livePlayerInit() {
		if ( body.classList.contains( 'logged-in' ) ) {
			livePlayer.style.top = headerHeight + wpAdminHeight + 'px';
			livePlayer.style.height = windowHeight - wpAdminHeight - headerHeight + 'px';
		} else {
			livePlayer.style.top = headerHeight + 'px';
			livePlayer.style.height = windowHeight - headerHeight + 'px';
		}
		livePlayer.classList.remove( 'live-player--fixed' );
		livePlayer.classList.add( 'live-player--init' );
	}

	function liveLinksAddHeight() {
		if ( body.classList.contains( 'logged-in' ) ) {
			liveLinks.style.height = windowHeight - headerHeight - wpAdminHeight - livePlayerStreamSelectHeight - liveStreamHeight - 36 + 'px';
		} else {
			liveLinks.style.height = windowHeight - headerHeight - livePlayerStreamSelectHeight - liveStreamHeight - 36 + 'px';
		}
		liveLinksWidget.style.height = liveLinksWidgetHeight + 'px';
	}

	/**
	 * creates a re-usable variable that will call a button name, element to hide, and element to display
	 *
	 * @param btn
	 * @param elemHide
	 * @param elemDisplay
	 */
	var lpAction = function(btn, elemHide, elemDisplay) {
		this.btn = btn;
		this.elemHide = elemHide;
		this.elemDisplay = elemDisplay;
	};

	/**
	 * this function will create a re-usable function to hide and display elements based on lpAction
	 */
	lpAction.prototype.playAction = function() {
		var that = this; // `this`, when registering an event handler, won't ref the method's parent object, so a var it is
		that.btn.addEventListener( 'click', function() {
			that.elemHide.style.display = 'none';
			that.elemDisplay.style.display = 'inline-block';
		}, false);
	};

	/**
	 * variables used for button interactions on the live player
	 */
	var playLp, pauseLp, resumeLp, playBtn, pauseBtn, resumeBtn, lpListenNow, lpNowPlaying;
	playBtn = document.getElementById('playButton');
	pauseBtn = document.getElementById('pauseButton');
	resumeBtn = document.getElementById('resumeButton');
	lpListenNow = document.getElementById('live-stream__listen-now');
	lpNowPlaying = document.getElementById('live-stream__now-playing');

	/**
	 * creates new method of lpAction with custom btn, element to hide, and element to display
	 *
	 * @type {lpAction}
	 */
	playLp = new lpAction(playBtn, lpListenNow, lpNowPlaying);
	pauseLp = new lpAction(pauseBtn, lpNowPlaying, lpListenNow);
	resumeLp = new lpAction(resumeBtn, lpListenNow, lpNowPlaying);

	/**
	 * Call the actions
	 */
	if ( playBtn != null ) {
		playLp.playAction();
	}
	if ( pauseBtn != null ) {
		pauseLp.playAction();
	}
	if ( resumeBtn != null ) {
		resumeLp.playAction();
	}

	/**
	 * adds headroom.js functionality to the header
	 *
	 * @type {Window.Headroom}
	 *
	 * @todo remove this at a later point if headroom is not required for header interaction
	 */
	headroom = new Headroom( header, {
		offset: headerHeight,
		tolerance : 0,
		classes: {
			"pinned": "header--pinned",
			"unpinned": "header--unpinned"
		},
		onTop : function() {
			livePlayerInit();
		},
		onNotTop : function() {
			livePlayerFix();
		}
	});

	/**
	 * Toggles a class to the body when the mobile nav button is clicked
	 */
	function toggleNavButton() {
		body.classList.toggle( 'mobile-nav--open' );
	}
	mobileNavButton.addEventListener( 'click', toggleNavButton, false );

	/**
	 * Toggles a class to the Live Play Stream Select box when the box is clicked
	 */
	function toggleStreamSelect() {
		livePlayerStreamSelect.classList.toggle( 'open' );
	}
	livePlayerStreamSelect.addEventListener( 'click', toggleStreamSelect, false );

	/**
	 * Toggles a class to the live links when the live player is clicked clicked on smaller screens
	 */
	function onAirClick() {
		body.classList.toggle( 'live-player--open' );
	}

	function upNextClick() {
		body.classList.toggle( 'live-player--open' );
	}

	function nowPlayingClick() {
		body.classList.toggle( 'live-player--open' );
	}

	function resizeWindow() {
		if( window.innerWidth <= 767 ) {
			onAir.addEventListener( 'click', onAirClick, false );
			nowPlaying.addEventListener( 'click', nowPlayingClick, false );
		}
		if ( window.innerWidth >= 768 ) {
			window.addEventListener( 'load', livePlayerInit, false );
		}
	}

	var scrollDebounce = _debounce(getScrollPosition, 50);
	var scrollThrottle = _throttle(getScrollPosition, 50);
	var resizeDebounce = _debounce(resizeWindow, 50);
	var resizeThrottle = _throttle(resizeWindow, 50);

	if( window.innerWidth <= 767 ) {
		onAir.addEventListener( 'click', onAirClick, false );
		upNext.addEventListener( 'click', upNextClick, false );
		nowPlaying.addEventListener( 'click', nowPlayingClick, false );
	}

	if ( window.innerWidth >= 768 ) {
		window.addEventListener( 'load', function() {
			livePlayerInit();
			liveLinksAddHeight();
		}, false );
		window.addEventListener( 'scroll', function() {
			scrollDebounce();
			scrollThrottle();
		}, false );

		window.addEventListener( 'resize', function() {
			resizeDebounce();
			resizeThrottle();
		}, false);
	}

})();
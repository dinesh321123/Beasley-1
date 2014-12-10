(function() {

	var livePlayerFix, livePlayerInit,

		body = document.querySelector( 'body' ),
		html = document.querySelector( 'html'),
		mobileNavButton = document.querySelector( '.mobile-nav__toggle' ),
		header = document.getElementById( 'header' ),
		headerHeight = header.offsetHeight,
		livePlayer = document.getElementById( 'live-player__sidebar' ),
		livePlayerStreamSelect = document.querySelector( '.live-player__stream--current' ),
		livePlayerStreamSelectHeight = livePlayerStreamSelect.offsetHeight,
		wpAdminHeight = 32,
		onAir = document.getElementById( 'on-air' ),
		upNext = document.getElementById( 'up-next'),
		nowPlaying = document.getElementById( 'nowPlaying' ),
		liveLinks = document.getElementById( 'live-links' ),
		liveLink = document.querySelector( '.live-link__title'),
		liveLinksWidget = document.querySelector( '.widget--live-player' ),
		liveLinksWidgetHeight = liveLinksWidget.offsetHeight,
		liveStream = document.getElementById( 'live-player' ),
		liveStreamHeight = liveStream.offsetHeight,
		windowHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0),
		windowWidth = this.innerWidth || this.document.documentElement.clientWidth || this.document.body.clientWidth || 0,
		scrollObject = {};

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
		if (body.classList.contains( 'live-player--open')) {
			document.body.style.overflow = 'hidden';
			html.style.overflow = 'hidden';
		} else {
			document.body.style.overflow = 'auto';
			html.style.overflow = 'auto';
		}
	}

	function upNextClick() {
		body.classList.toggle( 'live-player--open' );
		if (body.classList.contains( 'live-player--open')) {
			body.style.overflow = 'hidden';
			html.style.overflow = 'hidden';
		} else {
			body.style.overflow = 'auto';
			html.style.overflow = 'auto';
		}
	}

	function nowPlayingClick() {
		body.classList.toggle( 'live-player--open' );
		if (body.classList.contains( 'live-player--open')) {
			body.style.overflow = 'auto';
			html.style.overflow = 'auto';
		} else {
			body.style.overflow = 'hidden';
			html.style.overflow = 'hidden';
		}
	}

	function liveLinksClose() {
		if (body.classList.contains( 'live-player--open')) {
			body.classList.remove('live-player--open');
		}
	}

	function resizeWindow() {
		if( window.innerWidth <= 767 ) {
			if(onAir != null) {
				onAir.addEventListener( 'click', onAirClick, false );
			}
			if(upNext != null) {
				upNext.addEventListener( 'click', upNextClick, false );
			}
			if(nowPlaying != null) {
				nowPlaying.addEventListener( 'click', nowPlayingClick, false );
			}
			if(playBtn != null || resumeBtn != null) {
				var playerActive;
				playerActive = function() {
					body.classList.add( 'live-player--active' );
					nowPlaying.style.display = 'block';
					upNext.style.display = 'none';
					onAir.style.display = 'none';
				};
				playBtn.addEventListener( 'click', function() {
					playerActive();
				});
				resumeBtn.addEventListener( 'click', function() {
					playerActive();
				});
			}
			if(pauseBtn != null) {
				pauseBtn.addEventListener( 'click', function() {
					body.classList.remove( 'live-player--active' );
					nowPlaying.style.display = 'none';
					upNext.style.display = 'block';
					onAir.style.display = 'block';
				});
			}
			if(liveLink != null) {
				liveLink.addEventListener('click', liveLinksClose(), false);
			}
		}
		if ( window.innerWidth >= 768 ) {
			window.addEventListener( 'load', livePlayerInit, false );
		}
	}

	var scrollDebounce = _.debounce(getScrollPosition, 50),
		scrollThrottle = _.throttle(getScrollPosition, 50),
		resizeDebounce = _.debounce(resizeWindow, 50),
		resizeThrottle = _.throttle(resizeWindow, 50);

	if( window.innerWidth <= 767 ) {
		if(onAir != null) {
			onAir.addEventListener( 'click', onAirClick, false );
		}
		if(upNext != null) {
			upNext.addEventListener( 'click', upNextClick, false );
		}
		if(nowPlaying != null) {
			nowPlaying.addEventListener( 'click', nowPlayingClick, false );
		}
		if(liveLink != null) {
			liveLink.addEventListener('click', liveLinksClose(), false);
		}
		window.addEventListener( 'resize', function() {
			resizeDebounce();
			resizeThrottle();
		}, false);
	} else {
		window.addEventListener( 'load', function() {
			livePlayerInit();
			if(liveLink != null) {
				liveLinksAddHeight();
			}
		}, false );
		window.addEventListener( 'scroll', function() {
			scrollDebounce();
			scrollThrottle();
		}, false );
	}

})();
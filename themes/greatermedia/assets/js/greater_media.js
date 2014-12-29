(function() {

	/**
	 * global variables
	 *
	 * @type {HTMLElement}
	 */
	var body = document.querySelector( 'body' ),
		html = document.querySelector( 'html'),
		mobileNavButton = document.querySelector( '.mobile-nav__toggle' ),
		pageWrap = document.getElementById( 'page-wrap' ),
		header = document.getElementById( 'header' ),
		headerHeight = header.offsetHeight,
		livePlayer = document.getElementById( 'live-player__sidebar' ),
		livePlayerStreamSelect = document.querySelector( '.live-player__stream--current' ),
		livePlayerStreamSelectHeight = livePlayerStreamSelect.offsetHeight,
		livePlayerCurrentName = livePlayerStreamSelect.querySelector( '.live-player__stream--current-name' ),
		livePlayerStreams = livePlayerStreamSelect.querySelectorAll( '.live-player__stream--item' ),
		wpAdminHeight = 32,
		onAir = document.getElementById( 'on-air' ),
		upNext = document.getElementById( 'up-next'),
		nowPlaying = document.getElementById( 'nowPlaying' ),
		liveLinks = document.getElementById( 'live-links' ),
		liveLink = document.querySelector( '.live-link__title'),
		liveLinksWidget = document.querySelector( '.widget--live-player' ),
		liveStream = document.getElementById( 'live-player' ),
		liveStreamHeight = liveStream.offsetHeight,
		windowHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0),
		windowWidth = this.innerWidth || this.document.documentElement.clientWidth || this.document.body.clientWidth || 0,
		scrollObject = {},
		searchForm = document.getElementById( 'header__search--form'),
		searchBtn = document.getElementById( 'header__search'),
		collapseToggle = document.querySelector('*[data-toggle="collapse"]');

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
		var liveLinksWidgetHeight = liveLinksWidget.offsetHeight;
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
		addEventHandler(that.btn,elemClick,function() {
			that.elemHide.style.display = 'none';
			that.elemDisplay.style.display = 'inline-block';
		});
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
	 * Toggles a class to the body when the mobile nav button is clicked
	 */
	function toggleNavButton() {
		body.classList.toggle( 'mobile-nav--open' );
	}
	addEventHandler(mobileNavButton,elemClick,toggleNavButton);

	/**
	 * Toggles a target element.
	 * 
	 * @param {MouseEvent} e
	 */
	function toggleCollapsedElement(e) {
		var target = document.querySelector(this.getAttribute('data-target')),
			currentText = this.innerText,
			newText = this.getAttribute('data-alt-text');

		e.preventDefault();

		target.style.display = target.style.display != 'none' ? 'none' : 'block';

		this.innerText = newText;
		this.setAttribute('data-alt-text', currentText);
	}
	addEventHandler(collapseToggle, elemClick, toggleCollapsedElement);
	
	/**
	 * Toggles a class to the Live Play Stream Select box when the box is clicked
	 */
	function toggleStreamSelect() {
		livePlayerStreamSelect.classList.toggle( 'open' );
	}
	addEventHandler(livePlayerStreamSelect,elemClick,toggleStreamSelect);
	
	/**
	 * Selects a Live Player Stream
	 */
	function selectStream() {
		var selected_stream = this.querySelector( '.live-player__stream--name' ).textContent;

		livePlayerCurrentName.textContent = selected_stream;
		document.dispatchEvent( new CustomEvent( 'live-player-stream-changed', { 'detail': selected_stream } ) );
	}

	for ( var i = 0; i < livePlayerStreams.length; i++ ) {
		addEventHandler(livePlayerStreams[i],elemClick,selectStream);
	}

	/**
	 * Toggles a class to the live links when the live player `On Air` is clicked on smaller screens
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

	/**
	 * Toggles a class to the live links when the live player `Up Next` is clicked on smaller screens
	 */
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

	/**
	 * Toggles a class to the live links when the live player `Now Playing` is clicked on smaller screens
	 */
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

	/**
	 * Closes the live links
	 */
	function liveLinksClose() {
		if (body.classList.contains( 'live-player--open')) {
			body.classList.remove('live-player--open');
		}
	}

	/**
	 * Resize Window function for when a user scales down their browser window below 767px
	 */
	function resizeWindow() {
		if( window.innerWidth <= 767 ) {
			if(onAir != null) {
				addEventHandler(onAir,elemClick,onAirClick);
			}
			if(upNext != null) {
				addEventHandler(upNext,elemClick,upNextClick);
			}
			if(nowPlaying != null) {
				addEventHandler(nowPlaying,elemClick,nowPlayingClick);
			}
			if(playBtn != null || resumeBtn != null) {
				var playerActive;
				playerActive = function() {
					body.classList.add( 'live-player--active' );
					nowPlaying.style.display = 'block';
					upNext.style.display = 'none';
					onAir.style.display = 'none';
				};
				addEventHandler(playBtn,elemClick,playerActive);
				addEventHandler(resumeBtn,elemClick,playerActive);
			}
			if(pauseBtn != null) {
				addEventHandler(pauseBtn,elemClick,function() {
					body.classList.remove( 'live-player--active' );
					nowPlaying.style.display = 'none';
					upNext.style.display = 'block';
					onAir.style.display = 'block';
				});
			}
			if(liveLinksWidget != null) {
				addEventHandler(liveLinksWidget,elemClick,liveLinksClose);
			}
		}
		if ( window.innerWidth >= 768 ) {
			addEventHandler(window,elemLoad,livePlayerInit);
		}
	}

	/**
	 * A function to show the header search when an event is targeted.
	 *
	 * @param e
	 */
	function showSearch(e) {
		e = e || window.event;
		if (searchForm !== null) {
			searchForm.classList.toggle('header__search--open');
		}
		e.cancelBubble = true;
		if (e.stopPropagation)
			e.stopPropagation();
	}

	/**
	 * A function to hide the header search when an event is targeted.
	 *
	 * @param e
	 */
	function closeSearch(e) {
		e = e || window.event;
		if (searchForm !== null && searchForm.classList.contains('header__search--open')) {
			searchForm.classList.remove('header__search--open');
		}
		e.cancelBubble = true;
		if (e.stopPropagation)
			e.stopPropagation();
	}

	/**
	 * Event listeners to run on click to show and close the search.
	 */
	if (searchBtn !== null) {
		searchBtn.addEventListener('click', showSearch, false);
		pageWrap.addEventListener('click', closeSearch, false);
		/**
		 * An event listener is also in place for the header search form so that when a user clicks inside of it, it will
		 * not hide. This is key because the header search for sits within the element that the click event that closes the
		 * search. If this is event listener is not in place and a user clicks within the search area, it will close.
		 */
		searchForm.addEventListener('click', function(e) {
			e.stopPropagation();
		});
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
	if( window.innerWidth <= 767 ) {
		if(onAir != null) {
			addEventHandler(onAir,elemClick,onAirClick);
		}
		if(upNext != null) {
			addEventHandler(upNext,elemClick,upNextClick);
		}
		if(nowPlaying != null) {
			addEventHandler(nowPlaying,elemClick,nowPlayingClick);
		}
		if(liveLinksWidget != null) {
			addEventHandler(liveLinksWidget,elemClick,liveLinksClose);
		}
		addEventHandler(window,elemResize,function() {
			resizeDebounce();
			resizeThrottle();
		});
	} else {
		addEventHandler(window,elemLoad,function() {
			livePlayerInit();
			if(liveLinksWidget != null) {
				liveLinksAddHeight();
			}
		});
		addEventHandler(window,elemScroll,function() {
			scrollDebounce();
			scrollThrottle();
		});
	}

})();

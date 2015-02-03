/**
 * Greater Media
 *
 * Copyright (c) 2014 10up
 * Licensed under the GPLv2+ license.
 */
(function () {

	/**
	 * global variables
	 *
	 * @type {jQuery}
	 */
	var $ = jQuery;

	var body = document.querySelector('body'),
		html = document.querySelector('html'),
		mobileNavButton = document.querySelector('.mobile-nav__toggle'),
		siteWrap = document.getElementById('site-wrap'),
		pageWrap = document.getElementById('page-wrap'),
		header = document.getElementById('header'),
		livePlayer = document.getElementById('live-player__sidebar'),
		liveStreamContainer = document.querySelector('.live-stream'),
		livePlayerStream = document.querySelector('.live-player__stream'),
		livePlayerStreamSelect = document.querySelector('.live-player__stream--current'),
		livePlayerCurrentName = livePlayerStreamSelect.querySelector('.live-player__stream--current-name'),
		livePlayerStreams = livePlayerStreamSelect.querySelectorAll('.live-player__stream--item'),
		liveLinksMoreBtn = document.querySelector('.live-links--more__btn'),
		liveLinksEnd = document.getElementById('live-links__widget--end'),
		wpAdminBar = document.getElementById('wpadminbar'),
		wpAdminHeight = 32,
		onAir = document.getElementById( 'on-air' ),
		upNext = document.getElementById( 'up-next'),
		nowPlaying = document.getElementById( 'nowPlaying' ),
		liveLinks = document.getElementById( 'live-links' ),
		liveLink = document.querySelector( '.live-link__title'),
		liveLinksWidget = document.querySelector( '.widget--live-player' ),
		liveLinksWidgetTitle = document.querySelector('.widget--live-player__title'),
		liveLinksMore = document.querySelector('.live-links--more'),
		liveStream = document.getElementById( 'live-player' ),
		windowWidth = this.innerWidth || this.document.documentElement.clientWidth || this.document.body.clientWidth || 0,
		windowHeight = this.innerHeight|| this.document.documentElement.clientHeight || this.document.body.clientHeight || 0,
		scrollObject = {},
		collapseToggle = document.querySelector('*[data-toggle="collapse"]'),
		breakingNewsBanner = document.getElementById('breaking-news-banner'),
		$overlay = $('.overlay-mask'),
		livePlayerMore = document.getElementById('live-player--more'),
		mainContent = document.querySelector('.main');

	/**
	 * function to dynamically calculate the offsetHeight of an element
	 *
	 * @param elem
	 * @returns {number}
	 */
	function elemHeight(elem) {
		if (elem != null && elem === header && breakingNewsBanner != null) {
			return elem.offsetHeight + breakingNewsBanner.offsetHeight;
		} else if (elem != null) {
			return elem.offsetHeight;
		}
	}

	function elemTopOffset(elem) {
		if (elem != null) {
			return elem.offsetTop;
		}
	}

	function elemHeightOffset(elem) {
		if (elem != null) {
			return elemHeight(elem) - elemTopOffset(elem);
		}
	}

	function windowHeight(elem) {
		return Math.max(document.documentElement.clientHeight, elem.innerHeight || 0);
	}

	function documentHeight() {
		var html = document.documentElement;

		return Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);
	}

	function elementInViewport(elem) {
		if (elem != null) {
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
	function addEventHandler(elem, eventType, handler) {
		if (elem.addEventListener)
			elem.addEventListener(eventType, handler, false);
		else if (elem.attachEvent)
			elem.attachEvent('on' + eventType, handler);
	}

	/**
	 * function for the initial state of the live player and scroll position one
	 */
	function lpPosBase() {
		if (body.classList.contains('logged-in')) {
			if (livePlayer != null ) {
				livePlayer.style.top = elemHeight(wpAdminBar) + elemHeight(header) + 'px';
				livePlayer.style.height = windowHeight(window) - elemHeight(wpAdminBar) - elemHeight(header) + 'px';
			}
			if (liveLinks != null ) {
				liveLinks.style.height = windowHeight(window) - elemHeight(wpAdminBar) - elemHeight(header) - elemHeight(livePlayerStreamSelect) - elemHeight(liveStream) + 'px';
			}
			if (liveLinksWidget != null ) {
				liveLinksWidget.style.maxHeight = windowHeight(window) - elemHeight(wpAdminBar) - elemHeight(header) - elemHeight(livePlayerStreamSelect) - elemHeight(liveStream) - elemHeight(liveLinksWidgetTitle) - elemHeight(nowPlaying) + 'px';
			}
		} else {
			if (livePlayer != null ) {
				livePlayer.style.top = elemHeight(header) + 'px';
				livePlayer.style.height = windowHeight(window) - elemHeight(header) + 'px';
			}
			if (liveLinks != null ) {
				liveLinks.style.height = windowHeight(window) - elemHeight(header) - elemHeight(livePlayerStreamSelect) - elemHeight(liveStream) + 'px';
			}
			if (liveLinksWidget != null ) {
				liveLinksWidget.style.maxHeight = windowHeight(window) - elemHeight(header) - elemHeight(livePlayerStreamSelect) - elemHeight(liveStream) - elemHeight(liveLinksWidgetTitle) - elemHeight(nowPlaying) + 'px';
			}
		}
		if (livePlayer != null ) {
			livePlayer.classList.remove('live-player--fixed');
			livePlayer.classList.add('live-player--init');
		}
	}

	/**
	 * function for the live player when a user starts scrolling and the header is not in view
	 */
	function lpPosScrollInit() {
		if (body.classList.contains('logged-in')) {
			if (livePlayer != null ) {
				livePlayer.style.top = elemHeight(wpAdminBar) + elemHeight(header) + 'px';
				livePlayer.style.height = windowHeight(window) - elemHeight(wpAdminBar) + 'px';
			}
			if (liveLinks != null ) {
				liveLinks.style.height = windowHeight(window) - elemTopOffset(header) - elemHeight(livePlayerStreamSelect) - elemHeight(liveStream) + 'px';
			}
			if (liveLinksWidget != null ) {
				liveLinksWidget.style.maxHeight = windowHeight(window) - elemTopOffset(header) - elemHeight(livePlayerStreamSelect) - elemHeight(liveStream) - elemHeight(liveLinksWidgetTitle) - elemHeight(nowPlaying) + 'px';
			}
		} else {
			if (livePlayer != null ) {
				livePlayer.style.top = elemHeight(header) + 'px';
				livePlayer.style.height = windowHeight(window) - elemHeight(wpAdminBar) - elemHeight(header) + 'px';
			}
			if (liveLinks != null ) {
				liveLinks.style.height = windowHeight(window) - elemHeightOffset(header) - elemHeight(livePlayerStreamSelect) - elemHeight(liveStream) + 'px';
			}
			if (liveLinksWidget != null ) {
				liveLinksWidget.style.maxHeight = windowHeight(window) - elemHeightOffset(header) - elemHeight(livePlayerStreamSelect) - elemHeight(liveStream) - elemHeight(liveLinksWidgetTitle) - elemHeight(nowPlaying) + 'px';
			}
		}
		if (livePlayer != null ) {
			livePlayer.classList.remove('live-player--fixed');
			livePlayer.classList.add('live-player--init');
		}
	}

	/**
	 * function for the live player when the header is no longer in view
	 */
	function lpPosNoHeader() {
		if (body.classList.contains('logged-in')) {
			if (livePlayer != null ) {
				livePlayer.style.top = elemHeight(wpAdminBar) + 'px';
				livePlayer.style.height = windowHeight(window) - elemHeight(wpAdminBar) + 'px';
			}
			if (liveLinks != null ) {
				liveLinks.style.height = windowHeight(window) - elemHeight(wpAdminBar) - elemHeight(livePlayerStreamSelect) - elemHeight(liveStream) + 'px';
			}
			if (liveLinksWidget != null ) {
				liveLinksWidget.style.maxHeight = windowHeight(window) - elemHeight(wpAdminBar) - elemHeight(livePlayerStreamSelect) - elemHeight(liveStream) - elemHeight(liveLinksWidgetTitle) - elemHeight(nowPlaying) + 'px';
			}
		} else {
			if (livePlayer != null ) {
				livePlayer.style.top = '0px';
				livePlayer.style.height = windowHeight(window) + 'px';
			}
			if (liveLinks != null ) {
				liveLinks.style.height = windowHeight(window) - elemHeight(livePlayerStreamSelect) - elemHeight(liveStream) + 'px';
			}
			if (liveLinksWidget != null ) {
				liveLinksWidget.style.maxHeight = windowHeight(window) - elemHeight(livePlayerStreamSelect) - elemHeight(liveStream) - elemHeight(liveLinksWidgetTitle) - elemHeight(nowPlaying) + 'px';
			}
		}
		if (livePlayer != null ) {
			livePlayer.classList.remove('live-player--init');
			livePlayer.classList.add('live-player--fixed');
		}
	}

	/**
	 * default height for the live player
	 */
	function lpPosDefault() {
		if (livePlayer != null) {
			if (body.classList.contains('logged-in')) {
				livePlayer.style.top = elemHeight(wpAdminBar) + elemHeight(header) + 'px';
			} else {
				livePlayer.style.top = elemHeight(header) + 'px';
			}
		}

	}

	function lpHeight() {
		if (livePlayer != null) {
			livePlayer.style.height = elemHeight(siteWrap) - elemHeight(header) + 'px';
		}
	}

	function liveLinksHeight() {
		if (liveLinks != null) {
			liveLinks.style.height = windowHeight - elemHeight(header) - elemHeight(liveStreamContainer) + 'px';
		}
	}

	var liveLinksIn = new Waypoint.Inview({
		element: document.getElementById('live-links__widget--end'),
		entered: function(direction) {
			if (direction === 'down') {
				console.log('--- New Waypoint on Entry ---');
				liveLinksMore.classList.remove('show-more');
			}
		}
	});

	var liveLinksOut = new Waypoint.Inview({
		element: document.getElementById('live-links__widget--end'),
		exited: function(direction) {
			if (direction === 'up') {
				console.log('--- New Waypoint on Exit---');
				liveLinksMore.classList.add('show-more');
			}
		}
	});



	function liveLinksReadMore() {
		if (liveLinksWidget != null && elemHeight(liveLinksWidget) >= windowHeight) {
			liveLinksMore.classList.add('show-more');
			liveLinksIn();
			liveLinksOut();
		}
	}

	function liveLinksScroll() {
		var start = liveLinks.scrollTop,
			to = windowHeight - elemHeight(header) - elemHeight(liveStreamContainer),
			change = to - start,
			currentTime = 0,
			increment = 20,
			duration = 500;

		var animateScroll = function(){
			currentTime += increment;
			var val = Math.easeInOutQuad(currentTime, start, change, duration);
			liveLinks.scrollTop = val;
			if(currentTime < duration) {
				setTimeout(animateScroll, increment);
			}
		};
		animateScroll();
	}

	/**
	 * Ease in and Out animation
	 *
	 * @param t = current time
	 * @param b = start value
	 * @param c = change in value
	 * @param d = duration
	 * @returns {*}
	 */
	Math.easeInOutQuad = function (t, b, c, d) {
		t /= d/2;
		if (t < 1) return c/2*t*t + b;
		t--;
		return -c/2 * (t*(t-2) - 1) + b;
	};


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
     * from Js Window resize script is not neccessary on popupPlayer window
     */
    if( document.getElementById( 'popup-player-livestream' ) ){
        return;
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
		if (window.innerWidth >= 768) {
			scrollObject = {
				x: window.pageXOffset,
				y: window.pageYOffset
			};

			if (scrollObject.y == 0) {
				if (liveStreamContainer.classList.contains('live-stream--fixed')) {
					liveStreamContainer.classList.remove('live-stream--fixed');
				}

			} else if (scrollObject.y >= 1 && elementInViewport(header)) {
				if (liveStreamContainer.classList.contains('live-stream--fixed')) {
					liveStreamContainer.classList.remove('live-stream--fixed');
				}
				if(liveLinks != null) {
					liveLinks.style.marginTop = '0px';
				}
			} else if (!elementInViewport(header)) {
				liveStreamContainer.classList.add('live-stream--fixed');
				if(liveLinks != null) {
					liveLinks.style.marginTop = elemHeight(liveStreamContainer) + 'px';
				}
			} else {
				if (liveStreamContainer.classList.contains('live-stream--fixed')) {
					liveStreamContainer.classList.remove('live-stream--fixed');
				}
			}
			lpHeight();
			liveLinksReadMore();
		}
	}

	/**
	 * adds some styles to the live player that would be called at mobile breakpoints. This is added specifically to
	 * deal with a window being resized.
	 */
	function livePlayerMobileReset() {
		if (livePlayer != null) {
			if (livePlayer.classList.contains('live-player--init')) {
				livePlayer.classList.remove('live-player--init');
			}
			if (livePlayer.classList.contains('live-player--fixed')) {
				livePlayer.classList.remove('live-player--fixed');
			}
			livePlayer.classList.add('live-player--mobile');
		}
	}

	/**
	 * adds some styles to the live player that would be called at desktop breakpoints. This is added specifically to
	 * deal with a window being resized.
	 */
	function livePlayerDesktopReset() {
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
	 * creates a re-usable variable that will call a button name, element to hide, and element to display
	 *
	 * @param btn
	 * @param elemHide
	 * @param elemDisplay
	 */
	var lpAction = function (btn, elemHide, elemDisplay) {
		this.btn = btn;
		this.elemHide = elemHide;
		this.elemDisplay = elemDisplay;
	};

	/**
	 * this function will create a re-usable function to hide and display elements based on lpAction
	 */
	lpAction.prototype.playAction = function () {
		var that = this; // `this`, when registering an event handler, won't ref the method's parent object, so a var it is
		addEventHandler(that.btn, elemClick, function () {
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
	resumeLp = new lpAction(resumeBtn, lpListenNow, lpNowPlaying);

	/**
	 * Toggles a class to the body when the mobile nav button is clicked
	 */
	function toggleNavButton() {
		body.classList.toggle('mobile-nav--open');
	}

	addEventHandler(mobileNavButton, elemClick, toggleNavButton);

	/**
	 * Toggles a target element.
	 *
	 * @param {MouseEvent} e
	 */
	function toggleCollapsedElement(e) {
		var target = $($(this).attr('data-target')).get(0),
			currentText = $(this).html(),
			newText = $(this).attr('data-alt-text');

		e.preventDefault();

		target.style.display = target.style.display != 'none' ? 'none' : 'block';

		$(this).html(newText);
		$(this).attr('data-alt-text', currentText);
	}

	if (collapseToggle != null) {
		$(collapseToggle ).click(toggleCollapsedElement);
	}

	/**
	 * Toggles a class to the Live Play Stream Select box when the box is clicked
	 */
	function toggleStreamSelect() {
		livePlayerStreamSelect.classList.toggle('open');
		livePlayerStream.classList.toggle('open');
	}

	addEventHandler(livePlayerStreamSelect, elemClick, toggleStreamSelect);

	/**
	 * Selects a Live Player Stream
	 */
	function selectStream() {
		var selected_stream = this.querySelector('.live-player__stream--name').textContent;

		livePlayerCurrentName.textContent = selected_stream;
		document.dispatchEvent(new CustomEvent('live-player-stream-changed', {'detail': selected_stream}));
	}

	for (var i = 0; i < livePlayerStreams.length; i++) {
		addEventHandler(livePlayerStreams[i], elemClick, selectStream);
	}

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
	 * Toggles a class to the body when an element is clicked on small screens.
	 */
	function openLivePlayer() {
		if (window.innerWidth <= 767) {
			body.classList.toggle('live-player--open');
			//liveLinksMobileState();
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
			//liveLinksMobileState();
		}
	}

	function playerActive() {
		body.classList.add('live-player--active');
	}

	function playerNotActive() {
		body.classList.remove('live-player--active');
	}

	/**
	 * Resize Window function for when a user scales down their browser window below 767px
	 */
	function resizeWindow() {
		if (window.innerWidth <= 767) {
			if (livePlayer != null) {
				liveLinksReadMore();
				livePlayerMobileReset();
			}
		} else {
			if (livePlayer != null) {
				livePlayerDesktopReset();
				lpPosDefault();
				liveLinksReadMore();
				addEventHandler(window, elemScroll, function () {
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
		liveLinksReadMore();
		addEventHandler(window, elemScroll, function () {
			scrollDebounce();
			scrollThrottle();
		});
	}


	if (onAir != null) {
		addEventHandler(onAir, elemClick, openLivePlayer);
	}
	if (upNext != null) {
		addEventHandler(upNext, elemClick, openLivePlayer);
	}
	if (nowPlaying != null) {
		addEventHandler(nowPlaying, elemClick, openLivePlayer);
	}
	if (livePlayerMore != null) {
		addEventHandler(livePlayerMore, 'click', openLivePlayer);
	}
	if (liveLinksWidget != null) {
		addEventHandler(liveLinksWidget, elemClick, liveLinksClose);
	}
	if (playBtn != null || resumeBtn != null) {
		addEventHandler(resumeBtn, elemClick, playerActive);
	}
	if (pauseBtn != null) {
		addEventHandler(pauseBtn, elemClick, playerNotActive);
	}

	addEventHandler(window, elemResize, function () {
		resizeDebounce();
		resizeThrottle();
	});

	liveLinksReadMore();

	function init_menu_overlay() {
		var $menu = jQuery(document.querySelector('.header__nav--list')),
			$secondary = jQuery(document.querySelector('.header__secondary')),
			$overlay = jQuery(document.querySelector('.menu-overlay-mask'));

		$menu.on('mouseover', '.menu-item-has-children, .header__account--small', function (e) {
			$overlay.addClass('is-visible');
		});
		$menu.on('mouseout', '.menu-item-has-children, .header__account--small', function (e) {
			$overlay.removeClass('is-visible');
		});

		$secondary.on('mouseover', '.header__account--small, .header__account--large.logged-in', function (e) {
			$overlay.addClass('is-visible');
		});
		$secondary.on('mouseout', '.header__account--small, .header__account--large.logged-in', function (e) {
			$overlay.removeClass('is-visible');
		});
	}

	init_menu_overlay();

	(function ($) {
		$(document).on('click', '.popup', function () {
			var href = $(this).attr('href'),
				x = screen.width / 2 - 700 / 2,
				y = screen.height / 2 - 450 / 2;
			
			window.open(href, href, 'height=485,width=700,scrollbars=yes,resizable=yes,left=' + x + ',top=' + y);

			return false;
		});
		$(document).ready(function() {
			$('.article__content').fitVids({customSelector: "div[id^='playerwrapper']"});
		});
	})(jQuery);

	function personality_toggle() {
		var $button = jQuery('.person-toggle');
		start = jQuery('.personality__meta').first().height(); // get the height of the meta before we start, basically tells us whether we're using the mobile or desktop height

		$button.on('click', function (e) {
			var $this = $(this);
			$parent = $this.parent().parent('.personality');
			$meta = $this.siblings('.personality__meta');
			curr = $meta.height();
			auto = $meta.css('height', 'auto').height(),
				offset = '';

			$parent.toggleClass('open');
			// if( $parent.hasClass('open') ) {
			// 	$meta.height(curr).animate({height: auto * 0.69}, 1000); // the 0.69 adjusts for the difference in height due to the overflow: visible wrapping the text
			// } else {
			// 	$meta.height(curr).animate({height: start}, 1000);
			// }


			if ($this.hasClass('active')) {
				$this.text('More');
			} else {
				$this.text('Less');
			}
			$this.toggleClass('active');
		});
	}

	$(document).bind( 'pjax:end', function () {
		personality_toggle();
	});

})();

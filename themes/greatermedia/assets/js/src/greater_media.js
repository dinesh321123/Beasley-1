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
		breakingNewsBanner = document.getElementById('breaking-news-banner'),
		$overlay = $('.overlay-mask'),
		livePlayerMore = document.getElementById('live-player--more'),
		mainContent = document.querySelector('.main'),
		footer = document.querySelector('.footer'),
		$table = $('table'),
		$tableTd = $('table td');

	/**
	 * function to dynamically calculate the offsetHeight of an element
	 *
	 * @param elem
	 * @returns {number}
	 */
	function elemHeight(elem) {
		return elem.offsetHeight;
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
	 * default height for the live player
	 */
	function lpPosDefault() {
		if (livePlayer != null) {
			if (body.classList.contains('logged-in')) {
				livePlayer.style.top = wpAdminHeight + elemHeight(header) + 'px';
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
		var liveLinksBlogRoll = document.getElementById('live-links__blogroll');
		if (liveLinksBlogRoll != null) {
			var liveLinksItem = liveLinksBlogRoll.getElementsByTagName('li');
		}

		if(liveLinksWidget != null & liveLinksMore != null && liveLinksItem.length <= 1) {
			liveLinksMore.classList.add('show-more--muted');
		}
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
				if (livePlayer != null) {
					livePlayer.style.removeProperty('top');
				}
			}
			lpHeight();
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
			liveLinks.style.marginTop = '0px';
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

	function mobileOpenLocation() {
		var y = window.pageYOffset;

		siteWrap.style.top = '-' + y + 'px';
	}

	function mobileCloseLocation() {
		var y = window.pageYOffset;

		siteWrap.style.removeProperty('top');
	}

	function toggleNavButton() {
		body.classList.toggle('mobile-nav--open');

		if ($('.mobile-nav--open').length) {
			showBlocker();
			mobileOpenLocation();
		} else {
			hideBlocker();
			mobileCloseLocation();
		}
	}

	addEventHandler(mobileNavButton, elemClick, toggleNavButton);

	/**
	 * Function to handle stream selection through a dropdown
	 */
	function streamSelection() {
		var livePlayerStream = document.querySelector('.live-player__stream'),
			livePlayerStreamSelect = document.querySelector('.live-player__stream--current'),
			livePlayerCurrentName = document.querySelector('.live-player__stream--current-name'),
			livePlayerStreams = document.querySelectorAll('.live-player__stream--item');

		function toggleStreamSelect() {
			if (livePlayerStreamSelect != null) {
				livePlayerStreamSelect.classList.toggle('open');
			}

			if (livePlayerStream !== null) {
				livePlayerStream.classList.toggle('open');
			}
		}

		if (livePlayerStreamSelect !== null) {
			addEventHandler(livePlayerStreamSelect, elemClick, toggleStreamSelect);
		}

		/**
		 * Selects a Live Player Stream
		 */
		function selectStream() {
			var selected_stream = this.querySelector('.live-player__stream--name').textContent;

			if (livePlayerCurrentName != null) {
				livePlayerCurrentName.textContent = selected_stream;
			}

			document.dispatchEvent(new CustomEvent('live-player-stream-changed', {'detail': selected_stream}));
		}

		if (livePlayerStreams !== null) {
			for (var i = 0; i < livePlayerStreams.length; i++) {
				addEventHandler(livePlayerStreams[i], elemClick, selectStream);
			}
		}
	}

	streamSelection();

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
	 * Adds a class to a HTML table to make the table responsive
	 */
	function responsiveTables() {
		$table.addClass('responsive');
		$tableTd.removeAttr('width');
	}

	responsiveTables();

	function addGigyaBodyClass() {
		if (! body.classList.contains('gmr-user')) {
			body.classList.add('gmr-user');
		}
	}

	if (is_gigya_user_logged_in()) {
		addGigyaBodyClass();
	}

	/**
	 * Resize Window function for when a user scales down their browser window below 767px
	 */
	function resizeWindow() {
		if (window.innerWidth <= 767) {
			if (livePlayer != null) {
				livePlayerMobileReset();
			}
		} else {
			if (livePlayer != null) {
				livePlayerDesktopReset();
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
		liveLinksHeight();
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

	addEventHandler(window, elemResize, function () {
		resizeDebounce();
		resizeThrottle();
	});

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

	function addHoverMobile() {
		$('.header__nav ul li').on('click touchstart', function() {
			$(this).addClass('active');
		});
	}

	addHoverMobile();

	function removeHoverMobile() {
		$('.header__nav ul li').removeClass('active');
	}

	function removeoverlay() {
		var $overlay = jQuery(document.querySelector('.menu-overlay-mask'));

		$overlay.removeClass('is-visible');
	}

	function addMenuHover() {
		$('.header__nav ul li').hover(
			function () {
				$(this).addClass('active');
			},
			function () {
				$(this).removeClass('active');
			}
		);
	}

	addMenuHover();

	(function ($) {
		$(document).on('click', '.popup', function () {
			var href = $(this).attr('href'),
				x = screen.width / 2 - 700 / 2,
				y = screen.height / 2 - 450 / 2;

			window.open(href, href, 'height=485,width=700,scrollbars=yes,resizable=yes,left=' + x + ',top=' + y);

			return false;
		});

		/**
		 * Toggles a target element.
		 * @param {MouseEvent} e
		 */
		$(document).on('click', '*[data-toggle="collapse"]', function(e) {
			var target = $($(this).attr('data-target')).get(0),
				currentText = $(this).html(),
				newText = $(this).attr('data-alt-text');

			target.style.display = target.style.display != 'none' ? 'none' : 'block';

			$(this).html(newText);
			$(this).attr('data-alt-text', currentText);

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
		hideBlocker();
		removeHoverMobile();
		removeoverlay();
	});

	var getBlockerDiv = function() {
		var $div = $('#mobile-nav-blocker');
		if ($div.length === 0) {
			$('<div id="mobile-nav-blocker"></div>').insertAfter('#mobile-nav');
			$div = $('#mobile-nav-blocker');
			$div.on('click', toggleNavButton);
		}

		return $div;
	};

	var showBlocker = function() {
		var $blocker = getBlockerDiv();

		$blocker.css({
			width: $(document).width(),
			height: $(document).height(),
			display: 'block',
		});
	};

	var hideBlocker = function() {
		var $blocker = getBlockerDiv();
		$blocker.css({'display': 'none'});
		if ($blocker.hasClass('active')) {
			$blocker.removeClass('active');
		}
	};

	/**
	 * Returns user agents for mobile devices. We need to be able to detect common mobile devices in order to remove
	 * the double tap click issue that appears, specifically in iOS. If we do not detect the agent, this will stay
	 * active in the menu at all times and breaks Pjax.
	 *
	 * @type {{TOUCH_DOWN_EVENT_NAME: string, TOUCH_UP_EVENT_NAME: string, TOUCH_MOVE_EVENT_NAME: string, TOUCH_DOUBLE_TAB_EVENT_NAME: string, isAndroid: Function, isBlackBerry: Function, isIOS: Function, isOpera: Function, isWindows: Function, isMobile: Function}}
	 */
	var Environment = {
		//mobile or desktop compatible event name, to be used with '.on' function
		TOUCH_DOWN_EVENT_NAME: 'mousedown touchstart',
		TOUCH_UP_EVENT_NAME: 'mouseup touchend',
		TOUCH_MOVE_EVENT_NAME: 'mousemove touchmove',
		TOUCH_DOUBLE_TAB_EVENT_NAME: 'dblclick dbltap',

		isAndroid: function() {
			return navigator.userAgent.match(/Android/i);
		},
		isBlackBerry: function() {
			return navigator.userAgent.match(/BlackBerry/i);
		},
		isIOS: function() {
			return navigator.userAgent.match(/iPhone|iPad|iPod/i);
		},
		isOpera: function() {
			return navigator.userAgent.match(/Opera Mini/i);
		},
		isWindows: function() {
			return navigator.userAgent.match(/IEMobile/i);
		},
		isMobile: function() {
			return (Environment.isAndroid() || Environment.isBlackBerry() || Environment.isIOS() || Environment.isOpera() || Environment.isWindows());
		}
	};

	/**
	 * Resolves issue that requires a double click on a sub-menu link on iOS.
	 */
	function stopClickMobile() {
		$('.sub-menu li a').on('click touchend', function (e) {
			var el = $(this);
			var link = el.attr('href');
			window.location = link;
		});
	}

	$(document).ready(function() {
		//showBlocker();

		personality_toggle();

	});

})();

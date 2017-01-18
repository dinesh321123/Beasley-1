(function(t){"use strict";function e(t,e,r){return t.addEventListener?t.addEventListener(e,r,!1):t.attachEvent?t.attachEvent("on"+e,r):void 0}function r(t,e){var r,n;for(r=0,n=t.length;n>r;r++)if(t[r]===e)return!0;return!1}function n(t,e){var r;t.createTextRange?(r=t.createTextRange(),r.move("character",e),r.select()):t.selectionStart&&(t.focus(),t.setSelectionRange(e,e))}function a(t,e){try{return t.type=e,!0}catch(r){return!1}}t.Placeholders={Utils:{addEventListener:e,inArray:r,moveCaret:n,changeType:a}}})(this),function(t){"use strict";function e(){}function r(){try{return document.activeElement}catch(t){}}function n(t,e){var r,n,a=!!e&&t.value!==e,u=t.value===t.getAttribute(V);return(a||u)&&"true"===t.getAttribute(D)?(t.removeAttribute(D),t.value=t.value.replace(t.getAttribute(V),""),t.className=t.className.replace(R,""),n=t.getAttribute(F),parseInt(n,10)>=0&&(t.setAttribute("maxLength",n),t.removeAttribute(F)),r=t.getAttribute(P),r&&(t.type=r),!0):!1}function a(t){var e,r,n=t.getAttribute(V);return""===t.value&&n?(t.setAttribute(D,"true"),t.value=n,t.className+=" "+I,r=t.getAttribute(F),r||(t.setAttribute(F,t.maxLength),t.removeAttribute("maxLength")),e=t.getAttribute(P),e?t.type="text":"password"===t.type&&M.changeType(t,"text")&&t.setAttribute(P,"password"),!0):!1}function u(t,e){var r,n,a,u,i,l,o;if(t&&t.getAttribute(V))e(t);else for(a=t?t.getElementsByTagName("input"):b,u=t?t.getElementsByTagName("textarea"):f,r=a?a.length:0,n=u?u.length:0,o=0,l=r+n;l>o;o++)i=r>o?a[o]:u[o-r],e(i)}function i(t){u(t,n)}function l(t){u(t,a)}function o(t){return function(){m&&t.value===t.getAttribute(V)&&"true"===t.getAttribute(D)?M.moveCaret(t,0):n(t)}}function c(t){return function(){a(t)}}function s(t){return function(e){return A=t.value,"true"===t.getAttribute(D)&&A===t.getAttribute(V)&&M.inArray(C,e.keyCode)?(e.preventDefault&&e.preventDefault(),!1):void 0}}function d(t){return function(){n(t,A),""===t.value&&(t.blur(),M.moveCaret(t,0))}}function g(t){return function(){t===r()&&t.value===t.getAttribute(V)&&"true"===t.getAttribute(D)&&M.moveCaret(t,0)}}function v(t){return function(){i(t)}}function p(t){t.form&&(T=t.form,"string"==typeof T&&(T=document.getElementById(T)),T.getAttribute(U)||(M.addEventListener(T,"submit",v(T)),T.setAttribute(U,"true"))),M.addEventListener(t,"focus",o(t)),M.addEventListener(t,"blur",c(t)),m&&(M.addEventListener(t,"keydown",s(t)),M.addEventListener(t,"keyup",d(t)),M.addEventListener(t,"click",g(t))),t.setAttribute(j,"true"),t.setAttribute(V,x),(m||t!==r())&&a(t)}var b,f,m,h,A,y,E,x,L,T,N,S,w,B=["text","search","url","tel","email","password","number","textarea"],C=[27,33,34,35,36,37,38,39,40,8,46],k="#ccc",I="placeholdersjs",R=RegExp("(?:^|\\s)"+I+"(?!\\S)"),V="data-placeholder-value",D="data-placeholder-active",P="data-placeholder-type",U="data-placeholder-submit",j="data-placeholder-bound",q="data-placeholder-focus",z="data-placeholder-live",F="data-placeholder-maxlength",G=document.createElement("input"),H=document.getElementsByTagName("head")[0],J=document.documentElement,K=t.Placeholders,M=K.Utils;if(K.nativeSupport=void 0!==G.placeholder,!K.nativeSupport){for(b=document.getElementsByTagName("input"),f=document.getElementsByTagName("textarea"),m="false"===J.getAttribute(q),h="false"!==J.getAttribute(z),y=document.createElement("style"),y.type="text/css",E=document.createTextNode("."+I+" { color:"+k+"; }"),y.styleSheet?y.styleSheet.cssText=E.nodeValue:y.appendChild(E),H.insertBefore(y,H.firstChild),w=0,S=b.length+f.length;S>w;w++)N=b.length>w?b[w]:f[w-b.length],x=N.attributes.placeholder,x&&(x=x.nodeValue,x&&M.inArray(B,N.type)&&p(N));L=setInterval(function(){for(w=0,S=b.length+f.length;S>w;w++)N=b.length>w?b[w]:f[w-b.length],x=N.attributes.placeholder,x?(x=x.nodeValue,x&&M.inArray(B,N.type)&&(N.getAttribute(j)||p(N),(x!==N.getAttribute(V)||"password"===N.type&&!N.getAttribute(P))&&("password"===N.type&&!N.getAttribute(P)&&M.changeType(N,"text")&&N.setAttribute(P,"password"),N.value===N.getAttribute(V)&&(N.value=x),N.setAttribute(V,x)))):N.getAttribute(D)&&(n(N),N.removeAttribute(V));h||clearInterval(L)},100)}M.addEventListener(t,"beforeunload",function(){K.disable()}),K.disable=K.nativeSupport?e:i,K.enable=K.nativeSupport?e:l}(this);
/*jshint browser:true */
/*!
* FitVids 1.1
*
* Copyright 2013, Chris Coyier - http://css-tricks.com + Dave Rupert - http://daverupert.com
* Credit to Thierry Koblentz - http://www.alistapart.com/articles/creating-intrinsic-ratios-for-video/
* Released under the WTFPL license - http://sam.zoy.org/wtfpl/
*
* FORKED by Steve Meyers on 7/21/15 to ensure that Embedly embeds don't receive an extra div wrapped around them.
*
*
*/

;(function( $ ){

  'use strict';

  $.fn.fitVids = function( options ) {
    var settings = {
      customSelector: null,
      ignore: null
    };

    if(!document.getElementById('fit-vids-style')) {
      // appendStyles: https://github.com/toddmotto/fluidvids/blob/master/dist/fluidvids.js
      var head = document.head || document.getElementsByTagName('head')[0];
      var css = '.fluid-width-video-wrapper{width:100%;position:relative;padding:0;}.fluid-width-video-wrapper iframe,.fluid-width-video-wrapper object,.fluid-width-video-wrapper embed {position:absolute;top:0;left:0;width:100%;height:100%;}';
      var div = document.createElement("div");
      div.innerHTML = '<p>x</p><style id="fit-vids-style">' + css + '</style>';
      head.appendChild(div.childNodes[1]);
    }

    if ( options ) {
      $.extend( settings, options );
    }

    return this.each(function(){
      var selectors = [
        'iframe[src*="player.vimeo.com"]:not(.embedly-card)',
        'iframe[src*="youtube.com"]:not(.embedly-card)',
        'iframe[src*="youtube-nocookie.com"]:not(.embedly-card)',
        'iframe[src*="kickstarter.com"][src*="video.html"]:not(.embedly-card)',
        'object',
        'embed'
      ];

      if (settings.customSelector) {
        selectors.push(settings.customSelector);
      }

      var ignoreList = '.fitvidsignore';

      if(settings.ignore) {
        ignoreList = ignoreList + ', ' + settings.ignore;
      }

      var $allVideos = $(this).find(selectors.join(','));
      $allVideos = $allVideos.not('object object'); // SwfObj conflict patch
      $allVideos = $allVideos.not(ignoreList); // Disable FitVids on this video.

      $allVideos.each(function(){
        var $this = $(this);
        if($this.parents(ignoreList).length > 0) {
          return; // Disable FitVids on this video.
        }
        if (this.tagName.toLowerCase() === 'embed' && $this.parent('object').length || $this.parent('.fluid-width-video-wrapper').length) { return; }
        if ((!$this.css('height') && !$this.css('width')) && (isNaN($this.attr('height')) || isNaN($this.attr('width'))))
        {
          $this.attr('height', 9);
          $this.attr('width', 16);
        }
        var height = ( this.tagName.toLowerCase() === 'object' || ($this.attr('height') && !isNaN(parseInt($this.attr('height'), 10))) ) ? parseInt($this.attr('height'), 10) : $this.height(),
            width = !isNaN(parseInt($this.attr('width'), 10)) ? parseInt($this.attr('width'), 10) : $this.width(),
            aspectRatio = height / width;
        if(!$this.attr('id')){
          var videoID = 'fitvid' + Math.floor(Math.random()*999999);
          $this.attr('id', videoID);
        }
        $this.wrap('<div class="fluid-width-video-wrapper"></div>').parent('.fluid-width-video-wrapper').css('padding-top', (aspectRatio * 100)+'%');
        $this.removeAttr('height').removeAttr('width');
      });
    });
  };
// Works with either jQuery or Zepto
})( window.jQuery || window.Zepto );

(function($, document, undefined) {

	/**
	 * Global Variables
	 *
	 * @type {*|HTMLElement}
	 */
	var $mobileMenu = $(document.querySelectorAll('ul.js-mobile-sub-menus')),
		$menuOverlay = $(document.querySelector('.menu-overlay-mask'));

	/**
	 * Init Function
	 */
	function init() {

		$mobileMenu.on('click.greaterMedia.Menus', 'a.show-subnavigation', openSubMenu);

		$mobileMenu.on('click.greaterMedia.Menus', 'a.mobile-menu-submenu-back-link', closeSubMenu);

		$menuOverlay.on('click', closeSubMenu);

	}

	/**
	 * Closes the SubMenu
	 *
	 * @param event
	 */
	function closeSubMenu(event) {
		event.preventDefault();
		$(this).parents('.sub-menu').removeClass('is-visible');
	}

	/**
	 * Opens the SubMenu
	 *
	 * @param event
	 */
	function openSubMenu(event) {
		event.preventDefault();

		// collapse any other open menus before opening ours.
		$mobileMenu.find('.is-visible').removeClass('is-visible');
		$(this).siblings('.sub-menu').addClass('is-visible');
	}

	init();

})(jQuery, document);
(function($) {

	var ProfileMenuApp = function() {

	};

	ProfileMenuApp.prototype = {

		run: function() {
			var $body = $('body');
			var $largeLink = $('.header__account--large');
			if ($body.hasClass('gmr-user')) {
				$largeLink.toggleClass('logged-in');
			}

			var $container = $('.header__account--container');
			$container.append(this.getMenu());

			var $avatar = $('.header__account--btn');
			$avatar.attr('href', this.getAvatarLink());

			var thumbnailURL = this.getThumbnailURL();
			if (thumbnailURL) {
				var $img = $('<img />', { src: thumbnailURL });
				$avatar.html($img);
				$avatar.addClass('avatar');
			}
		},

		getAvatarLink: function() {
			var endpoint = is_gigya_user_logged_in() ? 'account' : 'login';
			return gigya_profile_path(endpoint);
		},

		getThumbnailURL: function() {
			return get_gigya_user_field('thumbnailURL');
		},

		getMenu: function() {
			var menu  = this.getMenuLabels();
			var n     = menu.length;
			var $menu = $('<ul class="header__account--links sub-menu"></ul>');
			var $li, $a, item;

			for ( var i = 0; i < n; i++ ) {
				item = menu[i];
				$li = $('<li></li>');

				$a = $('<a></a>', { href: gigya_profile_path(item.endpoint) });
				$a.text(item.label);
				$li.append($a);

				$menu.append($li);
			}

			return $menu;
		},

		getMenuLabels: function() {
			var menu;

			if (is_gigya_user_logged_in()) {
				menu = [
					{ label: 'Edit Account' , endpoint: 'account' } ,
					{ label: 'Logout'       , endpoint: 'logout' }
				];
			} else {
				menu = [
					{ label: 'Login/Register', endpoint: 'login' }
				];
			}

			return menu;
		}

	};

	$(document).ready(function() {
		var app = new ProfileMenuApp();
		app.run();
	});

}(jQuery));

(function ($) {
	// we don't need to use pjax:end event here
	$(document).ready(function() {
		var $onair = $('#on-air'),
			schedule = [],
			fallback = '',
			current_show = {},
			track_schedule, update_onair;

		if ($onair.length == 0) {
			return;
		}

		update_onair = function(title, show) {
			$onair.find('.on-air__title').text(title);
			$onair.find('.on-air__show').text(show);
		};

		track_schedule = function() {
			var now = new Date(),
				next = new Date(now.getTime() + 10 * 60 * 1000), // 10 minutes later
				found = false,
				starts, ends;

			for (var i = 0; i < schedule.length; i++) {
				starts = new Date(schedule[i].starts * 1000);
				ends = new Date(schedule[i].ends * 1000);
				
				if (starts <= now && now <= ends) {
					current_show = schedule[i];
					update_onair('On Air:', schedule[i].title);
					found = true;
				}

				if (starts <= next && next <= ends && schedule[i].title != current_show.title) {
					update_onair('Up Next:', schedule[i].title);
					found = true;
				}
			}

			if (!found) {
				update_onair('', fallback);
			}
		};
		
		$.get($onair.data('endpoint'), function(response) {
			if (response.success && response.data) {
				fallback = response.data.tagline || '';
				if ($.isArray(schedule)) {
					schedule = response.data.schedule;
				}
				
				track_schedule();
				setInterval(track_schedule, 1000);
			}
		});
	});
})(jQuery);

(function ($) {
	var $window = $(window);

	var __ready = function() {
		var $days = $('.shows__schedule--day'),
			header_bottom = $('#wpadminbar').outerHeight(),
			on_scroll;

		on_scroll = function() {
			var scroll_top = $window.scrollTop();
			
			$days.each(function() {
				var $day = $(this),
					$weekday = $day.find('.shows__schedule--dayofweek'),
					day_top = $day.offset().top,
					day_left = $day.offset().left,
					day_bottom = $day.height() + $day.offset().top,
					own_height = $weekday.height(),
					top;

				if (scroll_top + header_bottom >= day_top) {
					$day.addClass('fixed');

					top = scroll_top + header_bottom + own_height >= day_bottom
						? day_bottom - scroll_top - own_height
						: header_bottom;

					$weekday.width($day.width()).css({
						top: top + 'px',
						left: day_left + 'px'
					});
				} else {
					$day.removeClass('fixed');
					$weekday.width('auto').css({
						top: '0px',
						left: '0px'
					});
				}
			});
		};

		$window.resize(on_scroll);
		$window.scroll(on_scroll);

		on_scroll();
	};

	$(document).bind('pjax:end', __ready).ready(__ready);
})(jQuery);
(function($) {

	var findElementByClassPrefix = function($node, prefix) {
		var classList = $node.attr('class').split(' ');
		var n         = classList.length;
		var className;

		for (var i = 0; i < n; i++) {
			className = classList[i];
			if (className.indexOf(prefix) === 0) {
				return className;
			}
		}

		return null;
	};

	var ArticleFinder = function() {

	};

	ArticleFinder.prototype = {

		find: function() {
			var selector = this.getSelector();
			var $article = $(selector);

			if ( $article.length === 1 ) {
				return this.getArticleFromNode($article);
			} else {
				return null;
			}
		},

		getSelector: function() {
			return '.main .content .article';
		},

		getArticleFromNode: function($article) {
			var article = {
				id       : this.getArticleID($article),
				postType : this.getArticlePostType($article)
			};

			if (article.id !== null && article.postType !== null) {
				return article;
			} else {
				return null;
			}
		},

		getArticleID: function($article) {
			var id = $article.attr('id');
			var startsWithPost = id.indexOf('post-') === 0;
			var articleID;

			if (startsWithPost) {
				return id.substring(5);
			} else {
				return null;
			}
		},

		getArticlePostType: function($article) {
			var postTypeClass = findElementByClassPrefix($article, 'type-');
			if (postTypeClass !== null) {
				return postTypeClass.substring(5);
			} else {
				return null;
			}
		}

	};

	var ShareLogger  = function() {
		var self     = this;
		var selector = this.getShareSelector();
		var logger   = function(event) { return self.didShareClick(event); };

		$(selector).click(logger);
	};

	ShareLogger.prototype = {

		share: function(action) {
			save_gigya_action(action);
		},

		didShareClick: function(event) {
			var selector = this.getShareSelector();
			var article  = this.getCurrentArticle();
			var $link    = $(event.target);

			if (article !== null) {
				var params = {
					network : this.getShareNetwork($link),
					url     : this.getShareUrl()
				};

				var action  = this.getShareAction(article, params);
				this.share(action);
			}

			return true;
		},

		getShareSelector: function() {
			return 'a.social__link';
		},

		getCurrentArticle: function() {
			var finder = new ArticleFinder();
			return finder.find();
		},

		getShareNetwork: function($link) {
			var iconClass = findElementByClassPrefix($link, 'icon-');

			if (iconClass !== null) {
				return iconClass.substring(5);
			} else {
				return null;
			}
		},

		getShareUrl: function() {
			return [location.protocol, '//', location.host, location.pathname].join('');
		},

		getShareAction: function(article, params) {
			var action = {
				actionType: 'action:social_share',
				actionID: article.id,
				actionData: [
					{ name: 'network', value: params.network },
					{ name: 'url', value: params.url }
				]
			};

			return action;
		}

	};

	$(document).ready(function() {
		var shareLogger = new ShareLogger();
	});

	/* exports */
	window.ArticleFinder = ArticleFinder;

}(jQuery));

(function($, location, document) {
	var $document = $(document),
		classes = {},
		last_url = null,
		current_url = location.href,
		normalize_url,
		siteWrap = $('#site-wrap');

	normalize_url = function(url) {
		return url.replace(/[\?\#].*$/g, '');
	};

	$document.bind('pjax:popstate', function() {
		last_url = normalize_url(current_url);
	});

	$document.bind('pjax:beforeSend', function() {
		last_url = normalize_url(location.href);
	});

	$document.bind('pjax:end', function(e, xhr, options) {
		var $body = $('body'),
			body_classes = false,
			pattern = new RegExp('\<body.*?class=\"(.*?)\"', 'im');

		classes[last_url] = $body.attr('class');

		if (xhr) {
			body_classes = pattern.exec(xhr.responseText);
			if (body_classes && body_classes.length >= 2) {
				$body.attr('class', body_classes[1]);
			}
		} else {
			$body.attr('class', classes[normalize_url(options.url)]);
		}

		current_url = location.href;
	});

	/**
	 * Add "is-busy" class to the body when a Pjax request starts.
	 */
	$document.bind( 'pjax:start', function () {
		$( 'body').addClass( 'is-busy' );
	} );

	$document.bind( 'pjax:click', function () {
		$( 'body').addClass( 'pjax-start' );
	} );

	/**
	 * Remove the "is-busy" class from the body when a Pjax request ends.
	 */
	$document.bind( 'pjax:end', function () {
		$( 'body').removeClass( 'is-busy' );
	} );

	/**
	 * Adds `pjax--active` class to the `#site-wrap` element when a Pjax request starts. This class can be used for
	 * visual display when Pjax is active.
	 */
	$document.bind('pjax:start', function() {
		siteWrap.addClass('pjax--active');
	});
})(jQuery, location, document);
(function ($, window, document, undefined) {

	/**
	 * Global variables
	 */
	var body = document.querySelector('body');
	var html = document.querySelector('html');
	var header = document.getElementById('header');
	var footer = document.querySelector('.footer');
	var $table = $('table');
	var $tableTd = $('table td');

	/**
	 * Adds a class to a HTML table to make the table responsive
	 */
	function responsiveTables() {
		$table.addClass('responsive');
		$tableTd.removeAttr('width');
	}

	/**
	 * Fallback for adding a body class when a user is a Gigya authenticated user
	 */
	function addGigyaBodyClass() {
		if (! body.classList.contains('gmr-user')) {
			body.classList.add('gmr-user');
		}
	}

	/**
	 * Function to add pop-up for social links
	 *
	 * @returns {boolean}
	 */
	function socialPopup() {
		var href = $(this).attr('href'),
			x = screen.width / 2 - 700 / 2,
			y = screen.height / 2 - 450 / 2;

		window.open(href, href, 'height=485,width=700,scrollbars=yes,resizable=yes,left=' + x + ',top=' + y);

		return false;
	}

	/**
	 * Toggles a target element for contest rules
	 *
	 * @param {MouseEvent} e
	 * @returns {boolean}
	 */
	function contestRulesToggle(e) {
		var target = $($(this).attr('data-target')).get(0),
			currentText = $(this).html(),
			newText = $(this).attr('data-alt-text');

		target.style.display = target.style.display !== 'none' ? 'none' : 'block';

		$(this).html(newText);
		$(this).attr('data-alt-text', currentText);

		return false;
	}

	/**
	 * Personality Toggle
	 */
	function personality_toggle() {
		var $button = $('.person-toggle');
		start = $('.personality__meta').first().height(); // get the height of the meta before we start, basically tells us whether we're using the mobile or desktop height

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

	/**
	 * Init Functions
	 */
	responsiveTables();

	if (is_gigya_user_logged_in()) {
		addGigyaBodyClass();
	}

	/**
	 * Functions called on Document Ready
	 */
	$(document).ready(function() {
		personality_toggle();
		$('.article__content').fitVids({customSelector: "div[id^='playerwrapper']"});
	});

	/**
	 * Functions specific to document events
	 */
	$(document).on('click', '.popup', socialPopup);

	$(document).on('click', '*[data-toggle="collapse"]', contestRulesToggle);

	/**
	 * Functions that run after the pjax:end event
	 */
	$(document).bind( 'pjax:end', function () {
		personality_toggle();
	});
	
})(jQuery, window, document);
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
(function ($, window, document, undefined) {

	/**
	 * Global variables
	 */
	var body = document.querySelector('body');
	var mobileNavButton = document.querySelector('.mobile-nav__toggle');
	var siteWrap = document.getElementById('site-wrap');

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
	 * Allows the main content body to maintain it's vertical position when the mobile menu is opened
	 */
	function mobileOpenLocation() {
		var y = window.pageYOffset;

		siteWrap.style.top = '-' + y + 'px';
	}

	/**
	 * Returns the main content body to it's vertical position when the mobile menu is closed
	 */
	function mobileCloseLocation() {
		siteWrap.style.removeProperty('top');
	}

	/**
	 * Toggles a class to the body when the mobile nav button is clicked
	 */
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

	/**
	 * Adds a overlay to the body when a menu item that has a sub-menu, is hovered
	 */
	function init_menu_overlay() {
		var $menu = jQuery(document.querySelector('.header__nav--list')),
			$secondary = jQuery(document.querySelector('.header__secondary')),
			$overlay = jQuery(document.querySelector('.menu-overlay-mask')),
			$body = jQuery(document.querySelector('body')),
			$logo = jQuery(document.querySelector('.header__logo')),
			$subHeader = jQuery(document.querySelector('.header__sub'));

		$menu.on('mouseover', '.menu-item-has-children', function (e) {
			$overlay.addClass('is-visible');
			if($body.hasClass('news-site')) {
				$logo.addClass('is-visible');
				$subHeader.addClass('is-visible');
			}
		});
		$menu.on('mouseout', '.menu-item-has-children', function (e) {
			$overlay.removeClass('is-visible');
			if($body.hasClass('news-site')) {
				$logo.removeClass('is-visible');
				$subHeader.removeClass('is-visible');
			}
		});
	}

	/**
	 * Helps with hover issues on mobile
	 */
	function addHoverMobile() {
		$('.header__nav ul li').on('click touchstart', function() {
			$(this).addClass('active');
		});
	}

	/**
	 * Removes the active class added by addHoverMobile
	 */
	function removeHoverMobile() {
		$('.header__nav ul li').removeClass('active');
	}

	/**
	 * Triggered by pjax to remove the `is-visible` class when the pjax:end event is triggered
	 */
	function removeoverlay() {
		var $overlay = jQuery(document.querySelector('.menu-overlay-mask'));

		$overlay.removeClass('is-visible');
	}

	/**
	 * Adds an active class to a menu item when it is hovered
	 */
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

	/**
	 * Adds an visibility class to the logo when the menu is hovered
	 */
	function addLogoVisibility() {
		var $body = jQuery(document.querySelector('body')),
			$logo = jQuery(document.querySelector('.header__logo')),
			$headerMain = jQuery(document.querySelector('.header__main'));

		$headerMain.on('mouseover', function(e) {
			if ($body.hasClass('news-site')) {
				$logo.addClass('is-visible');
			}
		});

		$headerMain.on('mouseout', function(e) {
			if ($body.hasClass('news-site')) {
				$logo.removeClass('is-visible');
			}
		});
	}

	/**
	 * Inserts a new element on mobile to provide a blocker
	 *
	 * @returns {*|jQuery|HTMLElement}
	 */
	var getBlockerDiv = function() {
		var $div = $('#mobile-nav-blocker');
		if ($div.length === 0) {
			$('<div id="mobile-nav-blocker"></div>').insertAfter('#mobile-nav');
			$div = $('#mobile-nav-blocker');
			$div.on('click', toggleNavButton);
		}

		return $div;
	};

	/**
	 * Shows the blocker div that is created by getBlockerDiv
	 */
	var showBlocker = function() {
		var $blocker = getBlockerDiv();

		$blocker.css({
			width: $(document).width(),
			height: $(document).height(),
			display: 'block',
		});
	};

	/**
	 * Hides the blocker div that is shown by showBlocker
	 */
	var hideBlocker = function() {
		var $blocker = getBlockerDiv();
		$blocker.css({'display': 'none'});
		if ($blocker.hasClass('active')) {
			$blocker.removeClass('active');
		}
	};

	/**
	 * Init Functions
	 */
	addEventHandler(mobileNavButton, 'click', toggleNavButton);
	init_menu_overlay();
	addHoverMobile();
	addMenuHover();
	addLogoVisibility();

	/**
	 * Functions that run after the pjax:end event
	 */
	$(document).bind( 'pjax:end', function () {
		hideBlocker();
		removeHoverMobile();
		removeoverlay();
	});

})(jQuery, window, document);
(function($, window, document, undefined) {

	var $searchContainer = $( '#header__search--form '),
		$searchForm = $( '#header__search--form ' ).find( 'form' ),
		$searchBtn = $( '#header__search'),
		$searchInput = $( '#header-search' ),
		$overlay = $('.header-search-overlay-mask' );
	
	/**
	 * A function to show the header search when an event is targeted.
	 *
	 * @param e
	 */
	function showSearch(e) {
		e.preventDefault();
		
		if ( $searchContainer.hasClass( 'header__search--open' ) ) {
			return; 
		}
		
		$overlay.addClass( 'is-visible' )
		
		// Now, show the search form, but don't set focus until the transition
		// animation is complete. This is because Webkit browsers scroll to 
		// the element when it gets focus, and they scroll to it where it was
		// before the transition started.		
		if ( '0s' !== $searchContainer.css('transitionDuration') ) {
			$searchContainer.one('transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd', function () {
				$searchInput.focus().select();
			} );
		} else {
			$searchInput.focus().select();			
		}
		$searchContainer.addClass('header__search--open'); 
	}
	
	/**
	 * A function to hide the header search when an event is targeted.
	 *
	 * @param e
	 */
	function closeSearch(e) {
		e.preventDefault();
		
		if ( ! $searchContainer.hasClass( 'header__search--open' ) ) {
			return;
		}
		
		$searchContainer.removeClass( 'header__search--open' );
		$overlay.removeClass('is-visible');
		document.activeElement.blur();
	}
	
	/**
	 * Event listeners to run on click to show and close the search.
	 */
	$searchBtn.click( showSearch ); 
	
	/**
	 * Open search if user clicks on it.
	 */
	$searchInput.add( $searchForm.find( 'button[type=submit]' ) ).click( showSearch );
	
	function checkSearchField () {
		var $search_body = $searchContainer.find( '.header-search-body' );
		
		// Show the body only if there's text in the search field.
		if ( $searchInput.val().length ) {
			$search_body.addClass( 'is-visible' );
		} else {
			$search_body.removeClass( 'is-visible' );
		}
	}
	
	$searchInput.keyup( checkSearchField );
	
	checkSearchField(); 
	
	/**
	 * Close the search box when user presses escape.
	 */
	$(window).keydown(function (e) {
		if (e.keyCode === 27){
			closeSearch(e);
		}		
	});
	
	/**
	 * Handle enter key for Safari. 
	 */
	$searchForm.keydown( function ( e ) {
		if ( 13 === e.keyCode ) {
			$( this ).submit(); 
		}
	} );
	
	/**
	 * Close the search box (if open) if the user clicks on the overlay.
	 */
	$overlay.click(function (e) {
		closeSearch(e);
	});
	
	/**
	 * Close the search box (if open) if the user clicks the close button.
	 */
	$searchContainer.find( '.header__search--cancel' ).click( function ( e ) {
		e.preventDefault();
		closeSearch( e );
	} );
	
	/**
	 * Make "Search All Content" button trigger form submit.
	 */
	$searchContainer.find( '.header-search__search-all-btn' ).click( function () {
		$searchForm.submit(); 	
	} );
	
	/**
	 * PJAX workaround. PJAX is set to only handle links when they're clicked,
	 * so to get the form to work over PJAX we need to create a fake link and 
	 * then click it. Clunky but it is the quick fix for now. 
	 * 
	 * Note that we are calling click() on the DOM object, not the jQuery 
	 * object. This is the only way to get this to work on Safari. 
	 */
	$searchForm.submit( function ( e ) {
		e.preventDefault();		
		
		$( '<a></a>' )
			.attr( 'href', $( this ).attr( 'action' ) + '?s=' + $( this ).find( 'input[name=s]' ).val() )
			.appendTo( $( this ) )
			.get( 0 ).click() // Note we are triggering click on the DOM object, not the jQuery object.
		;
		
		closeSearch( e );
	} );
	
})(jQuery, window, document);
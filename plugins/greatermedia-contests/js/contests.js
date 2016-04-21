/*! Greater Media Contests - v1.0.6
 * http://10up.com/
 * Copyright (c) 2016;
 * Licensed GPLv2+
 */
(function ($) {
	var $event = $.event,
	$special,
	resizeTimeout;

	$special = $event.special.debouncedresize = {
		setup: function() {
			$( this ).on( "resize", $special.handler );
		},
		teardown: function() {
			$( this ).off( "resize", $special.handler );
		},
		handler: function( event, execAsap ) {
			// Save the context
			var context = this,
				args = arguments,
				dispatch = function() {
					// set correct event type
					event.type = "debouncedresize";
					$event.dispatch.apply( context, args );
				};

			if ( resizeTimeout ) {
				clearTimeout( resizeTimeout );
			}

			execAsap ?
				dispatch() :
				resizeTimeout = setTimeout( dispatch, $special.threshold );
		},
		threshold: 250
	};
})(jQuery);
// ======================= imagesLoaded Plugin ===============================
// https://github.com/desandro/imagesloaded

// $('#my-container').imagesLoaded(myFunction)
// execute a callback when all images have loaded.
// needed because .load() doesn't work on cached images

// callback function gets image collection as argument
//  this is the container

// original: MIT license. Paul Irish. 2010.
// contributors: Oren Solomianik, David DeSandro, Yiannis Chatzikonstantinou

// blank image data-uri bypasses webkit log warning (thx doug jones)
var BLANK = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

(function ($) {
	$.fn.imagesLoaded = function( callback ) {
		var $this = this,
			deferred = $.isFunction($.Deferred) ? $.Deferred() : 0,
			hasNotify = $.isFunction(deferred.notify),
			$images = $this.find('img').add( $this.filter('img') ),
			loaded = [],
			proper = [],
			broken = [];

		// Register deferred callbacks
		if ($.isPlainObject(callback)) {
			$.each(callback, function (key, value) {
				if (key === 'callback') {
					callback = value;
				} else if (deferred) {
					deferred[key](value);
				}
			});
		}

		function doneLoading() {
			var $proper = $(proper),
				$broken = $(broken);

			if ( deferred ) {
				if ( broken.length ) {
					deferred.reject( $images, $proper, $broken );
				} else {
					deferred.resolve( $images );
				}
			}

			if ( $.isFunction( callback ) ) {
				callback.call( $this, $images, $proper, $broken );
			}
		}

		function imgLoaded( img, isBroken ) {
			// don't proceed if BLANK image, or image is already loaded
			if ( img.src === BLANK || $.inArray( img, loaded ) !== -1 ) {
				return;
			}

			// store element in loaded images array
			loaded.push( img );

			// keep track of broken and properly loaded images
			if ( isBroken ) {
				broken.push( img );
			} else {
				proper.push( img );
			}

			// cache image and its state for future calls
			$.data( img, 'imagesLoaded', { isBroken: isBroken, src: img.src } );

			// trigger deferred progress method if present
			if ( hasNotify ) {
				deferred.notifyWith( $(img), [ isBroken, $images, $(proper), $(broken) ] );
			}

			// call doneLoading and clean listeners if all images are loaded
			if ( $images.length === loaded.length ){
				setTimeout( doneLoading );
				$images.unbind( '.imagesLoaded' );
			}
		}

		// if no images, trigger immediately
		if ( !$images.length ) {
			doneLoading();
		} else {
			$images.bind( 'load.imagesLoaded error.imagesLoaded', function( event ){
				// trigger imgLoaded
				imgLoaded( event.target, event.type === 'error' );
			}).each( function( i, el ) {
				var src = el.src;

				// find out if this image has been already checked for status
				// if it was, and src has not changed, call imgLoaded on it
				var cached = $.data( el, 'imagesLoaded' );
				if ( cached && cached.src === src ) {
					imgLoaded( el, cached.isBroken );
					return;
				}

				// if complete is true and browser supports natural sizes, try
				// to check for image status manually
				if ( el.complete && el.naturalWidth !== undefined ) {
					imgLoaded( el, el.naturalWidth === 0 || el.naturalHeight === 0 );
					return;
				}

				// cached images don't fire load sometimes, so we reset src, but only when
				// dealing with IE, or image is complete (loaded) and failed manual check
				// webkit hack from http://groups.google.com/group/jquery-dev/browse_thread/thread/eee6ab7b2da50e1f
				if ( el.readyState || el.complete ) {
					el.src = BLANK;
					el.src = src;
				}
			});
		}

		return deferred ? deferred.promise( $this ) : $this;
	};

})(jQuery);
(function ($, Modernizr, Waypoint) {
	var $window = $(window),
		winsize,
		$body = $('html, body'),
		// transitionend events
		transEndEventNames = {
			'WebkitTransition': 'webkitTransitionEnd',
			'MozTransition': 'transitionend',
			'OTransition': 'oTransitionEnd',
			'msTransition': 'MSTransitionEnd',
			'transition': 'transitionend'
		},
		transEndEventName = transEndEventNames[Modernizr.prefixed('transition')],
		support = Modernizr.csstransitions; // support for csstransitions

	$.fn.grid = function(config) {
		var $grids = $(this),
			adminBarHeight = $('#wpadminbar').height() || 0,
			settings = {
				minHeight: 816,
				speed: 200,
				easing: 'ease',
				loadMore: null,
				loadMoreWaypointOffset: '150%',
				loadMoreUrl: null,
				loadedMore: null,
				previewLoaded: null
			};
		
		// the settings..
		settings = $.extend(true, {}, settings, config);

		$grids.each(function() {
			var $grid = $(this), // list of items
				$items = $grid.children('li'), // the items
				$loadMore = $(settings.loadMore),
				current = -1, // current expanded item's index
				previewPos = -1, // position (top) of the expanded item used to know if the preview will expand in a different row
				scrollExtra = 0, // extra amount of pixels to scroll the window
				marginExpanded = 70, // extra margin when expanded (between preview overlay and the next items)
				itemsCountOnPage = $items.length,
				loadMoreIteration = 1,
				loadMoreLocked = false;

			function init() {
				// preload all images
				$grid.imagesLoaded(function () {
					// save item's size and offset
					saveItemInfo(true);
					// get window's size
					getWinSize();
					// initialize some events
					initEvents();
				});
			}

			// add more items to the grid.
			// the new items need to appended to the grid.
			// after that call Grid.addItems(theItems);
			function addItems($newitems) {
				$items = $items.add($newitems);

				$newitems.each(function () {
					var $item = $(this);
					$item.data({
						offsetTop: $item.offset().top,
						height: $item.height()
					});
				});

				initItemsEvents($newitems);
			}

			function loadItems() {
				var callbackUrl;
				
				if ($.isFunction(settings.loadMoreUrl)) {
					if (loadMoreLocked) {
						return;
					}

					callbackUrl = settings.loadMoreUrl(loadMoreIteration);
					if (!callbackUrl) {
						return;
					}

					loadMoreLocked = true;
					loadMoreIteration++;

					$loadMore.addClass('loading');

					$.get(callbackUrl, function(data) {
						var newItems = $($.trim(data));

						if (newItems.length > 0) {
							$grid.append(newItems);
							addItems(newItems);

							if (newItems.length >= itemsCountOnPage) {
								loadMoreLocked = false;
								$loadMore.removeClass('loading');
								Waypoint.refreshAll();

								if ($.isFunction(settings.loadedMore)) {
									settings.loadedMore(newItems);
								}
							} else {
								$loadMore.hide();
							}
						} else {
							$loadMore.hide();
						}
					});
				}
			}

			// saves the item's offset top and height (if saveheight is true)
			function saveItemInfo(saveheight) {
				$items.each(function () {
					var $item = $(this);
					$item.data('offsetTop', $item.offset().top);
					if (saveheight) {
						$item.data('height', $item.outerHeight());
					}
				});
			}

			function initEvents() {
				// when clicking an item, show the preview with the item's info and large image.
				// close the item if already expanded.
				// also close if clicking on the item's cross
				initItemsEvents($items);

				// on window resize get the window's size again
				// reset some values..
				$window.on('debouncedresize', function () {
					scrollExtra = 0;
					previewPos = -1;
					// save item's offset
					saveItemInfo();
					getWinSize();
					var preview = $.data(this, 'preview');
					if (typeof preview !== 'undefined') {
						hidePreview();
					}
				});

				// load more items when clicking a "load more" button or scroll down
				// to the button
				if ($loadMore.length > 0) {
					$loadMore.on('click', function() {
						loadItems();
						return false;
					});

					$loadMore.waypoint({
						offset: settings.loadMoreWaypointOffset,
						triggerOnce: false,
						handler: function(direction) {
							if (direction === 'down') {
								loadItems();
							}
						}
					});

					Waypoint.refreshAll();
				}
			}

			function initItemsEvents($items) {
				$items.on('click', 'span.preview-close', function () {
					hidePreview();
					return false;
				});

				$items.on('click', '> a', function (e) {
					var $item = $(this).parent();

					// check if item already opened
					if (current === $item.index()) {
						hidePreview();
					} else {
						showPreview($item);
					}

					return false;
				});
			}

			function getWinSize() {
				winsize = {width: $window.width(), height: $window.height()};
			}

			function showPreview($item) {
				var preview = $.data(this, 'preview'),
					// item's offset top
					position = $item.data('offsetTop');

				scrollExtra = 0;

				// if a preview exists and previewPos is different (different row) from item's top then close it
				if (typeof preview !== 'undefined') {
					// not in the same row
					if (previewPos !== position) {
						// if position > previewPos then we need to take te current preview's height in consideration when scrolling the window
						if (position > previewPos) {
							scrollExtra = preview.height;
						}
						hidePreview();
					}
					// same row
					else {
						preview.update($item);
						return false;
					}
				}

				// update previewPos
				previewPos = position;
				// initialize new preview for the clicked item
				preview = $.data(this, 'preview', new Preview($item));
				// expand preview overlay
				preview.open();
			}

			function hidePreview() {
				var preview = $.data(this, 'preview');

				current = -1;
				if (preview) {
					preview.close();
				}

				$.removeData(this, 'preview');
			}

			// the preview obj / overlay
			function Preview($item) {
				this.$item = $item;
				this.expandedIdx = this.$item.index();
				this.create();
				this.update();
			}

			Preview.prototype = {
				create: function () {
					// create Preview container:
					this.$closePreview = $('<span class="preview-close"></span>');
					this.$previewInner = $('<div class="preview-inner"></div>').append(this.$closePreview);
					this.$previewEl = $('<div class="preview"></div>').append(this.$previewInner);
					// append preview element to the item
					this.$item.append(this.getEl());
					// set the transitions for the preview and the item
					if (support) {
						this.setTransition();
					}
				},
				
				update: function ($item) {
					var $previewInner, $itemEl, content, self;

					self = this;
					if ($item) {
						self.$item = $item;
						self.getEl().appendTo($item);
					}

					// if already expanded remove class "og-expanded" from current item and add it to new item
					if (current !== -1) {
						var $currentItem = $items.eq(current);
						$currentItem.removeClass('expanded');
						self.$item.addClass('expanded');
						// position the preview correctly
						self.positionPreview();
					}

					// update current value
					current = self.$item.index();

					// reset preview inner element
					$previewInner = self.$previewInner;
					$previewInner.html(self.$closePreview);

					// update preview's content
					$itemEl = self.$item.children('a');
					content = $itemEl.data('content');
					if (!content) {
						$.get($itemEl.attr('href'), {ajax: 'true'}, function(response) {
							$itemEl.data('content', response);
							$previewInner.append(response);

							if ($.isFunction(settings.previewLoaded)) {
								settings.previewLoaded(self);
							}
						});
					} else {
						$previewInner.append(content);

						if ($.isFunction(settings.previewLoaded)) {
							settings.previewLoaded(self);
						}
					}
				},

				open: function () {
					setTimeout($.proxy(function () {
						// set the height for the preview and the item
						this.setHeights();
						// scroll to position the preview in the right place
						this.positionPreview();
					}, this), 25);
				},

				close: function () {
					var self = this,
						onEndFn = function () {
							if (support) {
								$(this).off(transEndEventName);
							}
							self.$item.removeClass('expanded');
							self.$previewEl.remove();
						};

					setTimeout($.proxy(function () {
						if (typeof this.$largeImg !== 'undefined') {
							this.$largeImg.fadeOut('fast');
						}
						this.$previewEl.css('height', 0);
						// the current expanded item (might be different from this.$item)
						var $expandedItem = $items.eq(this.expandedIdx);
						$expandedItem.css('height', $expandedItem.data('height')).on(transEndEventName, onEndFn);

						if (!support) {
							onEndFn.call();
						}
					}, this), 25);

					return false;
				},

				calcHeight: function () {
					var heightPreview = winsize.height - this.$item.data('height') - marginExpanded,
						itemHeight = winsize.height;

					if (heightPreview < settings.minHeight) {
						heightPreview = settings.minHeight;
						itemHeight = settings.minHeight + this.$item.data('height') + marginExpanded;
					}

					this.height = heightPreview;
					this.itemHeight = itemHeight;
				},
				
				setHeights: function () {
					var self = this,
						onEndFn = function () {
							if (support) {
								self.$item.off(transEndEventName);
							}
							self.$item.addClass('expanded');
						};

					this.calcHeight();
					this.$previewEl.css('height', this.height);
					this.$item.css('height', this.itemHeight).on(transEndEventName, onEndFn);

					if (!support) {
						onEndFn.call();
					}
				},
				
				positionPreview: function () {
					var position = this.$item.data('offsetTop'),
						previewOffsetT = this.$previewEl.offset().top - scrollExtra,
						scrollVal;

					// scroll page
					// case 1 : preview height + item height fits in window's height
					// case 2 : preview height + item height does not fit in window's height and preview height is smaller than window's height
					// case 3 : preview height + item height does not fit in window's height and preview height is bigger than window's height
					if (this.height + this.$item.data('height') + marginExpanded <= winsize.height) {
						scrollVal = position;
					} else if (this.height < winsize.height) {
						scrollVal = previewOffsetT - (winsize.height - this.height);
					} else {
						scrollVal = previewOffsetT;
					}

					$body.animate({scrollTop: scrollVal - adminBarHeight}, settings.speed);
				},

				setTransition: function () {
					this.$previewEl.css('transition', 'height ' + settings.speed + 'ms ' + settings.easing);
					this.$item.css('transition', 'height ' + settings.speed + 'ms ' + settings.easing);
				},
				
				getEl: function () {
					return this.$previewEl;
				}
			};

			init();
		});

		return $grids;
	};
})(jQuery, Modernizr, Waypoint);
/* globals get_gigya_profile_fields:false, gigya_profile_path:false */
/* globals _:false */
(function($) {
	var $document = $(document), container, gridContainer;

	var gridUpdateRating = function($item, delta) {
		var rating = parseInt($item.text().replace(/\D+/g, ''));

		if (isNaN(rating)) {
			rating = 0;
		}

		rating += delta;
		rating = rating.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,').replace(/\..*/g, '');

		$item.text(rating);
	};

	var gridPreviewLoaded = function(submission) {
		var $previewInner = submission.$previewInner,
			$item = submission.$item,
			$rating = $item.find('.contest__submission--rating b'),
			sync_vote = false;

		// init new gallery
		if ($.fn.cycle) {
			$previewInner.find('.cycle-slideshow').cycle();
		}

		// bind vote click event
		$previewInner.find('.contest__submission--vote').click(function() {
			var $this = $(this),
				$icon = $this.find('i.gmr-icon'),
				classes = $icon.attr('class');

			if (!sync_vote) {
				sync_vote = true;
				$icon.attr('class', 'gmr-icon icon-spin icon-spin');

				$.post(container.data('vote'), {ugc: $this.data('id')}, function(response) {
					sync_vote = false;
					$icon.attr('class', classes);

					if (response.success) {
						$item.addClass('voted');
						gridUpdateRating($rating, 1);
					}
				});
			}

			return false;
		});

		// bind unvote click event
		$previewInner.find('.contest__submission--unvote').click(function() {
			var $this = $(this),
				$icon = $this.find('i.gmr-icon'),
				classes = $icon.attr('class');

			if (!sync_vote) {
				sync_vote = true;
				$icon.attr('class', 'gmr-icon icon-spin icon-spin');

				$.post(container.data('unvote'), {ugc: $this.data('id')}, function(response) {
					sync_vote = false;
					$icon.attr('class', classes);

					if (response.success) {
						$item.removeClass('voted');
						gridUpdateRating($rating, -1);
					}
				});
			}

			return false;
		});

		$document.trigger('contest:preview-loaded');
	};

	var gridLoadMoreUrl = function(page) {
		return container.data('infinite') + (page + 1) + '/';
	};

	var __ready = function() {
		container = $('#contest-form');
		gridContainer = $('.contest__submissions--list');

		$document.on('submit', '#contest-form form', function() {
			var form = $(this),
				iframe, iframe_onload;

			if (!form.parsley || form.parsley().isValid()) {
				form.find('input, textarea, select, button').attr('readonly', 'readonly');
				form.find('i.gmr-icon').show();

				iframe_onload = function() {
					var iframe_document = iframe.contentDocument || iframe.contentWindow.document,
						iframe_body = iframe_document.getElementsByTagName('body')[0],
						scroll_to = container.offset().top - $('#wpadminbar').height() - 10;

					iframe_body = $.trim(iframe_body.innerHTML);
					if (iframe_body.length > 0) {
						container.html(iframe_body);
					} else {
						alert('Your submission failed. Please, enter required fields and try again.');
						form.find('input, textarea, select, button').removeAttr('readonly');
						form.find('i.gmr-icon').hide();
					}

					$('html, body').animate({scrollTop: scroll_to}, 200);
				};

				iframe = document.getElementById('theiframe');
				if (iframe.addEventListener) {
					iframe.addEventListener('load', iframe_onload, false);
				} else if (iframe.attachEvent) {
					iframe.attachEvent('onload', iframe_onload);
				}

				return true;
			}

			return false;
		});

		var showRestriction = function(restriction) {
			var $restrictions = $('.contest__restrictions');

			$restrictions.attr('class', 'contest__restrictions');
			if (restriction) {
				$restrictions.addClass(restriction);
			}
		};

		var loadContainerState = function(url) {
			$.get(url, function(response) {
				var restriction = null;

				if (response.success) {
					container.html(response.data.html);

					$('#contest-form form').parsley();
					$('.type-contest.collapsed').removeClass('collapsed');
				} else {
					restriction = response.data.restriction;
				}

				showRestriction(restriction);

				if (response.data && response.data.contest_id) {
					loadUserContestMeta(response.data.contest_id);
				}
			});
		};

		var loadUserContestMeta = function(contestID) {
			if (is_gigya_user_logged_in()) {
				get_gigya_profile_fields(['email', 'dateOfBirth'])
					.then(didLoadUserContestMeta);
			} else {
				var $form = $('.contest__form--user-info');
				$form.css('display', 'block');
			}
		};

		var didLoadUserContestMeta = function(response) {
			if (response.success) {
				showUserContestMeta(response.data);
			}
		};

		var showUserContestMeta = function(fields) {
			var userTemplate = '<span class="meta-title">Entry Details</span>' +
				'<a href="<%- editProfileUrl %>">Edit Your Profile</a>' +
				'<p class="meta-subtitle">This information is required for every entry.</p>' +
				'<dl>' +
				'<dt>Name: </dt>' +
				'<dd><%- firstName %> <%- lastName %></dd>' +
				'<dt>Email Address:</dt>' +
				'<dd><%- email %></dd>' +
				'<dt>Date of Birth: </dt>' +
				'<dd><%- dateOfBirth %></dd>' +
				'<dt>Zip: </dt>' +
				'<dd><%- zip %></dd>' +
				'</dl>';

			var data = {
				editProfileUrl : gigya_profile_path('account'),
				loginUrl       : gigya_profile_path('login'),
				firstName      : get_gigya_user_field('firstName'),
				lastName       : get_gigya_user_field('lastName'),
				email          : fields.email || 'N/A',
				age            : get_gigya_user_field('age'),
				dateOfBirth    : fields.dateOfBirth || 'N/A',
				zip            : get_gigya_user_field('zip'),
			};

			var template     = _.template(userTemplate);
			var html         = template(data);
			var $box        = $('.contest__form--user-info .user-info-box');

			$box.html(html);
			$box.css('display', 'block');

			var $userInfo = $('.contest__form--user-info');
			$userInfo.css('display', 'block');
		};

		$('.contest__restriction--min-age-yes').click(function() {
			loadContainerState(container.data('confirm-age'));
			return false;
		});

		$('.contest__restriction--min-age-no').click(function() {
			showRestriction('age-fails');
			return false;
		});

		if (container.length > 0) {
			loadContainerState(container.data('load'));
		}

		if (gridContainer.length > 0) {
			gridContainer.grid({
				loadMore: '.contest__submissions--load-more',
				previewLoaded: gridPreviewLoaded,
				loadMoreUrl: gridLoadMoreUrl
			});
		}
	};

	$document.bind('pjax:end', __ready).ready(__ready);
})(jQuery);

(function ($) {
	$(document).ready(function () {
		/**
		 * Generate a list of supported input types (text, date, range, etc.).
		 * Adapted from Modernizr, which is MIT licensed
		 * @see http://modernizr.com/
		 */
		function get_supported_input_types() {

			var inputElem = document.createElement('input'),
				docElement = document.documentElement,
				inputs = {},
				smile = ':)';

			return (function (props) {

				for (var i = 0, bool, inputElemType, defaultView, len = props.length; i < len; i++) {

					inputElem.setAttribute('type', inputElemType = props[i]);
					bool = inputElem.type !== 'text';

					if (bool) {

						inputElem.value = smile;
						inputElem.style.cssText = 'position:absolute;visibility:hidden;';

						if (/^range$/.test(inputElemType) && inputElem.style.WebkitAppearance !== undefined) {

							docElement.appendChild(inputElem);
							defaultView = document.defaultView;

							bool = defaultView.getComputedStyle &&
							defaultView.getComputedStyle(inputElem, null).WebkitAppearance !== 'textfield' &&
							(inputElem.offsetHeight !== 0);

							docElement.removeChild(inputElem);

						} else if (/^(search|tel)$/.test(inputElemType)) {
						} else if (/^(url|email)$/.test(inputElemType)) {
							bool = inputElem.checkValidity && inputElem.checkValidity() === false;

						} else {
							bool = inputElem.value !== smile;
						}
					}

					inputs[props[i]] = !!bool;
				}

				return inputs;

			})('search tel url email datetime date month week time datetime-local number range color'.split(' '));
		}

		// Add datepickers for start & end dates if not supported natively
		var supported_input_types = get_supported_input_types();
		if (!supported_input_types.hasOwnProperty('date') || false === supported_input_types.date) {
			$('input[type=date]').datetimepicker({
				timepicker: false,
				format    : 'm/d/Y'
			});

			$('input[type=time]').datetimepicker({
				datepicker: false,
				format    : 'g:i A',
				formatTime: 'g:i A',
				allowTimes: [
					'12:00 AM', '12:30 AM',
					'1:00 AM', '1:30 AM',
					'2:00 AM', '2:30 AM',
					'3:00 AM', '3:30 AM',
					'4:00 AM', '4:30 AM',
					'5:00 AM', '5:30 AM',
					'6:00 AM', '6:30 AM',
					'7:00 AM', '7:30 AM',
					'8:00 AM', '8:30 AM',
					'9:00 AM', '9:30 AM',
					'10:00 AM', '10:30 AM',
					'11:00 AM', '11:30 AM',
					'12:00 PM', '12:30 PM',
					'1:00 PM', '1:30 PM',
					'2:00 PM', '2:30 PM',
					'3:00 PM', '3:30 PM',
					'4:00 PM', '4:30 PM',
					'5:00 PM', '5:30 PM',
					'6:00 PM', '6:30 PM',
					'7:00 PM', '7:30 PM',
					'8:00 PM', '8:30 PM',
					'9:00 PM', '9:30 PM',
					'10:00 PM', '10:30 PM',
					'11:00 PM', '11:30 PM'
				]
			});
		}
	});
})(jQuery);
(function ($, Modernizr) {
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
				minHeight: 500,
				speed: 350,
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
								$.waypoints('refresh');

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

					$.waypoints('refresh');
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
})(jQuery, Modernizr);
/*! Greater Media Contests - v1.0.2
 * http://10up.com/
 * Copyright (c) 2015; * Licensed GPLv2+ */
/*!
jQuery Waypoints - v2.0.2
Copyright (c) 2011-2013 Caleb Troughton
Dual licensed under the MIT license and GPL license.
https://github.com/imakewebthings/jquery-waypoints/blob/master/licenses.txt
*/
(function(){var t=[].indexOf||function(t){for(var e=0,n=this.length;e<n;e++){if(e in this&&this[e]===t)return e}return-1},e=[].slice;(function(t,e){if(typeof define==="function"&&define.amd){return define("waypoints",["jquery"],function(n){return e(n,t)})}else{return e(t.jQuery,t)}})(this,function(n,r){var i,o,l,s,f,u,a,c,h,d,p,y,v,w,g,m;i=n(r);c=t.call(r,"ontouchstart")>=0;s={horizontal:{},vertical:{}};f=1;a={};u="waypoints-context-id";p="resize.waypoints";y="scroll.waypoints";v=1;w="waypoints-waypoint-ids";g="waypoint";m="waypoints";o=function(){function t(t){var e=this;this.$element=t;this.element=t[0];this.didResize=false;this.didScroll=false;this.id="context"+f++;this.oldScroll={x:t.scrollLeft(),y:t.scrollTop()};this.waypoints={horizontal:{},vertical:{}};t.data(u,this.id);a[this.id]=this;t.bind(y,function(){var t;if(!(e.didScroll||c)){e.didScroll=true;t=function(){e.doScroll();return e.didScroll=false};return r.setTimeout(t,n[m].settings.scrollThrottle)}});t.bind(p,function(){var t;if(!e.didResize){e.didResize=true;t=function(){n[m]("refresh");return e.didResize=false};return r.setTimeout(t,n[m].settings.resizeThrottle)}})}t.prototype.doScroll=function(){var t,e=this;t={horizontal:{newScroll:this.$element.scrollLeft(),oldScroll:this.oldScroll.x,forward:"right",backward:"left"},vertical:{newScroll:this.$element.scrollTop(),oldScroll:this.oldScroll.y,forward:"down",backward:"up"}};if(c&&(!t.vertical.oldScroll||!t.vertical.newScroll)){n[m]("refresh")}n.each(t,function(t,r){var i,o,l;l=[];o=r.newScroll>r.oldScroll;i=o?r.forward:r.backward;n.each(e.waypoints[t],function(t,e){var n,i;if(r.oldScroll<(n=e.offset)&&n<=r.newScroll){return l.push(e)}else if(r.newScroll<(i=e.offset)&&i<=r.oldScroll){return l.push(e)}});l.sort(function(t,e){return t.offset-e.offset});if(!o){l.reverse()}return n.each(l,function(t,e){if(e.options.continuous||t===l.length-1){return e.trigger([i])}})});return this.oldScroll={x:t.horizontal.newScroll,y:t.vertical.newScroll}};t.prototype.refresh=function(){var t,e,r,i=this;r=n.isWindow(this.element);e=this.$element.offset();this.doScroll();t={horizontal:{contextOffset:r?0:e.left,contextScroll:r?0:this.oldScroll.x,contextDimension:this.$element.width(),oldScroll:this.oldScroll.x,forward:"right",backward:"left",offsetProp:"left"},vertical:{contextOffset:r?0:e.top,contextScroll:r?0:this.oldScroll.y,contextDimension:r?n[m]("viewportHeight"):this.$element.height(),oldScroll:this.oldScroll.y,forward:"down",backward:"up",offsetProp:"top"}};return n.each(t,function(t,e){return n.each(i.waypoints[t],function(t,r){var i,o,l,s,f;i=r.options.offset;l=r.offset;o=n.isWindow(r.element)?0:r.$element.offset()[e.offsetProp];if(n.isFunction(i)){i=i.apply(r.element)}else if(typeof i==="string"){i=parseFloat(i);if(r.options.offset.indexOf("%")>-1){i=Math.ceil(e.contextDimension*i/100)}}r.offset=o-e.contextOffset+e.contextScroll-i;if(r.options.onlyOnScroll&&l!=null||!r.enabled){return}if(l!==null&&l<(s=e.oldScroll)&&s<=r.offset){return r.trigger([e.backward])}else if(l!==null&&l>(f=e.oldScroll)&&f>=r.offset){return r.trigger([e.forward])}else if(l===null&&e.oldScroll>=r.offset){return r.trigger([e.forward])}})})};t.prototype.checkEmpty=function(){if(n.isEmptyObject(this.waypoints.horizontal)&&n.isEmptyObject(this.waypoints.vertical)){this.$element.unbind([p,y].join(" "));return delete a[this.id]}};return t}();l=function(){function t(t,e,r){var i,o;r=n.extend({},n.fn[g].defaults,r);if(r.offset==="bottom-in-view"){r.offset=function(){var t;t=n[m]("viewportHeight");if(!n.isWindow(e.element)){t=e.$element.height()}return t-n(this).outerHeight()}}this.$element=t;this.element=t[0];this.axis=r.horizontal?"horizontal":"vertical";this.callback=r.handler;this.context=e;this.enabled=r.enabled;this.id="waypoints"+v++;this.offset=null;this.options=r;e.waypoints[this.axis][this.id]=this;s[this.axis][this.id]=this;i=(o=t.data(w))!=null?o:[];i.push(this.id);t.data(w,i)}t.prototype.trigger=function(t){if(!this.enabled){return}if(this.callback!=null){this.callback.apply(this.element,t)}if(this.options.triggerOnce){return this.destroy()}};t.prototype.disable=function(){return this.enabled=false};t.prototype.enable=function(){this.context.refresh();return this.enabled=true};t.prototype.destroy=function(){delete s[this.axis][this.id];delete this.context.waypoints[this.axis][this.id];return this.context.checkEmpty()};t.getWaypointsByElement=function(t){var e,r;r=n(t).data(w);if(!r){return[]}e=n.extend({},s.horizontal,s.vertical);return n.map(r,function(t){return e[t]})};return t}();d={init:function(t,e){var r;if(e==null){e={}}if((r=e.handler)==null){e.handler=t}this.each(function(){var t,r,i,s;t=n(this);i=(s=e.context)!=null?s:n.fn[g].defaults.context;if(!n.isWindow(i)){i=t.closest(i)}i=n(i);r=a[i.data(u)];if(!r){r=new o(i)}return new l(t,r,e)});n[m]("refresh");return this},disable:function(){return d._invoke(this,"disable")},enable:function(){return d._invoke(this,"enable")},destroy:function(){return d._invoke(this,"destroy")},prev:function(t,e){return d._traverse.call(this,t,e,function(t,e,n){if(e>0){return t.push(n[e-1])}})},next:function(t,e){return d._traverse.call(this,t,e,function(t,e,n){if(e<n.length-1){return t.push(n[e+1])}})},_traverse:function(t,e,i){var o,l;if(t==null){t="vertical"}if(e==null){e=r}l=h.aggregate(e);o=[];this.each(function(){var e;e=n.inArray(this,l[t]);return i(o,e,l[t])});return this.pushStack(o)},_invoke:function(t,e){t.each(function(){var t;t=l.getWaypointsByElement(this);return n.each(t,function(t,n){n[e]();return true})});return this}};n.fn[g]=function(){var t,r;r=arguments[0],t=2<=arguments.length?e.call(arguments,1):[];if(d[r]){return d[r].apply(this,t)}else if(n.isFunction(r)){return d.init.apply(this,arguments)}else if(n.isPlainObject(r)){return d.init.apply(this,[null,r])}else if(!r){return n.error("jQuery Waypoints needs a callback function or handler option.")}else{return n.error("The "+r+" method does not exist in jQuery Waypoints.")}};n.fn[g].defaults={context:r,continuous:true,enabled:true,horizontal:false,offset:0,triggerOnce:false};h={refresh:function(){return n.each(a,function(t,e){return e.refresh()})},viewportHeight:function(){var t;return(t=r.innerHeight)!=null?t:i.height()},aggregate:function(t){var e,r,i;e=s;if(t){e=(i=a[n(t).data(u)])!=null?i.waypoints:void 0}if(!e){return[]}r={horizontal:[],vertical:[]};n.each(r,function(t,i){n.each(e[t],function(t,e){return i.push(e)});i.sort(function(t,e){return t.offset-e.offset});r[t]=n.map(i,function(t){return t.element});return r[t]=n.unique(r[t])});return r},above:function(t){if(t==null){t=r}return h._filter(t,"vertical",function(t,e){return e.offset<=t.oldScroll.y})},below:function(t){if(t==null){t=r}return h._filter(t,"vertical",function(t,e){return e.offset>t.oldScroll.y})},left:function(t){if(t==null){t=r}return h._filter(t,"horizontal",function(t,e){return e.offset<=t.oldScroll.x})},right:function(t){if(t==null){t=r}return h._filter(t,"horizontal",function(t,e){return e.offset>t.oldScroll.x})},enable:function(){return h._invoke("enable")},disable:function(){return h._invoke("disable")},destroy:function(){return h._invoke("destroy")},extendFn:function(t,e){return d[t]=e},_invoke:function(t){var e;e=n.extend({},s.vertical,s.horizontal);return n.each(e,function(e,n){n[t]();return true})},_filter:function(t,e,r){var i,o;i=a[n(t).data(u)];if(!i){return[]}o=[];n.each(i.waypoints[e],function(t,e){if(r(i,e)){return o.push(e)}});o.sort(function(t,e){return t.offset-e.offset});return n.map(o,function(t){return t.element})}};n[m]=function(){var t,n;n=arguments[0],t=2<=arguments.length?e.call(arguments,1):[];if(h[n]){return h[n].apply(null,t)}else{return h.aggregate.call(null,n)}};n[m].settings={resizeThrottle:100,scrollThrottle:30};return i.load(function(){return n[m]("refresh")})})}).call(this);

;



window.Modernizr = (function( window, document, undefined ) {

    var version = '2.8.3',

    Modernizr = {},


    docElement = document.documentElement,

    mod = 'modernizr',
    modElem = document.createElement(mod),
    mStyle = modElem.style,

    inputElem  ,


    toString = {}.toString,    omPrefixes = 'Webkit Moz O ms',

    cssomPrefixes = omPrefixes.split(' '),

    domPrefixes = omPrefixes.toLowerCase().split(' '),


    tests = {},
    inputs = {},
    attrs = {},

    classes = [],

    slice = classes.slice,

    featureName,



    _hasOwnProperty = ({}).hasOwnProperty, hasOwnProp;

    if ( !is(_hasOwnProperty, 'undefined') && !is(_hasOwnProperty.call, 'undefined') ) {
      hasOwnProp = function (object, property) {
        return _hasOwnProperty.call(object, property);
      };
    }
    else {
      hasOwnProp = function (object, property) {
        return ((property in object) && is(object.constructor.prototype[property], 'undefined'));
      };
    }


    if (!Function.prototype.bind) {
      Function.prototype.bind = function bind(that) {

        var target = this;

        if (typeof target != "function") {
            throw new TypeError();
        }

        var args = slice.call(arguments, 1),
            bound = function () {

            if (this instanceof bound) {

              var F = function(){};
              F.prototype = target.prototype;
              var self = new F();

              var result = target.apply(
                  self,
                  args.concat(slice.call(arguments))
              );
              if (Object(result) === result) {
                  return result;
              }
              return self;

            } else {

              return target.apply(
                  that,
                  args.concat(slice.call(arguments))
              );

            }

        };

        return bound;
      };
    }

    function setCss( str ) {
        mStyle.cssText = str;
    }

    function setCssAll( str1, str2 ) {
        return setCss(prefixes.join(str1 + ';') + ( str2 || '' ));
    }

    function is( obj, type ) {
        return typeof obj === type;
    }

    function contains( str, substr ) {
        return !!~('' + str).indexOf(substr);
    }

    function testProps( props, prefixed ) {
        for ( var i in props ) {
            var prop = props[i];
            if ( !contains(prop, "-") && mStyle[prop] !== undefined ) {
                return prefixed == 'pfx' ? prop : true;
            }
        }
        return false;
    }

    function testDOMProps( props, obj, elem ) {
        for ( var i in props ) {
            var item = obj[props[i]];
            if ( item !== undefined) {

                            if (elem === false) return props[i];

                            if (is(item, 'function')){
                                return item.bind(elem || obj);
                }

                            return item;
            }
        }
        return false;
    }

    function testPropsAll( prop, prefixed, elem ) {

        var ucProp  = prop.charAt(0).toUpperCase() + prop.slice(1),
            props   = (prop + ' ' + cssomPrefixes.join(ucProp + ' ') + ucProp).split(' ');

            if(is(prefixed, "string") || is(prefixed, "undefined")) {
          return testProps(props, prefixed);

            } else {
          props = (prop + ' ' + (domPrefixes).join(ucProp + ' ') + ucProp).split(' ');
          return testDOMProps(props, prefixed, elem);
        }
    }

    tests['csstransitions'] = function() {
        return testPropsAll('transition');
    };



    for ( var feature in tests ) {
        if ( hasOwnProp(tests, feature) ) {
                                    featureName  = feature.toLowerCase();
            Modernizr[featureName] = tests[feature]();

            classes.push((Modernizr[featureName] ? '' : 'no-') + featureName);
        }
    }



     Modernizr.addTest = function ( feature, test ) {
       if ( typeof feature == 'object' ) {
         for ( var key in feature ) {
           if ( hasOwnProp( feature, key ) ) {
             Modernizr.addTest( key, feature[ key ] );
           }
         }
       } else {

         feature = feature.toLowerCase();

         if ( Modernizr[feature] !== undefined ) {
                                              return Modernizr;
         }

         test = typeof test == 'function' ? test() : test;

         if (typeof enableClasses !== "undefined" && enableClasses) {
           docElement.className += ' ' + (test ? '' : 'no-') + feature;
         }
         Modernizr[feature] = test;

       }

       return Modernizr;
     };


    setCss('');
    modElem = inputElem = null;


    Modernizr._version      = version;

    Modernizr._domPrefixes  = domPrefixes;
    Modernizr._cssomPrefixes  = cssomPrefixes;



    Modernizr.testProp      = function(prop){
        return testProps([prop]);
    };

    Modernizr.testAllProps  = testPropsAll;


    Modernizr.prefixed      = function(prop, obj, elem){
      if(!obj) {
        return testPropsAll(prop, 'pfx');
      } else {
            return testPropsAll(prop, obj, elem);
      }
    };



    return Modernizr;

})(this, this.document);
;
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
(function($, gmr) {
	var __ready, gridPreviewLoaded, gridLoadMoreUrl, gridUpdateRating;

	gridUpdateRating = function($item, delta) {
		var rating = parseInt($item.text().replace(/\D+/g, ''));

		if (isNaN(rating)) {
			rating = 0;
		}

		rating += delta;
		rating = rating.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,').replace(/\..*/g, '');

		$item.text(rating);
	};

	gridPreviewLoaded = function(submission) {
		var $previewInner = submission.$previewInner,
			$item = submission.$item,
			$rating = $item.find('.contest__submission--rating b'),
			sync_vote = false;

		// init new gallery
		if ($.fn.cycle) {
			$previewInner.find('.cycle-slideshow').cycle({
				next: '.gallery__next--btn'
			});
		}

		// bind gallery events
		if (GMR_Gallery) {
			GMR_Gallery.bindEvents();
		}

		// bind vote click event
		$previewInner.find('.contest__submission--vote').click(function() {
			var $this = $(this),
				$icon = $this.find('i.fa'),
				classes = $icon.attr('class');

			if (!sync_vote) {
				sync_vote = true;
				$icon.attr('class', 'fa fa-spinner fa-spin');

				$.post(gmr.endpoints.vote, {ugc: $this.data('id')}, function(response) {
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
				$icon = $this.find('i.fa'),
				classes = $icon.attr('class');

			if (!sync_vote) {
				sync_vote = true;
				$icon.attr('class', 'fa fa-spinner fa-spin');

				$.post(gmr.endpoints.unvote, {ugc: $this.data('id')}, function(response) {
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
	};

	gridLoadMoreUrl = function(page) {
		return gmr.endpoints.infinite + (page + 1) + '/';
	};

	__ready = function() {
		var container = $(gmr.selectors.container);

		container.on('submit', gmr.selectors.form, function() {
			var form = $(this);

			if (!form.parsley || form.parsley().isValid()) {
				var form_data = new FormData();
				
				form.find('input').each(function() {
					var input = this;

					if ('file' === input.type) {
						$(this.files).each(function(key, value) {
							form_data.append(input.name, value);
						});
					} else if ('radio' === input.type || 'checkbox' === input.type) {
						if (input.checked) {
							form_data.append(input.name, input.value);
						}
					} else {
						form_data.append(input.name, input.value);
					}
				});

				form.find('textarea, select').each(function() {
					form_data.append(this.name, this.value);
				});

				form.find('input, textarea, select, button').attr('disabled', 'disabled');
				form.find('i.fa').show();

				$.ajax({
					url: gmr.endpoints.submit,
					type: 'post',
					data: form_data,
					processData: false, // Don't process the files
					contentType: false, // Set content type to false as jQuery will tell the server its a query string request
					success: function(data) {
						container.html(data);
					}
				});
			}

			return false;
		});

		container.on('click', gmr.selectors.yes_age, function() {
			container.load(gmr.endpoints.confirm_age);
			return false;
		});
		
		container.on('click', gmr.selectors.no_age, function() {
			container.load(gmr.endpoints.reject_age);
			return false;
		});

		container.load(gmr.endpoints.load);

		$('.contest__submissions--list').grid({
			loadMore: '.contest__submissions--load-more',
			previewLoaded: gridPreviewLoaded,
			loadMoreUrl: gridLoadMoreUrl
		});
	};

	$(document).bind('pjax:end', __ready).ready(__ready);
})(jQuery, GreaterMediaContests);

/**
 * Set up date pickers for browsers without a native control
 */
document.addEventListener("DOMContentLoaded", function () {
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
		jQuery('input[type=date]').datetimepicker(
			{
				timepicker: false,
				format    : 'm/d/Y'
			}
		);

		jQuery('input[type=time]').datetimepicker(
			{
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
			}
		);
	}

}, false );
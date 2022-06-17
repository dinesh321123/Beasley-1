( function ( $, window, document ) {
	var $window = $( window );
	var $document = $( document );

	var getCurrentLocation = function() {
		return window.location.href.split('#')[0].split('?')[0];
	};

	$.fn.refreshBeasleyGalleryAds = function() {
		var $this = $( this );
		var sidebarSlots = [];

		$this.each( function() {
			sidebarSlots.push( $( this ).data( 'slot' ) );
		} );

		if ( sidebarSlots.length && googletag ) {
			setTimeout( function() {
				googletag.pubads().refresh( sidebarSlots );
			}, 500 );
		}

		return $this;
	};

	$.fn.beasleyGallery = function() {
		var $galleries = $( this );

		$galleries.each( function() {
			var $gallery = $( this );
			var $swiperContainer = $gallery.find( '.gallery-top' );
			var $galleryTopSlider = $swiperContainer.find( '.swiper-wrapper' );
			var $sidebar = $gallery.find( '.swiper-sidebar' );
			var $galleryThumbsSlider = $gallery.find( '.gallery-thumbs' );
			var updateHistory = true;

			var positionSidebar = function() {
				var $swiperWrapper = $swiperContainer.find( '.slick-track' );
				var $newActiveSlide = $swiperContainer.find( '.slick-current' );
				var newActiveSlideWidth = $newActiveSlide.width();
				var paddingRight = Math.floor( ( $window.width() - newActiveSlideWidth ) / 2 ) - 385;

				// We want to move the sidebar and add enough padding so that it's always at least 300px wide (exluding padding).
				// In some cases where the image is too wide, we want to apply a negative margin left to pull the image to the left
				// so that the sidebar has enough room.

				if ( ! $swiperContainer.hasClass( 'show-ad' ) && ! $newActiveSlide.hasClass( 'last-slide' ) ) {
					$sidebar.removeClass( 'hidden expand' );
					if ( window.matchMedia( "(min-width: 768px)" ).matches ) {
						if ( paddingRight < 0 ) {
							$swiperWrapper.css( 'marginLeft', paddingRight + 'px' );
							$sidebar.css( 'left', Math.floor( newActiveSlideWidth + paddingRight + ( $window.width() - newActiveSlideWidth ) / 2 ) + 'px' );
						} else {
							$swiperWrapper.css( 'marginLeft', '0px' );
							$sidebar.css( 'left', Math.floor( newActiveSlideWidth + ( $window.width() - newActiveSlideWidth ) / 2 ) + 'px' );
							$sidebar.css( 'paddingRight', paddingRight + 'px' );
						}
					}
				} else {
					// If a centered ad is active, hide the sidebar
					$sidebar.attr( 'style', '' ).addClass( 'hidden' );
				}
			};

			var updateURL = function( slide ) {
				var slideIndex = parseInt( slide + 1, 10 );
				if ( Number.isNaN( slideIndex ) ) {
					return;
				}

				var $newActiveSlide = $swiperContainer.find( '.slick-slide:nth-child(' + slideIndex + ')' );
				// If we're not on an ad slide, update the URL
				if ( ! $newActiveSlide.hasClass( 'meta-spacer' ) ) {
					var slug = $newActiveSlide.attr( 'data-slug' );
					var title = $newActiveSlide.attr( 'data-title' );
					// Save index in state object to navigate back to slide
					var stateObject = { index: slide };

					if ( window.history ) {
						history.pushState( stateObject, title, slug );
					}
				}
			};

			var resetSidebarMargin = function() {
				$swiperContainer.find( '.slick-track' ).css( 'marginLeft', '0px' );
			};

			var updateSidebarInfo = function( slide ) {
				var slideIndex = parseInt( slide + 1, 10 );
				if ( Number.isNaN( slideIndex ) ) {
					return;
				}

				var $newActiveSlide = $swiperContainer.find( '.slick-slide:nth-child(' + slideIndex + ')' );
				if ( $newActiveSlide.hasClass( 'meta-spacer' ) ) {
					return;
				}

				var title = $newActiveSlide.attr( 'data-title' );
				var caption = $newActiveSlide.attr( 'data-caption' );

				$sidebar.find( '.swiper-sidebar-title' ).text( title );
				$sidebar.find( '.swiper-sidebar-caption' ).text( caption );
				$sidebar.find( '.swiper-sidebar-download' ).attr( 'href', $newActiveSlide.attr( 'data-source' ) );

				var shareIndividualPhotos = parseInt( $swiperContainer.attr( 'data-share-photos' ), 10 );
				if ( Number.isNaN( shareIndividualPhotos ) || ! shareIndividualPhotos ) {
					// if we don't need to share individual photos, then we don't need to change share buttons
					return;
				}

				var url = encodeURIComponent( $newActiveSlide.attr( 'data-share' ) );

				$sidebar.find( '.social__link.icon-facebook' ).attr( 'href', 'https://www.facebook.com/sharer/sharer.php?u=' + url + '&title=' + encodeURIComponent( title ) );
				$sidebar.find( '.social__link.icon-twitter' ).attr( 'href', 'https://twitter.com/home?status=' + encodeURIComponent( title ) + '+' + url );
				$sidebar.find( '.social__link.icon-google-plus' ).attr( 'href', 'https://plus.google.com/share?url=' + url );
			};

			var maybeShowCenteredAd = function( newSlide ) {
				var slideIndex = parseInt( newSlide + 1, 10 );
				if ( Number.isNaN( slideIndex ) ) {
					return;
				}

				var $newActiveSlide = $swiperContainer.find( '.slick-slide:nth-child(' + slideIndex + ')' );
				if ( $newActiveSlide.hasClass( 'meta-spacer' ) ) {
					$sidebar.addClass( 'hidden' );
					$swiperContainer.addClass( 'show-ad' );
					$gallery.find( '.swiper-sidebar-meta .gmr-ad' ).refreshBeasleyGalleryAds();
				} else {
					if ( $swiperContainer.hasClass( 'show-ad' ) ) {
						$gallery.find( '.swiper-meta-inner .gmr-ad' ).refreshBeasleyGalleryAds();
					}

					$sidebar.removeClass( 'hidden' );
					$swiperContainer.removeClass( 'show-ad' );
				}
			};

			var maybeHideSidebar = function( newSlide ) {
				var slideIndex = parseInt( newSlide + 1, 10 );
				if ( Number.isNaN( slideIndex ) ) {
					return;
				}

				var $newActiveSlide = $swiperContainer.find( '.slick-slide:nth-child(' + slideIndex + ')' );
				if ( $newActiveSlide.hasClass( 'last-slide' ) ) {
					$sidebar.addClass( 'hidden' );
					$galleryThumbsSlider.addClass( 'hidden' );
				} else {
					$sidebar.removeClass( 'hidden' );
					$galleryThumbsSlider.removeClass( 'hidden' );
				}
			};

			var reposition = function() {
				resetSidebarMargin();
				positionSidebar();
			};

			var updateCurrentSlide = function() {
				var $slide = $swiperContainer.find( '.swiper-slide[data-slug="' + getCurrentLocation() + '"]' );
				if ( $slide && $slide.length ) {
					var slideIndex = parseInt( $slide.attr( 'data-index' ), 10 );
					if ( ! Number.isNaN( slideIndex ) ) {
						$galleryTopSlider.slick( 'slickGoTo', slideIndex, true );
					}
				}
			};

			// Expand sidebar on mobile
			$gallery.find( '.swiper-sidebar-expand' ).click( function( e ) {
				e.preventDefault();
				$sidebar.toggleClass( 'expand' );
				return false;
			} );

			// Fullscreen
			$gallery.find( '.swiper-sidebar-fullscreen' ).click( function( e ) {
				e.preventDefault();
				$swiperContainer.toggleClass( 'fullscreen' );

				setTimeout( function() {
					$galleryTopSlider.slick( 'setPosition' );
					reposition();
				}, 400 );
			} );

			// Go to the correct slide if users press back button
			$window.on( 'popstate', function( event ) {
				updateHistory = false;
				updateCurrentSlide();
				updateHistory = true;
			} );

			var galleryInitialIndex = 0;
			var $initialSlide = $swiperContainer.find( '.swiper-slide[data-slug="' + getCurrentLocation() + '"]' );
			if ( $initialSlide && $initialSlide.length ) {
				galleryInitialIndex = parseInt( $initialSlide.attr( 'data-index' ), 10 );
				if ( Number.isNaN( galleryInitialIndex ) ) {
					galleryInitialIndex = 0;
				}
			}

			$galleryTopSlider.on( 'init', function( event, slick ) {
				positionSidebar();
				updateSidebarInfo( galleryInitialIndex );
				$swiperContainer.removeClass( 'loading' );
			} );

			$galleryThumbsSlider.on( 'init', function( event, slick ) {
				// We want to know which thumb is an ad spacer.
				slick.$slides.each( function() {
					if ( $( this ).find( '.meta-spacer' ).length ) {
						$( this ).addClass( 'is-meta' );
					}
				} );

				$galleryThumbsSlider.removeClass( 'loading' );
			} );

			$galleryTopSlider.slick( {
				infinite: false,
				speed: 300,
				slidesToShow: 1,
				centerMode: true,
				variableWidth: true,
				adaptiveHeight: true,
				asNavFor: '.gallery-thumbs',
				arrows: true,
				prevArrow: '<button type="button" class="slick-prev"><span class="icon-arrow-prev"></span></button>',
				nextArrow: '<button type="button" class="slick-next"><span class="icon-arrow-next"></span></button>',
				initialSlide: galleryInitialIndex,
				responsive: [
					{
						breakpoint: 767,
						settings: {
							variableWidth: false,
							centerMode: false,
						}
					}
				]
			} );

			$galleryThumbsSlider.slick( {
				infinite: false,
				speed: 300,
				slidesToShow: 10,
				centerMode: true,
				focusOnSelect: true,
				variableWidth: true,
				asNavFor: '.gallery-top .swiper-wrapper',
				arrows: false,
				initialSlide: galleryInitialIndex
			} );

			$galleryTopSlider.on( 'beforeChange', function( event, slick, currentSlide, nextSlide ) {
				resetSidebarMargin();
				maybeShowCenteredAd( nextSlide );
				maybeHideSidebar( nextSlide );
			} );

			$galleryTopSlider.on( 'afterChange', function( event, slick, currentSlide ) {
				// 300ms is the global animation speed
				positionSidebar();

				if ( updateHistory ) {
					updateURL( currentSlide );
				}

				updateSidebarInfo( currentSlide );
			} );

			$window.on( 'resize', _.debounce( reposition, 300 ) );
		} );

		return $galleries;
	};

	var __ready = function() {
		$( '.gallery' ).beasleyGallery();
	};

	$window.load( __ready );
	$document.bind( 'pjax:end', _.debounce( __ready, 300 ) );
} )( jQuery, window, document );

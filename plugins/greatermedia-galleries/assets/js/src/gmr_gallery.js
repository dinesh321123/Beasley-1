(function ($, window, undefined) {
	"use strict";

	var document = window.document,
		$document = $(document),
		$window = $(window),
		__ready;

	__ready = function() {
		$document.find( '.gallery' ).each( function () {
			var $gallery = $( this ),
				gallery = $gallery.get(),
				$main = $gallery.find( '.gallery__slide--images' ),
				main = $main.get(0),
				$main_wrapper = $gallery.find( '.gallery__slides' ),
				slideshow = $gallery.find( '.gallery__slide--images.cycle-slideshow' ),
				$caption = $gallery.find( '.gallery__content' ),
				$sidebar = $gallery.find( '.gallery__thumbnails' ),
				$slide_paging = $gallery.find( '.gallery__paging' ),
				$slide_paging_previews = $gallery.find( '.gallery__previews' ),
				$single_thumbnail = $gallery.find( '.gallery__previews div div' );
	
			/**
			 * Bind the gallery full screen toggle
			 */
			function bind_events() {
				var hashChange = false;
	
				slideshow = $gallery.find( '.gallery__slide--images.cycle-slideshow' );
				$slide_paging_previews = $gallery.find( '.gallery__previews' );
	
				/**
				 * Make sure thumbnails are updated before the slideshow cycles.
				 */
				slideshow.on( 'cycle-before', function ( event, optionHash ) {
					update_thumbnails(optionHash.nextSlide); // nextSlide = incoming slide. could be backward
					//$caption.cycle( 'goto', optionHash.nextSlide );
				} );

				// Record page view
				slideshow.on('cycle-after', function () {
					// track page views
					if ("function" === typeof(ga)) {
						ga('send', 'pageview');
					} else if ("object" === typeof(_gaq) ) {
						// Older google analytics
						_gaq.push(['_trackPageview']);
					}
				});
	
				/**
				 * Wire up additional events after the slideshow has fully initialized.
				 */
				slideshow.on( 'cycle-update-view', function( event, optionHash ) {
					update_thumbnails( optionHash.currSlide );
				} );
	
				/**
				 * On mobile, regroup thumbnails on page load
				 */
				slideshow.on( 'cycle-initialized', function( e, opts ) {
					responsive_thumbnails();
				} );
	
				/**
				 * Make sure the preview buttons transition the slideshow.
				 */
				$slide_paging_previews.on( 'click', '.cycle-slide div', function () {
					var $this = $( this ),
						index = $this.data( 'cycle-index' );
	
					slideshow.cycle( 'goto', index );
					$caption.cycle( 'goto', index );
				} );
	
				// Make sure we disable other hashchange events that attempt to capture manual hash changes.
				$document.on( 'cycle-pre-initialize', function( e, opts ) {
					$( window ).off( 'hashchange', opts._onHashChange);
				});
	
				// Rebind Captions - Code taken directly from jquery2.cycle.caption.js (with certain code standard updates)
				$document.on( 'cycle-update-view', function ( e, opts, slideOpts, currSlide ) {
					if ( opts.captionModule !== 'caption' ) {
						return;
					}
	
					var el;
					$.each( ['caption', 'overlay'], function () {
						var name = this,
							template = slideOpts[name + 'Template'],
							el = opts.API.getComponent( name );
	
						if ( el.length && template ) {
							el.html( opts.API.tmpl( template, slideOpts, opts, currSlide ) );
							el.show();
						}
						else {
							el.hide();
						}
					} );
				} );
	
				$window.resize( responsive_thumbnails );
			}
	
			/**
			 * Are we on a mobile browser (or anything smaller than 768px)?
			 * @returns bool
			 */
			function isMobile() {
				return $gallery.hasClass( 'ismobile' );
			}
	
			function isTablet() {
				return $gallery.hasClass( 'istablet' );
			}
	
			/**
			 * Regroup the thumbnails in mobile to fit the screen
			 */
			function responsive_thumbnails() {
				// If we're moving from mobile to normal, shift things around
				if ( $window.width() >= 768 && isMobile() ) {
					$gallery.removeClass( 'ismobile' );
					$sidebar.append( $caption );
					regroup_thumbnails( get_thumbs_per_page() );
					update_thumbnails( $main.data( "cycle.opts" ).currSlide );
	
					$main.css( 'height', '' );
					$main_wrapper.css( 'height', '' );
					$gallery.find( '.gallery__previews, .gallery__previews--group' ).css( 'height', '' );
					return;
				}
	
				// If we're on a small screen but ismobile is not set, shift things around
				if ( $window.width() < 480 && ! isMobile() ) {
					$gallery.addClass( 'ismobile' );
					regroup_thumbnails( get_thumbs_per_page() );
					update_thumbnails( $main.data( "cycle.opts" ).currSlide );
				}
	
				// If the window is being resized, adjust the thumbnail and main image height
				if ( isMobile() ) {
					var main_height, thumb_height;
					$main.css( 'height', ( main_height - 20 ) + 'px' );
					$main_wrapper.css( 'height', main_height );
	
					thumb_height = $single_thumbnail.width();
					$gallery.find( '.gallery__previews, .gallery__previews--group' ).css( 'height', thumb_height + 'px' );
				}
	
				if ( $window.width() >= 480 && $window.width() < 769 && ! isTablet() ) {
					$gallery.addClass( 'istablet' );
					regroup_thumbnails( get_thumbs_per_page() );
					update_thumbnails( $main.data( "cycle.opts" ).currSlide );
				}
	
			}
	
			/**
			 * Regroup the thumbnails in paged divs
			 *
			 * This is used when going between widescreen and other views, because the
			 * widescreen view can only hold 8 thumbnails per page while the other views
			 * can hold 15 per page.
			 *
			 * @param number_in_group
			 */
			function regroup_thumbnails( number_in_group ) {
				var $thumbnails_group = $gallery.find( '.gallery__previews--group' );
	
				$thumbnails_group.children( 'div' ).appendTo( '.gallery__previews' );
				$thumbnails_group.remove();
				$gallery.find( '.gallery__previews div' ).each( function() {
					var $this = $( this );
					if ( undefined === $this.attr( 'id' ) ) {
						$this.remove();
					}
				} );
				$slide_paging_previews
					.each( function () {
						var divs = $( 'div', this );
						for ( var i = 0; i < divs.length; i += number_in_group ) {
							divs.slice( i, i + number_in_group ).wrapAll( '<div class="gallery__previews--group"></div>' );
						}
					} )
					.cycle( 'reinit' );
			}
	
			/**
			 * Update the slide thumbnails to we're viewing the correct group.
			 *
			 * @param {Number} selected_slide
			 */
			function update_thumbnails( selected_slide ) {
				var slides_per_page = get_thumbs_per_page();
	
				$gallery.find( '.gallery__slide--active' ).removeClass( 'gallery__slide--active' );
				$gallery.find( '#preview-' + selected_slide ).addClass( 'gallery__slide--active' );
				var selected_slide_group = Math.floor( selected_slide / slides_per_page );
				$slide_paging_previews.cycle( 'goto', selected_slide_group );
			}
	
			function get_thumbs_per_page() {
				var slides_per_page = 8;
				if ( isMobile() ) {
					slides_per_page = 3;
				}
				if ( isTablet() ) {
					slides_per_page = 5;
				}
				return slides_per_page;
			}
	
			/**
			 * Update sharing links and short URL in sharing overlay
			 */
			function update_share_urls() {
				var share_url, share_title;
				if ( $gallery.find( '#share-image' ).is( ':checked' ) ) {
					share_url   = $gallery.find( 'input.slide-url' ).val();
					share_title = $gallery.find( 'input.slide-title' ).val();
				} else {
					share_url   = $gallery.find( 'input.gallery-url' ).val();
					share_title = $gallery.find( 'input.gallery-title' ).val();
				}
				var url_twitter  = 'http://twitter.com/home?status=' + share_url + '%20-%20' + share_title;
				var url_facebook = 'http://www.facebook.com/sharer.php?u=' + share_url + '&amp;t=' + share_title;
				var url_linkedin = '#';
	
				$gallery.find( '.gallery-toolbar .fa-twitter' ).attr( 'href', url_twitter );
				$gallery.find( '.gallery-toolbar .fa-facebook' ).attr( 'href', url_facebook );
				$gallery.find( '.gallery-toolbar .short-url' ).html( '<a href="' + share_url + '">' + share_url + '</a>' );
			}
	
			bind_events();
		} ); 
	};

	$document.bind('pjax:end', function() {
		$document.find('.cycle-slideshow').cycle();
		__ready();
	});

	$document.bind('contest:preview-loaded', __ready);
	
	$document.ready(__ready);
})(jQuery, window);

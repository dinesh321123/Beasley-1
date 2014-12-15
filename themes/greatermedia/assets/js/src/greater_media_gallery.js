(function ( $, window, undefined ) {
	"use strict";

	var document = window.document,
		$document = $( document ),
		$window = $( window ),
		slideshow = $( '.gallery__slide--images.cycle-slideshow' ),
		gallery = document.querySelectorAll( '.gallery' ),
		main = document.querySelector( '.gallery__slide--images' ),
		$gallery = $( gallery ),
		$main = $( main ),
		$main_wrapper = $( '.gallery__slides' ),
		$caption = $( '.caption' ),
		$sidebar = $( '.gallery__thumbnails' ),
		$slide_paging = $( '.slide-paging' ),
		$slide_paging_previews = $( '.slide-paging-previews' ),
		$toolbar_thumbnails = $( '.toolbar-thumbnails' ),
		$single_thumbnail = $( '.slide-paging-previews div div' );

	/**
	 * Bind the gallery full screen toggle
	 */
	function bind_events() {
		var hashChange = false;

		/**
		 * Make sure thumbnails are updated before the slideshow cycles.
		 */
		slideshow.on( 'cycle-before', function ( event, optionHash ) {
			update_thumbnails( optionHash.nextSlide );
			$caption.cycle( 'goto', optionHash.nextSlide );
		} );

		/**
		 * Wire up additional events after the slideshow has fully initialized.
		 */
		slideshow.on( 'cycle-update-view', function( event, optionHash ) {
			update_thumbnails( optionHash.currSlide );
		} );

		/**
		 * Update slide sharing URL and title (hidden values) when slide changes,
		 * then update sharing links
		 */
		slideshow.on( 'cycle-update-view', function( event, optionHash, slideOptionsHash, currentSlideEl ) {
			$( 'input.slide-url' ).val( slideOptionsHash.slide_shorturl );
			$( 'input.slide-title' ).val( slideOptionsHash.slide_title );
			update_share_urls( slideOptionsHash.slide_shorturl, slideOptionsHash.slide_title );
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
			$( '.slide-paging-previews, .slide-previews-group' ).css( 'height', '' );
			return;
		}

		// If we're on a small screen but ismobile is not set, shift things around
		if ( $window.width() < 768 && ! isMobile() ) {
			$gallery.addClass( 'ismobile' );
			$sidebar.prepend( $caption );
			regroup_thumbnails( get_thumbs_per_page() );
			update_thumbnails( $main.data( "cycle.opts" ).currSlide );
		}

		// If the window is being resized, adjust the thumbnail and main image height
		if ( isMobile() ) {
			var main_height, thumb_height;
			$main.css( 'height', ( main_height - 20 ) + 'px' );
			$main_wrapper.css( 'height', main_height );

			thumb_height = $single_thumbnail.width();
			$( '.slide-paging-previews, .slide-previews-group' ).css( 'height', thumb_height + 'px' );
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
		var $thumbnails_group = $( '.slide-previews-group' );

		$thumbnails_group.children( 'div' ).appendTo( '.slide-paging-previews' );
		$thumbnails_group.remove();
		$( '.slide-paging-previews div' ).each( function() {
			var $this = $( this );
			if ( undefined == $this.attr( 'id' ) ) {
				$this.remove();
			}
		} );
		$slide_paging_previews
			.each( function () {
				var divs = $( 'div', this );
				for ( var i = 0; i < divs.length; i += number_in_group ) {
					divs.slice( i, i + number_in_group ).wrapAll( '<div class="slide-previews-group"></div>' );
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

		$( '.gallery__slide--active' ).removeClass( 'gallery__slide--active' );
		$( '#preview-' + selected_slide ).addClass( 'gallery__slide--active' );
		var selected_slide_group = Math.floor( selected_slide / slides_per_page );
		$slide_paging_previews.cycle( 'goto', selected_slide_group );
	}

	function get_thumbs_per_page() {
		var slides_per_page = 8;
		if ( isMobile() ) {
			slides_per_page = 3;
		}
		return slides_per_page;
	}

	/**
	 * Update sharing links and short URL in sharing overlay
	 */
	function update_share_urls() {
		var share_url, share_title;
		if ( $( '#share-image' ).is( ':checked' ) ) {
			share_url   = $( 'input.slide-url' ).val();
			share_title = $( 'input.slide-title' ).val();
		} else {
			share_url   = $( 'input.gallery-url' ).val();
			share_title = $( 'input.gallery-title' ).val();
		}
		var url_twitter  = 'http://twitter.com/home?status=' + share_url + '%20-%20' + share_title;
		var url_facebook = 'http://www.facebook.com/sharer.php?u=' + share_url + '&amp;t=' + share_title;
		var url_linkedin = 'http://www.linkedin.com/shareArticle?mini=true&url=' + share_url + '&title=' + share_title + '&source=World+Economic+Forum';

		$( '.gallery-toolbar .fa-twitter' ).attr( 'href', url_twitter );
		$( '.gallery-toolbar .fa-facebook' ).attr( 'href', url_facebook );
		$( '.gallery-toolbar .fa-linkedin' ).attr( 'href', url_linkedin );
		$( '.gallery-toolbar .short-url' ).html( '<a href="' + share_url + '">' + share_url + '</a>' );
	}

	bind_events();

	// Some galleries are set to be in widescreen by default
	if ( isWidescreen() ) {
		expand_wide();
	}

	window.GMR_Gallery = {
		isFullscreen: isFullscreen,
		expand      : expand_full,
		collapse    : collapse_full
	};

})( jQuery, window );

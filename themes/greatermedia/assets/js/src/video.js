( function( $, config ) {
	var $document = $( document );

	var startEvent = 'click';
	if ( navigator.userAgent.match( /iPhone/i ) || navigator.userAgent.match( /iPad/i ) || navigator.userAgent.match( /Android/i ) ) {
		startEvent = 'touchend';
	}

	var livestreamVideo = function( el ) {
		var $this = $( el );
		var $parent = $this.parents( '.livestream-oembed' );

		var id = el.id;
		var player = videojs( el, { techOrder: ['flash', 'html5'] } );
		var videoArgs = {
			src: $this.data( 'src' ),
			type: 'application/x-mpegURL',
			withCredentials: true
		};

		player.src( videoArgs );
		player.hlsQualitySelector();

		var adTagUrl = $parent.data( 'adTag' );
		if ( adTagUrl ) {
			if ( adTagUrl.indexOf( 'sz=' ) < 1 ) {
				adTagUrl += '&sz=' + el.offsetWidth + 'x' + el.offsetHeight;
			}

			if ( adTagUrl.indexOf( 'url=' ) < 1 ) {
				adTagUrl += '&url=' + encodeURIComponent( window.location.href );
			}

			if ( adTagUrl.indexOf( 'description_url=' ) < 1 ) {
				adTagUrl += '&description_url=' + encodeURIComponent( window.location.href );
			}

			player.ima( {
				id: id,
				adTagUrl: adTagUrl,
				showCountdown: true
			} );

			var wrapper = document.getElementById( id );
			if ( wrapper ) {
				// Initialize the ad container when the video player is clicked, but only the
				// first time it's clicked.
				var initAdDisplayContainer = function() {
					player.ima.initializeAdDisplayContainer();
					wrapper.removeEventListener( startEvent, initAdDisplayContainer );
				};

				wrapper.addEventListener( startEvent, initAdDisplayContainer );
			}
		}
	}

	var __ready = function() {
		$( '.livestream-oembed .video-js[data-src]' ).each( function() {
			try {
				livestreamVideo( this );
			} catch( err ) {
				console.error( err );
			}
		} );
	};

	$document.ready( __ready );
	$document.bind( 'pjax:end', _.debounce( __ready, 300 ) );
} )( jQuery );

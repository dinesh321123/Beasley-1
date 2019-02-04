( function( $, config ) {
	var $document = $( document );

	var startEvent = 'click';
	if ( navigator.userAgent.match( /iPhone/i ) || navigator.userAgent.match( /iPad/i ) || navigator.userAgent.match( /Android/i ) ) {
		startEvent = 'touchend';
	}

	var detectIE = function() {
		var ua = window.navigator.userAgent;
	
		var msie = ua.indexOf('MSIE ');
		if (msie > 0) {
			// IE 10 or older => return version number
			return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
		}
	
		var trident = ua.indexOf('Trident/');
		if (trident > 0) {
			// IE 11 => return version number
			var rv = ua.indexOf('rv:');
			return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
		}
	
		var edge = ua.indexOf('Edge/');
		if (edge > 0) {
			// Edge (IE 12+) => return version number
			return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
		}
	
		// other browser
		return false;
	}

	console.log( 'DETECT IE: ', detectIE() );

	var livestreamVideo = function( el ) {
		var $this = $( el );
		var $parent = $this.parents( '.livestream-oembed' );

		var videojsOptions = detectIE()
			? { techOrder: ['flash', 'html5'] }
			: {};

		console.log( 'videojs options: ', videojsOptions );

		var id = el.id;
		var player = videojs( el, videojsOptions );
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

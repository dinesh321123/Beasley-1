( function( $ ) {

	$( document ).ready( function() {
		$.fn.isInViewport = function () {
			let elementTop = $(this).offset().top;
			let elementBottom = elementTop + $(this).outerHeight();
			let viewportTop = $(window).scrollTop();
			let viewportBottom = viewportTop + $(window).height();
			return elementBottom > viewportTop && elementTop < viewportBottom;
		};

		$(window).scroll(function () {
			let jacappsGaInfoTrack = $('.jacapps-ga-info.track');
			let jacappsGaInfo =  $('.jacapps-ga-info');
			if (jacappsGaInfoTrack.length) {
				if (jacappsGaInfoTrack.isInViewport()) {
					var GaLocation = jacappsGaInfoTrack.attr('data-location');
					console.log('Page View URL', GaLocation);

					if(!GaInfoForJacapps.google_analytics){
						console.log('Please config google analytics.');
						return;
					}
					if(GaLocation) {
						console.log('GA send for URL', GaLocation);
						ga('create', GaInfoForJacapps.google_analytics, 'auto');
						ga('set', 'location', GaLocation);
						ga('send', 'pageview');
					}
				}
			}
			if(jacappsGaInfo.length) {
				jacappsGaInfo.each(function () {
					if ($(this).isInViewport()) {
						$(this).removeClass('track');
					}
				});
			}
		});
	} );

} )( jQuery );

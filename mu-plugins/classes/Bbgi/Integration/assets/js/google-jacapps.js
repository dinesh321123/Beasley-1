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
			let jacappsGaInfoTrack = $('.common-mobile-ga-info.track');
			let jacappsGaInfo =  $('.common-mobile-ga-info');
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
						window.beasleyanalytics.createAnalytics(GaInfoForJacapps.google_analytics, 'auto');
						window.beasleyanalytics.setAnalytics('location', GaLocation);
						window.beasleyanalytics.sendEvent('pageview');
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

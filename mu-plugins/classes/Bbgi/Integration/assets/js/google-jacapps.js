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

					var GaEmbedAuthor = jacappsGaInfoTrack.attr('data-embed-author');
					console.log('Page View Embed Author', GaEmbedAuthor);

					if(!GaInfoForJacapps.google_analytics || !GaInfoForJacapps.google_author_dimension){
						console.log('Please config google analytics.');
						return;
					}
					if(GaLocation || GaEmbedAuthor) {
						let setFlag = false;
						ga('create', GaInfoForJacapps.google_analytics, 'auto');
						if(GaLocation) {
							setFlag = true;
							ga('set', 'location', GaLocation);
							console.log('GA send for URL', GaLocation);
						}
						if(GaEmbedAuthor && GaInfoForJacapps.google_author_dimension) {
							setFlag = true;
							const dimensionKey = 'dimension'+GaInfoForJacapps.google_author_dimension;
							ga('set', dimensionKey, GaEmbedAuthor);
							console.log('GA send for Embed Author', GaEmbedAuthor);
						}
						if(setFlag) {
							ga('send', 'pageview');
						}
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
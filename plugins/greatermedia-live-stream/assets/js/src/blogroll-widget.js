(function ($, gmr) {
	var refresh_widget, active_stream;

	refresh_widget = function() {
		$.ajax(gmr.ajaxurl + active_stream + '/').done(function(response) {
			$('.widget_gmr_blogroll_widget').html(response);
		});
	};

	$(document).ready(function () {
		active_stream = $('.live-player__stream--current-name').text();
		window.setInterval(refresh_widget, gmr.interval);
	});

	$(document).bind('live-player-stream-changed', function(e) {
		active_stream = e.originalEvent.detail;
		refresh_widget();
	});
})(jQuery, gmr_blogroll_widget);
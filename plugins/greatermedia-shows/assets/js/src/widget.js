/*global jQuery, document, gmrs, window */
(function ($, gmrs) {
	var refresh_widget, active_stream;

	refresh_widget = function() {
		$.ajax({
			url: gmrs.ajaxurl,
			data: {stream: active_stream}
		}).done(function(response) {
			$('.widget_gmr_shows_widget').html(response);
		});
	};

	$(document).ready(function () {
		window.setInterval(refresh_widget, gmrs.interval);
	});

	$(document).bind('live-player-stream-changed', function(e) {
		active_stream = e.originalEvent.detail;
		refresh_widget();
	});
})(jQuery, gmrs);
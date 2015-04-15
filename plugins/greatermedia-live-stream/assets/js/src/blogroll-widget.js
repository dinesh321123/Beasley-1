(function ($, gmr) {
	var $document = $(document), refresh_widget, active_stream;

	refresh_widget = function() {
		$.ajax(gmr.ajaxurl + active_stream + '/').done(function(response) {
			$('.widget_gmr_blogroll_widget').html(response);
			$document.trigger('blogroll-widget-updated');
		});
	};

	$document.ready(function () {
		var curStream = $('.live-player__stream--current-name');
		var primaryStream = gmr.primary;

		if (curStream.length > 0) {
			active_stream = curStream.text();
		} else {
			active_stream = primaryStream;
		}
		window.setInterval(refresh_widget, gmr.interval);
	});

	$document.bind('live-player-stream-changed', function(e) {
		active_stream = e.originalEvent.detail;
		refresh_widget();
	});
})(jQuery, gmr_blogroll_widget);
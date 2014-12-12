/*global jQuery, document, gmrs, window */
(function ($, gmrs) {
	$(document).ready(function () {
		window.setInterval(function() {
			$.ajax(gmrs.ajaxurl).done(function(response) {
				$('.widget_gmr_shows_widget').html(response);
			});
		}, gmrs.interval);
	});
})(jQuery, gmrs);
/*! GreaterMedia Shows - v1.0.0
 * http://wordpress.org/plugins
 * Copyright (c) 2014; * Licensed GPLv2+ */
(function ($, gmrs) {
	$(document).ready(function () {
		window.setInterval(function() {
			$.ajax(gmrs.ajaxurl).done(function(response) {
				$('.widget_gmr_shows_widget').html(response);
			});
		}, gmrs.interval);
	});
})(jQuery, gmrs);
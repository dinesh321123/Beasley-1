/*! GreaterMedia Live Links - v1.0.0
 * http://wordpress.org/plugins
 * Copyright (c) 2015; * Licensed GPLv2+ */
(function($, window, document, live_links) {
	$(document).ready(function() {
		$('#wp-admin-bar-add-live-link').click(function() {
			var location = document.location,
				encode = encodeURIComponent,
				url = live_links.url + '&u=' + encode(location.href) + '&t=' + encode(document.title) + '&v=4',
				open_window;

			open_window = function () {
				var width = 720,
					height = 250,
					left = (screen.width/2)-(width/2),
					top = (screen.height/2)-(height/2);

				if (!window.open(url, 't', 'toolbar=0,resizable=1,copyhistory=0,scrollbars=1,status=1,width=' + width + ',height=' + height + ',top=' + top + ',left=' + left)) {
					location.href = url;
				}
			};

			if (/Firefox/.test(navigator.userAgent)) {
				setTimeout(open_window, 0);
			} else {
				open_window();
			}

			return false;
		});
	});
})(jQuery, window, document, live_links);
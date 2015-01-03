/* globals gmr_show:false */
(function ($, gmr) {
	$(document).ready(function () {
		var paged = 1,
			sync = false;
		
		$('#show__live-links--more').click(function() {
			var $link = $(this);

			if (!sync) {
				sync = true;

				$link.find('i').show();
				
				$.get(gmr.ajaxurl, {page: ++paged}, function(data) {
					data = $.trim(data);

					if (data.length > 0) {
						$link.parent().find('ul').append(data);
						sync = false;
						$link.find('i').hide();
					} else {
						$link.hide();
					}
				});
			}
			
			return false;
		});
	});
})(jQuery, gmr_show);
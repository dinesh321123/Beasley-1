(function ($) {
	// we don't need to use pjax:end event here
	$(document).ready(function() {
		var $onair = $('#on-air'),
			schedule = [],
			fallback = '',
			current_show = {},
			track_schedule, update_onair;

		if ($onair.length == 0) {
			return;
		}

		update_onair = function(title, show) {
			$onair.find('.on-air__title').text(title);
			$onair.find('.on-air__show').text(show);
		};

		track_schedule = function() {
			var now = new Date(),
				next = new Date(now.getTime() + 10 * 60 * 1000), // 10 minutes later
				found = false,
				starts, ends;

			for (var i = 0; i < schedule.length; i++) {
				starts = new Date(schedule[i].starts * 1000);
				ends = new Date(schedule[i].ends * 1000);
				
				if (starts <= now && now <= ends) {
					current_show = schedule[i];
					update_onair('On Air:', schedule[i].title);
					found = true;
				}

				if (starts <= next && next <= ends && schedule[i].title != current_show.title) {
					update_onair('Up Next:', schedule[i].title);
					found = true;
				}
			}

			if (!found) {
				update_onair('', fallback);
			}
		};
		
		$.get($onair.data('endpoint'), function(response) {
			if (response.success && response.data) {
				fallback = response.data.tagline || '';
				if ($.isArray(schedule)) {
					schedule = response.data.schedule;
				}
				
				track_schedule();
				setInterval(track_schedule, 1000);
			}
		});
	});
})(jQuery);

(function ($) {
	var $window = $(window);

	var __ready = function() {
		var $days = $('.shows__schedule--day'),
			header_bottom = $('#wpadminbar').outerHeight(),
			on_scroll;

		on_scroll = function() {
			var scroll_top = $window.scrollTop();
			
			$days.each(function() {
				var $day = $(this),
					$weekday = $day.find('.shows__schedule--dayofweek'),
					day_top = $day.offset().top,
					day_left = $day.offset().left,
					day_bottom = $day.height() + $day.offset().top,
					own_height = $weekday.height(),
					top;

				if (scroll_top + header_bottom >= day_top) {
					$day.addClass('fixed');

					top = scroll_top + header_bottom + own_height >= day_bottom
						? day_bottom - scroll_top - own_height
						: header_bottom;

					$weekday.width($day.width()).css({
						top: top + 'px',
						left: day_left + 'px'
					});
				} else {
					$day.removeClass('fixed');
					$weekday.width('auto').css({
						top: '0px',
						left: '0px'
					});
				}
			});
		};

		$window.resize(on_scroll);
		$window.scroll(on_scroll);

		on_scroll();
	};

	$(document).bind('pjax:end', __ready).ready(__ready);
})(jQuery);
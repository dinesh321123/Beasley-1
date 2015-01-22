(function ($) {
	var __ready = function () {
		var container = $('#survey-form');

		var showRestriction = function(restriction) {
			var $restrictions = $('.contest__restrictions');

			$restrictions.attr('class', 'contest__restrictions');
			if (restriction) {
				$restrictions.addClass(restriction);
			}
		};
		
		var loadContainerState = function(url) {
			$.get(url, function(response) {
				var restriction = null;

				if (response.success) {
					container.html(response.data.html);
					$('.type-survey.collapsed').removeClass('collapsed');
				} else {
					restriction = response.data.restriction;
				}

				showRestriction(restriction);
			});
		};
		
		container.on('submit', 'form', function() {
			var form = $(this),
				iframe;

			if (!form.parsley || form.parsley().isValid()) {
				form.find('input, textarea, select, button').attr('readonly', 'readonly');
				form.find('i.fa').show();

				iframe = document.getElementById('theiframe');
				iframe.onload = function() {
					var iframe_document = iframe.contentDocument || iframe.contentWindow.document,
						iframe_body = iframe_document.getElementsByTagName('body')[0],
						scroll_to = container.offset().top - $('#wpadminbar').height() - 10;

					container.html(iframe_body.innerHTML);
					$('html, body').animate({scrollTop: scroll_to}, 200);
				};

				return true;
			}

			return false;
		});
		
		if (container.length > 0) {
			loadContainerState(container.data('load'));
		}
	};

	$(document).bind('pjax:end', __ready).ready(__ready);
})(jQuery);

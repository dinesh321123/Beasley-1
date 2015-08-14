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
				iframe, iframe_onload;

			if (!form.parsley || form.parsley().isValid()) {
				form.find('input, textarea, select, button').attr('readonly', 'readonly');
				form.find('i.gmr-icon').show();

				iframe_onload = function() {
					var iframe_document = iframe.contentDocument || iframe.contentWindow.document,
						iframe_body = iframe_document.getElementsByTagName('body')[0],
						scroll_to = container.offset().top - $('#wpadminbar').height() - 10;

					iframe_body = $.trim(iframe_body.innerHTML);
					if (iframe_body.length > 0) {
						container.html(iframe_body);
					} else {
						alert('Your submission failed. Please, enter required fields and try again.');
						form.find('input, textarea, select, button').removeAttr('readonly');
						form.find('i.gmr-icon').hide();
					}

					$('html, body').animate({scrollTop: scroll_to}, 200);
				};

				iframe = document.getElementById('theiframe');
				if (iframe.addEventListener) {
					iframe.addEventListener('load', iframe_onload, false);
				} else if (iframe.attachEvent) {
					iframe.attachEvent('onload', iframe_onload);
				}

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

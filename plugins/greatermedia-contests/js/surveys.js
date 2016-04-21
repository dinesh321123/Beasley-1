/*! Greater Media Contests - v1.0.6
 * http://10up.com/
 * Copyright (c) 2016;
 * Licensed GPLv2+
 */
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

(function ($) {
	$(document).ready(function () {
		/**
		 * Generate a list of supported input types (text, date, range, etc.).
		 * Adapted from Modernizr, which is MIT licensed
		 * @see http://modernizr.com/
		 */
		function get_supported_input_types() {

			var inputElem = document.createElement('input'),
				docElement = document.documentElement,
				inputs = {},
				smile = ':)';

			return (function (props) {

				for (var i = 0, bool, inputElemType, defaultView, len = props.length; i < len; i++) {

					inputElem.setAttribute('type', inputElemType = props[i]);
					bool = inputElem.type !== 'text';

					if (bool) {

						inputElem.value = smile;
						inputElem.style.cssText = 'position:absolute;visibility:hidden;';

						if (/^range$/.test(inputElemType) && inputElem.style.WebkitAppearance !== undefined) {

							docElement.appendChild(inputElem);
							defaultView = document.defaultView;

							bool = defaultView.getComputedStyle &&
							defaultView.getComputedStyle(inputElem, null).WebkitAppearance !== 'textfield' &&
							(inputElem.offsetHeight !== 0);

							docElement.removeChild(inputElem);

						} else if (/^(search|tel)$/.test(inputElemType)) {
						} else if (/^(url|email)$/.test(inputElemType)) {
							bool = inputElem.checkValidity && inputElem.checkValidity() === false;

						} else {
							bool = inputElem.value !== smile;
						}
					}

					inputs[props[i]] = !!bool;
				}

				return inputs;

			})('search tel url email datetime date month week time datetime-local number range color'.split(' '));
		}

		// Add datepickers for start & end dates if not supported natively
		var supported_input_types = get_supported_input_types();
		if (!supported_input_types.hasOwnProperty('date') || false === supported_input_types.date) {
			$('input[type=date]').datetimepicker({
				timepicker: false,
				format    : 'm/d/Y'
			});

			$('input[type=time]').datetimepicker({
				datepicker: false,
				format    : 'g:i A',
				formatTime: 'g:i A',
				allowTimes: [
					'12:00 AM', '12:30 AM',
					'1:00 AM', '1:30 AM',
					'2:00 AM', '2:30 AM',
					'3:00 AM', '3:30 AM',
					'4:00 AM', '4:30 AM',
					'5:00 AM', '5:30 AM',
					'6:00 AM', '6:30 AM',
					'7:00 AM', '7:30 AM',
					'8:00 AM', '8:30 AM',
					'9:00 AM', '9:30 AM',
					'10:00 AM', '10:30 AM',
					'11:00 AM', '11:30 AM',
					'12:00 PM', '12:30 PM',
					'1:00 PM', '1:30 PM',
					'2:00 PM', '2:30 PM',
					'3:00 PM', '3:30 PM',
					'4:00 PM', '4:30 PM',
					'5:00 PM', '5:30 PM',
					'6:00 PM', '6:30 PM',
					'7:00 PM', '7:30 PM',
					'8:00 PM', '8:30 PM',
					'9:00 PM', '9:30 PM',
					'10:00 PM', '10:30 PM',
					'11:00 PM', '11:30 PM'
				]
			});
		}
	});
})(jQuery);
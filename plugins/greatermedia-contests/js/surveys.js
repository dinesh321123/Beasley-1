/*! Greater Media Contests - v1.0.3
 * http://10up.com/
 * Copyright (c) 2015;
 * Licensed GPLv2+
 */
(function ($, gmr) {
	var __ready = function () {
		$('.' + gmr.form_class).submit(function() {
			var form = $(this);

			if (!form.parsley || form.parsley().isValid()) {
				var form_data = new FormData();

				form.find('input').each(function() {
					var input = this;

					if ('file' === input.type) {
						$(this.files).each(function(key, value) {
							form_data.append(input.name, value);
						});
					} else if ('radio' === input.type || 'checkbox' === input.type) {
						if (input.checked) {
							form_data.append(input.name, input.value);
						}
					} else {
						form_data.append(input.name, input.value);
					}
				});

				form.find('textarea, select').each(function() {
					form_data.append(this.name, this.value);
				});

				form.find('input, textarea, select, button').attr('disabled', 'disabled');
				form.find('i.fa').show();

				$.ajax({
					url: gmr.ajax_url,
					type: 'post',
					data: form_data,
					processData: false, // Don't process the files
					contentType: false, // Set content type to false as jQuery will tell the server its a query string request
					dataType: 'json',
					success: function (data, textStatus, jqXHR) {
						if ('success' === textStatus && data.data.message) {
							var wrapper = document.createElement('p');
							wrapper.class = 'survey_thank_you';
							wrapper.innerText = data.data.message;
							form.replaceWith(wrapper);
						}
					}
				});
			}

			return false;
		});
	};

	$(document).bind('pjax:end', __ready).ready(__ready);
})(jQuery, GreaterMediaSurveys);
document.addEventListener("DOMContentLoaded", function () {
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
		jQuery('input[type=date]').datetimepicker(
			{
				timepicker: false,
				format    : 'm/d/Y'
			}
		);

		jQuery('input[type=time]').datetimepicker(
			{
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
			}
		);
	}

}, false );
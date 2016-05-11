/*! Greater Media Contests - v1.0.6
 * http://10up.com/
 * Copyright (c) 2016;
 * Licensed GPLv2+
 */
(function ($, gmr, JSON) {
	$(document).ready(function() {
		var formbuilder = new Formbuilder({
			selector: '#survey_embedded_form',
			bootstrapData: gmr.form,
			controls: ['address', 'checkboxes', 'date', 'dropdown', 'email', 'paragraph', 'radio', 'section_break', 'text', 'website']
		});

		formbuilder.on('save', function(payload) {
			// payload is a JSON string representation of the form
			$('#survey_embedded_form_data').val(encodeURIComponent(JSON.stringify(JSON.parse(payload).fields)).replace(/'/g, "%27"));
		});

		// Default the hidden field with the form data loaded from the server
		$('#survey_embedded_form_data').val(encodeURIComponent(JSON.stringify(gmr.form)).replace(/'/g, "%27"));
	});
})(jQuery, GreaterMediaContestsForm, JSON);

/**
 * Set up date pickers for browsers without a native control
 */
document.addEventListener("DOMContentLoaded", function() {
		/**
		 * Generate a list of supported input types (text, date, range, etc.).
		 * Adapted from Modernizr, which is MIT licensed
		 * @see http://modernizr.com/
		 */
		function func_supported_input_types() {

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

		var supported_input_types = func_supported_input_types();

		// Add datepickers for start & end dates if not supported natively
		if (!supported_input_types.hasOwnProperty('date') || false === supported_input_types.date) {
			jQuery('#greatermedia_contest_start').add('#greatermedia_contest_end').datetimepicker(
				{
					format:'m/d/Y',
					timepicker: false
				}
			);
		}

	}, false);
/*! Greater Media Contests - v1.0.4
 * http://10up.com/
 * Copyright (c) 2015;
 * Licensed GPLv2+
 */
(function ($) {
	$(document).ready(function () {
		var formbuilder = new Formbuilder({
			selector: '#contest_embedded_form',
			bootstrapData: GreaterMediaContestsForm.form,
			controls: []
		});

		formbuilder.on('showEditView', function($el, model) {
			console.log(model);
			if (model.cid === 'c5' || model.cid === 'c6') {
				$el.find('input[data-rv-checked="model.required"]')
				   .prop('checked', true)
				   .attr('disabled', 'disabled');
			}
		});

		formbuilder.on('save', function (payload) {
			// payload is a JSON string representation of the form
			$('#contest_embedded_form_data').val(encodeURIComponent(JSON.stringify(JSON.parse(payload).fields)));
		});

		// Default the hidden field with the form data loaded from the server
		$('#contest_embedded_form_data').val(encodeURIComponent(JSON.stringify(GreaterMediaContestsForm.form)));

		$('#contest-settings ul.tabs a').click(function() {
			$('#contest-settings ul.tabs li.active').removeClass('active');
			$(this).parent().addClass('active');
			
			$('#contest-settings div.tab.active').removeClass('active');
			$('#contest-settings').find($(this).attr('href')).addClass('active');
			return false;
		});
	});
})(jQuery);

/**
 * Set up date pickers for browsers without a native control
 */
document.addEventListener("DOMContentLoaded", function() {
	/**
	 * Generate a list of supported input types (text, date, range, etc.).
	 * Adapted from Modernizr, which is MIT licensed
	 * @see http://modernizr.com/
	 */
	function check_supported_input_types() {
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
	var supported_input_types = check_supported_input_types();
	if (!supported_input_types.hasOwnProperty('date') || false === supported_input_types.date) {
		jQuery('#greatermedia_contest_start').add('#greatermedia_contest_end').datetimepicker({
			format: 'm/d/Y',
			timepicker: false
		});
	}
}, false );
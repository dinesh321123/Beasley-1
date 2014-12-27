/*! Greater Media Contests - v1.0.0
 * http://10up.com/
 * Copyright (c) 2014; * Licensed GPLv2+ */
document.addEventListener("DOMContentLoaded", function() {
	var args = {
		selector: '#contest_embedded_form'
	},

	// Every contest form includes Name and Email address fields by default
	default_form = '[{"label":"Name","field_type":"text","required":true,"sticky":"true","field_options":{"size":"medium"},"cid":"c5"},{"label":"Email Address","field_type":"email","required":true,"sticky":"true","field_options":{"sticky":true},"cid":"c9"}]';

	if (GreaterMediaContestsForm.form) {
		args.bootstrapData = JSON.parse(GreaterMediaContestsForm.form);
	}
	else {
		args.bootstrapData = JSON.parse(default_form);
	}

	args.controls = [];

	var formbuilder = new Formbuilder(args);

	formbuilder.on('save', function (payload) {
		// payload is a JSON string representation of the form
		document.getElementById('contest_embedded_form_data').value = encodeURIComponent(JSON.stringify(JSON.parse(payload).fields));
	});

	// Default the hidden field with the form data loaded from the server
	document.getElementById('contest_embedded_form_data').value = encodeURIComponent(JSON.stringify(GreaterMediaContestsForm.form));

}, false );

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
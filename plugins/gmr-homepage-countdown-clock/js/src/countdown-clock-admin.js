(function ($) {
	$(document).ready(function () {
		$('#countdown-clock-settings ul.tabs a').click(function() {
			$('#countdown-clock-settings ul.tabs li.active').removeClass('active');
			$(this).parent().addClass('active');

			$('#countdown-clock-settings div.tab.active').removeClass('active');
			$('#countdown-clock-settings').find($(this).attr('href')).addClass('active');
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
		jQuery('#greatermedia_countdown_date').datetimepicker({
			format: 'm/d/Y',
			timepicker: false
		});
	}
}, false );

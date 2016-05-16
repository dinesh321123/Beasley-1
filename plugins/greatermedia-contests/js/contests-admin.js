/*! Greater Media Contests - v1.0.6
 * http://10up.com/
 * Copyright (c) 2016;
 * Licensed GPLv2+
 */
(function ($) {
	$(document).ready(function () {
		if (document.getElementById('contest_embedded_form')) {
			var formbuilder = new Formbuilder({
				selector: '#contest_embedded_form',
				bootstrapData: GreaterMediaContestsForm.form,
				controls: ['address', 'checkboxes', 'date', 'dropdown', 'email', 'paragraph', 'radio', 'section_break', 'text', 'website', 'file']
			});

			formbuilder.on('save', function (payload) {
				// payload is a JSON string representation of the form
				$('#contest_embedded_form_data').val(encodeURIComponent(JSON.stringify(JSON.parse(payload).fields)).replace(/'/g, "%27"));
			});

			// Default the hidden field with the form data loaded from the server
			$('#contest_embedded_form_data').val(encodeURIComponent(JSON.stringify(GreaterMediaContestsForm.form)).replace(/'/g, "%27"));
		}
	});
})(jQuery);

(function ($) {
	$(document).ready(function () {
		$('#contest-settings ul.tabs a').click(function() {
			$('#contest-settings ul.tabs li.active').removeClass('active');
			$(this).parent().addClass('active');

			$('#contest-settings div.tab.active').removeClass('active');
			$('#contest-settings').find($(this).attr('href')).addClass('active');
			return false;
		});
	});
})(jQuery);

(function ($) {
	$(document).ready(function () {
		$('.mis-pub-radio').each(function() {
			var $this = $(this),
				$switchSelect = $this.find('.radio-select'),
				$editLink = $this.find('.edit-radio'),
				origin_value = $switchSelect.find('input:radio:checked').val();

			$editLink.click(function() {
				if ($switchSelect.is(':hidden')) {
					$switchSelect.slideDown('fast').find('input[type="radio"]').first().focus();
					$(this).hide();
				}
				return false;
			});

			$switchSelect.find('.cancel-radio').click(function() {
				$switchSelect.slideUp('fast', function() {
					$editLink.show().focus();

					$switchSelect.find('input:radio').each(function() {
						$(this).prop('checked', $(this).val() === origin_value);
					});
				});

				return false;
			});

			$switchSelect.find('.save-radio').click(function(e) {
				// Don't return false, so we can still listen for this to happen elsewhere
				e.preventDefault();

				var selected = $switchSelect.find('input:radio:checked');

				$switchSelect.slideUp('fast', function() {
					$editLink.show();

					origin_value = selected.val();
					$this.find('.radio-value').text(selected.parent().text());
				});
			});
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
		jQuery('#greatermedia_contest_start,#greatermedia_contest_end,#greatermedia_contest_vote_end,#greatermedia_contest_vote_start').datetimepicker({
			format: 'm/d/Y',
			timepicker: false
		});

		jQuery('#greatermedia_contest_start_time,#greatermedia_contest_end_time,#greatermedia_contest_vote_end_time,#greatermedia_contest_vote_start_time').datetimepicker({
			format: 'H:i',
			datepicker: false
		});
	}
}, false );

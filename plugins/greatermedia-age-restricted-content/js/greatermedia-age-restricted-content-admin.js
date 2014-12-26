jQuery(function () {

	window.GreaterMediaAgeRestrictedContentAdmin = (function () {

		var module = {};

		module.template = GreaterMediaAgeRestrictedContent.templates.tinymce;

		module.create_popup_title = GreaterMediaAgeRestrictedContent.strings['Age Restricted Content'];
		module.edit_popup_title = GreaterMediaAgeRestrictedContent.strings['Age Restricted Content'];

		module.button_onclick = function() {
			// Do nothing
		}

		/**
		 * Returns a translated description of a age restriction
		 *
		 * @param string age_restriction
		 *
		 * @return string description
		 */
		function age_restriction_description(age_restriction) {

			if ('string' !== typeof age_restriction) {
				return ('undefined' !== typeof GreaterMediaAgeRestrictedContentAdmin) ? GreaterMediaAgeRestrictedContent.strings['No restriction'] : 'No restriction';
			}

			if ('18plus' === age_restriction) {
				return ('undefined' !== typeof GreaterMediaAgeRestrictedContentAdmin) ? GreaterMediaAgeRestrictedContent.strings['18+'] : '18+';
			} else if ('21plus' === age_restriction) {
				return ('undefined' !== typeof GreaterMediaAgeRestrictedContentAdmin) ? GreaterMediaAgeRestrictedContent.strings['21+'] : '21+';
			} else {
				return ('undefined' !== typeof GreaterMediaAgeRestrictedContentAdmin) ? GreaterMediaAgeRestrictedContent.strings['No restriction'] : 'No restriction';
			}

		}

		module.view_gethtml = function () {

			var attrs = this.shortcode.attrs.named,
				options = {
					content: this.shortcode.content,
					status : undefined
				};

			if (attrs.status) {
				options.status = attrs.status;
			}

			return this.template(options);

		};

		module.view_edit_popup_body_fields = function (parsed_shortcode) {

			var value;

			if (parsed_shortcode && undefined !== parsed_shortcode) {
				value = age_restriction_description(parsed_shortcode.attrs.named.status);
			}
			else {
				value = age_restriction_description('18+');
			}

			return [
				{
					type  : 'listbox',
					id    : 'gm-age-restricted-status',
					name  : 'status',
					label : ('undefined' !== typeof GreaterMediaAgeRestrictedContentAdmin) ? GreaterMediaAgeRestrictedContent.strings['Restricted to'] : 'Restricted to',
					/**
					 * After you select something from a ComboBox, TinyMCE shows the *value* of the selected
					 * option, not the text, making it the most backward ComboBox I've ever met. The
					 * view_edit_popup_onsubmit() method will need to map these labels back to their respective
					 * values.
					 */
					values: [{text: '18+', value: '18plus'}, {text: '21+', value: '21plus'}],
					value : value
				}
			];

		};

		/**
		 * Process form submission event from the popup.
		 *
		 * @param Event submit_event
		 * @returns {{status: *}}
		 */
		module.view_edit_popup_onsubmit = function (submit_event) {

			/**
			 * Map backwards ComboBox values (actually the labels) to valid values
			 * @param status_description a possibly translated combo box value description (not the value)
			 * @return string proper value
 			 */
			function age_restriction_desc_to_value(status_description) {

				if ('undefined' === typeof GreaterMediaAgeRestrictedContentAdmin || 'undefined' === typeof GreaterMediaAgeRestrictedContentAdmin['strings']) {

					// Default translation
					if ('18plus' === status_description) {
						return '18plus';
					} else if ('21plus' === status_description) {
						return '21plus';
					}
					else {
						return '';
					}

				}
				else {

					//  Translated strings
					if (GreaterMediaAgeRestrictedContent.strings['18plus'] === status_description) {
						return '18plus';
					} else if (GreaterMediaAgeRestrictedContent.strings['21plus'] === status_description) {
						return '21plus';
					}
					else {
						return '';
					}

				}

			}

			return {
				status: age_restriction_desc_to_value(submit_event.data.status)
			};

		};

		// Implement the postbox feature
		var age_restriction_div = jQuery('#agerestrictiondiv');
		age_restriction_div.html(GreaterMediaAgeRestrictedContent.templates.age_restriction);

		// Show the radio buttons
		jQuery("a[href='#edit_age_restriction']").click(function () {

			age_restriction_div.slideDown();

			if (true !== age_restriction_div.data('populated')) {

				age_restriction_div.find('input').filter('[name=ar_status]').filter('[value="' + jQuery('#hidden_age_restriction').val() + '"]').attr('checked', 'checked');

				age_restriction_div.data('populated', true);
			}

		});

		// Cancel button
		age_restriction_div.find('.cancel-age-restriction').click(function () {

			age_restriction_div.find('input').filter('[name=ar_status]').filter('[value="' + jQuery('#hidden_age_restriction').val() + '"]').attr('checked', 'checked');

			age_restriction_div.slideUp();

		});

		// Update hidden fields
		age_restriction_div.find('.save-age-restriction').click(function () {

			var checked_option = age_restriction_div.find('input').filter('[name=ar_status]').filter(':checked');

			jQuery('#hidden_age_restriction').val(checked_option.val());

			jQuery('#agerestrictiondiv').slideUp();

			jQuery('#age-restriction-value').find('b').text(checked_option.parent().text());

		});

		return module;

	})();

});
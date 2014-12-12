jQuery(function () {

	window.GreaterMediaLoginRestrictedContentAdmin = (function () {

		var module = {};

		module.template = GreaterMediaLoginRestrictedContent.templates.tinymce;

		module.create_popup_title = GreaterMediaLoginRestrictedContent.strings['Login Restricted Content'];
		module.edit_popup_title = GreaterMediaLoginRestrictedContent.strings['Login Restricted Content'];

		module.button_onclick = function() {
			// Do nothing
		}

		/**
		 * Returns a translated description of a login restriction
		 *
		 * @param string $login_restriction
		 *
		 * @return string description
		 */
		function login_restriction_description($login_restriction) {

			if ('string' !== typeof $login_restriction) {
				return ('undefined' !== typeof GreaterMediaLoginRestrictedContent) ? GreaterMediaLoginRestrictedContent.strings['No restriction'] : 'No restriction';
			}

			if ('logged-in' === $login_restriction) {
				return ('undefined' !== typeof GreaterMediaLoginRestrictedContent) ? GreaterMediaLoginRestrictedContent.strings['Logged in'] : 'Logged in';
			} else if ('logged-out' === $login_restriction) {
				return ('undefined' !== typeof GreaterMediaLoginRestrictedContent) ? GreaterMediaLoginRestrictedContent.strings['Logged out'] : 'Logged out';
			} else {
				return ('undefined' !== typeof GreaterMediaLoginRestrictedContent) ? GreaterMediaLoginRestrictedContent.strings['No restriction'] : 'No restriction';
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
				value = login_restriction_description(parsed_shortcode.attrs.named.status);
			}
			else {
				value = login_restriction_description('logged-in');
			}

			return [
				{
					type  : 'listbox',
					id    : 'gm-login-restricted-status',
					name  : 'status',
					label : ('undefined' !== typeof GreaterMediaLoginRestrictedContent) ? GreaterMediaLoginRestrictedContent.strings['Must be'] : 'Must be',
					/**
					 * After you select something from a ComboBox, TinyMCE shows the *value* of the selected
					 * option, not the text, making it the most backward ComboBox I've ever met. The
					 * view_edit_popup_onsubmit() method will need to map these labels back to their respective
					 * values.
					 */
					values: [{text: 'Logged in', value: 'Logged in'}, {text: 'Logged out', value: 'Logged out'}],
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
			function login_restriction_desc_to_value(status_description) {

				if ('undefined' === typeof GreaterMediaLoginRestrictedContent || 'undefined' === typeof GreaterMediaLoginRestrictedContent['strings']) {

					// Default translation
					if ('Logged in' === status_description) {
						return 'logged-in';
					} else if ('Logged out' === status_description) {
						return 'logged-out';
					}
					else {
						return '';
					}

				}
				else {

					//  Translated strings
					if (GreaterMediaLoginRestrictedContent.strings['Logged in'] === status_description) {
						return 'logged-in';
					} else if (GreaterMediaLoginRestrictedContent.strings['Logged out'] === status_description) {
						return 'logged-out';
					}
					else {
						return '';
					}

				}

			}

			return {
				status: login_restriction_desc_to_value(submit_event.data.status)
			};

		};

		// Implement the postbox feature
		var login_restriction_div = jQuery('#loginrestrictiondiv');
		login_restriction_div.html(GreaterMediaLoginRestrictedContent.templates.login_restriction);

		// Show the radio buttons
		jQuery("a[href='#edit_login_restriction']").click(function () {

			login_restriction_div.slideDown();

			if (true !== login_restriction_div.data('populated')) {

				login_restriction_div.find('input').filter('[name=lr_status]').filter('[value="' + jQuery('#hidden_login_restriction').val() + '"]').attr('checked', 'checked');

				login_restriction_div.data('populated', true);
			}

		});

		// Cancel button
		login_restriction_div.find('.cancel-login-restriction').click(function () {

			login_restriction_div.find('input').filter('[name=lr_status]').filter('[value="' + jQuery('#hidden_login_restriction').val() + '"]').attr('checked', 'checked');

			login_restriction_div.slideUp();

		});

		// Update hidden fields
		login_restriction_div.find('.save-login-restriction').click(function () {

			var checked_option = login_restriction_div.find('input').filter('[name=lr_status]').filter(':checked');

			jQuery('#hidden_login_restriction').val(checked_option.val());

			jQuery('#loginrestrictiondiv').slideUp();

			jQuery('#login-restriction-value').find('b').text(checked_option.parent().text());

		});

		return module;

	})();

});
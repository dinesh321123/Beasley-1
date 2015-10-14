jQuery(function () {

	window.GreaterMediaTimedContentAdmin = (function () {

		var module = {};

		module.template = GreaterMediaTimedContent.templates.tinymce;

		module.create_popup_title = GreaterMediaTimedContent.strings['Timed Content'];
		module.edit_popup_title = GreaterMediaTimedContent.strings['Timed Content'];

		module.button_onclick = function () {


		};

		module.view_gethtml = function () {

			var attrs = this.shortcode.attrs.named,
				options = {
					content: this.shortcode.content,
					show   : undefined,
					hide   : undefined
				};

			// Format the "show" date for display using the date.format library
			if (attrs.show) {
				options.show = new Date(attrs.show).format(GreaterMediaTimedContent.formats.mce_view_date);
			}

			// Format the "hide" date for display using the date.format library
			if (attrs.hide) {
				options.hide = new Date(attrs.hide).format(GreaterMediaTimedContent.formats.mce_view_date);
			}

			return this.template(options);

		};

		module.view_edit_popup_body_fields = function (parsed_shortcode) {

			var show_time = '', hide_time = '';

			function is_parseable_date(date_string) {

				var date_obj = new Date(date_string);
				return (date_obj instanceof Date) && isFinite(date_obj);

			}

			if (parsed_shortcode !== undefined) {

				if (parsed_shortcode.attrs.named.show && is_parseable_date(parsed_shortcode.attrs.named.show)) {
					show_time = new Date(parsed_shortcode.attrs.named.show).format(GreaterMediaTimedContent.formats.mce_view_date);
				}

				if (parsed_shortcode.attrs.named.hide && is_parseable_date(parsed_shortcode.attrs.named.hide)) {
					hide_time = new Date(parsed_shortcode.attrs.named.hide).format(GreaterMediaTimedContent.formats.mce_view_date);
				}

			}

			return [
				{
					type : 'textbox',
					id   : 'gm-show-date',
					name : 'show',
					label: GreaterMediaTimedContent.strings['Show content on'],
					value: show_time
				},
				{
					type : 'textbox',
					id   : 'gm-hide-date',
					name : 'hide',
					label: GreaterMediaTimedContent.strings['Hide content on'],
					value: hide_time
				}
			];
		};

		module.view_edit_popup_visible = function () {

			jQuery('#gm-show-date, #gm-hide-date').datetimepicker({
				format: GreaterMediaTimedContent.formats.mce_view_date,
				inline: false,
				lang  : 'en'
			});

		};

		module.view_edit_popup_onsubmit = function (submit_event) {

			var attributes = {};

			if (submit_event.data.show && '' !== submit_event.data.show) {
				attributes.show = new Date(submit_event.data.show).toISOString();
			}

			if (submit_event.data.hide && '' !== submit_event.data.hide) {
				attributes.hide = new Date(submit_event.data.hide).toISOString();
			}

			return attributes;

		};

		var exp_timestamp_div = jQuery('#exptimestampdiv');
		exp_timestamp_div.html(GreaterMediaTimedContent.templates.expiration_time);
		var exp_mm = jQuery('#exp_mm'),
			exp_jj = jQuery('#exp_jj'),
			exp_aa = jQuery('#exp_aa'),
			exp_hh = jQuery('#exp_hh'),
			exp_mn = jQuery('#exp_mn');

		// Show the time entry boxes
		jQuery("a[href='#edit_exptimestamp']").click(function () {

			exp_timestamp_div.slideDown();

			if (true !== exp_timestamp_div.data('populated')) {

				exp_mm.val(jQuery('#hidden_exp_mm').val());
				exp_jj.val(jQuery('#hidden_exp_jj').val());
				exp_aa.val(jQuery('#hidden_exp_aa').val());
				exp_hh.val(jQuery('#hidden_exp_hh').val());
				exp_mn.val(jQuery('#hidden_exp_mn').val());

				exp_timestamp_div.data('populated', true);
			}

		});

		// Cancel button
		exp_timestamp_div.find('.cancel-timestamp').click(function () {

			exp_mm.val(jQuery('#hidden_exp_mm').val());
			exp_jj.val(jQuery('#hidden_exp_jj').val());
			exp_aa.val(jQuery('#hidden_exp_aa').val());
			exp_hh.val(jQuery('#hidden_exp_hh').val());
			exp_mn.val(jQuery('#hidden_exp_mn').val());

			exp_timestamp_div.slideUp();

		});

		// Update hidden fields
		exp_timestamp_div.find('.save-timestamp').click(function () {

			jQuery('#hidden_exp_mm').val(exp_mm.val());
			jQuery('#hidden_exp_jj').val(exp_jj.val());
			jQuery('#hidden_exp_aa').val(exp_aa.val());
			jQuery('#hidden_exp_hh').val(exp_hh.val());
			jQuery('#hidden_exp_mn').val(exp_mn.val());

			exp_timestamp_div.slideUp();
			debugger;
			var expiration_date = new Date();
			expiration_date.setMonth(parseInt(exp_mm.val(), 10) - 1);
			expiration_date.setDate(exp_jj.val());
			expiration_date.setFullYear(exp_aa.val());
			expiration_date.setHours(exp_hh.val());
			expiration_date.setMinutes(exp_mn.val());

			jQuery('#exptimestamp').find('b').text(expiration_date.format(GreaterMediaTimedContent.formats.date));

		});

		// Remove expiration timestamp
		exp_timestamp_div.find('.remove-timestamp').click(function () {

			jQuery('#exp_mm, #hidden_exp_mm').val('');
			jQuery('#exp_jj, #hidden_exp_jj').val('');
			jQuery('#exp_aa, #hidden_exp_aa').val('');
			jQuery('#exp_hh, #hidden_exp_hh').val('');
			jQuery('#exp_mn, #hidden_exp_mn').val('');

			exp_timestamp_div.slideUp();
			jQuery('#exptimestamp').find('b').text(GreaterMediaTimedContent.strings.never);

		});

		return module;

	})();

});
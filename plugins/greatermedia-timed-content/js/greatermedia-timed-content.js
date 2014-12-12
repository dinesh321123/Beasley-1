(function () {

	window.GreaterMediaTimedContent = (function ($, undefined) {

		var public = {};

		function current_unix_time_utc() {
			return Math.floor(new Date().getTime() / 1000);
		}

		jQuery('.timed-content').each(function () {

			var content_block = jQuery(this),
				show = content_block.data('show-time'),
				hide = content_block.data('hide-time'),
				now_utc = current_unix_time_utc();

			if (!(_.isNumber(show))) {
				show = Number.MIN_VALUE;
			}

			if (!(_.isNumber(hide))) {
				hide = Number.MAX_VALUE;
			}

			if (now_utc > show && hide > now_utc) {
				// leave content visible
			}
			else {
				jQuery(content_block).hide();
			}

		});

		public.button_onclick = function () {
			var time_restricted_editor_popup = {

				title: GreaterMediaTimedContent.strings['Timed Content'],

				body: [
					{
						type : 'textbox',
						id   : 'gm-show-date',
						name : 'show',
						label: GreaterMediaTimedContent.strings['Show content on'],
						value: ''
					},
					{
						type : 'textbox',
						id   : 'gm-hide-date',
						name : 'hide',
						label: GreaterMediaTimedContent.strings['Hide content on'],
						value: ''
					}
				],

				/**
				 * When the popup is submitted, generate a shortcode and insert it into the editor
				 *
				 * @param {Event} e
				 */
				onsubmit: function (e) {

					editor.insertContent(
						new wp.shortcode({
							tag    : 'time-restricted',
							attrs  : {
								show: new Date(e.data.show).toISOString(),
								hide: new Date(e.data.hide).toISOString()
							},
							content: tinymce.activeEditor.selection.getContent()
						}).string()
					);

				},

				width : 600,
				height: 130

			};

			self.editor.windowManager.open(time_restricted_editor_popup);
			jQuery('#gm-show-date, #gm-hide-date').datetimepicker({
				format: GreaterMediaTimedContent.formats.mce_view_date,
				inline: false,
				lang  : 'en'
			});

		};

		return public;

	}(jQuery));

})();
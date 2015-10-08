(function () {

	var self = this;

	// Register a custom button in the TinyMCE toolbar
	tinymce.PluginManager.add('gm_timed_content_mce_button', function (editor, url) {

		self.editor = editor;
		self.editor.addButton('gm_timed_content_mce_button', {
			icon   : 'gm-timed-content-icon',
			onclick: function () {
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

						var attributes = {};

						function is_parseable_date(date_string) {

							var date_obj = new Date(date_string);
							return (date_obj instanceof Date) && isFinite(date_obj);

						}

						if (e.data.show && is_parseable_date(e.data.show)) {
							attributes.show = new Date(e.data.show).toISOString();
						}

						if (e.data.hide && is_parseable_date(e.data.hide)) {
							attributes.hide = new Date(e.data.hide).toISOString();
						}

						editor.insertContent(
							new wp.shortcode({
								tag    : 'time-restricted',
								attrs  : attributes,
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

			}
		});

	});

	// Register a custom TinyMCE View for displaying the shortcode in the WYSIWYG editor
	wp.mce.views.register('time-restricted', {

		View: _.extend( wp.mce.View.prototype, {

			template: _.template(GreaterMediaTimedContent.templates.tinymce),

			postID: jQuery('#post_ID').val(),

			initialize: function (options) {
				this.shortcode = options.shortcode;
			},

			getHtml: function () {

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

			}

		}),

		// Stock toView implementation from wp.mce.views
		toView: function( content ) {
			var match = wp.shortcode.next( this.type, content );

			if ( ! match ) {
				return;
			}

			return {
				index: match.index,
				content: match.content,
				options: {
					shortcode: match.shortcode
				}
			};
		},

		edit: function (node) {

			var edit_self = this,
				parsed_shortcode = wp.shortcode.next('time-restricted', decodeURIComponent(node.dataset.wpviewText)).shortcode,
				show_time = new Date(parsed_shortcode.attrs.named.show),
				hide_time = new Date(parsed_shortcode.attrs.named.hide),
				time_restricted_editor_popup = {

					title: GreaterMediaTimedContent.strings['Timed Content'],

					body: [
						{
							type : 'textbox',
							id   : 'gm-show-date',
							name : 'show',
							label: GreaterMediaTimedContent.strings['Show content on'],
							value: show_time.format(GreaterMediaTimedContent.formats.mce_view_date)
						},
						{
							type : 'textbox',
							id   : 'gm-hide-date',
							name : 'hide',
							label: GreaterMediaTimedContent.strings['Hide content on'],
							value: hide_time.format(GreaterMediaTimedContent.formats.mce_view_date)
						},
						{
							type     : 'textbox',
							multiline: true,
							minHeight: 200,
							name     : 'content',
							label    : GreaterMediaTimedContent.strings['Content'],
							value    : node.querySelector('.content').innerHTML
						}
					],

					buttons: [
						{
							text   : GreaterMediaTimedContent.strings['Ok'],
							onclick: 'submit'
						},
						{
							text   : GreaterMediaTimedContent.strings['Cancel'],
							onclick: 'close'
						}
					],

					onsubmit: function (e) {

						editor.insertContent(
							new wp.shortcode({
								tag    : 'time-restricted',
								attrs  : {
									show: new Date(e.data.show).toISOString(),
									hide: new Date(e.data.hide).toISOString()
								},
								content: e.data.content
							}).string()
						);

						tinymce.activeEditor.windowManager.close();

					},

					width : 600,
					height: 340
				};

			self.editor.windowManager.open(time_restricted_editor_popup);
			jQuery('#gm-show-date, #gm-hide-date').datetimepicker({
				format: GreaterMediaTimedContent.formats.mce_view_date,
				inline: false,
				lang  : 'en'
			});


		}

	});

})();

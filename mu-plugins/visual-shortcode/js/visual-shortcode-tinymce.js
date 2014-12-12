(function () {

	var self = this;

	function js_module_sanity_check(shortcode_name, js_module_name) {

		// Check if the shortcode's module implementation has been loaded on this page
		if (!window[VisualShortcode.registry[shortcode_name].js_module]) {
			return false;
		}

		var js_module = window[VisualShortcode.registry[shortcode_name].js_module],
			check_passes = true,
			required_methods = [
				'button_onclick',
				'view_gethtml',
				'view_edit_popup_onsubmit'
			];

		for (var required_method_index in required_methods) {
			var method_name = required_methods[required_method_index];
			if (!js_module.hasOwnProperty(method_name)) {
				console && console.log(js_module_name + ' needs a ' + method_name + ' method');
				check_passes = false;
			}
		}

		return check_passes;

	}

	if (VisualShortcode && VisualShortcode.registry) {

		for (var current_shortcode_name in VisualShortcode.registry) {

			if (VisualShortcode.registry.hasOwnProperty(current_shortcode_name)) {

				if (!js_module_sanity_check(current_shortcode_name, VisualShortcode.registry[current_shortcode_name].js_module)) {
					return;
				}

				/**
				 * Wrapping the body of the loop in a closure so functions and inline code here all use
				 * the right shortcode_name.
				 */
				(function (shortcode_name) {

					// Register a custom button in the TinyMCE toolbar
					tinymce.PluginManager.add(
						VisualShortcode.registry[shortcode_name].plugin_name,
						function (editor, url) {

							function button_onclick_handler(e) {

								var title = VisualShortcode.strings['Create'],
									content = tinymce.activeEditor.selection.getContent(),
									parsed_shortcode = (wp.shortcode.next(shortcode_name, content)) ? wp.shortcode.next(shortcode_name, content).shortcode : undefined,
									body = [];

								if (window[VisualShortcode.registry[shortcode_name].js_module].create_popup_title) {
									title = window[VisualShortcode.registry[shortcode_name].js_module].create_popup_title;
								}

								if (window[VisualShortcode.registry[shortcode_name].js_module].view_edit_popup_body_fields) {
									body = (window[VisualShortcode.registry[shortcode_name].js_module].view_edit_popup_body_fields.call(this, parsed_shortcode)).concat(body);
								}

								var popup = {

									title: title,

									body: body,

									/**
									 * When the popup is submitted, generate a shortcode and insert it into the editor
									 *
									 * @param {Event} e
									 */
									onsubmit: function (e) {

										var attrs = window[VisualShortcode.registry[shortcode_name].js_module].view_edit_popup_onsubmit.call(this, e);

										editor.insertContent(
											new wp.shortcode({
												tag    : shortcode_name,
												attrs  : attrs,
												content: tinymce.activeEditor.selection.getContent()
											}).string()
										);

									},

									width : 600,
									height: 130

								};

								self.editor.windowManager.open(popup);
								if (window[VisualShortcode.registry[shortcode_name].js_module].view_edit_popup_visible) {
									window[VisualShortcode.registry[shortcode_name].js_module].view_edit_popup_visible.call(this);
								}

							}

							self.editor = editor;

							self.editor.addButton(VisualShortcode.registry[shortcode_name].button, {
								icon   : VisualShortcode.registry[shortcode_name].icon_class,
								onclick: button_onclick_handler,
								title: VisualShortcode.registry[shortcode_name].button_title
							});

						}
					);

					var VisualShortcodeBaseView = _.extend(wp.mce.View.prototype, {

						postID: jQuery('#post_ID').val(),

						initialize: function (options) {
							this.shortcode = options.shortcode;
						},

						// Stock toView implementation from wp.mce.views
						toView    : function (content) {
							var match = wp.shortcode.next(this.type, content);

							if (!match) {
								return;
							}

							return {
								index  : match.index,
								content: match.content,
								options: {
									shortcode: match.shortcode
								}
							};
						}

					});

					// Register a custom TinyMCE View for displaying the shortcode in the WYSIWYG editor
					wp.mce.views.register(shortcode_name, {

						View: _.extend({}, VisualShortcodeBaseView, {

							template: _.template(window[VisualShortcode.registry[shortcode_name].js_module].template),

							getHtml: window[VisualShortcode.registry[shortcode_name].js_module].view_gethtml

						}),

						edit: function (node) {

							var edit_self = this,
								parsed_shortcode = wp.shortcode.next(shortcode_name, decodeURIComponent(node.dataset.wpviewText)).shortcode,
								body = [{
									type     : 'textbox',
									multiline: true,
									minHeight: 200,
									name     : 'content',
									label    : VisualShortcode.strings['Content'],
									value    : node.querySelector('.content').innerHTML
								}],
								title = VisualShortcode.strings['Edit'];

							if (window[VisualShortcode.registry[shortcode_name].js_module].edit_popup_title) {
								title = window[VisualShortcode.registry[shortcode_name].js_module].edit_popup_title;
							}

							if (window[VisualShortcode.registry[shortcode_name].js_module].view_edit_popup_body_fields) {
								body = (window[VisualShortcode.registry[shortcode_name].js_module].view_edit_popup_body_fields.call(this, parsed_shortcode)).concat(body);
							}

							var popup = {

								title: title,

								body: body,

								buttons: [
									{
										text   : VisualShortcode.strings['Ok'],
										onclick: 'submit'
									},
									{
										text   : VisualShortcode.strings['Cancel'],
										onclick: 'close'
									}
								],

								onsubmit: function (e) {

									var attrs = window[VisualShortcode.registry[shortcode_name].js_module].view_edit_popup_onsubmit.call(this, e);

									editor.insertContent(
										new wp.shortcode({
											tag    : shortcode_name,
											attrs  : attrs,
											content: e.data.content
										}).string()
									);

									tinymce.activeEditor.windowManager.close();

								},

								width : 600,
								height: 340
							};

							tinymce.activeEditor.windowManager.open(popup);

							if (window[VisualShortcode.registry[shortcode_name].js_module].view_edit_popup_visible) {
								window[VisualShortcode.registry[shortcode_name].js_module].view_edit_popup_visible.call(this);
							}


						}

					});

				})(current_shortcode_name);
			}
		}
	}

})();

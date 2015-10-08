(function($) {

	var Convertor = function() {
		this.toHTMLFragmentProxy   = $.proxy(this.toHTMLFragment, this);
		this.replaceShortcodeProxy = $.proxy(this.replaceShortcode, this);
	};

	Convertor.prototype = {

		toHTML: function(content) {
			var html = wp.shortcode.replace(
				this.plugin.getShortcodeTag(), content, this.toHTMLFragmentProxy
			);

			html = this.replaceEmptyBody(html);

			return html;
		},

		toShortcode: function(content) {
			var $root       = $('<div></div>');
			$root.html(content);

			var selector    = this.getSelector();
			var $shortcodes = $(selector, $root);

			$shortcodes.each(this.replaceShortcodeProxy);

			var html = $root.html();

			return html;
		},

		/* helpers */
		toHTMLFragment: function(shortcode) {
			var data        = shortcode.attrs.named;
			var dataAttrs   = this.toDataAttrs(data);
			dataAttrs.class = this.plugin.getClassName();

			var $root   = $('<div></div>', dataAttrs);
			var $header = $('<span></span>', { class: 'meta', contenteditable: false });
			var $body   = $('<div></div>', { class: 'body' });

			$body.html(shortcode.content);
			$header.html(this.plugin.getMetaLabel(data));

			$root.data('status', data.status);
			$root.append($header);
			$root.append($body);

			var html = $root.prop('outerHTML');

			return html;
		},

		toDataAttrs: function(data) {
			var attrs = {};

			for (var key in data) {
				if (data.hasOwnProperty(key)) {
					attrs['data-' + key] = data[key];
				}
			}

			return attrs;
		},

		replaceShortcode: function(index, shortcode) {
			var $shortcode    = $(shortcode);
			var shortcodeNode = this.plugin.nodeForTarget(shortcode);
			var $body         = $('.body', $shortcode);
			var data          = this.plugin.getDataFromNode(shortcodeNode);
			var wpShortcode   = new wp.shortcode({
				tag     : this.plugin.getShortcodeTag(),
				attrs   : data,
				type    : this.plugin.getType(),
				content : $body.html()
			});

			var html = wpShortcode.string();

			$shortcode.replaceWith(html);
		},

		replaceEmptyBody: function(html) {
			html = html.replace('<div class="body">&nbsp;</div>', '&nbsp;');

			return html;
		},

		getSelector: function() {
			return '.' + this.plugin.getClassName();
		},

	};

	var Menu = function() {

	};

	Menu.prototype = {

		plugin: null,
		button: null,

		register: function() {
			var editor  = this.plugin.getEditor();
			var command = this.plugin.getCommand();
			var buttons = this.getButtons();
			var button;

			for (var i = 0; i < buttons.length; i++) {
				button = buttons[i];
				editor.addButton(command, button);
			}
		},

		getButtons: function() {
			var self = this;

			return [
				{
					title        : this.plugin.getDisplayName(),
					cmd          : this.plugin.getCommand(),
					image        : '',
					onPostRender : function() { self.button = this; },
				}
			];
		},

		setEnabled: function(enabled) {
			if (this.button) {
				this.button.disabled(!enabled);
			}
		}

	};

	var Dialog = function() {

	};

	Dialog.prototype = {

		plugin     : null,
		win        : null,
		data       : {},
		activeNode : null,
		isNew      : true,

		open: function() {
			var editor        = this.plugin.getEditor();
			var params        = this.getParams();
			var windowManager = editor.windowManager;

			this.activeNode = this.plugin.getSelectedNode();
			this.win        = windowManager.open(params);

			if (!this.data.isNew) {
				// KLUDGE - TinyMCE buttons have inline styles
				var $removeButton = $('#content-restricted-remove-button');
				$removeButton.css('left', '10px');
			}

			this.didOpen();
		},

		close: function() {
			this.win.close();
		},

		didOpen: function() {

		},

		didSubmit: function(event) {
			var data = event.data;
			var plugin = this.plugin;
			var editor = plugin.getEditor();

			if (this.plugin.isFocussed()) {
				var focusState = plugin.getFocusState();
				var mode       = focusState.value;
				var target     = focusState.target;
				var $target    = $(target);
				var $meta      = $('.meta', $target);
				var targetNode = this.plugin.nodeForTarget(target);

				this.plugin.saveDataToNode(targetNode, data);
				$meta.html(this.plugin.getMetaLabel(data));
			} else {
				var selectedText = this.plugin.getSelectedText();
				var shortcode = new wp.shortcode({
					tag     : this.plugin.getShortcodeTag(),
					attrs   : data,
					type    : this.plugin.getType(),
					content : selectedText,
				});

				var content = shortcode.string();

				editor.execCommand('mceInsertContent', false, content);
			}

			editor.selection.collapse();
		},

		didClose: function(event) {

		},

		didRemove: function(event) {
			var $activeNode = $(this.activeNode);
			var className = this.plugin.getClassName();

			if ($activeNode.hasClass(className)) {
				$activeNode.removeClass(className);
			} else {
				$parent = $activeNode.parents('.' + className);
				if ($parent.length !== 0) {
					var body = $('.body', $parent);
					$parent.replaceWith(body.html());
				}
			}

			this.close();
		},

		getData: function() {
			return this.data;
		},

		getParams: function() {
			var size   = this.getSize();
			var params = {
				title   : this.getTitle(),
				width   : size.width,
				height  : size.height,
				buttons : this.getButtons(this.isNew),
				body    : this.getBody(),

				onsubmit : $.proxy(this.didSubmit, this),
				onclose  : $.proxy(this.didClose, this),
			};

			return params;
		},

		getSize: function() {
			return { width: 400, height: 100 };
		},

		getTitle: function() {
			return 'abstract:Dialog';
		},

		getButtons: function(isNew) {
			var buttons = [
				{
					text: 'Ok',
					onclick: 'submit'
				},
				{
					text: 'Cancel',
					onclick: 'close'
				}
			];

			if (!isNew) {
				buttons.unshift(
					{
						text: 'Remove',
						id: 'content-restricted-remove-button',
						onclick: $.proxy(this.didRemove, this),
					}
				);
			}

			return buttons;
		},

		getBody: function(isNew) {
			return 'abstract';
		},

	};

	var PluginGroup = function(groupID) {
		this.groupID    = groupID;
		this.plugins    = [];
		this.registered = false;
	};

	PluginGroup.prototype = {

		add: function(plugin) {
			this.plugins.push(plugin);
			this.register(plugin);
		},

		register: function(plugin) {
			if (!this.registered) {
				var editor = plugin.getEditor();
				editor.onNodeChange.add($.proxy(this.didNodeChange, this));

				this.registered = true;
			}
		},

		didNodeChange: function(editor, controlManager, node) {
			var groupHasFocus = this.groupHasFocus();
			var n             = this.plugins.length;
			var i, plugin;

			for (i = 0; i < n; i++) {
				plugin = this.plugins[i];

				if (plugin.isFocussed()) {
					/* plugin in focus - enable */
					plugin.setEnabled(true);
				} else if (groupHasFocus) {
					/* plugin does not have focus but group has
					 * focus - disable current
					 */
					plugin.setEnabled(false);
				} else {
					/* plugin is not focus - group also does not have
					 * focus - outside content restricted focus - enable
					 */
					plugin.setEnabled(true);
				}
			}
		},

		groupHasFocus: function() {
			var n = this.plugins.length;
			var i, plugin;

			for (i = 0; i < n; i++) {
				plugin = this.plugins[i];

				if (plugin.isFocussed()) {
					return true;
				}
			}

			return false;
		},

	};

	var Plugin = function() {

	};

	Plugin.prototype = {

		getName: function() {
			return 'shortcodePlugin';
		},

		getPluginName: function() {
			return this.getName().replace('-', '');
		},

		getShortcodeTag: function() {
			return this.getName();
		},

		getClassName: function() {
			return this.getShortcodeTag();
		},

		getDisplayName: function() {
			return 'Shortcode Plugin';
		},

		getCommand: function() {
			return this.getName();
		},

		getGroupID: function() {
			return this.group.groupID;
		},

		getNamespace: function() {
			return 'tinymce.plugins.' + this.getPluginName();
		},

		getType: function() {
			return 'closed';
		},

		getMetaLabel: function(data) {
			return 'abstract:meta-label';
		},

		getConvertor: function() {
			if (!this.convertor) {
				this.convertor = new Convertor();
				this.convertor.plugin = this;
			}

			return this.convertor;
		},

		getDialog: function() {
			var dialog = new Dialog();
			dialog.plugin = this;

			return dialog;
		},

		getMenu: function() {
			var menu = new Menu();
			menu.plugin = this;

			return menu;
		},

		getEditor: function() {
			return this.editor;
		},

		getSelection: function() {
			return this.getEditor().selection;
		},

		getSelectedNode: function() {
			return this.getSelection().getNode();
		},

		getSelectedText: function() {
			return this.getSelection().getContent();
		},

		getSelectedData: function() {
			var focusState = this.getFocusState();
			var target     = focusState.target;

			if (target) {
				var targetNode = this.nodeForTarget(target);
				return this.getDataFromNode(targetNode);
			} else {
				return {};
			}
		},

		getFocusState: function() {
			var node      = this.getSelectedNode();
			var $node     = $(node);
			var className = this.getClassName();
			var state     = 'outside';
			var target    = null;

			if ($node.hasClass(className)) {
				state = 'at';
				target = node;
			} else {
				var parents = $node.parents('.' + className);
				if (parents.length !== 0) {
					state  = 'inside';
					target = parents;
				}
			}

			return { value: state, target: target };
		},

		isFocussed: function() {
			var state = this.getFocusState();
			return state.value === 'at' || state.value === 'inside';
		},

		setEnabled: function(enabled) {
			this.menu.setEnabled(enabled);
		},

		initialize: function() {
			this.editor.addCommand(this.getCommand(), this.callback('didCommand'));

			this.menu = this.getMenu();
			this.menu.register();

			this.editor.onNodeChange.add(this.callback('didNodeChange'));
			this.editor.on('BeforeSetContent', this.callback('didBeforeSetContent'));
			this.editor.on('PostProcess', this.callback('didPostProcess'));
		},

		/* TinyMCE Events */
		didCommand: function() {
			var dialog   = this.getDialog();
			dialog.data  = this.getSelectedData();
			dialog.isNew = !this.isFocussed();

			dialog.open();
		},

		didNodeChange: function(editor, controlManager, node) {
			var hasFocus = this.isFocussed();
			controlManager.setActive(this.getCommand(), hasFocus);

			var focusState = this.getFocusState();
			var isInside   = false;
			var target     = focusState.target;
			var className  = this.getClassName() + '-focus';

			if (this.$prevTarget) {
				this.$prevTarget.toggleClass(className, false);
			}

			if (target) {
				var $target = $(target);

				$target.toggleClass(className, true);
				this.$prevTarget = $target;
			}
		},

		didBeforeSetContent: function(event) {
			if (content) {
				var convertor = this.getConvertor();
				event.content = convertor.toHTML(event.content);
			}
		},

		didPostProcess: function(event) {
			if (event.get) {
				var convertor = this.getConvertor();
				event.content = convertor.toShortcode(event.content);
			}
		},

		/* helpers */
		callback: function(method) {
			return $.proxy(this[method], this);
		},

		nodeForTarget: function(target) {
			if (target instanceof jQuery) {
				return target.get(0);
			} else {
				return target;
			}
		},

		saveDataToNode: function(node, data) {
			for (var key in data) {
				if (data.hasOwnProperty(key)) {
					node.setAttribute('data-' + key, data[key]);
				}
			}
		},

		getDataFromNode: function(node) {
			var data       = {};
			var pattern    = /^data-/;
			var attributes = node.attributes;
			var n          = attributes.length;
			var cursor     = ('data-').length;
			var i, key, name;

			for (i = 0; i < n; i++) {
				attribute = attributes[i];
				name      = attribute.name;

				if (pattern.test(name)) {
					key       = name.substring(cursor);
					data[key] = attribute.value;
				}
			}

			return data;
		},
	};

	var PluginFactory = function() {
		this.pluginGroups = {};
	};

	PluginFactory.prototype = {

		register: function(pluginName, klass) {
			var self    = this;
			var builder = function(editor, editorUrl) {
				return self.build(editor, editorUrl, klass);
			};

			tinymce.PluginManager.add(pluginName, builder);
		},

		build: function(editor, editorUrl, klass) {
			var plugin       = new klass();
			var groupID      = editor.id;

			plugin.editor    = editor;
			plugin.editorUrl = editorUrl;
			plugin.group     = this.store(groupID, plugin);
			plugin.initialize();

			return plugin;
		},

		store: function(groupID, plugin) {
			if (!this.pluginGroups[groupID]) {
				this.pluginGroups[groupID] = new PluginGroup(groupID);
			}

			var group = this.pluginGroups[groupID];
			group.add(plugin);

			return group;
		}

	};

	PluginFactory.instance = new PluginFactory();

	window.VisualShortcodeRedux = {
		Convertor     : Convertor,
		Menu          : Menu,
		Dialog        : Dialog,
		Plugin        : Plugin,
		PluginFactory : PluginFactory,
		PluginGroup   : PluginGroup,

		registerPlugin: function(name, klass) {
			if (!this.factory) {
				this.factory = new PluginFactory();
			}

			return this.factory.register(name, klass);
		}
	};

}(jQuery));

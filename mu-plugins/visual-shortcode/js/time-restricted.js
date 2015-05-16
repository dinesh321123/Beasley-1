(function($) {

	var TimeRestrictedConvertor = function() {
		this.toHTMLFragmentProxy   = $.proxy(this.toHTMLFragment, this);
		this.replaceShortcodeProxy = $.proxy(this.replaceShortcode, this);
	};

	TimeRestrictedConvertor.prototype = $.extend(new VisualShortcodeRedux.Convertor(), {

		toDataAttrs: function(data) {
			if (data.show) {
				var show = data.show;
				var showUTC = new Date(show);
				showUTC = showUTC.toISOString();
				data.show = showUTC;
			}

			if (data.hide) {
				var hide    = data.hide;
				var hideUTC = new Date(hide);

				hideUTC   = hideUTC.toISOString();
				data.hide = hideUTC;
			}

			var Convertor = VisualShortcodeRedux.Convertor;
			return Convertor.prototype.toDataAttrs.call(this, data);
		}

	});

	var TimeRestrictedMenu = function() {

	};

	TimeRestrictedMenu.prototype = $.extend(new VisualShortcodeRedux.Menu(), {

	});

	var TimeRestrictedDialog = function() {

	};

	TimeRestrictedDialog.prototype = $.extend(new VisualShortcodeRedux.Dialog(), {

		getTitle: function() {
			return 'Time Restricted Content';
		},

		getBody: function() {
			return [
				{
					type: 'textbox',
					name: 'show',
					id: 'time-restriction-show-input',
					label: 'Show Content On',
					value: this.getShow(),
				},
				{
					type: 'textbox',
					name: 'hide',
					id: 'time-restriction-hide-input',
					label: 'Hide Content On',
					value: this.getHide(),
				}
			];
		},

		getShow: function() {
			var data = this.getData();

			if (data.show) {
				return data.show;
			} else {
				return '';
			}
		},

		getHide: function() {
			var data = this.getData();

			if (data.hide) {
				return data.hide;
			} else {
				return '';
			}
		},

		getData: function() {
			var data = this.data;

			if (data.show) {
				var show = data.show;
				show = this.formatDateForInput(show);
				data.show = show;
			}

			if (data.hide) {
				var hide = data.hide;
				hide = this.formatDateForInput(hide);
				data.hide = hide;
			}

			return data;
		},

		formatDateForInput: function(date) {
			return this.plugin.formatDate(date);
		},

		didOpen: function() {
			var $showInput = $('#time-restriction-show-input');
			var $hideInput = $('#time-restriction-hide-input');
			var format = 'M d, Y h:i a';
			var opts = { format: format };

			$showInput.datetimepicker(opts);
			$hideInput.datetimepicker(opts);
		},

		didSubmit: function(event) {
			var data = event.data;

			if (data.show !== '') {
				var show    = data.show;
				var showUTC = new Date(show);

				showUTC   = showUTC.toISOString();
				data.show = showUTC;
			} else {
				delete data.show;
			}

			if (data.hide !== '') {
				var hide    = data.hide;
				var hideUTC = new Date(hide);

				hideUTC = hideUTC.toISOString();
				data.hide = hideUTC;
			} else {
				delete data.hide;
			}

			var Dialog = VisualShortcodeRedux.Dialog;
			event.data = data;

			if (data.show !== '' || data.hide !== '') {
				return Dialog.prototype.didSubmit.call(this, event);
			}
		},

	});

	var TimeRestrictedPlugin = function() {
	};

	TimeRestrictedPlugin.prototype = $.extend(new VisualShortcodeRedux.Plugin(), {

		getName: function() {
			return 'timeRestricted';
		},

		getShortcodeTag: function() {
			return 'time-restricted';
		},

		getDisplayName: function() {
			return 'Time Restriction';
		},

		getMetaLabel: function(data) {
			var label = '';

			if (data.show) {
				label += 'Show: ' + this.formatDate(data.show);
			}

			if (data.hide) {
				if (data.show) {
					label += ' ; ';
				}

				label += 'Hide: ' + this.formatDate(data.hide);
			}

			return label;
		},

		formatDate: function(date) {
			var dateObj = new Date(date);
			return dateObj.format('M d, Y h:i a');
		},

		getMenu: function() {
			var menu = new TimeRestrictedMenu();
			menu.plugin = this;

			return menu;
		},

		getDialog: function(isNew) {
			var dialog    = new TimeRestrictedDialog();
			dialog.isNew  = isNew;
			dialog.data   = {};
			dialog.plugin = this;

			return dialog;
		},

		getConvertor: function() {
			if (!this.convertor) {
				this.convertor = new TimeRestrictedConvertor();
				this.convertor.plugin = this;
			}

			return this.convertor;
		}

	});

	$(document).ready(function() {
		VisualShortcodeRedux.registerPlugin('timeRestricted', TimeRestrictedPlugin);
	});

}(jQuery));

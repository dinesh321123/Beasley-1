(function($) {

	var AgeRestrictedMenu = function() {

	};

	AgeRestrictedMenu.prototype = $.extend(new VisualShortcodeRedux.Menu(), {

	});

	var AgeRestrictedDialog = function() {

	};

	AgeRestrictedDialog.prototype = $.extend(new VisualShortcodeRedux.Dialog(), {

		getTitle: function() {
			return 'Age Restricted Content';
		},

		getBody: function() {
			return [
				{
					type: 'listbox',
					name: 'status',
					label: 'Restricted To',
					values: this.getStatusValues(),
					value: this.getStatus(),
				}
			];
		},

		getStatusValues: function() {
			return [
				{ text: '18+', value: '18plus' },
				{ text: '21+', value: '21plus' },
			];
		},

		getStatus: function() {
			var data = this.getData();

			if (data.status) {
				return data.status;
			} else {
				return '18plus';
			}
		}

	});

	var AgeRestrictedPlugin = function() {
	};

	AgeRestrictedPlugin.prototype = $.extend(new VisualShortcodeRedux.Plugin(), {

		getName: function() {
			return 'ageRestricted';
		},

		getShortcodeTag: function() {
			return 'age-restricted';
		},

		getDisplayName: function() {
			return 'Age Restriction';
		},

		getMetaLabel: function(data) {
			return 'Restricted to: ' + this.getMetaStatus(data);
		},

		getData: function() {
			var data = this.data;

			if (data.status) {
				data.status = this.toValue(data.status);
			}

			return data;
		},

		getMetaStatus: function(data) {
			return this.toLabel(data.status);
		},

		toLabel: function(status) {
			if (status === '18plus' || status === '18+') {
				return '18+';
			} else if (status === '21plus' || status === '21+') {
				return '21+';
			} else {
				return 'N/A';
			}
		},

		toValue: function(status) {
			if (status === '18plus' || status === '18+') {
				return '18plus';
			} else if (status === '21plus' || status === '21+') {
				return '21plus';
			} else {
				return 'N/A';
			}
		},

		getMenu: function() {
			var menu = new AgeRestrictedMenu();
			menu.plugin = this;

			return menu;
		},

		getDialog: function(isNew) {
			var dialog = new AgeRestrictedDialog();
			dialog.isNew = isNew;
			dialog.data = {};
			dialog.plugin = this;

			return dialog;
		}

	});

	$(document).ready(function() {
		VisualShortcodeRedux.registerPlugin('ageRestricted', AgeRestrictedPlugin);
	});

}(jQuery));

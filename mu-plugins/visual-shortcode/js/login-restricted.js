(function($) {

	var LoginRestrictedMenu = function() {

	};

	LoginRestrictedMenu.prototype = $.extend(new VisualShortcodeRedux.Menu(), {

	});

	var LoginRestrictedDialog = function() {

	};

	LoginRestrictedDialog.prototype = $.extend(new VisualShortcodeRedux.Dialog(), {

		getTitle: function() {
			return 'Login Restricted Content';
		},

		getBody: function() {
			return [
				{
					type: 'listbox',
					name: 'status',
					label: 'Must be',
					values: this.getStatusValues(),
					value: this.getStatus(),
				}
			];
		},

		getStatusValues: function() {
			return [
				{ text: 'Logged In', value: 'logged-in' },
				{ text: 'Logged Out', value: 'logged-out' },
			];
		},

		getStatus: function() {
			var data = this.getData();

			if (data.status) {
				return data.status;
			} else {
				return 'logged-in';
			}
		}

	});

	var LoginRestrictedPlugin = function() {
	};

	LoginRestrictedPlugin.prototype = $.extend(new VisualShortcodeRedux.Plugin(), {

		getName: function() {
			return 'loginRestricted';
		},

		getShortcodeTag: function() {
			return 'login-restricted';
		},

		getDisplayName: function() {
			return 'Login Restriction';
		},

		getMetaLabel: function(data) {
			return 'Must be: ' + this.getMetaStatus(data);
		},

		getMetaStatus: function(data) {
			var status = data.status;

			if (status === 'logged-in' || status === 'Logged In') {
				return 'Logged In';
			} else if (status === 'logged-out' || status === 'logged-out') {
				return 'Logged Out';
			} else {
				return 'N/A';
			}
		},

		getMenu: function() {
			var menu = new LoginRestrictedMenu();
			menu.plugin = this;

			return menu;
		},

		getDialog: function(isNew) {
			var dialog = new LoginRestrictedDialog();
			dialog.isNew = isNew;
			dialog.data = {};
			dialog.plugin = this;

			return dialog;
		}

	});

	$(document).ready(function() {
		VisualShortcodeRedux.registerPlugin('loginRestricted', LoginRestrictedPlugin);
	});

}(jQuery));

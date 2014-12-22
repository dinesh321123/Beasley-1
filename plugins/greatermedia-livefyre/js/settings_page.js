(function($) {

	var ajaxApi;

	var getUserSettings = function() {
		var settings = {
			network_name : $('#network_name').val(),
			network_key  : $('#network_key').val(),
			site_id      : $('#site_id').val(),
			site_key     : $('#site_key').val()
		};

		return settings;
	};

	var saveSettings = function() {
		var settings = getUserSettings();
		var params = {
			'settings': settings
		};

		changeSubmitEnabled(false);
		changeStatus('updated', 'Saving Settings ...');

		ajaxApi.request('change_livefyre_settings', params)
			.then(didSaveSettings)
			.fail(didSaveSettingsError);
	};

	var didSaveSettings = function(response) {
		if (response.success) {
			changeSubmitEnabled(true);
			changeStatus('updated', 'Settings Saved.');
		} else {
			didSaveSettingsError(response);
		}
	};

	var didSaveSettingsError = function(response) {
		changeSubmitEnabled(true);
		changeStatus('error', 'Failed to save settings: ' + response.data);
	};

	var changeSubmitEnabled = function(enabled) {
		$submit = $('#submit');
		$submit.toggleClass('disabled', !enabled);
	};

	var prevType = null;
	var changeStatus = function(type, message) {
		var $status = $('#settings-message');
		$status.css('display', 'block');

		if (prevType) {
			$status.toggleClass(prevType, false);
		}

		$status.toggleClass(type, true);
		prevType = type;

		var $p = $('p', $status);
		$p.text(message);
	};

	var main = function() {
		var config = window.livefyre_settings_data;
		ajaxApi = new WpAjaxApi(config.data);

		var $submit = $('#submit');
		$submit.on('click', function() {
			saveSettings();
		});
	};

	$(document).ready(function() {
		main();
	});

}(jQuery));

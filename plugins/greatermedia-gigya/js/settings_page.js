(function ($) {

	var saveGigyaSettings = function(ajaxApi) {
		var data = {
			gigya_api_key: $('#gigya_api_key').val(),
			gigya_secret_key: $('#gigya_secret_key').val()
		};

		changeStatus('updated', 'Verifying Settings ...');

		$submit = $('#submit');
		$submit.toggleClass('disabled', true);

		ajaxApi.request('change_gigya_settings', data)
			.then(saveSuccess)
			.fail(saveError);
	};

	var saveSuccess = function(response) {
		if (!response.success) {
			return saveError(response);
		}

		changeStatus('updated', 'Settings Saved.');
		resetSubmit();
	};

	var saveError = function(response) {
		changeStatus('error', response.data);
		resetSubmit();
	};

	var resetSubmit = function() {
		$submit = $('#submit');
		$submit.toggleClass('disabled', false);
	};

	var prevType = null;
	var changeStatus = function(type, message) {
		var $status = $('#member-query-status-message');
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
		var config = window.member_query_settings;
		config.change_gigya_settings_nonce = $('#change_gigya_settings_nonce').val();

		var ajaxApi = new WpAjaxApi(config);
		var $submit = $('#submit');

		$submit.on('click', function() {
			saveGigyaSettings(ajaxApi);
		});
	};

	$(document).ready(function() {
		main();
	});

}(jQuery));

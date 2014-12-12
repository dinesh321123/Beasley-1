/**
 * Greater Media Gravity Forms Integration js
 * http://wordpress.org/themes
 *
 */

jQuery(function($) {
	'use strict';

	function init() {
		gigya.accounts.getAccountInfo({ callback: getAccountInfoResponse });
		bindRegisterLogin();

		// Handle successful login / registration
		gigya.socialize.addEventHandlers({
			onLogin:loginSuccess
		});
	}

	function bindRegisterLogin() {
		$('.register').on('click', showRegisterForm);
		$('.login').on('click', showLoginForm);
	}

	function showRegisterForm(e) {
		e.preventDefault();
		gigya.accounts.showScreenSet({screenSet: 'Contest-login', startScreen:'gigya-register-screen', containerID:'gigya-controls'});
	}

	function showLoginForm(e) {
		e.preventDefault();
		gigya.accounts.showScreenSet({screenSet: 'Contest-login', startScreen:'gigya-login-screen', containerID:'gigya-controls'});
	}

	/**
	 * Checks to see if the user is logged in, changes the header appropriately.
	 * @param response
	 */
	function getAccountInfoResponse(response) {

		if (response.errorCode == 0) { // Success, this user is logged in

			//console.log(response);

			// set hidden form fields
			$('input#gigya_UID').val(response.UID);
			$('input#gigya_name').val(response.profile.firstName + ' ' + response.profile.lastName);

			// hide login/registration form
			$('#gigya-login-wrap').hide();

			// show gravity form
			$('form.hide').removeClass('hide');

		} else { // User not yet logged

			// show login/registration form
			gigya.accounts.showScreenSet({ screenSet:'Contest-login', startScreen:'gigya-register-screen', containerID:'gigya-controls' });

			// disable gform fields
			$('form.hide input, form.hide textarea, form.hide select').attr('disabled','disabled');

		}
	}

	/**
	 * On successful login, hide tab controls & show thank you screen
	 * @param response
	 */
	function loginSuccess(eventObj) {

		// Display thank you screen
		gigya.accounts.showScreenSet({
			screenSet:'Contest-login',
			startScreen:'gigya-thank-you-screen',
			containerID:'gigya-controls'
		});

		// hide login/registration form
		$('#gigya-login-wrap').hide();

		// show gravity form
		$('form.hide input, form.hide textarea, form.hide select').removeAttr('disabled');
		$('form.hide').removeClass('hide');

	}

	init();

});
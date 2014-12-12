jQuery(function () {

	// Hide content if the criteria (logged in or not logged in) is met
	if ('undefined' !== typeof GreaterMediaGigyaAuth && GreaterMediaGigyaAuth.is_gigya_user_logged_in()) {
		jQuery('.login-restricted-content').filter('[data-status="logged-out"]').hide();
	}
	else {
		jQuery('.login-restricted-content').filter('[data-status="logged-in"]').hide();
	}

});
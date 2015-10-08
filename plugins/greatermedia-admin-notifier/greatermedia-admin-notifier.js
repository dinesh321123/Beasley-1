window.GreaterMediaAdminNotifier = {

	message: function (message) {
		jQuery('#wpbody-content .wrap h2').after('<div class="updated below-h2"><p>' + message + '</p></div>');
	},

	error: function (message) {
		jQuery('#wpbody-content .wrap h2').after('<div class="error below-h2"><p>' + message + '</p></div>');
	}

}
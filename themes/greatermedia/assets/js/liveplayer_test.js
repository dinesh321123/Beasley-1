var gigya = gigya || {};
gigya.accounts = gigya.accounts || {};
gigya.accounts.eventHandlers = gigya.accounts.eventHandlers || {};

/**
 * @see http://developers.gigya.com/020_Client_API/020_Accounts/accounts.addEventHandlers
 * @type {object}
 */
gigya.accounts.addEventHandlers = gigya.accounts.addEventHandlers || function (params) {

	// I have no idea how Gigya keeps track of multiple even handlers internally. I'm just faking it to get this done.
	var uniqid = Math.floor((Math.random() * 100000) + 1);

	if (undefined === gigya.accounts.eventHandlers[uniqid]) {
		gigya.accounts.eventHandlers[uniqid] = {};
	}

	if (undefined !== params.onLogin) {
		gigya.accounts.eventHandlers[uniqid].login = params.onLogin;
	}

	if (undefined !== params.onLogout) {
		gigya.accounts.eventHandlers[uniqid].logout = params.onLogout;
	}

	if (undefined !== params.cid) {
		gigya.accounts.eventHandlers[uniqid].cid = params.cid;
	}

	if (undefined !== params.callback) {
		gigya.accounts.eventHandlers[uniqid].callback = params.callback;
	}

	if (undefined !== params.context) {
		gigya.accounts.eventHandlers[uniqid].cid = params.context;
	}

	return {
		errorCode   : 0,
		errorMessage: '',
		operation   : 'addEventHandlers',
		context     : params.context || {}
	};

}

gigya.accounts._callEventHandlers = gigya.accounts._callEventHandlers || function (event_name) {

	var event_data = {
		eventName: event_name,
	}

	console.log('called ' + event_name);
	console.log(gigya.accounts.eventHandlers);

	for (var registration_index in gigya.accounts.eventHandlers) {
		if (gigya.accounts.eventHandlers.hasOwnProperty(registration_index)) {
			if (undefined !== gigya.accounts.eventHandlers[registration_index][event_name]) {

				event_data.context = gigya.accounts.eventHandlers[registration_index].context || undefined;
				if ('login' === event_name) {
					event_data = {
						eventName         : event_name,
						context           : gigya.accounts.eventHandlers[registration_index].context || undefined,
						UID               : '12345',
						UIDSignature      : '12345',
						signatureTimestamp: new Date().getTime(),
						loginMode         : 'standard',
						provider          : 'Twitter',
						profile           : {},
						data              : {},
						remember          : true
					};
				}

				gigya.accounts.eventHandlers[registration_index][event_name].call(window, event_data);
			}
		}
	}
}

jQuery(function () {

	var $window = jQuery(window),
		livePlayerListen = jQuery('#live-stream__listen-now'), // targets the `Listen Live` button
		livePlayerPlaying = jQuery('#live-stream__now-playing'),
		livePlayerTest = jQuery('.live-stream__test'), // targets the div that contains the test toggle
		livePlayerLabel = jQuery('.live-stream__test--label'),
		livePlayerSwitch = jQuery('.live-stream__test--audio'), // targets the actual toggle so we can bind a click to it
		livePlayer = jQuery('.live-stream__player'), // targets the live player
		livePlayerVolume = jQuery('.live-player__volume'),
		onAir = jQuery('.on-air'),
		upNext = jQuery('.up-next');

	function listenLive() {
		/**
		 * By default, the `Listen Now` button will not be visible. When the gigya API is authenticated, this state will
		 * change. In order to not change this function that we will need for live use, we will just target the div here
		 * and change the state
		 */
		livePlayerListen.css('display', 'inline-block');
		livePlayerPlaying.css('display', 'none');
		livePlayerVolume.css('display', 'none');
		upNext.css('display', 'none');
		livePlayerLabel.css('color', '#ffffff');

		/**
		 * This statement will check if a the user has authenticated with Gigya.
		 *
		 * If the user has authenticated, the `Listen Now` button will not display, the test toggle will be checked in
		 * order to provide another point of verification or authentication, and the live player will be displayed.
		 *
		 * If the user has not authenticated, the `Listen Now` button will be displayed while the toggle and livePlayer
		 * will not display.
		 *
		 * This will run when a page is loaded so that the click actions defined below do not have to take place.
		 */
		if (GreaterMediaGigyaAuth.is_gigya_user_logged_in()) {
			livePlayerSwitch.prop('checked', 'checked');
			livePlayer.css('display', 'block');
			livePlayerListen.css('display', 'none');
			livePlayerPlaying.css('display', 'inline-block');
			livePlayerTest.css('display', 'block');
			livePlayerVolume.css('display', 'block');
			upNext.css('display', 'block');
			onAir.css('display', 'none');
		} else {
			livePlayerListen.css('display', 'inline-block');
			livePlayerPlaying.css('display', 'none');
			livePlayerTest.css('display', 'none');
			livePlayer.css('display', 'none');
			livePlayerVolume.css('display', 'none');
			upNext.css('display', 'none');
			onAir.css('display', 'block');
		}

		/**
		 * This statement binds a click action to the `Listen Now` button
		 *
		 * When a user clicks the `Listen Now` button, the test toggle will be displayed and the `Listen Now` button
		 * will be hidden.
		 */
		livePlayerListen.click(function() {
			if ( livePlayerTest.css('display') == 'block') {
				livePlayerTest.css('display', 'none');
				livePlayerListen.css('display', 'inline-block');
			} else {
				livePlayerTest.css('display', 'block');
				livePlayerListen.css('display', 'none');
			}
		});

		/**
		 * This function creates the login and logout functions
		 *
		 * When a user clicks an associated element, the live player will be displayed.
		 *
		 * When a user un-checks the toggle, the live player will be hidden, the toggle will be hidden, and the `Listen
		 * Now` button will be displayed.
		 *
		 * This also runs a check to see if the toggle has been checked. If so, a cookie will be set showing that the
		 * user has been authenticated with Gigya, while unchecking will change the state of the cookie to logged out.
		 */
		livePlayerSwitch.click(function() {

			var event;

			if (livePlayer.css('display') == 'none') {
				livePlayer.css('display', 'block');
				livePlayerListen.css('display', 'none');
				livePlayerPlaying.css('display', 'inline-block');
				livePlayerTest.css('display', 'block');
				livePlayerVolume.css('display', 'block');
				upNext.css('display', 'block');
				onAir.css('display', 'none');
			} else {
				livePlayer.css('display', 'none');
				livePlayerListen.css('display', 'inline-block');
				livePlayerPlaying.css('display', 'none');
				livePlayerTest.css('display', 'none');
				livePlayerVolume.css('display', 'none');
				upNext.css('display', 'none');
				onAir.css('display', 'block');
			}

			if (livePlayerSwitch.is(':checked')) {
				// Logged into gigya
				gigya.accounts._callEventHandlers('login');
			}
			else {
				// Logged out of gigya
				gigya.accounts._callEventHandlers('logout');
			}

		});

	}

	listenLive();

});
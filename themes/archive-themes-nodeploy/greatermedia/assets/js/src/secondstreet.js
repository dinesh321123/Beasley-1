(function ($, document) {
	var $document = $(document),
		__ready;

	__ready = function() {
		$('div[data-ss-embed]').each(function() {
			var $ssembed = $(this),
				$script = $(document.createElement('script'));

			$.each($ssembed.prop("attributes"), function() {
				$script.attr(this.name, this.value);
			});

			$ssembed.replaceWith($script);
		});
	};

	$document.bind('pjax:end', __ready).ready(__ready);
})(jQuery, document);

(function(window) {
	function SecondStreetAuth() {
		this.id = 2;
		this._loginHandlers = [];
		this._logoutHandlers = [];
		this._loginCanceledHandlers = [];
	}

	SecondStreetAuth.prototype.triggerLoginHandlers = function() {
		var loginData = this._loginData();
		for (var i = 0; i < this._loginHandlers.length; i++) {
			this._loginHandlers[i](loginData);
		}
	};

	SecondStreetAuth.prototype.triggerLogoutHandlers = function() {
		var logoutData = this._logoutData();
		for (var i = 0; i < this._logoutHandlers.length; i++) {
			this._logoutHandlers[i](logoutData);
		}
	};

	SecondStreetAuth.prototype.triggerLoginCanceledHandlers = function() {
		var logoutData = this._logoutData();
		for (var i = 0; i < this._loginCanceledHandlers.length; i++) {
			this._loginCanceledHandlers[i](logoutData);
		}
	};

	/**
	 * purpose: A method that, when called, informs Second Street if the user
	 *          is currently logged into your authentication system. If the user
	 *          is logged in, it should return their LoginData. If the user is
	 *          not logged in, it should return null.
	 *
	 * context: Second Street will call this method when its embed code starts
	 *          up, so that it can know the initial user login state before
	 *          attaching event handlers. Second Street may call this method
	 *          one or more times.
	 */
	SecondStreetAuth.prototype.isLoggedIn = function() {
		var data = this._loginData();
		return data.uuid ? data : null;
	};

	/**
	 * purpose: A method that, when called, informs Second Street if the user
	 *          is currently logged into your authentication system. If the user
	 *          is logged in, it should return their LoginData. If the user is
	 *          not logged in, it should return null.
	 *
	 * context: Second Street will call this method when its embed code starts
	 *          up, so that it can know the initial user login state before
	 *          attaching event handlers. Second Street may call this method
	 *          one or more times.
	 */
	SecondStreetAuth.prototype.requestLogin = function() {
		return false;
	};

	/**
	 * purpose: A method that, when called, registers a function to be called
	 *          when the user logs into your authentication system.
	 *
	 * context: Second Street will call this method when its embed code starts
	 *          up, so that it can log the user into the embedded content when
	 *          they log into your site. Second Street may attach one or more
	 *          handlers.
	 */
	SecondStreetAuth.prototype.addLoginHandler = function(fn) {
		if (this._loginHandlers.indexOf(fn) < 0) {
			this._loginHandlers.push(fn);
		}
	};

	/**
	 * purpose: A method that, when called, registers a function to be called
	 *          when your website's login UI (the same one requestLogin() shows)
	 *          is aborted by the user without logging into your website.
	 *
	 * context: Second Street will call this method when its embed code starts
	 *          up, so that it is aware when the user chooses not to log in.
	 *          Second Street may attach one or more handlers.
	 */
	SecondStreetAuth.prototype.addLoginCanceledHandler = function(fn) {
		if (this._loginCanceledHandlers.indexOf(fn) < 0) {
			this._loginCanceledHandlers.push(fn);
		}

	};

	/**
	 * purpose: A method that, when called, registers a function to be called
	 *          when the user logs out of your authentication system.
	 *
	 * context: Second Street will call this method when its embed code starts
	 *          up, so that it can log the user out of the embedded content when
	 *          they log out of your site. Second Street may attach one or more
	 *          handlers.
	 */
	SecondStreetAuth.prototype.addLogoutHandler = function(fn) {
		if (this._logoutHandlers.indexOf(fn) < 0) {
			this._logoutHandlers.push(fn);
		}
	};

	SecondStreetAuth.prototype._loginData = function() {
		var data = {
			thirdPartyId: this.id,
			uuid: false
		};

		var bbgi = window.BeasleyJavascriptInterface || {};
		if (typeof bbgi.getAuthorization === 'function') {
			var jwt = bbgi.getAuthorization();
			if (jwt && jwt.length) {
				try {
					var payload = jwt.split('.')[1] || '';
					var base64 = payload.replace(/-/g, '+').replace(/_/g, '/');
					var user = JSON.parse(window.atob(base64));

					data.uuid = user.user_id;
					data.email = user.email;
				} catch(e) {
				}
			}
		}

		return data;
	};

	SecondStreetAuth.prototype._logoutData = function() {
		return {
			thirdPartyId: this.id
		};
	};

//	window.SecondStreetThirdPartyAuth = new SecondStreetAuth();
})(window);

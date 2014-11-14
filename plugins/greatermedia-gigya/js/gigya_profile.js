(function($) {

	var GigyaSessionStore = function() {
		this.cookieValue = {};
	};

	GigyaSessionStore.prototype = {

		isEnabled: function() {
			return Cookies.enabled;
		},

		set: function(key, value) {
			this.cookieValue[key] = value;
		},

		get: function(key) {
			return this.cookieValue[key];
		},

		save: function(persistent) {
			Cookies.set(
				this.getCookieName(),
				this.serialize(this.cookieValue),
				this.getCookieOptions(persistent)
			);
		},

		load: function() {
			if (!this.isEnabled()) {
				return;
			}

			var cookieText  = Cookies.get(this.getCookieName());
			this.cookieValue = this.deserialize(cookieText);
		},

		clear: function() {
			var options = {
				path   : this.getCookiePath(),
				domain : this.getCookieDomain()
			};

			Cookies.expire(this.getCookieName(), options);
			this.cookieValue = {};
		},

		getCookieOptions: function(persistent) {
			return {
				path    : this.getCookiePath(),
				domain  : this.getCookieDomain(),
				secure  : this.isSecurePage(),
				expires : this.getCookieTimeout(persistent)
			};
		},

		getCookieName: function() {
			return 'gigya_profile';
		},

		getCookiePath: function() {
			return '/';
		},

		getCookieDomain: function() {
			return location.hostname;
		},

		getCookieTimeout: function(persistent) {
			// TODO: must mirror gigya sessions
			if (persistent) {
				return 365 * 24 * 60 * 60; // 1 year
			} else {
				return 30 * 60; // 30 minutes
			}
		},

		serialize: function(cookieValue) {
			return JSON.stringify(cookieValue);
		},

		deserialize: function(cookieText) {
			if (cookieText) {
				var cookieValue;

				try {
					cookieValue = JSON.parse(cookieText);
				} finally {
					if (!this.isObject(cookieValue)) {
						cookieValue = {};
					}
				}

				return cookieValue;
			} else {
				return {};
			}
		},

		isObject: function(obj) {
			return (!!obj) && (obj.constructor === Object);
		},

		isSecurePage: function() {
			return location.protocol === 'https:';
		},

	};

	var GigyaSession = function(store) {
		this.store   = store;
	};

	GigyaSession.prototype = {

		isEnabled: function() {
			return this.store.isEnabled();
		},

		isLoggedIn: function() {
			return !!this.getUserID();
		},

		register: function(profile) {
			// TODO: Trigger DS.Store sync callback
			this.login(profile);
		},

		login: function(profile) {
			for (var property in profile) {
				if (profile.hasOwnProperty(property)) {
					this.store.set(property, profile[property]);
				}
			}

			this.store.save();
		},

		logout: function() {
			this.store.clear();
		},

		getUserID: function() {
			return this.store.get('UID');
		},

	};

	var GigyaEventsListener = function(session, willRegister) {
		this.session      = session;
		this.willRegister = !!willRegister;

		gigya.accounts.addEventHandlers({
			onLogin: $.proxy(this.didLogin, this),
			onLogout: $.proxy(this.didLogout, this)
		});
	};

	GigyaEventsListener.prototype = {

		didLogin: function(response) {
			if (this.willRegister) {
				this.willRegister = false;
				this.didRegister(response);
				return;
			}

			var profile = this.profileForResponse(response);
			this.session.login(profile);
			this.redirect('/');
		},

		didRegister: function(response) {
			var profile = this.profileForResponse(response);
			this.session.register(profile);
			this.redirect('/');
		},

		didLogout: function() {
			this.session.logout();
			this.redirect('/');
		},

		profileForResponse: function(response) {
			var profile = {
				UID       : response.UID,
				firstName : response.profile.firstName,
				lastName  : response.profile.lastName,
				age       : response.profile.age,
				zip       : response.zip
			};

			return profile;
		},

		redirect: function(defaultDest) {
			var redirectUrl = this.getRedirectUrl(defaultDest);

			if (redirectUrl) {
				location.href = redirectUrl;
			}
		},

		getRedirectUrl: function(defaultDest) {
			var dest = this.getQueryParam('dest');

			if (dest.indexOf('/') === 0) {
				return dest;
			} else {
				return defaultDest;
			}
		},

		// StackOverflow: 901115
		getQueryParam: function(name) {
			name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
			var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
				results = regex.exec(location.search);
			return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
		}

	};

	var GigyaScreenSetView = function(config, screenSet, session) {
		this.config    = config;
		this.screenSet = screenSet;
		this.session   = session;
	};

	GigyaScreenSetView.prototype = {

		render: function() {
			this.show(this.getCurrentScreen());
		},

		show: function(name) {
			console.log('show', name);
			if (name !== 'gigya-logout-screen') {
				gigya.accounts.showScreenSet({
					screenSet: this.screenSet,
					startScreen: name,
					containerID: 'profile-content'
				});
			} else {
				gigya.accounts.logout({
					cid: this.session.getUserID(),
				});
			}
		},

		getCurrentScreen: function() {
			var pageName = this.config.current_page;
			return this.pageToScreenSet(pageName);
		},

		screenSets: {
			'register'        : 'gigya-register-screen',
			'login'           : 'gigya-login-screen',
			'logout'          : 'gigya-logout-screen',
			'forgot-password' : 'gigya-forgot-password-screen',
			'settings'        : 'gigya-update-profile-screen'
		},

		pageToScreenSet: function(pageName) {
			var screenSet = this.screenSets[pageName];

			if (screenSet) {
				return screenSet;
			} else {
				return 'gigya-login-screen';
			}
		}

	};

	var GigyaProfileApp = function() {
		this.config       = window.gigya_profile_meta;
		var willRegister  = this.config.current_page === 'register';

		this.store          = new GigyaSessionStore();
		this.session        = new GigyaSession(this.store);
		this.eventsListener = new GigyaEventsListener(this.session, willRegister);
	};

	GigyaProfileApp.prototype = {

		run: function() {
			this.store.load();
			this.screenSetView = new GigyaScreenSetView(
				this.config,
				this.getCurrentScreenSet(),
				this.session
			);

			this.screenSetView.render();
		},

		getCurrentPage: function() {
			return this.config.current_page;
		},

		getCurrentScreenSet: function() {
			switch (this.getCurrentPage()) {
				case 'settings':
					return 'GMR-ProfileUpdate';

				default:
					return 'GMR-RegistrationLogin';
			}
		}

	};

	$(document).ready(function() {
		var app = new GigyaProfileApp();
		app.run();
	});

}(jQuery));

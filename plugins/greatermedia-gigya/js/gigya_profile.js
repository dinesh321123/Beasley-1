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
			var value = this.cookieValue[key];
			return value;
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
			var cookieText = JSON.stringify(cookieValue);
			if (window.btoa) {
				return btoa(cookieText);
			} else {
				return cookieText;
			}
		},

		deserialize: function(cookieText) {
			if (cookieText) {
				var cookieValue;

				try {
					if (window.atob) {
						cookieText = atob(cookieText);
					}
					cookieValue = JSON.parse(cookieText);
				} catch (err) {
					// ignore
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

	var GigyaSessionController = function(session, willRegister) {
		this.session      = session;
		this.willRegister = !!willRegister;

		gigya.accounts.addEventHandlers({
			onLogin: $.proxy(this.didLogin, this),
			onLogout: $.proxy(this.didLogout, this)
		});
	};

	GigyaSessionController.prototype = {

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
			var anchor = this.getQueryParam('anchor');

			if (dest.indexOf('/') === 0) {
				if (anchor !== '') {
					return dest + '#' + anchor;
				} else {
					return dest;
				}
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
		this.config         = config;
		this.screenSet      = screenSet;
		this.session        = session;
		this.activeScreenID = '';

		this.didBeforeScreenHandler = $.proxy(this.didBeforeScreen, this);
		this.didAfterScreenHandler  = $.proxy(this.didAfterScreen, this);
		this.didErrorHandler        = $.proxy(this.didError, this);
	};

	GigyaScreenSetView.prototype = {

		render: function() {
			this.show(this.getCurrentScreen());

			var $message = $('.profile-page__sidebar .profile-header-link');
			$message.on('click', $.proxy(this.didProfileHeaderClick, this));
		},

		show: function(name) {
			this.activeScreenID = name;

			gigya.accounts.showScreenSet({
				screenSet: this.screenSet,
				startScreen: name,
				containerID: 'profile-content',
				onBeforeScreenLoad: this.didBeforeScreenHandler,
				onAfterScreenLoad: this.didAfterScreenHandler,
				onError: this.didErrorHandler,
				onBeforeSubmit: this.didBeforeSubmitHandler,
				onFieldChanged: this.didBeforeSubmitHandler,
			});

			if (name === 'gigya-logout-screen') {
				gigya.accounts.logout({
					cid: this.session.getUserID(),
				});
			}
		},

		getCurrentScreen: function() {
			var pageName = this.config.current_page;
			return this.pageToScreenSet(pageName);
		},

		getPageForScreenID: function(screenID) {
			switch (screenID) {
				case 'gigya-login-screen':
				case 'gigya-logout-screen':
				case 'gigya-login-success-screen':
					return 'login';

				case 'gigya-register-screen':
				case 'gigya-register-complete-screen':
					return 'join';

				case 'gigya-update-profile-screen':
					return 'account';

				case 'gigya-forgot-password-screen':
					return 'forgot-password';

				default:
					throw new Error( 'Unknown activeScreenID: ' + this.activeScreenID );
			}
		},

		screenSets            : {
			'join'            : 'gigya-register-screen',
			'login'           : 'gigya-login-screen',
			'logout'          : 'gigya-logout-screen',
			'forgot-password' : 'gigya-forgot-password-screen',
			'account'         : 'gigya-update-profile-screen'
		},

		screenLabels: {
			join: {
				header: 'Register',
				message: 'Membership gives you access to all areas of the site, including full membership-only contests and the ability to submit content to share with the site and other members.',
			},
			login: {
				header: 'Login',
				message: 'Please enter your login details to access full membership-only contests and the ability to submit content to share with the site and other members.',
			},
			account: {
				header: 'Manage Your Account',
				message: 'Help us get to know you better, manage your communication preferences, or change your password.'
			},
			'forgot-password': {
				header: 'Password Reset',
				message: 'Forgot your password? No worries, it happens. We\'ll send you a password reset email.'
			},
			'cookies-required': {
				header: 'Cookies Required',
				message: 'It doesn\'t look like your browser is letting us set a cookie. These small bits of information are stored in your browser and allow us to ensure you stay logged in. They are required to use the site and can generally be authorized in your browser\'s preferences or settings screen.'
			}
		},

		pageToScreenSet: function(pageName) {
			var screenSet = this.screenSets[pageName];

			if (screenSet) {
				return screenSet;
			} else {
				return 'gigya-login-screen';
			}
		},

		didBeforeScreen: function(event) {
			var screenID = event.nextScreen;
			this.updateSidebar(this.getPageForScreenID(screenID));
		},

		didAfterScreen: function(event) {
			this.scrollToTop();
		},

		didError: function(event) {
			console.log('didError', event);
		},

		scrollToTop: function() {
			var root   = $('html, body');
			var target = $('#profile-content');
			var params = {
				scrollTop: target.offset().top
			};

			//console.log('animate', params);
			root.animate(params, 500);
		},

		updateSidebar: function(screenName) {
			var $header  = $('.profile-page__sidebar .profile-header-text');
			var $message = $('.profile-page__sidebar .profile-message');
			var $sep     = $('.profile-page__sidebar .profile-header-sep');
			var $link    = $('.profile-page__sidebar .profile-header-link');
			var labels   = this.screenLabels[screenName];

			if (screenName === 'login') {
				$link.text(this.screenLabels.join.header);
				$link.css('display', 'inline');
				$sep.css('display', 'inline');
			} else if (screenName === 'join') {
				$link.text(this.screenLabels.login.header);
				$link.css('display', 'inline');
				$sep.css('display', 'inline');
			} else {
				$link.css('display', 'none');
				$sep.css('display', 'none');
			}

			$header.text(labels.header);
			$message.text(labels.message);
		},

		didProfileHeaderClick: function(event) {
			var $link = $('.profile-page__sidebar .profile-header-link');
			var text = $link.text().toLowerCase(); // KLUDGE, WIP

			if (text === 'login') {
				//this.controller.redirect('/members/login');
				this.show('gigya-login-screen');
			} else if (text === 'register') {
				//this.controller.redirect('/members/join');
				this.show('gigya-register-screen');
			}

			return false;
		}

	};

	var GigyaProfileApp = function() {
		this.config       = window.gigya_profile_meta;
		var willRegister  = this.config.current_page === 'register';

		this.store      = new GigyaSessionStore();
		this.session    = new GigyaSession(this.store);
		this.controller = new GigyaSessionController(this.session, willRegister);
	};

	GigyaProfileApp.prototype = {

		run: function() {
			var currentPage = this.getCurrentPage();

			if (this.store.isEnabled()) {
				this.store.load();

				if (this.session.isLoggedIn()) {
					if (currentPage === 'login' || currentPage === 'register' || currentPage == 'forgot-password') {
						this.controller.redirect('/');
						return;
					}
				} else {
					if (currentPage === 'account' || currentPage === 'logout') {
						this.controller.redirect('/members/login?dest=%2Fmembers%2Faccount');
						return;
					}
				}

				this.screenSetView = new GigyaScreenSetView(
					this.config,
					this.getCurrentScreenSet(),
					this.session
				);

				this.screenSetView.controller = this.controller; // KLUDGE
				this.screenSetView.render();
			} else if (currentPage !== 'cookies-required') {
				this.controller.redirect('/members/cookies-required');
			}
		},

		/* must be logged in to access these pages */
		loggedInPages: [
			'logout',
			'account'
		],

		isLoggedInPage: function(pageName) {
			return _.indexOf(this.loggedInPages, pageName) !== -1;
		},

		getCurrentPage: function() {
			return this.config.current_page;
		},

		getCurrentScreenSet: function() {
			return 'GMR-CustomScreenSet';
			switch (this.getCurrentPage()) {
				case 'account':
					return this.config.gigya_account_screenset;

				default:
					return this.config.gigya_auth_screenset;
			}
		}

	};

	var app = new GigyaProfileApp();

	$(document).ready(function() {
		app.run();
	});

	// TODO: the helpers probably need to be separate
	window.is_gigya_user_logged_in = function() {
		return app.session.isEnabled() && app.session.isLoggedIn();
	};

	window.get_gigya_user_id = function() {
		return app.session.getUserID();
	};

	window.get_gigya_user_field = function(field) {
		return app.session.getUserField(field);
	};

	// KLUDGE: Duplication
	$(document).on('pjax:beforeSend', function(event, xhr, settings) {
		var url = settings.url;
		var a = document.createElement('a');
		a.href = url;

		var search   = a.search.replace('_pjax=.page-wrap', '');
		search       = search.replace('_pjax=.main', '');
		var pathname = a.pathname + search;

		if (pathname.indexOf('/members/') === 0) {
			location.href = pathname;
			return false;
		} else {
			return true;
		}

	});

}(jQuery));

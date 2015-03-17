(function($) {

	var ajaxApi = new WpAjaxApi(window.gigya_profile_meta);

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
			var options = this.getCookieOptions(persistent);

			Cookies.set(
				this.getCookieName(),
				this.serialize(this.cookieValue),
				options
			);

			// if you've just logged in, assuming that the previous
			// livefyre token is now invalid, it will auto-refresh the
			// next time you visit a page that supports comments
			Cookies.expire('livefyre_token', options);
		},

		load: function() {
			if (!this.isEnabled()) {
				return;
			}

			var cookieText   = Cookies.get(this.getCookieName());
			this.cookieValue = this.deserialize(cookieText);
		},

		clear: function() {
			var options = {
				path   : this.getCookiePath(),
				domain : this.getCookieDomain()
			};

			Cookies.expire(this.getCookieName(), options);
			Cookies.expire('livefyre_token', options);
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

		save: function(profile) {
			for (var property in profile) {
				if (profile.hasOwnProperty(property)) {
					this.store.set(property, profile[property]);
				}
			}

			this.store.save(true);
		},

		register: function(profile) {
			this.save(profile);
			return ajaxApi.request('register_account', {});
		},

		login: function(profile) {
			this.save(profile);
			return ajaxApi.request('gigya_login', {});
		},

		update: function(profile) {
			this.store.clear();
			this.save(profile);
		},

		logout: function() {
			this.store.clear();
		},

		getUserID: function() {
			return this.store.get('UID');
		},

		getUserField: function(field) {
			if (this.isLoggedIn()) {
				return this.store.get(field);
			} else {
				return null;
			}
		}
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
			var self = this;

			this.session.login(profile)
				.then(function() {
					self.redirect('/', 'login');
				})
				.fail(function() {
					// TODO: What to do if gigya_login ajax failed?
					self.redirect('/', login);
				});
		},

		didRegister: function(response) {
			var profile = this.profileForResponse(response);
			var self    = this;

			this.session.register(profile)
				.then(function() {
					self.redirect('/');
				})
				.fail(function() {
					// TODO: What to do if account_register failed?
					self.redirect('/');
				});
		},

		didLogout: function() {
			this.session.logout();
			this.redirect('/');
		},

		didProfileUpdate: function(profile, data) {
			var response = {
				UID: this.session.getUserID(),
				profile: profile,
				data: data
			};

			var profile_to_update = this.profileForResponse(response);
			this.session.update(profile_to_update);
			ajaxApi.request('update_account', {});
		},

		profileForResponse: function(response) {
			var profile = {
				UID            : response.UID,
				firstName      : response.profile.firstName,
				lastName       : response.profile.lastName,
				age            : response.profile.age,
				zip            : response.profile.zip,
				nielsen_optout : response.data ? response.data.nielsen_optout : false
			};

			if (response.profile.thumbnailURL) {
				profile.thumbnailURL = response.profile.thumbnailURL;
			}

			return profile;
		},

		redirect: function(defaultDest, source) {
			var redirectUrl = this.getRedirectUrl(defaultDest);
			if (source === 'login' && redirectUrl.indexOf('/members/account') === 0) {
				// override redirect to account page when redirecting after login
				// even if dest is to the account page
				redirectUrl = '/';
			}

			if (redirectUrl) {
				if (location.replace) {
					location.replace(redirectUrl);
				} else {
					location.href = redirectUrl;
				}
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
		},

		resetPassword: function(newPassword) {
			var params = {
				api_key: this.getQueryParam('apiKey'),
				password_reset_token: this.getQueryParam('pwrt'),
				new_password: newPassword
			};

			this.screenSetView.show('gigya-reset-link-password-progress-screen');
			ajaxApi.request('reset_password', params)
				.then($.proxy(this.didResetPassword, this))
				.fail($.proxy(this.didResetPasswordError, this));
		},

		didResetPassword: function(response) {
			if (response.success) {
				this.screenSetView.show('gigya-reset-link-password-success-screen');
			} else {
				this.didResetPasswordError(response);
			}
		},

		didResetPasswordError: function(response) {
			this.resetError = response.data;
			this.screenSetView.show('gigya-reset-link-password-screen');
		}

	};

	var GigyaScreenSetView   = function(config, screenSet, session) {
		this.config          = config;
		this.screenSet       = screenSet;
		this.session         = session;
		this.activeScreenID  = '';
		this.submitTimeoutID = -1;

		this.didBeforeScreenHandler = $.proxy(this.didBeforeScreen, this);
		this.didAfterScreenHandler  = $.proxy(this.didAfterScreen, this);
		this.didErrorHandler        = $.proxy(this.didError, this);
		this.didLogoutClickHandler  = $.proxy(this.didLogoutClick, this);
		this.didBeforeSubmitHandler = $.proxy(this.didBeforeSubmit, this);
		this.didFieldChangeHandler  = $.proxy(this.didFieldChange, this);

		this.loadLabels();
	};

	GigyaScreenSetView.prototype = {

		render: function() {
			this.show(this.getCurrentScreen());

			var $message = $('.profile-page__sidebar .profile-header-link');
			$message.on('click', $.proxy(this.didProfileHeaderClick, this));
		},

		loadLabels: function() {
			var labels = ['join', 'login', 'logout', 'forgot-password', 'account'];
			for (var i = 0; i < labels.length; i++) {
				this.loadLabel(labels[i]);
			}
		},

		loadLabel: function(name) {
			var labelKey  = name;
			var configKey = name;

			if (!this.screenLabels[labelKey]) {
				this.screenLabels[labelKey] = { header: '', message: '' };
			}

			if (this.config[configKey + '_header']) {
				this.screenLabels[labelKey].header  = this.config[configKey + '_header'];
			}

			if (this.config[configKey + '_message']) {
				this.screenLabels[labelKey].message = this.config[configKey + '_message'];
			}
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
				onFieldChanged: this.didFieldChangeHandler
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

		getActiveScreen: function() {
			return this.getPageForScreenID(this.activeScreenID);
		},

		getPageForScreenID: function(screenID) {
			switch (screenID) {
				case 'gigya-login-screen':
				case 'gigya-login-success-screen':
					return 'login';

				case 'gigya-logout-screen':
					return 'logout';

				case 'gigya-register-screen':
				case 'gigya-register-complete-screen':
				case 'gigya-register-success-screen':
					return 'join';

				case 'gigya-update-profile-screen':
				case 'gigya-update-profile-success-screen':
				case 'gigya-change-password-screen':
				case 'gigya-change-password-success-screen':
					return 'account';

				case 'gigya-forgot-password-screen':
				case 'gigya-forgot-password-sent-screen':
					return 'forgot-password';

				case 'gigya-reset-link-password-screen':
				case 'gigya-reset-link-password-progress-screen':
				case 'gigya-reset-link-password-success-screen':
					return 'reset-password';

				case 'gigya-verify-email-screen':
				case 'gigya-resend-verification-code-screen':
				case 'gigya-resend-verification-code-success-screen':
				case 'gigya-resend-verification-code-update-screen':
				case 'gigya-resend-verification-code-update-success-screen':
					return 'verify-email';

				default:
					throw new Error( 'Unknown activeScreenID: ' + this.activeScreenID );
			}
		},

		screenSets            : {
			'join'            : 'gigya-register-screen',
			'login'           : 'gigya-login-screen',
			'logout'          : 'gigya-logout-screen',
			'forgot-password' : 'gigya-forgot-password-screen',
			'account'         : 'gigya-update-profile-screen',
			'reset-password'  : 'gigya-reset-link-password-screen',
			'verify-email'    : 'gigya-verify-email-screen',
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
			},
			'reset-password': {
				header: 'Reset Password',
				message: 'Enter your new password to reset it.'
			},
			'verify-email': {
				header: 'Email Verification',
				message: 'Please verify that your profile email belongs to you.'
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
			this.activeScreenID = event.currentScreen;
			this.scrollToTop();
			this.initSubmitListener();

			switch (event.currentScreen) {
				case 'gigya-update-profile-screen':
					this.registerLogoutButton();
					break;

				case 'gigya-register-complete-screen':
					this.controller.willRegister = true;
					this.updateGeoFields();
					break;

				case 'gigya-update-profile-success-screen':
					var profile = this.profileFromEvent(event),
						data = this.dataFromEvent(event);
					this.controller.didProfileUpdate(profile, data);
					break;

				case 'gigya-reset-link-password-screen':
					if (this.controller.resetError) {
						var $errorMsg = $('#gigya-reset-link-password-screen .reset-link-password-error-msg');
						$errorMsg.text(this.controller.resetError);
						$errorMsg.css('display', 'block');
						$errorMsg.css('visibility', 'visible');
					}
					break;

				case 'gigya-resend-verification-code-screen':
				case 'gigya-resend-verification-code-update-screen':
					if (event.profile.email) {
						var $resendEmail = $('#resend-email');
						$resendEmail.val(event.profile.email);
					}
					break;

			}
		},

		profileFromEvent: function(event) {
			var profile     = event.profile;
			var new_profile = event.response.requestParams.profile;

			for (var field in new_profile) {
				if (new_profile.hasOwnProperty(field) && profile.hasOwnProperty(field)) {
					profile[field] = new_profile[field];
				}
			}

			if (new_profile.birthYear) {
				profile.age = this.calcAge(new_profile.birthYear);
			}

			return profile;
		},

		dataFromEvent: function(event) {
			var data     = event.data;
			var new_data = event.response.requestParams.data;

			for (var field in new_data) {
				if (new_data.hasOwnProperty(field) && data.hasOwnProperty(field)) {
					data[field] = new_data[field];
				}
			}

			return data;
		},

		calcAge: function(birthYear) {
			var date        = new Date();
			var currentYear = date.getFullYear();
			var age         = Math.max(0, currentYear - birthYear);

			return age;
		},

		registerLogoutButton: function() {
			var $logout = $('#gigya-update-profile-screen .logout-button');
			$logout.one('click', this.didLogoutClickHandler);
		},

		didLogoutClick: function(event) {
			this.show('gigya-logout-screen');
			gigya.accounts.logout();
			return false;
		},

		didError: function(event) {
			if (console && console.log) {
				console.log('didError', event);
			}
		},

		scrollToTop: function() {
			var root   = $('html, body');
			var target = $('#profile-content');
			var params = {
				scrollTop: target.offset().top
			};

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
			$message.html(labels.message);
		},

		didProfileHeaderClick: function(event) {
			var $link = $('.profile-page__sidebar .profile-header-link');
			var screen = this.getActiveScreen();

			if (screen === 'login') {
				this.show('gigya-register-screen');
			} else if (screen === 'join') {
				this.show('gigya-login-screen');
			}

			return false;
		},

		didBeforeSubmit: function(event) {
			if (event.form === 'gigya-reset-link-password-form') {
				this.controller.resetPassword(event.formData.newPassword);
				return false;
			}
		},

		didFieldChange: function(event) {
			if (event.screen === 'gigya-update-profile-screen' && event.field === 'profile.email') {
				var $link = $('.verify-email-link');
				$link.css('display', 'inline-block');
			}
		},

		getStates: function() {
			var $profileState = $('#profile-state option');
			var states = [];

			$profileState.each(function(index, option) {
				var $option = $(option);
				states.push({label: $option.text(), value: $option.val()});
			});

			return states;
		},

		updateGeoFields: function() {
			var states        = this.getStates();
			var $profileCity  = $('#profile-city');
			var $profileState = $('#profile-state');
			var currentCity   = $profileCity.val();
			var parser        = new GeoLocationParser(states);
			var result = null;

			try {
				result = parser.parse(currentCity);
			} catch (e) {
				// no op
			}

			if (result !== null) {
				$profileCity.val(result.city);
				$profileState.val(result.state);
			} else {
				$profileCity.val('');
				$profileState.val('');
			}
		},

		initSubmitListener: function() {
			var $submitButton = $('.gigya-input-submit-button');
			$submitButton.click($.proxy(this.didInputSubmitClick, this));
		},

		didInputSubmitClick: function(event) {
			clearTimeout(this.submitTimeoutID);
			this.submitTimeoutID = setTimeout($.proxy(this.checkForSubmitErrors, this), 150);
		},

		checkForSubmitErrors: function() {
			var $activeErrors = $('.gigya-error-msg-active');
			if ($activeErrors.length > 0) {
				var $firstError = $($activeErrors[0]);
				var params = {
					scrollTop: $firstError.offset().top - 50
				};

				var root   = $('html, body');
				root.animate(params, 500);
			}
		}

	};

	var GeoLocationParser = function(states) {
		this.states = states;
	};

	GeoLocationParser.prototype = {

		parse: function(text) {
			var parts    = text.split(',');
			var total    = parts.length;
			var lastPart = this.trim(parts[total - 1]);
			var state, city;

			if (lastPart === 'United States' || lastPart === 'US' || lastPart === 'USA') {
				state = this.trim(parts[total - 2]);
				city  = this.trim(parts.slice(0, total - 2).join(', '));
			} else {
				state = lastPart;
				city  = this.trim(parts.slice(0, total - 1).join(', '));
			}

			var foundState = this.findState(state);

			if (foundState) {
				return {
					state: foundState,
					city: city
				};
			} else {
				return null;
			}
		},

		findState: function(state) {
			var n = this.states.length;
			var option;

			for (var i = 0; i < n; i++) {
				option = this.states[i];
				if (option.label === state) {
					return option.value;
				}
			}

			return null;
		},

		trim: function(str) {
			var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
			return str.replace(rtrim, '');
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
					if (currentPage === 'account') {
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
				this.controller.screenSetView = this.screenSetView;
				this.screenSetView.render();
			} else if (currentPage !== 'cookies-required') {
				this.controller.redirect('/members/cookies-required');
			}
		},

		rerun: function(pageName) {
			this.config.current_page = pageName;
			this.controller.screenSetView.render();
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

	// KLUDGE: Lots of duplication here
	var escapeValue = function(value) {
		value = '' + value;
		value = value.replace(/[!'()*]/g, escape);
		value = encodeURIComponent(value);

		return value;
	};

	var build_query = function(params) {
		var value;
		var output = [];
		var anchor;

		for (var key in params) {
			if (params.hasOwnProperty(key) && params[key]) {
				value = params[key];
				value = escapeValue(value);
				key   = escapeValue(key);

				output.push(key + '=' + value);
			}
		}

		return output.join('&');
	};

	var endpoint       = 'members';
	var actionFromPath = function(path) {
		var a = document.createElement('a');
		a.href = path;

		var pathname = a.pathname;
		var parts    = pathname.split('/');
		var total    = parts.length;

		if (parts[total-1] === '') {
			return parts[total-2];
		} else {
			return parts[total-1];
		}
	};

	window.gigya_profile_path = function(action, params) {
		if (!params) {
			params = {};
		}

		if (!params.dest && (action === 'login' || action === 'logout')) {
			params.dest = location.pathname;
		}

		var path       = '/' + endpoint + '/' + action;
		var destAction = actionFromPath(params.dest);

		if (action === destAction) {
			params.dest = undefined;
		}

		var query = build_query(params);

		if (query !== '') {
			return path + '?' + build_query(params);
		} else {
			return path;
		}
	};

	var getPathnameFromUrl = function(url) {
		var a = document.createElement('a');
		a.href = url;

		var search   = a.search.replace('_pjax=.page-wrap', '');
		search       = search.replace('_pjax=.main', '');

		var pathname = a.pathname + search;

		return pathname;
	};

	$(document).on('pjax:beforeSend', function(event, xhr, settings) {
		var pathname = getPathnameFromUrl(settings.url);

		if (pathname.indexOf('/members/') === 0) {
			if (window.gigya_profile_loaded) {
				return true;
			} else {
				location.href = pathname;
				return false;
			}
		} else {
			return true;
		}

	});

	$(document).on('pjax:end', function(event, xhr, settings) {
		var pathname = getPathnameFromUrl(settings.url);

		if (pathname.indexOf('/members/account') === 0) {
			app.rerun('account');
		} else if (pathname.indexOf('/members/logout') === 0) {
			app.rerun('logout');
		}
	});

	window.gigya_profile_loaded = true;

}(jQuery));

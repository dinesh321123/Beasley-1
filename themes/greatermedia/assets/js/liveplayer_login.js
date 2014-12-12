(function($) {

	var WpAjaxApi = function(config) {
		this.config = config;
	};

	WpAjaxApi.prototype = {

		nonceFor: function(action) {
			return this.config[action + '_nonce'];
		},

		urlFor: function(action) {
			var queryParams = {};
			queryParams[action + '_nonce'] = this.nonceFor(action);

			var url = this.config.ajax_url;
			url += url.indexOf('?') === -1 ? '?' : '&';
			url += $.param(queryParams);

			return url;
		},

		request: function(action, data) {
			if (!data) {
				data = {};
			}

			var url         = this.urlFor(action);
			var requestData = {
				'action': action,
				'action_data': JSON.stringify(data)
			};

			return $.post(url, requestData);
		},

	};

	var GigyaSession      = function(sessionData, ajaxApi) {
		this.sessionData  = sessionData;
		this.ajaxApi      = ajaxApi;
		this.authorized   = false;
		this.willRegister = false;
		this.mediator     = $({});

		gigya.accounts.addEventHandlers({
			onLogin: $.proxy(this.didLogin, this),
			onLogout: $.proxy(this.didLogout, this)
		});
	};

	GigyaSession.prototype = {

		get_cid: function() {
			return this.sessionData.cid || '';
		},

		isAuthorized: function() {
			return this.authorized;
		},

		authorize: function() {
			var cid = this.get_cid();

			if (cid !== '') {
				gigya.accounts.getAccountInfo({
					cid: cid,
					callback: $.proxy(this.gotAccountInfo, this)
				});
			} else {
				this.notify();
			}
		},

		gotAccountInfo: function(response) {
			if (response.errorCode === 0) {
				this.authorized = true;
				this.account    = response;
			} else {
				this.authorized = false;
			}

			this.notify();
		},

		on: function(event, listener) {
			this.mediator.on(event, listener);
		},

		notify: function(event) {
			if (!event) {
				event = 'change';
			}

			this.mediator.trigger(event, this);
		},

		didLogin: function(response) {
			if (this.willRegister) {
				this.didRegister(response);
			}

			this.account    = response;
			this.authorized = true;
			this.notify();

			var data = {
				'UID': response.UID
			};

			this.ajaxApi.request('gigya_login', data)
				.then($.proxy(this.didLoginRelay, this))
				.fail($.proxy(this.didLoginRelayError, this));
		},

		didLoginRelay: function(response) {
			location.reload();
		},

		didLoginRelayError: function(response) {
			console.log('didLoginRelayError', response);
		},

		didRegister: function(response) {
			this.willRegister = false;

			var listNames = [];

			// TODO: fix this after Gigya support ticket response
			if (response.data.vipGroup) {
				listNames.push('VIP Newsletter');
			}

			if (response.data.birthdayGreetingsGroup) {
				listNames.push('Birthday Greetings');
			}

			if (response.data.bigFrigginDealGroup) {
				listNames.push('Big Deal');
			}

			var data = {
				'UID': response.UID,
				'listNames': listNames
			};

			this.ajaxApi.request('register_account', data)
				.then($.proxy(this.didRegisterRelay, this))
				.fail($.proxy(this.didRegisterRelayError, this));
		},

		didRegisterRelay: function(response) {
			console.log('didRegisterRelay', response);
			location.reload();
		},

		didRegisterRelayError: function(response) {
			// TODO: UI
			console.log('didRegisterRelayError', response);
		},

		didLogout: function(response) {
			this.authorized = false;
			this.notify();
			this.ajaxApi.request('gigya_logout')
				.then($.proxy(this.didLogoutRelay, this))
				.fail($.proxy(this.didLogoutRelayError, this));
		},

		didLogoutRelay: function(response) {
			console.log('didLogoutRelay', response);
			location.reload();
		},

		didLogoutRelayError: function(response) {
			// TODO: UI
			console.log('didLogoutRelayError', response);
		},

	};

	var AccountMenuView = function(session) {
		this.session = session;
		this.session.on('change', $.proxy(this.didSessionChange, this));

		this.container = $('#live-player');
		this.container.on('click', $.proxy(this.didContainerClick, this));
	};

	AccountMenuView.prototype = {

		render: function() {
			var authorized = this.session.isAuthorized();

			$('#live-player--listen_now').css('visibility', 'visible');

			if (authorized) {
				$('.live-player--listen_btn').css('visibility', 'hidden');
				$('.gmlp-nav').css('visibility', 'visible');
				$('.gmlp-nav').addClass('open');
			} else {
				$('.live-player--listen_btn').css('visibility', 'visible');
				$('.gmlp-nav').css('visibility', 'hidden');
				$('.gmlp-nav').removeClass('open');
			}
		},

		didContainerClick: function(event) {
			var target = $(event.target);

			if (target.attr('id') === 'live-player--listen_now') {
				if (!this.session.isAuthorized()) {
					this.showLoginScreen();
				} else {
					this.showLogoutScreen();
				}
				return false;
			}
		},

		didSessionChange: function() {
			this.render();
		},

		showScreenSet: function(name) {
			gigya.accounts.showScreenSet({
				screenSet: 'GMR-RegistrationLogin',
				startScreen: name
			});
		},

		showLoginScreen: function() {
			this.showScreenSet('gigya-login-screen');
		},

		showRegisterScreen: function() {
			/* TODO: Find Gigya's onRegister event */
			this.session.willRegister = true;
			this.showScreenSet('gigya-register-screen');
		},

		showLogoutScreen: function() {
			gigya.accounts.logout({
				cid: this.session.get_cid(),
				callback: $.proxy(this.refresh, this)
			});
		},

		refresh: function() {
			location.reload();
		}

	};

	$(document).ready(function() {
		var sessionData     = window.gigya_session_data.data;
		var ajaxApi         = new WpAjaxApi(sessionData);
		var session         = new GigyaSession(sessionData, ajaxApi);
		var accountMenuView = new AccountMenuView(session);

		session.authorize();
	});

	/* jquery */
}(jQuery));

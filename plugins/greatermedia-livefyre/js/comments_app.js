(function($) {

	var CommentsConfig = function(config) {
		this.config = config;
	};

	CommentsConfig.prototype = {

		getNetworkConfig: function() {
			return {
				network: this.config.livefyre_options.network_name
			};
		},

		getConvConfig: function() {
			return [{
				siteId         : this.config.livefyre_options.site_id,
				articleId      : this.config.livefyre_options.article_id,
				el             : 'livefyre-comments',
				collectionMeta : this.config.tokens.collection_meta,
				checksum       : this.config.tokens.checksum,
				readOnly       : this.config.livefyre_options.read_only
			}];
		},

		getToken: function(name) {
			return this.config.tokens[name];
		},

		getOption: function(name) {
			return this.config.livefyre_options[name];
		}

	};

	var GigyaAuthDelegate = function(config) {
		this.config = config;
	};

	GigyaAuthDelegate.prototype = {

		login: function(callback) {
			if (is_gigya_user_logged_in()) {
				callback(null, this.getAuthParams());
			} else {
				this.redirect('login', {
					dest: this.config.getOption('article_path'),
					anchor: 'livefyre-comments'
				});
			}
		},

		logout: function(callback) {
			callback(null);
			this.redirect('logout', {
				dest: this.config.getOption('article_path'),
				anchor: 'livefyre-comments'
			});
		},

		editProfile: function() {
			this.redirect('account', { mode: 'edit' });
		},

		viewProfile: function() {
			this.redirect('account', { mode: 'view' });
		},

		redirect: function(actionName, params) {
			var profilePath = gigya_profile_path(actionName, params);
			location.href = profilePath;
		},

	};

	var CommentsApp = function() {
		this.config = new CommentsConfig(
			window.livefyre_comments_data.data
		);

		this.ajaxApi   = new WpAjaxApi(window.livefyre_comments_data);
		this.authToken = this.loadAuthToken();

		if (this.canLoadComments()) {
			this.authorize();
		} else {
			var self = this;

			$(document).ready(function() {
				self.authorize();
			});
		}
	};

	CommentsApp.prototype = {

		authorize: function() {
			if (is_gigya_user_logged_in()) {
				this.ajaxApi.request('get_livefyre_auth_token', {})
					.then($.proxy(this.didAuthorize, this))
					.fail($.proxy(this.didAuthorizeError, this));
			} else {
				this.load();
			}
		},

		loadAuthToken: function() {
			if (is_gigya_user_logged_in()) {
				var value = Cookies.get('livefyre_token');

				if (value) {
					return value;
				} else {
					return '';
				}
			} else {
				Cookies.expire('livefyre_token');
				return '';
			}
		},

		saveAuthToken: function(value) {
			Cookies.set('livefyre_token', value, this.getCookieOptions(true));
		},

		getCookieOptions: function(persistent) {
			return {
				path    : '/',
				domain  : location.hostname,
				secure  : location.protocol === 'https:',
				expires : this.getCookieTimeout(persistent)
			};
		},

		getCookieTimeout: function(persistent) {
			if (persistent) {
				return 365 * 24 * 60 * 60; // 1 year
			} else {
				return 30 * 60; // 30 minutes
			}
		},

		didAuthorize: function(response) {
			if (response.success) {
				if (response.data !== '') {
					this.authToken = response.data;
					this.saveAuthToken(this.authToken);
				}
			} else {
				this.didAuthorizeError(response);
			}

			this.load();
		},

		didAuthorizeError: function(response) {
			this.authToken = '';
		},

		getAuthParams: function() {
			return { livefyre: this.authToken };
		},

		load: function() {
			var self    = this;
			var runFunc = function(Conv, auth) {
				self.run(Conv, auth);
			};

			Livefyre.require(this.getModules(), runFunc);
		},

		run: function(Conv, auth) {
			this.Conv = Conv;
			this.auth = auth;

			this.convInstance = this.buildConv(Conv);
			this.authDelegate = new GigyaAuthDelegate(this.config);

			auth.delegate(this.authDelegate);

			if (is_gigya_user_logged_in()) {
				try {
					auth.authenticate(this.getAuthParams());
				} catch (e) {
					//console.log('Failed to login to Livefyre: ' + e.message);
				}
			} else {
				// TODO: Check with livefyre
				auth.emit('logout');
			}
		},

		getModules: function() {
			return [
				'fyre.conv#3',
				'auth'
			];
		},

		buildConv: function(Conv) {
			var self = this;

			return new Conv(
				this.config.getNetworkConfig(),
				this.config.getConvConfig(),
				function(widget) {
					widget.on('commentPosted', function(data) {
						self.didPostComment(data);
					});
				}
			);
		},

		didPostComment: function(data) {
			var title = this.config.getOption('article_title');
			var url   = location.href;

			var action = {
				actionType: 'action:comment',
				actionID: this.config.getOption('article_id'),
				actionData: [
					{ name: 'title', value: title },
					{ name: 'url', value: url }
				]
			};

			save_gigya_action(action);
		},

		canLoadComments: function() {
			return window.Livefyre && $('#livefyre-comments').length > 0;
		}

	};

	var app = new CommentsApp();

}(jQuery));

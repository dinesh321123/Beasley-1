(function($) {

	var CommentsConfig = function() {
		this.collectionCache = {};
	};

	CommentsConfig.prototype = {

		getConfig: function() {
			return window.livefyre_comments_data.data;
		},

		hasCachedCollectionConfig: function() {
			return !!this.collectionCache[location.href];
		},

		getCachedCollectionConfig: function() {
			return this.collectionCache[location.href];
		},

		getCollectionConfig: function() {
			if (!this.hasCachedCollectionConfig()) {
				var data = JSON.parse(JSON.stringify(window.livefyre_collection_data));
				this.collectionCache[location.href] = window.livefyre_collection_data;
			}

			return this.getCachedCollectionConfig();
		},

		getNetworkConfig: function() {
			var config = this.getConfig();

			return {
				network: config.network_name
			};
		},

		getConvConfig: function() {
			var config = this.getConfig();
			var collectionConfig = this.getCollectionConfig();

			return [{
				siteId         : config.site_id,
				articleId      : collectionConfig.post_meta.article_id,
				el             : 'livefyre-comments',
				collectionMeta : collectionConfig.tokens.collection_meta,
				checksum       : collectionConfig.tokens.checksum,
				readOnly       : collectionConfig.post_meta.read_only
			}];
		},

		getToken: function(name) {
			var config = this.getConfig();
			return config.tokens[name];
		},

		getOption: function(name) {
			var config = this.getCollectionConfig();
			return config.post_meta[name];
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
			this.redirect('account');
		},

		viewProfile: function() {
			this.redirect('account');
		},

		redirect: function(actionName, params) {
			var profilePath = gigya_profile_path(actionName, params);
			location.href = profilePath;
		},

	};

	var CommentsApp    = function() {
		this.config    = new CommentsConfig();
		this.ajaxApi   = new WpAjaxApi(window.livefyre_comments_data);
		this.authToken = this.loadAuthToken();

		var self = this;

		$(document).on('pjax:end', function() {
			console.log('pjax:end fired');
			self.start();
		});

		$(document).on('pjax:popstate', function() {
			console.log('popstate fired');
		});
	};

	CommentsApp.prototype = {

		start: function() {
			if (this.canLoadComments()) {
				this.authorize();
			}
		},

		authorize: function() {
			if (is_gigya_user_logged_in() && this.authToken === '') {
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

	$(document).ready(function() {
		var app = new CommentsApp();
		app.start();
	});

}(jQuery));

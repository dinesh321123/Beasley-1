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
				checksum       : this.config.tokens.checksum
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
				this.redirect('/profile/login?dest=' + this.config.getOption('article_path'));
			}
		},

		logout: function(callback) {
			callback(null);
			this.redirect('/profile/logout?dest=' + this.config.getOption('article_path'));
		},

		editProfile: function() {
			this.redirect('/profile/settings'); // TODO
		},

		viewProfile: function() {
			this.redirect('/profile/settings'); // TODO
		},

		redirect: function(path) {
			location.href = path;
		},

		getAuthParams: function() {
			var params = {
				livefyre: this.config.getToken('auth')
			};

			return params;
		}

	};

	var CommentsApp = function() {
		this.config = new CommentsConfig(
			window.livefyre_comments_data.data
		);
	};

	CommentsApp.prototype = {

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
				auth.authenticate(this.authDelegate.getAuthParams());
			}
		},

		getModules: function() {
			return [
				'fyre.conv#3',
				'auth'
			];
		},

		buildConv: function(Conv) {
			return new Conv(
				this.config.getNetworkConfig(),
				this.config.getConvConfig(),
				function() {} // TODO
			);
		},

	};

	$(document).ready(function() {
		var app = new CommentsApp();
		app.load();
	});

}(jQuery));

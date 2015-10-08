(function($) {

	var MediaWallConfig = function() {
		this.config = window.livefyre_media_wall_options.data;
	};

	MediaWallConfig.prototype = {

		getCollectionConfig: function(wallID) {
			var collectionConfig = {
				network     : this.config.network_name,
				siteId      : this.config.site_id,
				articleId   : this.config.article_id,
				environment : this.config.environment
			};

			//Hardcoded params from the Media Wall CPT
			//collectionConfig.network     = 'gmphiladelphia.fyre.co';
			//collectionConfig.siteId      = '363887';
			//collectionConfig.articleId   = 'custom-1412878981291';
			//collectionConfig.environment = 'livefyre.com';

			this.loadWallParams(wallID, collectionConfig);

			return collectionConfig;
		},

		getConfigFor: function(wallID) {
			var wallConfig = {
				el: $('#media-wall-' + wallID)[0],
				collectionConfig: this.getCollectionConfig(wallID)
			};

			this.loadWallParams(wallID, wallConfig);

			return wallConfig;
		},

		getWall: function() {
			return this.config.wall;
		},

		getWallParams: function(wallID) {
			var params = window['livefyre_media_wall_' + wallID];
			return params.data;
		},

		loadWallParams: function(wallID, target) {
			var wallParams = this.getWallParams(wallID);
			for (var param in wallParams) {
				if (wallParams.hasOwnProperty(param)) {
					target[param] = wallParams[param];
				}
			}
		}

	};

	var MediaWallLoader    = function(config, LiveMediaWall, SDK) {
		this.config        = config;
		this.LiveMediaWall = LiveMediaWall;
		this.SDK           = SDK;
		this.wallViews     = [];
	};

	MediaWallLoader.prototype = {

		load: function() {
			var wallID = this.config.getWall();
			this.loadWall(wallID);
		},

		loadWall: function(wallID) {
			var wallConfig = this.config.getConfigFor(wallID);
			var wallView   = this.buildWall(wallConfig);

			this.wallViews.push(wallView);
		},

		buildWall: function(wallConfig) {
			var collection = new (this.SDK.Collection)(wallConfig.collectionConfig);
			wallConfig.collection = collection;

			return new (this.LiveMediaWall)(wallConfig);
		}

	};

	MediaWallApp = function() {
		this.config = new MediaWallConfig();
	};

	MediaWallApp.prototype = {

		load: function() {
			var self = this;
			var runFunc = function() { self.run.apply(self, arguments); };

			Livefyre.require(this.getModules(), runFunc);
		},

		run: function(LiveMediaWall, SDK) {
			this.loader = new MediaWallLoader( this.config, LiveMediaWall, SDK );
			this.loader.load();
		},

		getModules: function() {
			return [
				'streamhub-wall#3',
				'streamhub-sdk#2'
			];
		}

	};

	var app = new MediaWallApp();
	app.load();

}(jQuery));

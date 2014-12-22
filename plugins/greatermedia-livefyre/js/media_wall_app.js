(function($) {

	var MediaWallConfig = function() {
		this.config = window.livefyre_media_wall_options.data;
	};

	MediaWallConfig.prototype = {

		getCollectionConfig: function() {
			return {
				network     : this.config.network_name,
				siteId      : this.config.site_id,
				articleId   : this.config.article_id,
				environment : this.config.environment
			};
		},

		getConfigFor: function(wallID) {
			return {
				el: $('#media-wall-' + wallID)[0],
				collectionConfig: this.getCollectionConfig()
			};
		},

		getWalls: function() {
			return this.config.walls;
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
			var walls = this.config.getWalls();
			var n = walls.length;
			var i;
			var wallID;

			for (i = 0; i < n; i++) {
				wallID = walls[i];
				this.loadWall(wallID);
			}
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
			this.loader = new MediaWallLoader(
				this.config, LiveMediaWall, SDK
			);

			this.loader.load();
		},

		getModules: function() {
			return [
				'streamhub-wall#3',
				'streamhub-sdk#2'
			];
		}

	};

	//$(document).ready(function() {
		//console.log('doc ready');
	//});
	var app = new MediaWallApp();
	app.load();

}(jQuery));

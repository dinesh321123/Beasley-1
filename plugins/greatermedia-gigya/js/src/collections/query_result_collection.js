var QueryResultCollection = Backbone.Collection.extend({

	model: QueryResult,

	initialize: function(models, options) {
		this.activeConstraints = options.activeConstraints;
		this.pollDelay         = 3; // seconds
		this.maxQueryTime      = 5 * 60; // seconds
		this.maxRetries        = Math.floor( this.maxQueryTime / this.pollDelay );
		this.retries           = 0;
		this.fetchStatusProxy  = $.proxy(this.fetchStatus, this);
		this.lastProgress      = 0;

		Backbone.Collection.prototype.initialize(this, models, options);
	},

	search: function() {
		this.start();
	},

	start: function() {
		this.retries = 0;

		var constraints = this.activeConstraints.toJSON();
		var data        = {
			constraints: constraints,
			mode: 'start'
		};

		ajaxApi.request('preview_member_query', data)
			.then($.proxy(this.didStartSuccess, this))
			.fail($.proxy(this.didStartError, this));
	},

	didStartSuccess: function(response) {
		if (response.success) {
			this.memberQueryID = response.data.member_query_id;
			this.trigger('searchStart', this.memberQueryID);
			this.startPolling();
		} else {
			this.didStartError(response);
		}
	},

	didStartError: function(response) {
		this.reset([]);
		this.trigger('searchError', response.data);
	},

	onPoll: function() {
		this.fetchStatus();
	},

	fetchStatus: function() {
		var data = {
			mode: 'status',
			member_query_id: this.memberQueryID
		};

		this.retries++;
		ajaxApi.request('preview_member_query', data)
			.then($.proxy(this.didFetchStatusSuccess, this))
			.fail($.proxy(this.didFetchStatusError, this));
	},

	didFetchStatusSuccess: function(response) {
		if (response.success) {
			if (response.data.complete) {
				if ( ! response.data.errors ) {
					this.totalResults = response.data.total;
					this.reset(response.data.users);
					this.trigger('searchSuccess');
					this.clear();
				} else {
					this.reset([]);
					this.trigger('searchError', response.data.errors[0]);
				}
			} else {
				var progress = response.data.progress;
				if (this.lastProgress !== progress) {
					this.lastProgress = progress;
					this.retries--;
				}

				this.trigger('searchProgress', progress);
				this.startPolling();
			}
		} else {
			this.didFetchStatusError(response);
		}
	},

	didFetchStatusError: function(response) {
		this.startPolling();
	},

	getTotalResults: function() {
		return this.totalResults;
	},

	startPolling: function() {
		if (this.retries < this.maxRetries) {
			setTimeout(this.fetchStatusProxy, this.pollDelay * 1000);
		} else {
			this.trigger('searchTimeout');
		}
	},

	clear: function() {
		var data = {
			mode: 'clear',
			member_query_id: this.memberQueryID
		};

		this.retries++;
		ajaxApi.request('preview_member_query', data)
			.then($.proxy(this.didClearSuccess, this))
			.fail($.proxy(this.didClearError, this));
	},

	didClearSuccess: function(response) {

	},

	didClearError: function(response) {
		console.log('didClearError', response);
	}
});

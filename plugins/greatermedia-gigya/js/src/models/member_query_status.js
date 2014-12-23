var MemberQueryStatus = Backbone.Model.extend({

	defaults: {
		memberQueryID: -1
	},

	initialize: function(attr, opts) {
		Backbone.Model.prototype.initialize.call(this, attr, opts);

		this.intervalID = -1;
		this.delay      = 3; // seconds
		this.pollFunc   = $.proxy(this.poll, this);

		this.startPoll();
	},

	getStatusCode: function() {
		return this.get('statusCode');
	},

	getMemberQueryID: function() {
		return this.get('memberQueryID');
	},

	getEmailSegmentID: function() {
		return this.get('emailSegmentID');
	},

	getLastExport: function() {
		return this.get('lastExport');
	},

	getProgress: function() {
		return this.get('progress');
	},

	refresh: function() {
		var params = {
			member_query_id: this.getMemberQueryID()
		};

		ajaxApi.request('member_query_status', params)
			.then($.proxy(this.didRefresh, this))
			.fail($.proxy(this.didRefreshError, this));
	},

	didRefresh: function(response) {
		if (response.success) {
			this.set(response.data);
			this.trigger('refreshSuccess');

			if (this.get('statusCode') === 'running') {
				this.startPoll();
			} else if (response.data.errors) {
				this.trigger('refreshError', response.data.errors[0]);
			}
		} else {
			this.didRefreshError(response);
		}
	},

	didRefreshError: function(response) {
		this.trigger('refreshError', response.data);
	},

	startPoll: function() {
		clearTimeout(this.intervalID);
		this.intervalID = setTimeout(this.pollFunc, this.delay * 1000);
	},

	poll: function() {
		this.refresh();
	}

});

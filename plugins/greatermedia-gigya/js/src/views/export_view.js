var ExportView = Backbone.View.extend({

	template: getTemplate('export'),

	initialize: function(options) {
		Backbone.View.prototype.initialize.call(this, options);
		this.listenTo(this.model, 'change', this.render);
	},

	render: function() {
		var data = this.getStatusJSON();
		data.view = this;

		var html = this.template(data);

		this.$el.html(html);
		this.$el.css('visibility', 'visible');
	},

	getStatusJSON: function() {
		var meta = {};
		var statusCode = this.model.getStatusCode();

		if (statusCode === 'pending') {
			meta.statusText = 'Pending';
		} else if (statusCode === 'running') {
			meta.statusText = this.model.getProgress() + "% Completed ...";
		} else if (statusCode === 'completed') {
			meta.statusText = 'Completed';
		}


		var lastExport = this.model.getLastExport();

		if (lastExport) {
			meta.lastExport      = this.toHumanTime(this.model.getLastExport());
			meta.emailSegmentID  = this.model.getEmailSegmentID();
			meta.emailSegmentURL = this.toEmmaGroupURL(meta.emailSegmentID);
		} else {
			meta.lastExport = 'Never';
			meta.emailSegmentID = false;
		}

		return meta;
	},

	toEmmaGroupURL: function(groupID) {
		return 'https://app.e2ma.net/app2/audience/list/active/' + groupID + '/';
	},

	monthNames: [
		"January", "February", "March", "April", "May", "June",
		"July", "August", "September", "October", "November", "December"
	],

	toHumanTime: function(timestamp) {
		var time   = new Date(timestamp * 1000);
		var month  = time.getMonth();
		var monthName = this.monthNames[month];
		monthName = monthName.substring(0, 3);

		var day    = time.getDay();
		var year   = time.getFullYear();
		var hour   = time.getHours();
		if (hour < 10) hour = '0' + hour;

		var min    = time.getMinutes();
		if (min < 10) min = '0' + min;

		var sec    = time.getSeconds();
		if (sec < 10) sec = '0' + sec;

		var output = monthName + ' ' + day + ', ' + year + ' @ ' + hour + ':' + min + ':' + sec;

		return output;
	},

	getStatusText: function() {
		return 'statusText';
	},

	getEmailSegmentID: function() {
		return 'segment123';
	},

	getLastExport: function() {
		return '1 minute ago';
	}

});

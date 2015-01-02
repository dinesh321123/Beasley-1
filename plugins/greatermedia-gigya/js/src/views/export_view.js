var ExportView = Backbone.View.extend({

	template: getTemplate('export'),

	events: {
		'click .edit-segment-id-button': 'didEditSegmentClick',
		'click .change-segment-id-button': 'didChangeSegmentClick',
		'click .cancel-segment-id-button': 'didCancelSegmentClick',
	},

	initialize: function(options) {
		Backbone.View.prototype.initialize.call(this, options);
		this.listenTo(this.model, 'change', this.render);
		this.listenTo(this.model, 'changeSegmentStart', this.didChangeSegmentStart);
		this.listenTo(this.model, 'changeSegmentSuccess', this.didChangeSegmentSuccess);
		this.listenTo(this.model, 'changeSegmentError', this.didChangeSegmentError);
		this.listenTo(this.model, 'refreshSuccess', this.updateEditEnabled);
		this.listenTo(this.model, 'refreshError', this.updateEditEnabled);
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

	showChangeSegment: function() {
		var self = this;
		$editSegmentID = $('.edit-segment-id', this.$el);
		$editSegmentID.show( 'fast', function() {
			var emailSegmentID  = self.model.getEmailSegmentID() || '';
			var $emailSegmentID = $('#email-segment-id');
			var currentValue = $emailSegmentID.val();

			if (currentValue === '' && emailSegmentID !== '') {
				$emailSegmentID.val(emailSegmentID);
			}

			$emailSegmentID.focus();
		});

		this.setErrorMessage('');
	},

	hideChangeSegment: function() {
		$editSegmentID = $('.edit-segment-id', this.$el);
		$editSegmentID.hide( 'fast' );
	},

	didEditSegmentClick: function(event) {
		$editButton = $('.edit-segment-id-button', this.$el);
		var enabled = this.model.getStatusCode() !== 'running';

		if (enabled) {
			this.showChangeSegment();
			return false;
		}
	},

	didChangeSegmentClick: function(event) {
		var $emailSegmentID = $('#email-segment-id');
		var segmentID = $emailSegmentID.val();
		if (segmentID !== '') {
			this.model.changeEmailSegmentID(segmentID);
		} else {
			this.setErrorMessage('Invalid Segment ID');
			$emailSegmentID.focus();
		}
		return false;
	},

	didCancelSegmentClick: function(event) {
		this.hideChangeSegment();
	},

	setErrorMessage: function(message) {
		var $message = $('.error-message', this.$el);
		$message.text(message);
	},

	setSpinnerEnabled: function(enabled) {
		var $spinner = $('.spinner', this.$el);
		$spinner.css('display', enabled ? 'inline' : 'none');
	},

	didChangeSegmentStart: function() {
		this.setSpinnerEnabled(true);
		this.setErrorMessage('');
	},

	didChangeSegmentSuccess: function() {
		this.hideChangeSegment();
		this.setSpinnerEnabled(false);
	},

	didChangeSegmentError: function(message) {
		this.setErrorMessage(message);
		this.setSpinnerEnabled(false);
	},

	updateEditEnabled: function() {
		$editButton = $('.edit-segment-id-button', this.$el);
		var enabled = this.model.getStatusCode() !== 'running';
		$editButton.toggleClass('disabled', !enabled);
	}

});

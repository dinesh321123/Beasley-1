var PreviewView = Backbone.View.extend({

	events: {
		'click .preview-member-query-button': 'didPreviewClick'
	},

	initialize: function(options) {
		this.collection = options.collection;
		this.listenTo(this.collection, 'searchError', this.didSearchError);
		this.listenTo(this.collection, 'searchStart', this.didSearchStart);
		this.listenTo(this.collection, 'searchProgress', this.didSearchProgress);
		this.listenTo(this.collection, 'searchSuccess', this.didSearchSuccess);
		this.listenTo(this.collection, 'searchTimeout', this.didSearchTimeout);

		Backbone.View.prototype.initialize.call(this, options);
		//this.search();
		this.previewEnabled = true;

		this.stepper = new Stepper('didStep', this);
	},

	didPreviewClick: function(event) {
		if (this.previewEnabled) {
			this.search();
		}

		return false;
	},

	search: function() {
		this.setStatus('Searching, Please wait ...');
		this.collection.search();
	},

	didSearchStart: function() {
		this.stepper.start(0);
		this.setPreviewEnabled(false);
	},

	didSearchProgress: function(progress) {
		this.stepper.update(progress);
	},

	didStep: function(progress) {
		this.setStatus('Searching, Please wait ... ' + progress + '%');
	},

	didSearchSuccess: function() {
		this.stepper.stop();

		var total    = this.collection.getTotalResults();
		var message = total + ' records found';

		if (total > 0) {
			message += ', showing the first 5';
		} else {
			message += '.';
		}

		this.setStatus(message);
		this.setPreviewEnabled(true);
	},

	didSearchError: function(message) {
		this.stepper.stop();
		this.setPreviewEnabled(true);
		this.setStatus("Error: " + message);
	},

	didSearchTimeout: function() {
		this.stepper.stop();
		this.setStatus('Error: Query timed out, please try again.');
		this.setPreviewEnabled(true);
	},

	setStatus: function(message) {
		var div = $('.count-status');
		div.text(message);
	},

	setPreviewEnabled: function(enabled) {
		var previewButton = $('.preview-member-query-button', this.el);
		previewButton.toggleClass('disabled', !enabled);

		this.previewEnabled = enabled;
	},

});

var Stepper = function(callback, scope) {
	this.callback   = callback;
	this.scope      = scope;
	this.intervalID = -1;

	var self = this;
	this.stepFunc = function() {
		self.step();
	};
};

Stepper.prototype = {

	start: function(value) {
		this.value   = value;
		this.current = 0;
		this.startInterval();
	},

	update: function(value) {
		this.value = value;

		if (!this.isRunning()) {
			this.startInterval();
		}
	},

	startInterval: function() {
		clearInterval(this.intervalID);
		this.intervalID = setInterval(this.stepFunc, 100);
	},

	stop: function() {
		clearInterval(this.intervalID);
		this.intervalID = -1;
	},

	step: function() {
		if (this.current < this.value) {
			this.current++;
			this.scope[this.callback](this.current);
		} else {
			this.stop();
		}
	},

	isRunning: function() {
		return this.intervalID !== -1;
	}

};

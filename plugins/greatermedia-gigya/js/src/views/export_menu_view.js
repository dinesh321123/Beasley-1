var ExportMenuView = Backbone.View.extend({

	events: {
		'click .export-button': 'didClickExport'
	},

	initialize: function(options) {
		Backbone.View.prototype.initialize.call(this, options);
	},

	render: function() {
		var $submitButton = this.getSubmitButton();
		$submitButton.val('Save');
		$submitButton.toggleClass('button-primary', false);

		var $exportButton = $('<input name="export" type="button" class="button button-primary button-large export-button" id="export-button" value="Export">');
		$exportButton.insertBefore($submitButton);
	},

	getSubmitButton: function() {
		return $('#publish', this.$el);
	},

	didClickExport: function(event) {
		console.log('didClickExport');
		return false;
	}

});

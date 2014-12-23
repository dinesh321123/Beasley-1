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

	getPublishForm: function() {
		return this.getSubmitButton().parents('form:first');
	},

	didClickExport: function(event) {
		var exportField = $('<input>').attr({
			type: 'hidden',
			name: 'export_member_query',
			value: '1'
		});

		var form = this.getPublishForm();
		exportField.appendTo(form);

		var button = this.getSubmitButton();
		button.trigger('click');

		return false;
	},


});

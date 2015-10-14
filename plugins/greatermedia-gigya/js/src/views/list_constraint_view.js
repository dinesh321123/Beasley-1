var ListConstraintView = ConstraintView.extend({

	template: getTemplate('list_constraint'),

	initialize: function(model, opts) {
		ConstraintView.prototype.initialize.call(this, model, opts);

		this.listenTo(this.model, 'loadListStart', this.didLoadListStart);
		this.listenTo(this.model, 'loadListSuccess', this.didLoadListSuccess);
		this.listenTo(this.model, 'loadListError', this.didLoadListError);

		var choices = this.model.getChoices();
	},

	didLoadListStart: function() {
	},

	didLoadListSuccess: function(choices) {
		this.render();
	},

	didLoadListError: function(message) {
	}

});

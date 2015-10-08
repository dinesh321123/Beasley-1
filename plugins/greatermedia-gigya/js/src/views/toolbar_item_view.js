var ToolbarItemView = Backbone.CollectionView.extend({

	template: getTemplate('toolbar_item'),

	events: {
		'click .toolbar_item': 'didToolbarItemClick'
	},

	initialize: function() {
		this.render();
	},

	render: function() {
		var data = this.model.toViewJSON();
		var html = this.template(data);

		this.$el.html(html);
	},

	didToolbarItemClick: function(event) {
		var constraint = this.model.clone();

		this.$el.trigger('addConstraint', constraint);

		return false;
	}

});

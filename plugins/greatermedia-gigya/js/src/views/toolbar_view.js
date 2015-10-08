var ToolbarView = Backbone.CollectionView.extend({
	modelView: ToolbarItemView,

	events: {
		'addConstraint': 'addConstraint'
	},

	initialize: function(options) {
		this.activeConstraints = options.activeConstraints;
		options.tabIndex = -1;
		Backbone.CollectionView.prototype.initialize.call(this, options);
	},

	addConstraint: function(event, constraint) {
		this.activeConstraints.add(constraint);
	}
});

var ActiveConstraintsView = Backbone.CollectionView.extend({

	el: jQuery('#active_constraints'),
	emptyListCaption: getTemplate('empty_constraints'),

	events: {
		'copyConstraint': 'copyConstraint',
		'removeConstraint': 'removeConstraint'
	},

	_getModelViewConstructor: function(model) {
		var type = model.get('type');
		var kind = ConstraintCollection.kindForType(type);

		switch (kind) {
			case 'record':
				return EntryConstraintView;

			case 'likes':
				return LikeConstraintView;

			case 'favorites':
				return FavoriteConstraintView;

			default:
				return ConstraintView;
		}
	},

	initialize: function(options) {
		Backbone.CollectionView.prototype.initialize.call(this, options);
		this.listenTo(this.collection, 'add', this.didAdd);
	},

	copyConstraint: function(event, constraint) {
		var newConstraint = constraint.clone();
		var index         = this.collection.indexOf(constraint);

		this.collection.add(newConstraint, {at: index+1});
	},

	removeConstraint: function(event, constraint) {
		this.collection.remove(constraint);
	},

	render: function() {
		Backbone.CollectionView.prototype.render.call(this);
	},

	didAdd: function(model) {
		var view = this.viewManager.findByModel(model);
		this.scrollTo(view.$el);
	},

	scrollTo: function($target) {
		var root   = $('html, body');
		var params = {
			scrollTop: $target.offset().top - 60 // admin bar offset
		};

		root.animate(params, 500);
	},

});

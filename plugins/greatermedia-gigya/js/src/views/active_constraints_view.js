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

	copyConstraint: function(event, constraint) {
		var newConstraint = constraint.clone();
		var index         = this.collection.indexOf(constraint);

		this.collection.add(newConstraint, {at: index+1});
	},

	removeConstraint: function(event, constraint) {
		this.collection.remove(constraint);
	}

});

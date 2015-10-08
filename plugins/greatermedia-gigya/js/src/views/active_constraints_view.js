var ActiveConstraintsView = Backbone.CollectionView.extend({

	el: jQuery('#active_constraints'),
	emptyListCaption: getTemplate('empty_constraints'),

	events: {
		'copyConstraint': 'copyConstraint',
		'removeConstraint': 'removeConstraint',
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

			case 'list':
				return ListConstraintView;

			case 'email_engagement_tally':
				return EmailEngagementTallyConstraintView;

			case 'email_engagement':
				return EmailEngagementConstraintView;

			default:
				return ConstraintView;
		}
	},

	initialize: function(options) {
		Backbone.CollectionView.prototype.initialize.call(this, options);
		this.listenTo(this.collection, 'add', this.didAdd);
		this.listenTo(this, 'sortStop', this.didSortStop);
	},

	copyConstraint: function(event, constraint) {
		var newConstraint = constraint.clone();
		var index         = this.collection.indexOf(constraint);

		this.collection.add(newConstraint, {at: index+1});
	},

	removeConstraint: function(event, constraint) {
		var view    = this.viewManager.findByModel(constraint);
		var $el     = view.$el;
		var $parent = $el.parent();

		var self = this;
		$parent.slideUp(function() {
			self.collection.remove(constraint);
		});
	},

	render: function() {
		Backbone.CollectionView.prototype.render.call(this);
	},

	didAdd: function(model) {
		var view = this.viewManager.findByModel(model);
		var $el = view.$el;
		//var $parent = $el.parent();

		this.scrollTo($el);

		$el.css('opacity', 0);
		$el.animate({opacity: 1}, 500);

		var $parent = $el.parent();
		$parent.fadeIn();
	},

	scrollTo: function($target) {
		var root   = $('html, body');
		var params = {
			scrollTop: $target.offset().top - 60 // admin bar offset
		};

		root.animate(params, 500);
	},

	didSortStop: function(event) {
		this.collection.save();
	}

});

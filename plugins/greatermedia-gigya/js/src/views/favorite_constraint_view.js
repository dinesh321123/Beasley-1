var FavoriteConstraintView = ConstraintView.extend({

	template: getTemplate('favorite_constraint'),

	initialize: function(model, opts) {
		ConstraintView.prototype.initialize.call(this, model, opts);
	},

	render: function() {
		var data           = this.model.toViewJSON();
		data.view          = this;
		data.categories    = this.model.getCategories();
		data.favoriteTypes = this.model.getFavoriteTypes();

		var html = this.template(data);
		this.$el.html(html);
	},

	updateConstraint: function(constraint) {
		var operator     = $('.constraint-operator', this.el).val();
		var conjunction  = $('.constraint-conjunction', this.el).val();
		var value        = $('.constraint-value', this.el).val();
		var category     = $('.favorite-category', this.el).val();
		var favoriteType = $('.favorite-type', this.el).val();
		value            = this.parseValue(value, constraint.get('valueType'));

		var changes     = {
			operator: operator,
			value: value,
			conjunction: conjunction,
			category: category,
			favoriteType: favoriteType
		};

		constraint.set(changes);
	},


});

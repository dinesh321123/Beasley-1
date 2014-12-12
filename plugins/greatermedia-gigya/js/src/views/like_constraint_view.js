var LikeConstraintView = ConstraintView.extend({

	template: getTemplate('like_constraint'),

	initialize: function(model, opts) {
		ConstraintView.prototype.initialize.call(this, model, opts);
	},

	render: function() {
		var data        = this.model.toViewJSON();
		data.view       = this;
		data.categories = this.model.getCategories();

		var html = this.template(data);
		this.$el.html(html);
	},

	updateConstraint: function(constraint) {
		var operator    = $('.constraint-operator', this.el).val();
		var conjunction = $('.constraint-conjunction', this.el).val();
		var value       = $('.constraint-value', this.el).val();
		var category    = $('.like-category', this.el).val();
		value           = this.parseValue(value, constraint.get('valueType'));

		var changes     = {
			operator: operator,
			value: value,
			conjunction: conjunction,
			category: category
		};

		constraint.set(changes);
	},

});

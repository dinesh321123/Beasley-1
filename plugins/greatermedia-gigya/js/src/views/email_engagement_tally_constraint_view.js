var EmailEngagementTallyConstraintView = ConstraintView.extend({

	template: getTemplate('email_engagement_tally'),

	initialize: function(model, opts) {
		ConstraintView.prototype.initialize.call(this, model, opts);
	},

	updateConstraint: function(constraint) {
		var operator    = $('.constraint-operator', this.el).val();
		var conjunction = $('.constraint-conjunction', this.el).val();
		var value       = $('.constraint-value', this.el).val();
		var event_name  = $('.email-event', this.el).val();
		value           = this.parseValue(value, constraint.get('valueType'));

		var changes     = {
			operator: operator,
			value: value,
			conjunction: conjunction,
			event_name: event_name,
		};

		constraint.set(changes);
		this.renderConjunctionGuide();
	},

});

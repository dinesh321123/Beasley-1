var EmailEngagementTallyConstraint = Constraint.extend({

	defaults        : {
		type        : 'data:email_engagement_tally',
		operator    : 'equals',
		conjunction : 'and',
		valueType   : 'integer',
		value       : '',
		event_name  : 'message_click',
	},

	initialize: function(attr, opts) {
		Constraint.prototype.initialize.call(this, attr, opts);
	},

});

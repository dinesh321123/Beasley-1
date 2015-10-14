var ProfileConstraint = Constraint.extend({

	defaults        : {
		type        : 'profile:field_name',
		operator    : 'equals',
		conjunction : 'and',
		valueType   : 'string',
		value       : ''
	}

});

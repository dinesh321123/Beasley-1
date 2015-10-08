var ZipCodeConstraintView = ConstraintView.extend({

	template: getTemplate('zip_code_constraint'),

	initialize: function(model, opts) {
		ConstraintView.prototype.initialize.call(this, model, opts);
	},

	operatorsFor: function(valueType, type) {
		if (type === 'profile:zip') {
			return ['in', 'not in'];
		} else {
			return ConstraintView.prototype.operatorsFor.call(
				this, valueType, type
			);
		}
	}

});

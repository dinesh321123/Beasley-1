var ConstraintView = Backbone.View.extend({

	template: getTemplate('constraint'),

	events: {
		'click .copy-constraint': 'didCopyClick',
		'click .remove-constraint': 'didRemoveClick',
		'change': 'didChange'
	},

	initialize: function(options) {
		this.render();
	},

	render: function() {
		var data = this.model.toViewJSON();
		data.view = this;

		var html = this.template(data);

		this.$el.html(html);

		var $constraintField = $('.constraint-value', this.el);

		if (data.valueType === 'date') {
			$constraintField.datepicker({dateFormat: 'mm/dd/yy'});
		} else if (this.hasChoices()) {
			//$constraintField.select2();
		}

		/*
		var $operatorField    = $('.constraint-operator', this.el);
		var $conjunctionField = $('.constraint-conjunction', this.el);

		$operatorField.select2();
		$conjunctionField.select2();
		*/
	},

	didCopyClick: function(event) {
		this.$el.trigger('copyConstraint', this.model);
		return false;
	},

	didRemoveClick: function(event) {
		this.$el.trigger('removeConstraint', this.model);
		return false;
	},

	didChange: function(event) {
		this.updateConstraint(this.model, event.target);
	},

	updateConstraint: function(constraint) {
		var operator    = $('.constraint-operator', this.el).val();
		var conjunction = $('.constraint-conjunction', this.el).val();
		var value       = $('.constraint-value', this.el).val();
		value           = this.parseValue(value, constraint.get('valueType'));

		var changes     = {
			operator: operator,
			value: value,
			conjunction: conjunction
		};

		//console.log('updateConstraint', changes);
		constraint.set(changes);
	},

	parseValue: function(value, valueType) {
		if (valueType === 'boolean') {
			return value === 'true';
		} else if (valueType === 'integer') {
			return parseInt(value, 10);
		} else if (valueType === 'float') {
			return parseFloat(value, 10);
		} else {
			return value;
		}
	},

	allOperators: [
		'equals',
		'not equals',
		'greater than',
		'greater than or equal to',
		'less than',
		'less than or equal to',
		'contains',
		'not contains'
	],

	numericOperators : [
		'equals',
		'not equals',
		'greater than',
		'greater than or equal to',
		'less than',
		'less than or equal to',
	],

	stringOperators: [
		'equals',
		'not equals',
		'contains',
		'not contains'
	],

	nonFullTextOperators: [
		'equals',
		'not equals',
	],

	booleanOperators: [
		'equals',
		'not equals',
	],

	dateOperators: [
		'greater than',
		'greater than or equal to',
		'less than',
		'less than or equal to'
	],

	nonFullTextTypes: [
		'profile:zip',
		'profile:state',
		'profile:country',
		'profile:timezone',
		'action:social_share',
	],

	enumOperators: [
		'contains',
		'not contains',
	],

	operatorsFor: function(valueType, type) {
		if (valueType === 'integer' || valueType === 'float') {
			return this.numericOperators;
		} else if (valueType === 'string') {
			if (this.isNonFullTextType(type)) {
				return this.nonFullTextOperators;
			} else {
				return this.stringOperators;
			}
		} else if (valueType === 'boolean') {
			return this.booleanOperators;
		} else if (valueType === 'date') {
			return this.dateOperators;
		} else if (valueType === 'enum') {
			return this.enumOperators;
		} else {
			return this.allOperators;
		}

		return operators;
	},

	conjunctions: [
		'and', 'or'
	],

	hasChoices: function() {
		return this.model.hasMeta('choices');
	},

	isNonFullTextType: function(type) {
		if (type === undefined) {
			return false;
		} else {
			return _.indexOf(this.nonFullTextTypes, type) !== -1;
		}
	}

});

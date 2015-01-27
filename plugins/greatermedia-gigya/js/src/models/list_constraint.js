var ListConstraint = Constraint.extend({

	defaults        : {
		type        : 'data:foo_list',
		operator    : 'contains',
		conjunction : 'and',
		valueType   : 'string',
		value       : '',
	},

	initialize: function(attr, opts) {
		this.choicesLoaded = false;
		Constraint.prototype.initialize.call(this, attr, opts);
	},

	getListTypeName: function() {
		var type    = this.get('type');
		var parts   = type.split(':');
		var subType = parts[1];

		console.log('getListTypeName', type, parts, subType);
		return subType.replace('_list', '');
	},

	getChoices: function() {
		if (!this.listChoices) {
			this.listChoices = new Backbone.Collection([]);
			this.loadList();
		}

		return this.listChoices;
	},

	loadList: function() {
		var type   = this.getListTypeName();
		console.log('loadList: type', type);
		var params = { 'type': type };

		this.trigger('loadListStart');

		if (ListConstraint.cache[type]) {
			this.didLoadList(ListConstraint.cache[type]);
		} else {
			ajaxApi.request('get_choices_for_constraint_type', params)
				.then($.proxy(this.didLoadList, this))
				.fail($.proxy(this.didLoadListError, this));
		}
	},

	didLoadList: function(response) {
		if (response.success) {
			var choices = response.data;
			ListConstraint.cache[this.getListTypeName()] = response;

			this.listChoices.reset(choices, { silent: true });

			if ( this.get('value') === '' && choices.length > 0 ) {
				this.set('value', choices[0].value);
			}

			this.choicesLoaded = true;
			this.trigger('loadListSuccess', this.listChoices);
		} else {
			this.didLoadListError(response);
		}
	},

	didLoadListError: function(response) {
		this.trigger('loadListError', response.data);
	},

	toViewJSON: function() {
		var json = Constraint.prototype.toViewJSON.call(this);
		if (this.choicesLoaded) {
			json.choices = this.getChoices().toJSON();
		}

		return json;
	}

});

ListConstraint.cache = {};

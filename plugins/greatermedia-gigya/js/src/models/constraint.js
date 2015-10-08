var Constraint = Backbone.Model.extend({

	defaults        : {
		type        : 'constraint:type_name',
		operator    : 'equals',
		conjunction : 'and',
		valueType   : 'string',
		value       : ''
	},

	initialize: function(attr, opts) {
		Backbone.Model.prototype.initialize.call(this, attr, opts);

		if (attr.valueType === 'enum' && this.hasMeta('choices') && !attr.value) {
			var choices = this.getMeta('choices');

			if (choices && choices.length > 0) {
				var value = this.getMeta('choices')[0].value;
				this.set('value', value);
			}
		}
	},

	getMeta: function(key) {
		if (this.hasMeta(key)) {
			return AVAILABLE_CONSTRAINTS_META_MAP[this.get('type')][key];
		} else {
			return null;
		}
	},

	hasMeta: function(key) {
		var type = this.get('type');
		return !!AVAILABLE_CONSTRAINTS_META_MAP[type] &&
			   !!AVAILABLE_CONSTRAINTS_META_MAP[type][key];
	},

	toViewJSON: function() {
		var json    = this.toJSON();
		var type    = this.get('type');
		var metaMap = AVAILABLE_CONSTRAINTS_META_MAP[type];

		if (metaMap) {
			_.extend(json, metaMap);
		}

		return json;
	}

});

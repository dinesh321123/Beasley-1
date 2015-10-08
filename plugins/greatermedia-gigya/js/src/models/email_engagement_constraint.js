var EmailEngagementConstraint = Constraint.extend({

	defaults        : {
		type        : 'data:email_engagement',
		operator    : 'equals',
		conjunction : 'and',
		valueType   : 'integer',
		value       : '',
		event_name  : 'message_click',
		groups      : [],
	},

	initialize: function(attr, opts) {
		this.groupsLoaded      = false;
		this.staticGroups      = new Backbone.Collection([]);
		this.memberQueryGroups = new Backbone.Collection([]);

		Constraint.prototype.initialize.call(this, attr, opts);
	},

	getGroups: function() {
		return this.groups;
	},

	loadGroups: function() {
		var params = { type: 'email_engagement' };

		this.trigger('loadGroupsStart');

		ajaxApi.request('get_choices_for_constraint_type', params)
			.then($.proxy(this.didLoadGroups, this))
			.fail($.proxy(this.didLoadGroupsError, this));
	},

	didLoadGroups: function(response) {
		if (response.success) {
			var choices = response.data;
			this.staticGroups.reset(choices['static'], { silent: true });
			this.memberQueryGroups.reset(choices.member_query, { silent: true });

			this.groupsLoaded = true;
			this.trigger('loadGroupsSuccess', this.groups);
		} else {
			this.didLoadGroupsError(response.data);
		}
	},

	didLoadGroupsError: function(response) {
		this.trigger('didLoadGroupsError', response.data);
	},

	toViewJSON: function() {
		var json = Constraint.prototype.toViewJSON.call(this);
		json.groupsLoaded = this.groupsLoaded;

		if (this.groupsLoaded) {
			json.staticGroups      = this.staticGroups.toJSON();
			json.memberQueryGroups = this.memberQueryGroups.toJSON();
		} else {
			json.staticGroups      = [];
			json.memberQueryGroups = [];
		}

		return json;
	}

});

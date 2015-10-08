var ConstraintCollection = Backbone.Collection.extend({

	model: function(attr, options) {
		var kind     = ConstraintCollection.kindForType(attr.type);
		var klass    = ConstraintCollection.typesMap[kind] || Constraint;
		var instance = new klass(attr, options);

		return instance;
	},

	initialize: function(models, options) {
		this.on('change add remove reset', this.save, this);
		Backbone.Collection.prototype.initialize.call(this, models, options);
	},

	save: function() {
		var json         = JSON.stringify(this.toJSON());
		var $constraints = jQuery('#constraints');

		$constraints.attr('value', json);
		//console.log(json);
	}
}, {

	kindForType: function(type) {
		var typeList = type.split(':');

		if (typeList.length > 0) {
			var subType = typeList[1];

			// treats profile:likes and profile:favorites
			// as a custom type
			if (subType === 'likes') {
				return 'likes';
			} else if (subType === 'favorites') {
				return 'favorites';
			} else if (subType === 'email_engagement_tally') {
				return 'email_engagement_tally';
			} else if (subType === 'email_engagement') {
				return 'email_engagement';
			} else if (subType.match(/_list$/)) {
				return 'list';
			} else {
				return typeList[0];
			}
		} else {
			return type;
		}
	},

	typesMap        : {
		'system'    : Constraint,
		'profile'   : ProfileConstraint,
		'record'    : EntryConstraint,
		'likes'     : LikeConstraint,
		'favorites' : FavoriteConstraint,
		'list'      : ListConstraint,
		'email_engagement_tally': EmailEngagementTallyConstraint,
		'email_engagement': EmailEngagementConstraint,
	}

});

var FavoriteConstraint = Constraint.extend({

	defaults        : {
		type        : 'profile:favorites',
		operator    : 'equals',
		conjunction : 'and',
		valueType   : 'string',
		value       : '',
		favoriteType: 'music',
		category    : 'Any Category',
	},

	getCategories: function() {
		return FACEBOOK_CATEGORIES;
	},

	getFavoriteTypes: function() {
		return FACEBOOK_FAVORITE_TYPES;
	}

});

FACEBOOK_FAVORITE_TYPES = [
	{ label: 'Interests'  , value: 'interests'  } ,
	{ label: 'Music'      , value: 'music'      } ,
	{ label: 'Television' , value: 'television' } ,
	{ label: 'Activities' , value: 'activities' } ,
	{ label: 'Books'      , value: 'books'      }
];

var escapeValue = function(source) {
	if (typeof(source) === 'string') {
		source = source.replace(/"/g, 'C_DOUBLE_QUOTE');
		source = source.replace(/'/g, 'C_SINGLE_QUOTE');
		source = source.replace(/\\/g, 'C_BACKSLASH');
	}

	return source;
};

var unescapeValue = function(source) {
	if (typeof(source) === 'string') {
		source = source.replace(/C_DOUBLE_QUOTE/g, '"');
		source = source.replace(/C_SINGLE_QUOTE/g, "'");
		source = source.replace(/C_BACKSLASH/g, "\\");
	}

	return source;
};

var getTemplate = function(name) {
	return window.JST['src/templates/' + name + '.jst'];
};

var renderTemplate = function(name, data, settings) {
	if (!settings) {
		settings = {};
	}

	var template = getTemplate(name);
	var html     = template(data);

	return $(html);
};

if (typeof Object.create != 'function') {
	Object.create = (function() {
		var Object = function() {};
		return function (prototype) {
			if (arguments.length > 1) {
				throw Error('Second argument not supported');
			}
			if (typeof prototype != 'object') {
				throw TypeError('Argument must be an object');
			}
			Object.prototype = prototype;
			var result = new Object();
			Object.prototype = null;
			return result;
		};
	})();
}

this["JST"] = this["JST"] || {};

this["JST"]["src/templates/constraint.jst"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<ul class="constraint-toolbar">\n\t<li>\n\t\t<a\n\t\t\talt="f105"\n\t\t\tclass="dashicons dashicons-admin-page copy-constraint"\n\t\t\thref="#"\n\t\t\ttitle="Duplicate"\n\t\t/>\n\n\t\t<a\n\t\t\talt="f105"\n\t\t\tclass="dashicons dashicons-trash remove-constraint"\n\t\t\thref="#"\n\t\t\ttitle="Remove"\n\t\t/>\n\t</li>\n</ul>\n\n<p class="constraint-title">\n\t' +
__e( title ) +
'\n</p>\n\n<select class="constraint-operator">\n\t';
 _.each(view.operatorsFor(valueType, type), function(operatorItem) { ;
__p += '\n\t<option value="' +
__e( operatorItem ) +
'" ' +
((__t = ( operatorItem === operator ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t' +
__e( operatorItem ) +
'\n\t</option>\n\t';
 }) ;
__p += '\n</select>\n\n';
 if (view.hasChoices()) { ;
__p += '\n\t<select class="constraint-value">\n\t\t';
 _.each(choices, function(choiceItem) { ;
__p += '\n\t\t<option value="' +
__e( choiceItem.value ) +
'" ' +
((__t = ( choiceItem.value == value ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( choiceItem.label ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n';
 } else if (valueType === 'integer' || valueType === 'float') { ;
__p += '\n\t<input type="number" class="constraint-value constraint-value-text" value="' +
__e( value ) +
'" />\n';
 } else { ;
__p += '\n\t<input type="text" class="constraint-value constraint-value-text" value="' +
__e( value ) +
'" />\n';
 } ;
__p += '\n\n<select class="constraint-conjunction">\n\t';
 _.each(view.conjunctions, function(conjunctionItem) { ;
__p += '\n\t<option value="' +
__e( conjunctionItem ) +
'" ' +
((__t = ( conjunctionItem === conjunction ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t' +
__e( conjunctionItem ) +
'\n\t</option>\n\t';
 }) ;
__p += '\n</select>\n\n<div class="conjunction-guide">\n\t<p><span class="arrow-down"></span>OR any of the following conditions</p>\n</div>\n\n';

}
return __p
};

this["JST"]["src/templates/constraint_item.jst"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<li>\n\t<ul class="constraint-toolbar">\n\t\t<li>\n\t\t\t<a\n\t\t\t\tdata-id="' +
__e( constraint.id ) +
'"\n\t\t\t\talt="f105"\n\t\t\t\tclass="dashicons dashicons-admin-page copy-constraint"\n\t\t\t\thref="#"\n\t\t\t\ttitle="Duplicate"\n\t\t\t/>\n\n\t\t\t<a\n\t\t\t\tdata-id="' +
__e( constraint.id ) +
'"\n\t\t\t\talt="f105"\n\t\t\t\tclass="dashicons dashicons-trash remove-constraint"\n\t\t\t\thref="#"\n\t\t\t\ttitle="Remove"\n\t\t\t/>\n\t\t</li>\n\t</ul>\n\n\t<p class="constraint-title">\n\t\t' +
__e( constraint.title ) +
'\n\t</p>\n\n\t<select data-id="' +
__e( constraint.id ) +
'" class="constraint-operator">\n\t\t';
 _.each(view.operatorsFor(constraint.valueType), function(operator) { ;
__p += '\n\t\t<option value="' +
__e( operator ) +
'" ' +
((__t = ( constraint.operator === operator ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( view.operatorLabelFor(operator) ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n\n\t<input\n\t\ttype="text"\n\t\tclass="constraint-value"\n\t\tdata-id="' +
__e( constraint.id ) +
'"\n\t\tvalue="' +
__e( constraint.value ) +
'" />\n\n\t<select data-id="' +
__e( constraint.id ) +
'" class="constraint-conjunction">\n\t\t';
 _.each(view.conjunctions, function(conjunction) { ;
__p += '\n\t\t<option value="' +
__e( conjunction ) +
'" ' +
((__t = ( constraint.conjunction === conjunction ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( conjunction ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n</li>\n';

}
return __p
};

this["JST"]["src/templates/email_engagement.jst"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<ul class="constraint-toolbar">\n\t<li>\n\t\t<a\n\t\t\talt="f105"\n\t\t\tclass="dashicons dashicons-admin-page copy-constraint"\n\t\t\thref="#"\n\t\t\ttitle="Duplicate"\n\t\t/>\n\n\t\t<a\n\t\t\talt="f105"\n\t\t\tclass="dashicons dashicons-trash remove-constraint"\n\t\t\thref="#"\n\t\t\ttitle="Remove"\n\t\t/>\n\t</li>\n</ul>\n\n<p class="constraint-title">\n\t' +
__e( title ) +
'\n</p>\n\n<div class="email-engagement">\n\t<select class="email-event">\n\t\t';
 _.each(choices, function(choiceItem) { ;
__p += '\n\t\t<option value="' +
__e( choiceItem.value ) +
'" ' +
((__t = ( choiceItem.value == event_name ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( choiceItem.label ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n\n\t<select class="constraint-operator">\n\t\t';
 _.each(view.operatorsFor(valueType, type), function(operatorItem) { ;
__p += '\n\t\t<option value="' +
__e( operatorItem ) +
'" ' +
((__t = ( operatorItem === operator ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( operatorItem ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n\n\t<select class="constraint-value">\n\t\t';
 if (groupsLoaded) { ;
__p += '\n\t\t\t<option value="any">Any Mailing List</option>\n\t\t\t<optgroup label="Mailing Lists">\n\t\t\t\t';
 _.each(staticGroups, function(groupItem) { ;
__p += '\n\t\t\t\t<option value="' +
__e( groupItem.value ) +
'" ' +
((__t = ( groupItem.value == value ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t\t\t' +
__e( groupItem.label ) +
'\n\t\t\t\t</option>\n\t\t\t\t';
 }) ;
__p += '\n\t\t\t</optgroup>\n\t\t\t<optgroup label="Member Query Lists">\n\t\t\t\t';
 _.each(memberQueryGroups, function(groupItem) { ;
__p += '\n\t\t\t\t<option value="' +
__e( groupItem.value ) +
'" ' +
((__t = ( groupItem.value == value ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t\t\t' +
__e( groupItem.label ) +
'\n\t\t\t\t</option>\n\t\t\t\t';
 }) ;
__p += '\n\t\t\t</optgroup>\n\t\t';
 } else { ;
__p += '\n\t\t\t<option value="">Loading ...</option>\n\t\t';
 } ;
__p += '\n\t</select>\n\n\t<select class="constraint-conjunction">\n\t\t';
 _.each(view.conjunctions, function(conjunctionItem) { ;
__p += '\n\t\t<option value="' +
__e( conjunctionItem ) +
'" ' +
((__t = ( conjunctionItem === conjunction ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( conjunctionItem ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n</div>\n\n<div class="conjunction-guide">\n\t<p><span class="arrow-down"></span>OR any of the following conditions</p>\n</div>\n\n';

}
return __p
};

this["JST"]["src/templates/email_engagement_tally.jst"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<ul class="constraint-toolbar">\n\t<li>\n\t\t<a\n\t\t\talt="f105"\n\t\t\tclass="dashicons dashicons-admin-page copy-constraint"\n\t\t\thref="#"\n\t\t\ttitle="Duplicate"\n\t\t/>\n\n\t\t<a\n\t\t\talt="f105"\n\t\t\tclass="dashicons dashicons-trash remove-constraint"\n\t\t\thref="#"\n\t\t\ttitle="Remove"\n\t\t/>\n\t</li>\n</ul>\n\n<p class="constraint-title">\n\t' +
__e( title ) +
'\n</p>\n\n<p>\n\t<em>On any email in any list</em>\n</p>\n\n<div class="email-engagement-tally">\n\t<select class="constraint-operator">\n\t\t';
 _.each(view.operatorsFor(valueType, type), function(operatorItem) { ;
__p += '\n\t\t<option value="' +
__e( operatorItem ) +
'" ' +
((__t = ( operatorItem === operator ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( operatorItem ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n\n\t<input type="number" class="constraint-value constraint-value-text" value="' +
__e( value ) +
'" />\n\n\t<select class="email-event">\n\t\t';
 _.each(choices, function(choiceItem) { ;
__p += '\n\t\t<option value="' +
__e( choiceItem.value ) +
'" ' +
((__t = ( choiceItem.value == event_name ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( choiceItem.label ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n\n\t<select class="constraint-conjunction">\n\t\t';
 _.each(view.conjunctions, function(conjunctionItem) { ;
__p += '\n\t\t<option value="' +
__e( conjunctionItem ) +
'" ' +
((__t = ( conjunctionItem === conjunction ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( conjunctionItem ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n</div>\n\n<div class="conjunction-guide">\n\t<p><span class="arrow-down"></span>OR any of the following conditions</p>\n</div>\n\n';

}
return __p
};

this["JST"]["src/templates/empty_constraints.jst"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<p class="constraint-empty">Click to add filters</p>\n';

}
return __p
};

this["JST"]["src/templates/entry_answer.jst"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<label><strong>Select Answer: </strong></label>\n<select class="constraint-operator">\n\t';
 _.each(view.operatorsFor(valueType), function(operatorItem) { ;
__p += '\n\t<option value="' +
__e( operatorItem ) +
'" ' +
((__t = ( operatorItem === operator ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t' +
__e( operatorItem ) +
'\n\t</option>\n\t';
 }) ;
__p += '\n</select>\n\n';
 if (view.hasChoices()) { ;
__p += '\n\t<select class="constraint-value">\n\t\t';
 _.each(choices, function(choiceItem) { ;
__p += '\n\t\t<option value="' +
__e( choiceItem.value ) +
'" ' +
((__t = ( choiceItem.value == currentChoice ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( choiceItem.label ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n';
 } else if (fieldType === 'number' || fieldType === 'price') { ;
__p += '\n\t<input type="number" class="constraint-value constraint-value-text"\n\tvalue="' +
__e( value ) +
'" min="' +
__e( fieldOptions.min ) +
'" max="' +
__e( fieldOptions.max ) +
'" />\n';
 } else { ;
__p += '\n\t<input type="text" class="constraint-value constraint-value-text" value="' +
__e( value ) +
'" />\n';
 } ;
__p += '\n\n<select class="constraint-conjunction">\n\t';
 _.each(view.conjunctions, function(conjunctionItem) { ;
__p += '\n\t<option value="' +
__e( conjunctionItem ) +
'" ' +
((__t = ( conjunctionItem === conjunction ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t' +
__e( conjunctionItem ) +
'\n\t</option>\n\t';
 }) ;
__p += '\n</select>\n';

}
return __p
};

this["JST"]["src/templates/entry_constraint.jst"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<ul class="constraint-toolbar">\n\t<li>\n\t\t<a\n\t\t\talt="f105"\n\t\t\tclass="dashicons dashicons-admin-page copy-constraint"\n\t\t\thref="#"\n\t\t\ttitle="Duplicate"\n\t\t/>\n\n\t\t<a\n\t\t\talt="f105"\n\t\t\tclass="dashicons dashicons-trash remove-constraint"\n\t\t\thref="#"\n\t\t\ttitle="Remove"\n\t\t/>\n\t</li>\n</ul>\n\n<p class="constraint-title">\n\t' +
__e( title ) +
'\n</p>\n\n<div class="entry-select-group">\n\t<label><strong>Select ' +
__e( entryTypeName ) +
': </strong></label>\n\t<select class="entry-select-choice entry-select-type">\n\t</select>\n</div>\n\n<div class="entry-select-group">\n\t<label><strong>Select Question: </strong></label>\n\t<select class="entry-select-choice entry-select-field">\n\t</select>\n</div>\n\n<div class="entry-answer-group">\n\t<label><strong>Loading ... </strong></label>\n\t<p>&nbsp;</p>\n</div>\n\n<div class="conjunction-guide">\n\t<p><span class="arrow-down"></span>OR any of the following conditions</p>\n</div>\n';

}
return __p
};

this["JST"]["src/templates/entry_select.jst"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {

 _.each(choices, function(choice, index) { ;
__p += '\n\t<option value="' +
__e( choice.value ) +
'" ' +
((__t = ( choice.value == currentChoice ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t' +
__e( choice.label ) +
'\n\t</option>\n';
 }); ;
__p += '\n';

}
return __p
};

this["JST"]["src/templates/export.jst"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<div id="misc-publishing-actions" style="margin-bottom: 0.5em;">\n\n\t<div class="misc-pub-section misc-pub-post-status">\n\t\t<label for="post_status">Status:</label>\n\t\t<span id="post-status-display">' +
__e( statusText ) +
'</span>\n\t</div>\n\n\t<div class="misc-pub-section curtime misc-pub-curtime">\n\t\t<span id="visibility"> MyEmma Group:\n\t\t\t';
 if (emailSegmentID) { ;
__p += '\n\t\t\t\t<a href="' +
__e( emailSegmentURL ) +
'" target="_blank" class=\'email-segment-url\'>\n\t\t\t\t\t<b>' +
__e( emailSegmentID ) +
'</b>\n\t\t\t\t</a>\n\t\t\t';
 } else { ;
__p += '\n\t\t\t\t\t<b>N/A</b>\n\t\t\t';
 } ;
__p += '\n\t\t\t<a href="#" class="edit-segment-id-button">Edit</a>\n\t\t\t<div class="edit-segment-id" style="display:none">\n\t\t\t\t<p>\n\t\t\t\t\t<input type="text" name="segment_id" value="" id="email-segment-id" />\n\t\t\t\t</p>\n\t\t\t\t<p>\n\t\t\t\t\t<a href="#" class="button change-segment-id-button">OK</a>\n\t\t\t\t\t<a href="#" class="button-cancel cancel-segment-id-button">Cancel</a>\n\t\t\t\t\t<span class="spinner"></span>\n\t\t\t\t\t<span class="error-message"></span>\n\t\t\t\t</p>\n\t\t\t</div>\n\t\t</span>\n\t</div>\n\n\t<div class="misc-pub-section curtime misc-pub-curtime" style="padding-bottom: 1em;">\n\t\t<span id="timestamp"> Last Export: <b>' +
__e( lastExport ) +
'</b></span>\n\t</div>\n\n</div>\n';

}
return __p
};

this["JST"]["src/templates/favorite_constraint.jst"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<ul class="constraint-toolbar">\n\t<li>\n\t\t<a\n\t\t\talt="f105"\n\t\t\tclass="dashicons dashicons-admin-page copy-constraint"\n\t\t\thref="#"\n\t\t\ttitle="Duplicate"\n\t\t/>\n\n\t\t<a\n\t\t\talt="f105"\n\t\t\tclass="dashicons dashicons-trash remove-constraint"\n\t\t\thref="#"\n\t\t\ttitle="Remove"\n\t\t/>\n\t</li>\n</ul>\n\n<p class="constraint-title">\n\t' +
__e( title ) +
'\n</p>\n\n<div class="entry-select-group">\n\t<label><strong>Favorite Type: </strong></label>\n\t<select class="favorite-type">\n\t\t';
 _.each(favoriteTypes, function(favoriteItem) { ;
__p += '\n\t\t<option value="' +
__e( favoriteItem.value ) +
'" ' +
((__t = ( favoriteItem.value == favoriteType ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( favoriteItem.label ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n</div>\n\n<div class="entry-select-group">\n\t<label><strong>Category: </strong></label>\n\t<select class="favorite-category">\n\t\t';
 _.each(categories, function(categoryItem) { ;
__p += '\n\t\t<option value="' +
__e( categoryItem.value ) +
'" ' +
((__t = ( categoryItem.value == category ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( categoryItem.label ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n</div>\n\n<div class="entry-answer-group">\n\t<label><strong>Favorite: </strong></label>\n\n\t<select class="constraint-operator">\n\t\t';
 _.each(view.operatorsFor(valueType), function(operatorItem) { ;
__p += '\n\t\t<option value="' +
__e( operatorItem ) +
'" ' +
((__t = ( operatorItem === operator ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( operatorItem ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n\n\t<input type="text" class="constraint-value constraint-value-text" value="' +
__e( value ) +
'" />\n\n\t<select class="constraint-conjunction">\n\t\t';
 _.each(view.conjunctions, function(conjunctionItem) { ;
__p += '\n\t\t<option value="' +
__e( conjunctionItem ) +
'" ' +
((__t = ( conjunctionItem === conjunction ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( conjunctionItem ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n</div>\n\n<div class="conjunction-guide">\n\t<p><span class="arrow-down"></span>OR any of the following conditions</p>\n</div>\n';

}
return __p
};

this["JST"]["src/templates/like_constraint.jst"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<ul class="constraint-toolbar">\n\t<li>\n\t\t<a\n\t\t\talt="f105"\n\t\t\tclass="dashicons dashicons-admin-page copy-constraint"\n\t\t\thref="#"\n\t\t\ttitle="Duplicate"\n\t\t/>\n\n\t\t<a\n\t\t\talt="f105"\n\t\t\tclass="dashicons dashicons-trash remove-constraint"\n\t\t\thref="#"\n\t\t\ttitle="Remove"\n\t\t/>\n\t</li>\n</ul>\n\n<p class="constraint-title">\n\t' +
__e( title ) +
'\n</p>\n\n<div class="entry-select-group">\n\t<label><strong>Category: </strong></label>\n\t<select class="like-category">\n\t\t';
 _.each(categories, function(categoryItem) { ;
__p += '\n\t\t<option value="' +
__e( categoryItem.value ) +
'" ' +
((__t = ( categoryItem.value == category ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( categoryItem.label ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n</div>\n\n<div class="entry-answer-group">\n\t<label><strong>Like: </strong></label>\n\n\t<select class="constraint-operator">\n\t\t';
 _.each(view.operatorsFor(valueType), function(operatorItem) { ;
__p += '\n\t\t<option value="' +
__e( operatorItem ) +
'" ' +
((__t = ( operatorItem === operator ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( operatorItem ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n\n\t<input type="text" class="constraint-value constraint-value-text" value="' +
__e( value ) +
'" />\n\n\t<select class="constraint-conjunction">\n\t\t';
 _.each(view.conjunctions, function(conjunctionItem) { ;
__p += '\n\t\t<option value="' +
__e( conjunctionItem ) +
'" ' +
((__t = ( conjunctionItem === conjunction ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( conjunctionItem ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n</div>\n\n<div class="conjunction-guide">\n\t<p><span class="arrow-down"></span>OR any of the following conditions</p>\n</div>\n';

}
return __p
};

this["JST"]["src/templates/list_constraint.jst"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<ul class="constraint-toolbar">\n\t<li>\n\t\t<a\n\t\t\talt="f105"\n\t\t\tclass="dashicons dashicons-admin-page copy-constraint"\n\t\t\thref="#"\n\t\t\ttitle="Duplicate"\n\t\t/>\n\n\t\t<a\n\t\t\talt="f105"\n\t\t\tclass="dashicons dashicons-trash remove-constraint"\n\t\t\thref="#"\n\t\t\ttitle="Remove"\n\t\t/>\n\t</li>\n</ul>\n\n<p class="constraint-title">\n\t' +
__e( title ) +
'\n</p>\n\n<select class="constraint-operator">\n\t';
 _.each(view.operatorsFor(valueType, type), function(operatorItem) { ;
__p += '\n\t<option value="' +
__e( operatorItem ) +
'" ' +
((__t = ( operatorItem === operator ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t' +
__e( operatorItem ) +
'\n\t</option>\n\t';
 }) ;
__p += '\n</select>\n\n';
 if (view.hasChoices()) { ;
__p += '\n\t<select class="constraint-value">\n\t\t';
 _.each(choices, function(choiceItem) { ;
__p += '\n\t\t<option value="' +
__e( choiceItem.value ) +
'" ' +
((__t = ( choiceItem.value == value ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t\t' +
__e( choiceItem.label ) +
'\n\t\t</option>\n\t\t';
 }) ;
__p += '\n\t</select>\n';
 } else if (valueType === 'integer' || valueType === 'float') { ;
__p += '\n\t<input type="number" class="constraint-value constraint-value-text" value="' +
__e( value ) +
'" />\n';
 } else { ;
__p += '\n\t<input type="text" class="constraint-value constraint-value-text" value="' +
__e( value ) +
'" />\n';
 } ;
__p += '\n\n<select class="constraint-conjunction">\n\t';
 _.each(view.conjunctions, function(conjunctionItem) { ;
__p += '\n\t<option value="' +
__e( conjunctionItem ) +
'" ' +
((__t = ( conjunctionItem === conjunction ? 'selected="selected"' : ''  )) == null ? '' : __t) +
'">\n\t' +
__e( conjunctionItem ) +
'\n\t</option>\n\t';
 }) ;
__p += '\n</select>\n\n<div class="conjunction-guide">\n\t<p><span class="arrow-down"></span>OR any of the following conditions</p>\n</div>\n';

}
return __p
};

this["JST"]["src/templates/query_result_item.jst"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<span class="preview-result-name">\n\t';
 if (fields.first_name || fields.last_name) { ;
__p += '\n\t\t' +
__e( fields.first_name ) +
' ' +
__e( fields.last_name ) +
'\n\t';
 } else { ;
__p += '\n\t\tN/A\n\t';
 } ;
__p += '\n</span>\n<span class="preview-result-email" title="' +
__e( email ) +
'">\n\t' +
__e( view.domainFor(email) ) +
'\n</span>\n';

}
return __p
};

this["JST"]["src/templates/toolbar_item.jst"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<a title="Click to add" href="#" class="toolbar_item">\n\t' +
__e( title ) +
'\n</a>\n';

}
return __p
};
var EntryType = Backbone.Model.extend({

	defaults: {
		label: 'Entry Type',
		value: -1
	}

});

var EntryField = Backbone.Model.extend({
	defaults: {
		label: '',
		value: '',
		type: '',
		choices: [],
		fieldOptions: {}
	}
});

var Constraint = Backbone.Model.extend({

	defaults        : {
		type        : 'constraint:type_name',
		operator    : 'equals',
		conjunction : 'and',
		valueType   : 'string',
		value       : ''
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

var ProfileConstraint = Constraint.extend({

	defaults        : {
		type        : 'profile:field_name',
		operator    : 'equals',
		conjunction : 'and',
		valueType   : 'string',
		value       : ''
	}

});

var EntryConstraint = Constraint.extend({

	defaults         : {
		type         : 'record:contest',
		operator     : 'equals',
		conjunction  : 'and',
		valueType    : 'string', // TODO: this is actually polymorphic
		value        : '',
		entryTypeID  : -1,
		entryFieldID : -1
	},

	initialize: function(attr, opts) {
		Constraint.prototype.initialize.call(this, attr, opts);
		this.listenTo(this, 'change:entryTypeID', this.didChangeEntryTypeID);
	},

	getEntryType: function() {
		var types = this.get('type').split(':');
		var kind  = types[1];

		return kind;
	},

	getEntryTypes: function() {
		if (!this.entryTypes) {
			this.entryTypes = new EntryTypeCollection();
			this.loadEntryTypes();
		}

		return this.entryTypes;
	},

	loadEntryTypes: function() {
		var params = { type: this.getEntryType() };
		ajaxApi.request('list_entry_types', params)
			.then($.proxy(this.didLoadEntryTypes, this))
			.fail($.proxy(this.didLoadEntryTypesError, this));
	},

	didLoadEntryTypes: function(response) {
		var entryTypes = this.entryTypes;
		entryTypes.reset(response.data, {silent:true});

		if (this.get('entryTypeID') === -1) {
			// auto-select first entry
			var first = entryTypes.at(0);
			if (first !== undefined) {
				this.set('entryTypeID', first.get('value'), {silent:true});
			}
		}

		this.entryTypes.trigger('reset');
		this.loadEntryFields();
	},

	didLoadEntryTypesError: function(response) {
		console.log(response);
	},

	didChangeEntryTypeID: function() {
		this.loadEntryFields();
	},

	loadEntryFields: function() {
		var params = { entryTypeID: this.get('entryTypeID') };
		ajaxApi.request('list_entry_fields', params)
			.then($.proxy(this.didLoadEntryFields, this))
			.fail($.proxy(this.didLoadEntryFieldsError, this));
	},

	didLoadEntryFields: function(response) {
		var entryFields = this.getEntryFields();
		var first;

		entryFields.reset(response.data, {silent:true});

		if (this.get('entryFieldID') === -1) {
			// auto-select first field
			first = entryFields.at(0);
			if (first !== undefined) {
				this.set('entryFieldID', first.get('value'), {silent:true});
			}
		}

		entryFields.trigger('reset');
		this.trigger('change');
	},

	didLoadEntryFieldsError: function(response) {
		console.log(response);
	},

	getEntryFields: function() {
		if (!this.entryFields) {
			this.entryFields = new EntryFieldCollection();
		}

		return this.entryFields;
	},

	getEntryFieldChoices: function(entryFieldID) {
		if (!entryFieldID) {
			entryFieldID = this.get('entryFieldID');
		}

		var fieldCollection = this.getEntryFields();
		var field           = fieldCollection.findWhere({
			value: entryFieldID
		});

		if (field) {
			var choices = field.get('choices');
			if (choices && choices.length > 0) {
				return choices;
			} else {
				return [];
			}
		} else {
			return [];
		}
	},

	getEntryFieldOptions: function() {
		var fieldCollection = this.getEntryFields();
		var field           = fieldCollection.findWhere({
			value: this.get('entryFieldID')
		});

		if (field) {
			return field.get('fieldOptions');
		} else {
			return {};
		}
	},

	getEntryFieldType: function() {
		var fieldOptions = this.getEntryFieldOptions();
		if (fieldOptions.fieldType) {
			return fieldOptions.fieldType;
		} else {
			return 'text';
		}
	},

	hasChoices: function() {
		var choices = this.getEntryFieldChoices();
		return choices.length > 0;
	},

	toViewJSON: function() {
		var json = Constraint.prototype.toViewJSON.call(this);
		json.entryTypeName = this.getEntryTypeName(this.getEntryType());

		return json;
	},

	getEntryTypeName: function(type) {
		return type.charAt(0).toUpperCase() + type.substring(1).toLowerCase();
	}

});

var LikeConstraint = Constraint.extend({

	defaults        : {
		type        : 'profile:likes',
		operator    : 'equals',
		conjunction : 'and',
		valueType   : 'string',
		value       : '',
		category    : 'Any Category',
	},

	getCategories: function() {
		return FACEBOOK_CATEGORIES;
	}

});

FACEBOOK_CATEGORIES = [
	{ label: 'Actor/Director' },
	{ label: 'Movie' },
	{ label: 'Producer' },
	{ label: 'Writer' },
	{ label: 'Studio' },
	{ label: 'Movie Theater' },
	{ label: 'TV/Movie Award' },
	{ label: 'Fictional Character' },
	{ label: 'Movie Character' },
	{ label: 'Album' },
	{ label: 'Song' },
	{ label: 'Musician/Band' },
	{ label: 'Music Video' },
	{ label: 'Concert Tour' },
	{ label: 'Concert Venue' },
	{ label: 'Radio Station' },
	{ label: 'Record Label' },
	{ label: 'Music Award' },
	{ label: 'Music Chart' },
	{ label: 'Book' },
	{ label: 'Author' },
	{ label: 'Book Store' },
	{ label: 'Library' },
	{ label: 'Magazine' },
	{ label: 'Book Series' },
	{ label: 'TV Show' },
	{ label: 'TV Network' },
	{ label: 'TV Channel' },
	{ label: 'Athlete' },
	{ label: 'Artist' },
	{ label: 'Public Figure' },
	{ label: 'Journalist' },
	{ label: 'News Personality' },
	{ label: 'Chef' },
	{ label: 'Lawyer' },
	{ label: 'Doctor' },
	{ label: 'Business Person' },
	{ label: 'Comedian' },
	{ label: 'Entertainer' },
	{ label: 'Teacher' },
	{ label: 'Dancer' },
	{ label: 'Designer' },
	{ label: 'Photographer' },
	{ label: 'Entrepreneur' },
	{ label: 'Politician' },
	{ label: 'Government Official' },
	{ label: 'Sports League' },
	{ label: 'Professional Sports Team' },
	{ label: 'Coach' },
	{ label: 'Amateur Sports Team' },
	{ label: 'School Sports Team' },
	{ label: 'Restaurant/Cafe' },
	{ label: 'Bar' },
	{ label: 'Club' },
	{ label: 'Company' },
	{ label: 'Product/Service' },
	{ label: 'Website' },
	{ label: 'Cars' },
	{ label: 'Bags/Luggage' },
	{ label: 'Camera/Photo' },
	{ label: 'Clothing' },
	{ label: 'Computers' },
	{ label: 'Software' },
	{ label: 'Office Supplies' },
	{ label: 'Electronics' },
	{ label: 'Health/Beauty' },
	{ label: 'Appliances' },
	{ label: 'Building Materials' },
	{ label: 'Commercial Equipment' },
	{ label: 'Home Decor' },
	{ label: 'Furniture' },
	{ label: 'Household Supplies' },
	{ label: 'Kitchen/Cooking' },
	{ label: 'Patio/Garden' },
	{ label: 'Tools/Equipment' },
	{ label: 'Wine/Spirits' },
	{ label: 'Jewelry/Watches' },
	{ label: 'Pet Supplies' },
	{ label: 'Outdoor Gear/Sporting Goods' },
	{ label: 'Baby Goods/Kids Goods' },
	{ label: 'Media/News/Publishing' },
	{ label: 'Bank/Financial Institution' },
	{ label: 'Non-Governmental Organization (NGO)' },
	{ label: 'Insurance Company' },
	{ label: 'Small Business' },
	{ label: 'Energy/Utility' },
	{ label: 'Retail and Consumer Merchandise' },
	{ label: 'Automobiles and Parts' },
	{ label: 'Industrials' },
	{ label: 'Transport/Freight' },
	{ label: 'Health/Medical/Pharmaceuticals' },
	{ label: 'Aerospace/Defense' },
	{ label: 'Mining/Materials' },
	{ label: 'Farming/Agriculture' },
	{ label: 'Chemicals' },
	{ label: 'Consulting/Business Services' },
	{ label: 'Legal/Law' },
	{ label: 'Education' },
	{ label: 'Engineering/Construction' },
	{ label: 'Food/Beverages' },
	{ label: 'Telecommunication' },
	{ label: 'Biotechnology' },
	{ label: 'Computers/Technology' },
	{ label: 'Internet/Software' },
	{ label: 'Travel/Leisure' },
	{ label: 'Community Organization' },
	{ label: 'Political Organization' },
	{ label: 'Vitamins/Supplements' },
	{ label: 'Drugs' },
	{ label: 'Church/Religious Organization' },
	{ label: 'Phone/Tablet' },
	{ label: 'Games/Toys' },
	{ label: 'App Page' },
	{ label: 'Video Game' },
	{ label: 'Board Game' },
	{ label: 'Local Business' },
	{ label: 'Hotel' },
	{ label: 'Landmark' },
	{ label: 'Airport' },
	{ label: 'Sports Venue' },
	{ label: 'Arts/Entertainment/Nightlife' },
	{ label: 'Automotive' },
	{ label: 'Spas/Beauty/Personal Care' },
	{ label: 'Event Planning/Event Services' },
	{ label: 'Bank/Financial Services' },
	{ label: 'Food/Grocery' },
	{ label: 'Health/Medical/Pharmacy' },
	{ label: 'Home Improvement' },
	{ label: 'Pet Services' },
	{ label: 'Professional Services' },
	{ label: 'Business Services' },
	{ label: 'Community/Government' },
	{ label: 'Real Estate' },
	{ label: 'Shopping/Retail' },
	{ label: 'Public Places' },
	{ label: 'Attractions/Things to Do' },
	{ label: 'Sports/Recreation/Activities' },
	{ label: 'Tours/Sightseeing' },
	{ label: 'Transportation' },
	{ label: 'Hospital/Clinic' },
	{ label: 'Museum/Art Gallery' },
	{ label: 'Organization' },
	{ label: 'School' },
	{ label: 'University' },
	{ label: 'Non-Profit Organization' },
	{ label: 'Government Organization' },
	{ label: 'Cause' },
	{ label: 'Political Party' },
	{ label: 'Pet' },
	{ label: 'Middle School' },
];

(function() {
	var i = 0;
	var n = FACEBOOK_CATEGORIES.length;
	var category;

	for (i = 0; i < n; i++) {
		category = FACEBOOK_CATEGORIES[i];
		if (!category.value) {
			category.value = category.label;
		}
	}

	FACEBOOK_CATEGORIES = _.sortBy(FACEBOOK_CATEGORIES, 'label');
	FACEBOOK_CATEGORIES.unshift({
		label: 'Any Category', value: 'Any Category'
	});

}());

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

		//console.log('getListTypeName', type, parts, subType);
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
		//console.log('loadList: type', type);
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

var getSubscribedToListChoices = function() {
	var emmaGroups = window.member_query_meta.emma_groups;
	var n          = emmaGroups.length;
	var choices    = [];

	for (var i = 0; i < n; i++) {
		group = emmaGroups[i];
		choice = {
			label: group.group_name,
			value: group.group_id
		};

		choices.push(choice);
	}

	return choices;
};

var AVAILABLE_CONSTRAINTS = [


	/* System Fields */
	{
		type: 'data:registeredTimestamp',
		valueType: 'epoch',
		value: '01/01/2012',
		operator: 'greater than',
	},
	{
		type: 'system:lastLoginTimestamp',
		valueType: 'date',
		value: '01/01/2014',
		operator: 'greater than',
	},
	{
		type: 'system:isActive',
		valueType: 'boolean',
		value: true,
	},
	{
		type: 'system:isRegistered',
		valueType: 'boolean',
		value: true,
	},
	{
		type: 'system:isVerified',
		valueType: 'boolean',
		value: true,
	},
	{
		type: 'data:optout',
		valueType: 'boolean',
		value: true,
	},
	{
		type: 'data:subscribedToList',
		valueType: 'enum',
		operator: 'contains',
	},

	/* Profile fields */
	{
		type: 'profile:gender',
		valueType: 'string',
		value: 'm'
	},
	{
		type: 'profile:age',
		valueType: 'integer',
		value: 25
	},
	{
		type: 'profile:birthDay',
		valueType: 'integer',
		value: 1,
	},
	{
		type: 'profile:birthMonth',
		valueType: 'integer',
		value: 1
	},
	{
		type: 'profile:birthYear',
		valueType: 'integer',
		value: 1990
	},
	{
		type: 'profile:state',
		valueType: 'string',
		value: 'New York'
	},
	{
		type: 'profile:city',
		valueType: 'string'
	},
	/*
	{
		type: 'profile:country',
		valueType: 'string',
		value: 'United States'
	},
	*/
	{
		type: 'profile:zip',
		valueType: 'string',
		value: '01001'
	},

	/*
	{
		type: 'profile:timezone',
		valueType: 'string',
		value: 'America/New_York',
	},
	*/

	// Facebook
	{
		type: 'profile:likes',
		valueType: 'string',
		category: 'Any Category',
		value: '',
		conjunction: 'and',
	},
	{
		type: 'profile:favorites',
		valueType: 'string',
		category: 'Any Category',
		value: '',
		conjunction: 'and',
	},
	{
		type: 'data:listeningFrequency',
		valueType: 'string',
		value: '0',
	},
	{
		type: 'data:listeningLoyalty',
		valueType: 'string',
		value: '0',
	},

	/* Contests */
	{
		type: 'record:contest',
		valueType: 'string',
		entryTypeID: -1,
		entryFieldID: -1,
		conjunction: 'or',
	},
	{
		type: 'data:contest_list',
		valueType: 'enum',
		value: ''
	},

	/* Surveys */
	{
		type: 'record:survey',
		valueType: 'string',
		entryTypeID: -1,
		entryFieldID: -1,
		conjunction: 'or',
	},
	{
		type: 'data:survey_list',
		valueType: 'enum',
		value: ''
	},

	{
		type: 'data:comment_count',
		valueType: 'integer',
		value: 1,
	},
	{
		type: 'data:comment_status',
		valueType: 'boolean',
		value: true,
	},
	{
		type: 'action:comment_date',
		valueType: 'date',
		value: '01/01/2014',
		operator: 'greater than',
	},
	{
		type: 'data:social_share_count',
		valueType: 'integer',
		value: 1,
	},
	{
		type: 'data:social_share_status',
		valueType: 'boolean',
		value: true,
	},
	{
		type: 'action:social_share',
		valueType: 'string',
		value: '',
		operator: 'contains',
	},

	{
		type: 'data:email_engagement_tally',
		valueType: 'integer',
		value: '1',
		operator: 'equals',
	},
	{
		type: 'data:email_engagement',
		valueType: 'enum',
		value: 'any',
		operator: 'equals',
	},

	/*
	{
		type: 'data:member_query_message_open_list',
		valueType: 'list',
		value: '',
	},
	{
		type: 'data:member_query_message_open_status',
		valueType: 'boolean',
		value: true,
	},
	{
		type: 'data:member_query_message_open_count',
		valueType: 'integer',
		value: 1,
	},

	{
		type: 'data:member_query_message_click_list',
		valueType: 'list',
		value: '',
	},
	{
		type: 'data:member_query_message_click_status',
		valueType: 'boolean',
		value: true,
	},
	{
		type: 'data:member_query_message_click_count',
		valueType: 'integer',
		value: 1,
	},

	{
		type: 'data:static_group_message_open_list',
		valueType: 'list',
		value: '',
	},
	{
		type: 'data:static_group_message_open_status',
		valueType: 'boolean',
		value: true,
	},
	{
		type: 'data:static_group_message_open_count',
		valueType: 'integer',
		value: 1,
	},

	{
		type: 'data:static_group_message_click_list',
		valueType: 'boolean',
		value: '',
	},
	{
		type: 'data:static_group_message_click_status',
		valueType: 'boolean',
		value: true,
	},
	{
		type: 'data:static_group_message_click_count',
		valueType: 'integer',
		value: 1,
	},
	*/
];

/* Constraint Meta */

var AVAILABLE_CONSTRAINTS_META = [

	/* System Fields */
	{
		type: 'data:registeredTimestamp',
		title: 'Creation Date',
	},
	{
		type: 'system:isActive',
		title: 'Active Status',
		choices: [
			{ label: 'Active', value: true },
			{ label: 'Inactive', value: false }
		]
	},
	{
		type: 'system:isRegistered',
		title: 'Registration Status',
		choices: [
			{ label: 'Registered', value: true },
			{ label: 'Not Registered', value: false }
		]
	},
	{
		type: 'system:lastLoginTimestamp',
		title: 'Last Logged In'
	},
	{
		type: 'system:isVerified',
		title: 'Verified Status',
		choices: [
			{ label: 'Verified', value: true },
			{ label: 'Not Verified', value: false },
		]
	},

	/* Profile Fields */
	{
		type: 'profile:likes',
		title: 'Facebook Likes'
	},
	{
		type: 'profile:favorites',
		title: 'Facebook Favorites'
	},
	{
		type: 'profile:birthYear',
		title: 'Birth Year'
	},
	/*
	{
		type: 'profile:country',
		title: 'Country',
		choices: [
			{ label: 'Afghanistan',                       value: 'Afghanistan'        } ,
			{ label: 'Albania',                           value: 'Albania'            } ,
			{ label: 'Algeria',                           value: 'Algeria'            } ,
			{ label: 'American Samoa',                    value: 'Samoa'              } ,
			{ label: 'Andorra',                           value: 'Andorra'            } ,
			{ label: 'Angola',                            value: 'Angola'             } ,
			{ label: 'Antigua and Barbuda',               value: 'Barbuda'            } ,
			{ label: 'Argentina',                         value: 'Argentina'          } ,
			{ label: 'Armenia',                           value: 'Armenia'            } ,
			{ label: 'Australia',                         value: 'Australia'          } ,
			{ label: 'Austria',                           value: 'Austria'            } ,
			{ label: 'Azerbaijan',                        value: 'Azerbaijan'         } ,
			{ label: 'Bahamas',                           value: 'Bahamas'            } ,
			{ label: 'Bahrain',                           value: 'Bahrain'            } ,
			{ label: 'Bangladesh',                        value: 'Bangladesh'         } ,
			{ label: 'Barbados',                          value: 'Barbados'           } ,
			{ label: 'Belarus',                           value: 'Belarus'            } ,
			{ label: 'Belgium',                           value: 'Belgium'            } ,
			{ label: 'Belize',                            value: 'Belize'             } ,
			{ label: 'Benin',                             value: 'Benin'              } ,
			{ label: 'Bermuda',                           value: 'Bermuda'            } ,
			{ label: 'Bhutan',                            value: 'Bhutan'             } ,
			{ label: 'Bolivia',                           value: 'Bolivia'            } ,
			{ label: 'Bosnia and Herzegovina',            value: 'Herzegovina'        } ,
			{ label: 'Botswana',                          value: 'Botswana'           } ,
			{ label: 'Brazil',                            value: 'Brazil'             } ,
			{ label: 'Brunei',                            value: 'Brunei'             } ,
			{ label: 'Bulgaria',                          value: 'Bulgaria'           } ,
			{ label: 'Burkina Faso',                      value: 'Faso'               } ,
			{ label: 'Burundi',                           value: 'Burundi'            } ,
			{ label: 'Cambodia',                          value: 'Cambodia'           } ,
			{ label: 'Cameroon',                          value: 'Cameroon'           } ,
			{ label: 'Canada',                            value: 'Canada'             } ,
			{ label: 'Cape Verde',                        value: 'Verde'              } ,
			{ label: 'Cayman Islands',                    value: 'Islands'            } ,
			{ label: 'Central African Republic',          value: 'Republic'           } ,
			{ label: 'Chad',                              value: 'Chad'               } ,
			{ label: 'Chile',                             value: 'Chile'              } ,
			{ label: 'China',                             value: 'China'              } ,
			{ label: 'Colombia',                          value: 'Colombia'           } ,
			{ label: 'Comoros',                           value: 'Comoros'            } ,
			{ label: 'Congo, Democratic Republic of the', value: 'the'                } ,
			{ label: 'Congo, Republic of the',            value: 'the'                } ,
			{ label: 'Costa Rica',                        value: 'Rica'               } ,
			{ label: 'Croatia',                           value: 'Croatia'            } ,
			{ label: 'Cuba',                              value: 'Cuba'               } ,
			{ label: 'Cyprus',                            value: 'Cyprus'             } ,
			{ label: 'Czech Republic',                    value: 'Republic'           } ,
			{ label: 'Denmark',                           value: 'Denmark'            } ,
			{ label: 'Djibouti',                          value: 'Djibouti'           } ,
			{ label: 'Dominica',                          value: 'Dominica'           } ,
			{ label: 'Dominican Republic',                value: 'Republic'           } ,
			{ label: 'East Timor',                        value: 'Timor'              } ,
			{ label: 'Ecuador',                           value: 'Ecuador'            } ,
			{ label: 'Egypt',                             value: 'Egypt'              } ,
			{ label: 'El Salvador',                       value: 'Salvador'           } ,
			{ label: 'Equatorial Guinea',                 value: 'Guinea'             } ,
			{ label: 'Eritrea',                           value: 'Eritrea'            } ,
			{ label: 'Estonia',                           value: 'Estonia'            } ,
			{ label: 'Ethiopia',                          value: 'Ethiopia'           } ,
			{ label: 'Fiji',                              value: 'Fiji'               } ,
			{ label: 'Finland',                           value: 'Finland'            } ,
			{ label: 'France',                            value: 'France'             } ,
			{ label: 'French Polynesia',                  value: 'Polynesia'          } ,
			{ label: 'Gabon',                             value: 'Gabon'              } ,
			{ label: 'Gambia',                            value: 'Gambia'             } ,
			{ label: 'Georgia',                           value: 'Georgia'            } ,
			{ label: 'Germany',                           value: 'Germany'            } ,
			{ label: 'Ghana',                             value: 'Ghana'              } ,
			{ label: 'Greece',                            value: 'Greece'             } ,
			{ label: 'Greenland',                         value: 'Greenland'          } ,
			{ label: 'Grenada',                           value: 'Grenada'            } ,
			{ label: 'Guam',                              value: 'Guam'               } ,
			{ label: 'Guatemala',                         value: 'Guatemala'          } ,
			{ label: 'Guinea',                            value: 'Guinea'             } ,
			{ label: 'Guinea-Bissau',                     value: 'Bissau'             } ,
			{ label: 'Guyana',                            value: 'Guyana'             } ,
			{ label: 'Haiti',                             value: 'Haiti'              } ,
			{ label: 'Honduras',                          value: 'Honduras'           } ,
			{ label: 'Hong Kong',                         value: 'Kong'               } ,
			{ label: 'Hungary',                           value: 'Hungary'            } ,
			{ label: 'Iceland',                           value: 'Iceland'            } ,
			{ label: 'India',                             value: 'India'              } ,
			{ label: 'Indonesia',                         value: 'Indonesia'          } ,
			{ label: 'Iran',                              value: 'Iran'               } ,
			{ label: 'Iraq',                              value: 'Iraq'               } ,
			{ label: 'Ireland',                           value: 'Ireland'            } ,
			{ label: 'Israel',                            value: 'Israel'             } ,
			{ label: 'Italy',                             value: 'Italy'              } ,
			{ label: 'Jamaica',                           value: 'Jamaica'            } ,
			{ label: 'Japan',                             value: 'Japan'              } ,
			{ label: 'Jordan',                            value: 'Jordan'             } ,
			{ label: 'Kazakhstan',                        value: 'Kazakhstan'         } ,
			{ label: 'Kenya',                             value: 'Kenya'              } ,
			{ label: 'Kiribati',                          value: 'Kiribati'           } ,
			{ label: 'North Korea',                       value: 'Korea'              } ,
			{ label: 'South Korea',                       value: 'Korea'              } ,
			{ label: 'Kosovo',                            value: 'Kosovo'             } ,
			{ label: 'Kuwait',                            value: 'Kuwait'             } ,
			{ label: 'Kyrgyzstan',                        value: 'Kyrgyzstan'         } ,
			{ label: 'Laos',                              value: 'Laos'               } ,
			{ label: 'Latvia',                            value: 'Latvia'             } ,
			{ label: 'Lebanon',                           value: 'Lebanon'            } ,
			{ label: 'Lesotho',                           value: 'Lesotho'            } ,
			{ label: 'Liberia',                           value: 'Liberia'            } ,
			{ label: 'Libya',                             value: 'Libya'              } ,
			{ label: 'Liechtenstein',                     value: 'Liechtenstein'      } ,
			{ label: 'Lithuania',                         value: 'Lithuania'          } ,
			{ label: 'Luxembourg',                        value: 'Luxembourg'         } ,
			{ label: 'Macedonia',                         value: 'Macedonia'          } ,
			{ label: 'Madagascar',                        value: 'Madagascar'         } ,
			{ label: 'Malawi',                            value: 'Malawi'             } ,
			{ label: 'Malaysia',                          value: 'Malaysia'           } ,
			{ label: 'Maldives',                          value: 'Maldives'           } ,
			{ label: 'Mali',                              value: 'Mali'               } ,
			{ label: 'Malta',                             value: 'Malta'              } ,
			{ label: 'Marshall Islands',                  value: 'Islands'            } ,
			{ label: 'Mauritania',                        value: 'Mauritania'         } ,
			{ label: 'Mauritius',                         value: 'Mauritius'          } ,
			{ label: 'Mexico',                            value: 'Mexico'             } ,
			{ label: 'Micronesia',                        value: 'Micronesia'         } ,
			{ label: 'Moldova',                           value: 'Moldova'            } ,
			{ label: 'Monaco',                            value: 'Monaco'             } ,
			{ label: 'Mongolia',                          value: 'Mongolia'           } ,
			{ label: 'Montenegro',                        value: 'Montenegro'         } ,
			{ label: 'Morocco',                           value: 'Morocco'            } ,
			{ label: 'Mozambique',                        value: 'Mozambique'         } ,
			{ label: 'Myanmar',                           value: 'Myanmar'            } ,
			{ label: 'Namibia',                           value: 'Namibia'            } ,
			{ label: 'Nauru',                             value: 'Nauru'              } ,
			{ label: 'Nepal',                             value: 'Nepal'              } ,
			{ label: 'Netherlands',                       value: 'Netherlands'        } ,
			{ label: 'New Zealand',                       value: 'Zealand'            } ,
			{ label: 'Nicaragua',                         value: 'Nicaragua'          } ,
			{ label: 'Niger',                             value: 'Niger'              } ,
			{ label: 'Nigeria',                           value: 'Nigeria'            } ,
			{ label: 'Norway',                            value: 'Norway'             } ,
			{ label: 'Northern Mariana Islands',          value: 'Islands'            } ,
			{ label: 'Oman',                              value: 'Oman'               } ,
			{ label: 'Pakistan',                          value: 'Pakistan'           } ,
			{ label: 'Palau',                             value: 'Palau'              } ,
			{ label: 'Palestine',                         value: 'Palestine'          } ,
			{ label: 'Panama',                            value: 'Panama'             } ,
			{ label: 'Papua New Guinea',                  value: 'Guinea'             } ,
			{ label: 'Paraguay',                          value: 'Paraguay'           } ,
			{ label: 'Peru',                              value: 'Peru'               } ,
			{ label: 'Philippines',                       value: 'Philippines'        } ,
			{ label: 'Poland',                            value: 'Poland'             } ,
			{ label: 'Portugal',                          value: 'Portugal'           } ,
			{ label: 'Puerto Rico',                       value: 'Rico'               } ,
			{ label: 'Qatar',                             value: 'Qatar'              } ,
			{ label: 'Romania',                           value: 'Romania'            } ,
			{ label: 'Russia',                            value: 'Russia'             } ,
			{ label: 'Rwanda',                            value: 'Rwanda'             } ,
			{ label: 'Saint Kitts and Nevis',             value: 'Nevis'              } ,
			{ label: 'Saint Lucia',                       value: 'Lucia'              } ,
			{ label: 'Saint Vincent and the Grenadines',  value: 'Grenadines'         } ,
			{ label: 'Samoa',                             value: 'Samoa'              } ,
			{ label: 'San Marino',                        value: 'Marino'             } ,
			{ label: 'Sao Tome and Principe',             value: 'Principe'           } ,
			{ label: 'Saudi Arabia',                      value: 'Arabia'             } ,
			{ label: 'Senegal',                           value: 'Senegal'            } ,
			{ label: 'Serbia and Montenegro',             value: 'Montenegro'         } ,
			{ label: 'Seychelles',                        value: 'Seychelles'         } ,
			{ label: 'Sierra Leone',                      value: 'Leone'              } ,
			{ label: 'Singapore',                         value: 'Singapore'          } ,
			{ label: 'Slovakia',                          value: 'Slovakia'           } ,
			{ label: 'Slovenia',                          value: 'Slovenia'           } ,
			{ label: 'Solomon Islands',                   value: 'Islands'            } ,
			{ label: 'Somalia',                           value: 'Somalia'            } ,
			{ label: 'South Africa',                      value: 'South Africa'       } ,
			{ label: 'Spain',                             value: 'Spain'              } ,
			{ label: 'Sri Lanka',                         value: 'Lanka'              } ,
			{ label: 'Sudan',                             value: 'Sudan'              } ,
			{ label: 'Sudan, South',                      value: 'South'              } ,
			{ label: 'Suriname',                          value: 'Suriname'           } ,
			{ label: 'Swaziland',                         value: 'Swaziland'          } ,
			{ label: 'Sweden',                            value: 'Sweden'             } ,
			{ label: 'Switzerland',                       value: 'Switzerland'        } ,
			{ label: 'Syria',                             value: 'Syria'              } ,
			{ label: 'Taiwan',                            value: 'Taiwan'             } ,
			{ label: 'Tajikistan',                        value: 'Tajikistan'         } ,
			{ label: 'Tanzania',                          value: 'Tanzania'           } ,
			{ label: 'Thailand',                          value: 'Thailand'           } ,
			{ label: 'Togo',                              value: 'Togo'               } ,
			{ label: 'Tonga',                             value: 'Tonga'              } ,
			{ label: 'Trinidad and Tobago',               value: 'Tobago'             } ,
			{ label: 'Tunisia',                           value: 'Tunisia'            } ,
			{ label: 'Turkey',                            value: 'Turkey'             } ,
			{ label: 'Turkmenistan',                      value: 'Turkmenistan'       } ,
			{ label: 'Tuvalu',                            value: 'Tuvalu'             } ,
			{ label: 'Uganda',                            value: 'Uganda'             } ,
			{ label: 'Ukraine',                           value: 'Ukraine'            } ,
			{ label: 'United Arab Emirates',              value: 'Emirates'           } ,
			{ label: 'United Kingdom',                    value: 'Kingdom'            } ,
			{ label: 'United States',                     value: 'United States'      } ,
			{ label: 'Uruguay',                           value: 'Uruguay'            } ,
			{ label: 'Uzbekistan',                        value: 'Uzbekistan'         } ,
			{ label: 'Vanuatu',                           value: 'Vanuatu'            } ,
			{ label: 'Vatican City',                      value: 'City'               } ,
			{ label: 'Venezuela',                         value: 'Venezuela'          } ,
			{ label: 'Vietnam',                           value: 'Vietnam'            } ,
			{ label: 'Virgin Islands, British',           value: 'British'            } ,
			{ label: 'Virgin Islands, U.S.',              value: 'Virgin Islands, US' } ,
			{ label: 'Yemen',                             value: 'Yemen'              } ,
			{ label: 'Zambia',                            value: 'Zambia'             } ,
			{ label: 'Zimbabwe',                          value: 'Zimbabwe'           } ,
		]
	},
	*/
	{
		type: 'profile:zip',
		title: 'Zip Code'
	},
	{
		type: 'profile:gender',
		title: 'Gender',
		choices: [
			{ label: 'Male', value: 'm' },
			{ label: 'Female', value: 'f' },
			{ label: 'Unknown', value: 'u' }
		]
	},
	{
		type: 'profile:age',
		title: 'Age',
	},
	{
		type: 'profile:birthDay',
		title: 'Birth Day'
	},
	{
		type: 'profile:birthMonth',
		title: 'Birth Month',
		choices: [
			{ label: 'January'   , value: 1  } ,
			{ label: 'February'  , value: 2  } ,
			{ label: 'March'     , value: 3  } ,
			{ label: 'April'     , value: 4  } ,
			{ label: 'May'       , value: 5  } ,
			{ label: 'June'      , value: 6  } ,
			{ label: 'July'      , value: 7  } ,
			{ label: 'August'    , value: 8  } ,
			{ label: 'September' , value: 9  } ,
			{ label: 'October'   , value: 10 } ,
			{ label: 'November'  , value: 11 } ,
			{ label: 'December'  , value: 12 } ,
		]
	},
	{
		type: 'profile:state',
		title: 'State',
		choices: [
			{ label: 'Alabama'               , value: 'AL' } ,
			{ label: 'Alaska'                , value: 'AK' } ,
			{ label: 'Arizona'               , value: 'AZ' } ,
			{ label: 'Arkansas'              , value: 'AR' } ,
			{ label: 'California'            , value: 'CA' } ,
			{ label: 'Colorado'              , value: 'CO' } ,
			{ label: 'Connecticut'           , value: 'CT' } ,
			{ label: 'Delaware'              , value: 'DE' } ,
			{ label: 'District of Columbia'  , value: 'DC' } ,
			{ label: 'Florida'               , value: 'FL' } ,
			{ label: 'Georgia'               , value: 'GA' } ,
			{ label: 'Hawaii'                , value: 'HI' } ,
			{ label: 'Idaho'                 , value: 'ID' } ,
			{ label: 'Illinois'              , value: 'IL' } ,
			{ label: 'Indiana'               , value: 'IN' } ,
			{ label: 'Iowa'                  , value: 'IA' } ,
			{ label: 'Kansas'                , value: 'KS' } ,
			{ label: 'Kentucky'              , value: 'KY' } ,
			{ label: 'Louisiana'             , value: 'LA' } ,
			{ label: 'Maine'                 , value: 'ME' } ,
			{ label: 'Maryland'              , value: 'MD' } ,
			{ label: 'Massachusetts'         , value: 'MA' } ,
			{ label: 'Michigan'              , value: 'MI' } ,
			{ label: 'Minnesota'             , value: 'MN' } ,
			{ label: 'Mississippi'           , value: 'MS' } ,
			{ label: 'Missouri'              , value: 'MO' } ,
			{ label: 'Montana'               , value: 'MT' } ,
			{ label: 'Nebraska'              , value: 'NE' } ,
			{ label: 'Nevada'                , value: 'NV' } ,
			{ label: 'New Hampshire'         , value: 'NH' } ,
			{ label: 'New Jersey'            , value: 'NJ' } ,
			{ label: 'New Mexico'            , value: 'NM' } ,
			{ label: 'New York'              , value: 'NY' } ,
			{ label: 'North Carolina'        , value: 'NC' } ,
			{ label: 'North Dakota'          , value: 'ND' } ,
			{ label: 'Ohio'                  , value: 'OH' } ,
			{ label: 'Oklahoma'              , value: 'OK' } ,
			{ label: 'Oregon'                , value: 'OR' } ,
			{ label: 'Pennsylvania'          , value: 'PA' } ,
			{ label: 'Rhode Island'          , value: 'RI' } ,
			{ label: 'South Carolina'        , value: 'SC' } ,
			{ label: 'South Dakota'          , value: 'SD' } ,
			{ label: 'Tennessee'             , value: 'TN' } ,
			{ label: 'Texas'                 , value: 'TX' } ,
			{ label: 'Utah'                  , value: 'UT' } ,
			{ label: 'Vermont'               , value: 'VT' } ,
			{ label: 'Virginia'              , value: 'VA' } ,
			{ label: 'Washington'            , value: 'WA' } ,
			{ label: 'West Virginia'         , value: 'WV' } ,
			{ label: 'Wisconsin'             , value: 'WI' } ,
			{ label: 'Wyoming'               , value: 'WY' } ,
			{ label: 'Armed Forces Americas' , value: 'AA' } ,
			{ label: 'Armed Forces Europe'   , value: 'AE' } ,
			{ label: 'Armed Forces Pacific'  , value: 'AP' } ,
		]
	},
	{
		type: 'profile:city',
		title: 'City'
	},
	{
		type: 'profile:timezone',
		title: 'Timezone'
	},

	/* Contests */
	{
		type: 'record:contest',
		title: 'Contest Entry'
	},
	{
		type: 'data:contest_list',
		title: 'Contest Participation',
		choices: [
		]
	},
	{
		type: 'record:survey',
		title: 'Survey Entry'
	},
	{
		type: 'data:survey_list',
		title: 'Survey Participation',
		choices: [
		]
	},

	{
		type: 'data:comment_count',
		title: 'Comment Count'
	},
	{
		type: 'data:comment_status',
		title: 'Comment Status',
		choices: [
			{ label: 'Has Commented', value: true },
			{ label: 'Has Not Commented', value: false }
		]
	},
	{
		type: 'action:comment_date',
		title: 'Comment Date'
	},
	{
		type: 'data:optout',
		title: 'Optout Status',
		choices: [
			{ label: 'Yes', value: true },
			{ label: 'No', value: false }
		]
	},
	{
		type: 'data:subscribedToList',
		title: 'Subscribed To List',
		choices: getSubscribedToListChoices(),
	},
	{
		type: 'data:listeningFrequency',
		title: 'Listening Frequency',
		choices: [
			{ label: 'Less than 1 hour', value: '0' },
			{ label: '1 to 3 hours', value: '1' },
			{ label: 'more than 3 hours', value: '2' }
		]
	},
	{
		type: 'data:listeningLoyalty',
		title: 'Listening Loyalty',
		choices: [
			{ label: '0%', value: '0' },
			{ label: '10%', value: '10' },
			{ label: '20%', value: '20' },
			{ label: '30%', value: '30' },
			{ label: '40%', value: '40' },
			{ label: '50%', value: '50' },
			{ label: '60%', value: '60' },
			{ label: '70%', value: '70' },
			{ label: '80%', value: '80' },
			{ label: '90%', value: '90' },
			{ label: '100%', value: '100' },
		]
	},
	{
		type: 'data:social_share_count',
		title: 'Social Share Count'
	},
	{
		type: 'data:social_share_status',
		title: 'Social Share Status',
		choices: [
			{ label: 'Has Shared', value: true },
			{ label: 'Has Not Shared', value: false }
		]
	},
	{
		type: 'action:social_share',
		title: 'Social Share URL'
	},

	/*
	{
		type: 'data:member_query_message_open_list',
		title: 'Member Query Email Open',
		choices: []
	},
	{
		type: 'data:member_query_message_open_count',
		title: 'Member Query Email Open Count',
		choices: []
	},
	{
		type: 'data:member_query_message_open_status',
		title: 'Member Query Email Open Status',
		choices: [
			{ label: 'Has Opened', value: true },
			{ label: 'Has Not Opened', value: false }
		]
	},

	{
		type: 'data:member_query_message_click_list',
		title: 'Member Query Email Click',
		choices: []
	},
	{
		type: 'data:member_query_message_click_count',
		title: 'Member Query Email Click Count'
	},
	{
		type: 'data:member_query_message_click_status',
		title: 'Member Query Email Click Status',
		choices: [
			{ label: 'Has Clicked', value: true },
			{ label: 'Has Not Clicked', value: false }
		]
	},

	{
		type: 'data:static_group_message_open_list',
		title: 'Static Group Email Open',
		choices: []
	},
	{
		type: 'data:static_group_message_open_count',
		title: 'Static Group Email Open Count'
	},
	{
		type: 'data:static_group_message_open_status',
		title: 'Static Group Email Open Status',
		choices: [
			{ label: 'Has Opened', value: true },
			{ label: 'Has Not Opened', value: false }
		]
	},

	{
		type: 'data:static_group_message_click_list',
		title: 'Static Group Email Click',
		choices: []
	},
	{
		type: 'data:static_group_message_click_count',
		title: 'Static Group Email Click Count'
	},
	{
		type: 'data:static_group_message_click_status',
		title: 'Static Group Email Click Status',
		choices: [
			{ label: 'Has Clicked', value: true },
			{ label: 'Has Not Clicked', value: false }
		]
	},
	*/

	{
		type: 'data:email_engagement_tally',
		title: 'Aggregate Email List Engagement',
		choices: [
			{ label: 'Opens', value: 'message_open' },
			{ label: 'Clickthroughs', value: 'message_click' },
		]
	},
	{
		type: 'data:email_engagement',
		title: 'Email List Engagement',
		choices: [
			{ label: 'Has opened', value: 'message_open' },
			{ label: 'Has clicked through', value: 'message_click' },
		]
	}
];

var AVAILABLE_CONSTRAINTS_META_MAP = {};

(function() {
	var integerChoicesFor = function(start, end) {
		var choices = [];
		var i;

		for (i = start; i <= end; i++) {
			choice = { label: i, value: i };
			choices.push(choice);
		}

		return choices;
	};

	var i;
	var constraints = AVAILABLE_CONSTRAINTS_META;
	var map         = AVAILABLE_CONSTRAINTS_META_MAP;
	var n           = constraints.length;

	for ( i = 0; i < n; i++ ) {
		constraint = constraints[i];
		map[constraint.type] = constraint;

		if (constraint.type === 'profile:birthDay') {
			constraint.choices = integerChoicesFor(1, 31);
		}
	}
})();


var QueryResult = Backbone.Model.extend({

	defaults: {
		email: '',
		fields: {}
	}

});

var MemberQueryStatus = Backbone.Model.extend({

	defaults: {
		memberQueryID: -1
	},

	initialize: function(attr, opts) {
		Backbone.Model.prototype.initialize.call(this, attr, opts);

		this.intervalID = -1;
		this.delay      = 3; // seconds
		this.pollFunc   = $.proxy(this.poll, this);

		this.startPoll();
	},

	getStatusCode: function() {
		return this.get('statusCode');
	},

	getMemberQueryID: function() {
		return this.get('memberQueryID');
	},

	getEmailSegmentID: function() {
		return this.get('emailSegmentID');
	},

	getLastExport: function() {
		return this.get('lastExport');
	},

	getProgress: function() {
		return this.get('progress');
	},

	refresh: function() {
		var params = {
			member_query_id: this.getMemberQueryID()
		};

		ajaxApi.request('member_query_status', params)
			.then($.proxy(this.didRefresh, this))
			.fail($.proxy(this.didRefreshError, this));
	},

	didRefresh: function(response) {
		if (response.success) {
			this.set(response.data);
			this.trigger('refreshSuccess');

			if (this.get('statusCode') === 'running') {
				this.startPoll();
			} else if (response.data.errors) {
				this.trigger('refreshError', response.data.errors[0]);
			}
		} else {
			this.didRefreshError(response);
		}
	},

	didRefreshError: function(response) {
		this.trigger('refreshError', response.data);
	},

	startPoll: function() {
		clearTimeout(this.intervalID);
		this.intervalID = setTimeout(this.pollFunc, this.delay * 1000);
	},

	poll: function() {
		this.refresh();
	},

	changeEmailSegmentID: function(segmentID) {
		var params = {
			member_query_id: this.getMemberQueryID(),
			email_segment_id: segmentID
		};

		this.trigger('changeSegmentStart');
		ajaxApi.request('change_member_query_segment', params)
			.then($.proxy(this.didChangeSegment, this))
			.fail($.proxy(this.didChangeSegmentError, this));
	},

	didChangeSegment: function(response) {
		if (response.success) {
			this.set({'emailSegmentID': response.data});
			this.trigger('changeSegmentSuccess');
		} else {
			this.didChangeSegmentError(response);
		}
	},

	didChangeSegmentError: function(response) {
		this.trigger('changeSegmentError', response.data);
	}

});

var EntryTypeCollection = Backbone.Collection.extend({

	model: EntryType

});

var EntryFieldCollection = Backbone.Collection.extend({
	model: EntryField
});

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

var QueryResultCollection = Backbone.Collection.extend({

	model: QueryResult,

	initialize: function(models, options) {
		this.activeConstraints = options.activeConstraints;
		this.pollDelay         = 3; // seconds
		this.maxQueryTime      = 5 * 60; // seconds
		this.maxRetries        = Math.floor( this.maxQueryTime / this.pollDelay );
		this.retries           = 0;
		this.fetchStatusProxy  = $.proxy(this.fetchStatus, this);
		this.lastProgress      = 0;

		Backbone.Collection.prototype.initialize(this, models, options);
	},

	search: function() {
		this.start();
	},

	start: function() {
		this.retries = 0;

		var constraints = this.activeConstraints.toJSON();
		var data        = {
			constraints: constraints,
			mode: 'start'
		};

		ajaxApi.request('preview_member_query', data)
			.then($.proxy(this.didStartSuccess, this))
			.fail($.proxy(this.didStartError, this));
	},

	didStartSuccess: function(response) {
		if (response.success) {
			this.memberQueryID = response.data.member_query_id;
			this.trigger('searchStart', this.memberQueryID);
			this.startPolling();
		} else {
			this.didStartError(response);
		}
	},

	didStartError: function(response) {
		this.reset([]);
		this.trigger('searchError', response.data);
	},

	onPoll: function() {
		this.fetchStatus();
	},

	fetchStatus: function() {
		var data = {
			mode: 'status',
			member_query_id: this.memberQueryID
		};

		this.retries++;
		ajaxApi.request('preview_member_query', data)
			.then($.proxy(this.didFetchStatusSuccess, this))
			.fail($.proxy(this.didFetchStatusError, this));
	},

	didFetchStatusSuccess: function(response) {
		if (response.success) {
			if (response.data.complete) {
				if ( ! response.data.errors ) {
					this.totalResults = response.data.total;
					if (this.totalResults < 5 && response.data.users.length < this.totalResults) {
						this.totalResults = response.data.users.length;
					}
					this.reset(response.data.users);
					this.trigger('searchSuccess');
					this.clear();
				} else {
					this.reset([]);
					this.trigger('searchError', response.data.errors[0]);
					this.clear();
				}
			} else {
				var progress = response.data.progress;
				if (this.lastProgress !== progress) {
					this.lastProgress = progress;
					this.retries--;
				}

				this.trigger('searchProgress', progress);
				this.startPolling();
			}
		} else {
			this.didFetchStatusError(response);
		}
	},

	didFetchStatusError: function(response) {
		this.startPolling();
	},

	getTotalResults: function() {
		return this.totalResults;
	},

	startPolling: function() {
		if (this.retries < this.maxRetries) {
			setTimeout(this.fetchStatusProxy, this.pollDelay * 1000);
		} else {
			this.trigger('searchTimeout');
		}
	},

	clear: function() {
		var data = {
			mode: 'clear',
			member_query_id: this.memberQueryID
		};

		this.retries++;
		ajaxApi.request('preview_member_query', data)
			.then($.proxy(this.didClearSuccess, this))
			.fail($.proxy(this.didClearError, this));
	},

	didClearSuccess: function(response) {

	},

	didClearError: function(response) {
		//console.log('didClearError', response);
	}
});

var ToolbarItemView = Backbone.CollectionView.extend({

	template: getTemplate('toolbar_item'),

	events: {
		'click .toolbar_item': 'didToolbarItemClick'
	},

	initialize: function() {
		this.render();
	},

	render: function() {
		var data = this.model.toViewJSON();
		var html = this.template(data);

		this.$el.html(html);
	},

	didToolbarItemClick: function(event) {
		var constraint = this.model.clone();

		this.$el.trigger('addConstraint', constraint);

		return false;
	}

});

var ToolbarView = Backbone.CollectionView.extend({
	modelView: ToolbarItemView,

	events: {
		'addConstraint': 'addConstraint'
	},

	initialize: function(options) {
		this.activeConstraints = options.activeConstraints;
		options.tabIndex = -1;
		Backbone.CollectionView.prototype.initialize.call(this, options);
	},

	addConstraint: function(event, constraint) {
		this.activeConstraints.add(constraint);
	}
});

var ConstraintView = Backbone.View.extend({

	template: getTemplate('constraint'),

	events: {
		'click .copy-constraint': 'didCopyClick',
		'click .remove-constraint': 'didRemoveClick',
		'change': 'didChange',
		'change .conjunction-guide': 'renderConjunctionGuide',
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

		if (data.valueType === 'date' || data.valueType === 'epoch') {
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

	   this.renderConjunctionGuide();
	},

	renderConjunctionGuide: function() {
	   var $conjunctionGuide = $('.conjunction-guide', this.el);
	   var hasGuide = this.model.get('conjunction') === 'or';
	   $conjunctionGuide.css('display', hasGuide ? 'block' : 'none');
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
		this.renderConjunctionGuide();
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

	enumOperators: [
		'contains',
		'not contains',
	],

	listOperators: [
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
		} else if (valueType === 'date' || valueType === 'epoch') {
			return this.dateOperators;
		} else if (valueType === 'enum') {
			return this.enumOperators;
		} else if (valueType === 'list') {
			return this.listOperators;
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

var EntryConstraintView = ConstraintView.extend({

	template: getTemplate('entry_constraint'),
	entrySelectTemplate: getTemplate('entry_select'),
	entryAnswerTemplate: getTemplate('entry_answer'),

	initialize: function(model, opts) {
		ConstraintView.prototype.initialize.call(this, model, opts);
		this.listenTo(this.model.getEntryTypes(), 'reset', this.renderEntryTypes);
		this.listenTo(this.model.getEntryFields(), 'reset', this.renderEntryField);
	},

	renderEntryTypes: function() {
		var $entrySelect = $('.entry-select-type', this.el);
		var choices = this.model.getEntryTypes().toJSON();
		var data = {
			choices: choices,
			currentChoice: this.model.get('entryTypeID')
		};

		var html = this.entrySelectTemplate(data);
		$entrySelect.html(html);
	},

	renderEntryField: function() {
		var $entrySelectChoice = $('.entry-select-field', this.el);
		var choices = this.model.getEntryFields().toJSON();
		var data = {
			choices: choices,
			currentChoice: this.model.get('entryFieldID')
		};

		var html;

		if (choices.length === 0) {
			choices.push({ label: 'No questions found for contest', value: '-1' });
		}

		html = this.entrySelectTemplate(data);
		$entrySelectChoice.html(html);

		this.renderEntryAnswer();
	},

	renderEntryValueSelect: function() {
		var $entrySelectChoice = $('.constraint-value', this.el);
		var choices = this.model.getEntryFieldChoices();

		var data = {
			choices: choices,
			currentChoice: this.model.get('value')
		};

		var html = this.entrySelectTemplate(data);
		$entrySelectChoice.html(html);
	},

	renderEntryAnswer: function() {
		var $entryAnswer   = $('.entry-answer-group', this.el);
		var data           = this.model.toViewJSON();
		data.view          = this;
		data.choices       = this.model.getEntryFieldChoices();
		data.currentChoice = this.model.get('value');
		data.fieldType     = this.model.getEntryFieldType();
		data.fieldOptions  = this.model.getEntryFieldOptions();

		var html = this.entryAnswerTemplate(data);
		$entryAnswer.html(html);

		var fieldType = this.model.getEntryFieldType();

		if (fieldType === 'date') {
			var $constraintField = $('.constraint-value', this.el);
			$constraintField.datepicker({dateFormat: 'mm/dd/yy'});
		}
	},

	updateConstraint: function(constraint, source) {
		var operator     = $('.constraint-operator', this.el).val();
		var conjunction  = $('.constraint-conjunction', this.el).val();
		var value        = $('.constraint-value', this.el).val();
		var entryTypeID  = $('.entry-select-type', this.el).val();
		var entryFieldID = $('.entry-select-field', this.el).val();
		var $source      = $(source);
		value            = this.parseValue(value, constraint.get('valueType'));

		if ($source.hasClass('entry-select-type')) {
			value = '';
			entryFieldID = -1;
		} else if ($source.hasClass('entry-select-field')) {
			var fieldChoices = this.model.getEntryFieldChoices(entryFieldID);

			if (fieldChoices.length > 0) {
				value = fieldChoices[0].value;
			} else {
				value = '';
			}
		}

		var changes     = {
			operator: operator,
			value: value,
			conjunction: conjunction,
			entryTypeID: this.parseValue(entryTypeID, 'string'),
			entryFieldID: this.parseValue(entryFieldID, 'string')
		};

		//console.log('updateConstraint', changes);
		constraint.set(changes);
		this.renderEntryAnswer();
		this.renderConjunctionGuide();
	},

	hasChoices: function() {
		return this.model.hasChoices();
	},

	conjunctions: [
		'or'
	]

});

var LikeConstraintView = ConstraintView.extend({

	template: getTemplate('like_constraint'),

	initialize: function(model, opts) {
		ConstraintView.prototype.initialize.call(this, model, opts);
	},

	render: function() {
		var data        = this.model.toViewJSON();
		data.view       = this;
		data.categories = this.model.getCategories();

		var html = this.template(data);
		this.$el.html(html);
		this.renderConjunctionGuide();
	},

	updateConstraint: function(constraint) {
		var operator    = $('.constraint-operator', this.el).val();
		var conjunction = $('.constraint-conjunction', this.el).val();
		var value       = $('.constraint-value', this.el).val();
		var category    = $('.like-category', this.el).val();
		value           = this.parseValue(value, constraint.get('valueType'));

		var changes     = {
			operator: operator,
			value: value,
			conjunction: conjunction,
			category: category
		};

		constraint.set(changes);
		this.renderConjunctionGuide();
	},

});

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

var EmailEngagementConstraintView = ConstraintView.extend({

	template: getTemplate('email_engagement'),

	initialize: function(model, opts) {
		ConstraintView.prototype.initialize.call(this, model, opts);

		this.listenTo(this.model, 'loadGroupsStart', this.didLoadGroupsStart);
		this.listenTo(this.model, 'loadGroupsSuccess', this.didLoadGroupsSuccess);
		this.listenTo(this.model, 'loadGroupsError', this.didLoadGroupsError);

		this.model.loadGroups();
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

	didLoadGroupsSuccess: function(groups) {
		this.render();
	},

});

var ListConstraintView = ConstraintView.extend({

	template: getTemplate('list_constraint'),

	initialize: function(model, opts) {
		ConstraintView.prototype.initialize.call(this, model, opts);

		this.listenTo(this.model, 'loadListStart', this.didLoadListStart);
		this.listenTo(this.model, 'loadListSuccess', this.didLoadListSuccess);
		this.listenTo(this.model, 'loadListError', this.didLoadListError);

		var choices = this.model.getChoices();
	},

	didLoadListStart: function() {
	},

	didLoadListSuccess: function(choices) {
		this.render();
	},

	didLoadListError: function(message) {
	}

});

var FavoriteConstraintView = ConstraintView.extend({

	template: getTemplate('favorite_constraint'),

	initialize: function(model, opts) {
		ConstraintView.prototype.initialize.call(this, model, opts);
	},

	render: function() {
		var data           = this.model.toViewJSON();
		data.view          = this;
		data.categories    = this.model.getCategories();
		data.favoriteTypes = this.model.getFavoriteTypes();

		var html = this.template(data);
		this.$el.html(html);
	},

	updateConstraint: function(constraint) {
		var operator     = $('.constraint-operator', this.el).val();
		var conjunction  = $('.constraint-conjunction', this.el).val();
		var value        = $('.constraint-value', this.el).val();
		var category     = $('.favorite-category', this.el).val();
		var favoriteType = $('.favorite-type', this.el).val();
		value            = this.parseValue(value, constraint.get('valueType'));

		var changes     = {
			operator: operator,
			value: value,
			conjunction: conjunction,
			category: category,
			favoriteType: favoriteType
		};

		constraint.set(changes);
		this.renderConjunctionGuide();
	},


});

var ActiveConstraintsView = Backbone.CollectionView.extend({

	el: jQuery('#active_constraints'),
	emptyListCaption: getTemplate('empty_constraints'),

	events: {
		'copyConstraint': 'copyConstraint',
		'removeConstraint': 'removeConstraint',
	},

	_getModelViewConstructor: function(model) {
		var type = model.get('type');
		var kind = ConstraintCollection.kindForType(type);

		switch (kind) {
			case 'record':
				return EntryConstraintView;

			case 'likes':
				return LikeConstraintView;

			case 'favorites':
				return FavoriteConstraintView;

			case 'list':
				return ListConstraintView;

			case 'email_engagement_tally':
				return EmailEngagementTallyConstraintView;

			case 'email_engagement':
				return EmailEngagementConstraintView;

			default:
				return ConstraintView;
		}
	},

	initialize: function(options) {
		Backbone.CollectionView.prototype.initialize.call(this, options);
		this.listenTo(this.collection, 'add', this.didAdd);
		this.listenTo(this, 'sortStop', this.didSortStop);
	},

	copyConstraint: function(event, constraint) {
		var newConstraint = constraint.clone();
		var index         = this.collection.indexOf(constraint);

		this.collection.add(newConstraint, {at: index+1});
	},

	removeConstraint: function(event, constraint) {
		var view    = this.viewManager.findByModel(constraint);
		var $el     = view.$el;
		var $parent = $el.parent();

		var self = this;
		$parent.slideUp(function() {
			self.collection.remove(constraint);
		});
	},

	render: function() {
		Backbone.CollectionView.prototype.render.call(this);
	},

	didAdd: function(model) {
		var view = this.viewManager.findByModel(model);
		var $el = view.$el;
		//var $parent = $el.parent();

		this.scrollTo($el);

		$el.css('opacity', 0);
		$el.animate({opacity: 1}, 500);

		var $parent = $el.parent();
		$parent.fadeIn();
	},

	scrollTo: function($target) {
		var root   = $('html, body');
		var params = {
			scrollTop: $target.offset().top - 60 // admin bar offset
		};

		root.animate(params, 500);
	},

	didSortStop: function(event) {
		this.collection.save();
	}

});

var PreviewView = Backbone.View.extend({

	events: {
		'click .preview-member-query-button': 'didPreviewClick'
	},

	initialize: function(options) {
		this.collection = options.collection;
		this.listenTo(this.collection, 'searchError', this.didSearchError);
		this.listenTo(this.collection, 'searchStart', this.didSearchStart);
		this.listenTo(this.collection, 'searchProgress', this.didSearchProgress);
		this.listenTo(this.collection, 'searchSuccess', this.didSearchSuccess);
		this.listenTo(this.collection, 'searchTimeout', this.didSearchTimeout);

		Backbone.View.prototype.initialize.call(this, options);
		//this.search();
		this.previewEnabled = true;

		this.stepper = new Stepper('didStep', this);
	},

	didPreviewClick: function(event) {
		if (this.previewEnabled) {
			this.search();
		}

		return false;
	},

	search: function() {
		this.setStatus('Searching, Please wait ...');
		this.collection.search();
	},

	didSearchStart: function() {
		this.stepper.start(0);
		this.setPreviewEnabled(false);
	},

	didSearchProgress: function(progress) {
		this.stepper.update(progress);
	},

	didStep: function(progress) {
		this.setStatus('Searching, Please wait ... ' + progress + '%');
	},

	didSearchSuccess: function() {
		this.stepper.stop();

		var total    = this.collection.getTotalResults();
		var message = total + ' records found';

		if (total > 0) {
			var range = Math.min(5, total);
			message += ', showing the first ' + range;
		} else {
			message += '.';
		}

		this.setStatus(message);
		this.setPreviewEnabled(true);
	},

	didSearchError: function(message) {
		this.stepper.stop();
		this.setPreviewEnabled(true);
		this.setStatus("Error: " + message);
	},

	didSearchTimeout: function() {
		this.stepper.stop();
		this.setStatus('Error: Query timed out, please try again.');
		this.setPreviewEnabled(true);
	},

	setStatus: function(message) {
		var div = $('.count-status');
		div.text(message);
	},

	setPreviewEnabled: function(enabled) {
		var previewButton = $('.preview-member-query-button', this.el);
		previewButton.toggleClass('disabled', !enabled);

		var previewSpinner = $('.preview-spinner', this.el);
		previewSpinner.css('display', enabled ? 'none' : 'inline');

		this.previewEnabled = enabled;
	},

});

var Stepper = function(callback, scope) {
	this.callback   = callback;
	this.scope      = scope;
	this.intervalID = -1;

	var self = this;
	this.stepFunc = function() {
		self.step();
	};
};

Stepper.prototype = {

	start: function(value) {
		this.value   = value;
		this.current = 0;
		this.startInterval();
	},

	update: function(value) {
		this.value = value;

		if (!this.isRunning()) {
			this.startInterval();
		}
	},

	startInterval: function() {
		clearInterval(this.intervalID);
		this.intervalID = setInterval(this.stepFunc, 100);
	},

	stop: function() {
		clearInterval(this.intervalID);
		this.intervalID = -1;
	},

	step: function() {
		if (this.current < this.value) {
			this.current++;
			this.scope[this.callback](this.current);
		} else {
			this.stop();
		}
	},

	isRunning: function() {
		return this.intervalID !== -1;
	}

};

var QueryResultItemView = Backbone.CollectionView.extend({

	template: getTemplate('query_result_item'),

	render: function() {
		var data = this.model.toJSON();
		data.view = this;
		var html = this.template(data);

		this.$el.html(html);
	},

	domainFor: function(email) {
		var atIndex = email.indexOf('@');
		return email.substring(atIndex);
	}
});

var QueryResultsView = Backbone.CollectionView.extend({
	modelView: QueryResultItemView
});

var ExportView = Backbone.View.extend({

	template: getTemplate('export'),

	events: {
		'click .edit-segment-id-button': 'didEditSegmentClick',
		'click .change-segment-id-button': 'didChangeSegmentClick',
		'click .cancel-segment-id-button': 'didCancelSegmentClick',
	},

	initialize: function(options) {
		Backbone.View.prototype.initialize.call(this, options);
		this.listenTo(this.model, 'change', this.render);
		this.listenTo(this.model, 'changeSegmentStart', this.didChangeSegmentStart);
		this.listenTo(this.model, 'changeSegmentSuccess', this.didChangeSegmentSuccess);
		this.listenTo(this.model, 'changeSegmentError', this.didChangeSegmentError);
		this.listenTo(this.model, 'refreshSuccess', this.updateEditEnabled);
		this.listenTo(this.model, 'refreshError', this.updateEditEnabled);
	},

	render: function() {
		var data = this.getStatusJSON();
		data.view = this;

		var html = this.template(data);

		this.$el.html(html);
		this.$el.css('visibility', 'visible');
	},

	getStatusJSON: function() {
		var meta = {};
		var statusCode = this.model.getStatusCode();

		if (statusCode === 'pending') {
			meta.statusText = 'Pending';
		} else if (statusCode === 'running') {
			meta.statusText = this.model.getProgress() + "% Completed ...";
		} else if (statusCode === 'completed') {
			meta.statusText = 'Completed';
		}


		var lastExport = this.model.getLastExport();

		if (lastExport) {
			meta.lastExport      = this.toHumanTime(this.model.getLastExport());
			meta.emailSegmentID  = this.model.getEmailSegmentID();
			meta.emailSegmentURL = this.toEmmaGroupURL(meta.emailSegmentID);
		} else {
			meta.lastExport = 'Never';
			meta.emailSegmentID = false;
		}

		return meta;
	},

	toEmmaGroupURL: function(groupID) {
		return 'https://app.e2ma.net/app2/audience/list/active/' + groupID + '/';
	},

	monthNames: [
		"January", "February", "March", "April", "May", "June",
		"July", "August", "September", "October", "November", "December"
	],

	toHumanTime: function(timestamp) {
		var time   = new Date(timestamp * 1000);
		var month  = time.getMonth();
		var monthName = this.monthNames[month];
		monthName = monthName.substring(0, 3);

		var day    = time.getDay();
		var year   = time.getFullYear();
		var hour   = time.getHours();
		if (hour < 10) hour = '0' + hour;

		var min    = time.getMinutes();
		if (min < 10) min = '0' + min;

		var sec    = time.getSeconds();
		if (sec < 10) sec = '0' + sec;

		var output = monthName + ' ' + day + ', ' + year + ' @ ' + hour + ':' + min + ':' + sec;

		return output;
	},

	showChangeSegment: function() {
		var self = this;
		$editSegmentID = $('.edit-segment-id', this.$el);
		$editSegmentID.show( 'fast', function() {
			var emailSegmentID  = self.model.getEmailSegmentID() || '';
			var $emailSegmentID = $('#email-segment-id');
			var currentValue = $emailSegmentID.val();

			if (currentValue === '' && emailSegmentID !== '') {
				$emailSegmentID.val(emailSegmentID);
			}

			$emailSegmentID.focus();
		});

		this.setErrorMessage('');
	},

	hideChangeSegment: function() {
		$editSegmentID = $('.edit-segment-id', this.$el);
		$editSegmentID.hide( 'fast' );
	},

	didEditSegmentClick: function(event) {
		$editButton = $('.edit-segment-id-button', this.$el);
		var enabled = this.model.getStatusCode() !== 'running';

		if (enabled) {
			this.showChangeSegment();
			return false;
		}
	},

	didChangeSegmentClick: function(event) {
		var $emailSegmentID = $('#email-segment-id');
		var segmentID = $emailSegmentID.val();
		if (segmentID !== '') {
			this.model.changeEmailSegmentID(segmentID);
		} else {
			this.setErrorMessage('Invalid Segment ID');
			$emailSegmentID.focus();
		}
		return false;
	},

	didCancelSegmentClick: function(event) {
		this.hideChangeSegment();
	},

	setErrorMessage: function(message) {
		var $message = $('.error-message', this.$el);
		$message.text(message);
	},

	setSpinnerEnabled: function(enabled) {
		var $spinner = $('.spinner', this.$el);
		$spinner.css('display', enabled ? 'inline' : 'none');
	},

	didChangeSegmentStart: function() {
		this.setSpinnerEnabled(true);
		this.setErrorMessage('');
	},

	didChangeSegmentSuccess: function() {
		this.hideChangeSegment();
		this.setSpinnerEnabled(false);
	},

	didChangeSegmentError: function(message) {
		this.setErrorMessage(message);
		this.setSpinnerEnabled(false);
	},

	updateEditEnabled: function() {
		$editButton = $('.edit-segment-id-button', this.$el);
		var enabled = this.model.getStatusCode() !== 'running';
		$editButton.toggleClass('disabled', !enabled);
	}

});

var ExportMenuView = Backbone.View.extend({

	events: {
		'click .export-button': 'didClickExport'
	},

	initialize: function(options) {
		Backbone.View.prototype.initialize.call(this, options);
		this.listenTo(this.model, 'change', this.updateExportButton);
	},

	render: function() {
		var $submitButton = this.getSubmitButton();
		$submitButton.val('Save');
		$submitButton.toggleClass('button-primary', false);

		var $exportButton = $('<input name="export" type="button" class="button button-primary button-large export-button" id="export-button" value="Export">');
		$exportButton.insertBefore($submitButton);
		this.updateExportButton();
	},

	getSubmitButton: function() {
		return $('#publish', this.$el);
	},

	getPublishForm: function() {
		return this.getSubmitButton().parents('form:first');
	},

	didClickExport: function(event) {
		var disabled = this.model.getStatusCode() === 'running';
		if (disabled) return;

		var exportField = $('<input>').attr({
			type: 'hidden',
			name: 'export_member_query',
			value: '1'
		});

		var form = this.getPublishForm();
		exportField.appendTo(form);

		var button = this.getSubmitButton();
		button.trigger('click');

		return false;
	},

	updateExportButton: function() {
		var disabled = this.model.getStatusCode() === 'running';

		$exportButton = $('#export-button');
		$exportButton.toggleClass('disabled', disabled);
	}

});

var $ = jQuery;
var QueryBuilderApp = function() {
	$(document).ready($.proxy(this.initialize, this));
};

QueryBuilderApp.version = '0.1.0';
QueryBuilderApp.prototype = {

	initialize: function() {
		//TODO: IMPORTANT, should not be global
		window.ajaxApi           = new WpAjaxApi(member_query_meta);
		var loadedConstraints    = member_query_data.constraints || [];
		var availableConstraints = new ConstraintCollection(AVAILABLE_CONSTRAINTS);
		var activeConstraints    = new ConstraintCollection(loadedConstraints);
		var queryResults         = new QueryResultCollection([], { activeConstraints: activeConstraints });
		var memberQueryStatus    = new MemberQueryStatus(member_query_meta.status_meta);

		var toolbarView = new ToolbarView({
			el: $('#query_builder_toolbar'),
			collection: availableConstraints,
			activeConstraints: activeConstraints
		});

		var activeConstraintsView = new ActiveConstraintsView({
			el: $('#active_constraints'),
			collection: activeConstraints,
			sortable: true,
		});

		var previewView = new PreviewView({
			el: $('.preview-member-query'),
			collection: queryResults
		});

		var queryResultsView = new QueryResultsView({
			el: $('.member-query-results'),
			collection: queryResults
		});

		var exportView = new ExportView({
			el: $('#submitdiv #minor-publishing'),
			model: memberQueryStatus
		});

		var exportMenuView = new ExportMenuView({
			el: $('#submitdiv #major-publishing-actions'),
			model: memberQueryStatus
		});

		$('#query_builder_metabox').toggleClass('loading', false);
		$('#query_builder_metabox .loading-indicator').remove();

		toolbarView.render();
		activeConstraintsView.render();
		previewView.render();
		queryResultsView.render();
		exportView.render();
		exportMenuView.render();

		activeConstraints.save();

		$('.wrap-preloader').remove();
		$('.wrap').css('display', 'block');
	},

};

var app = new QueryBuilderApp();


/*! Greater Media - v0.1.0 - 2014-11-18
 * http://greatermedia.com
 * Copyright (c) 2014; * Licensed GPLv2+ */
/**
 * Public API
 */
var GreaterMediaGigyaAuth = (function (greaterMediaGigyaAuth, undefined) {

	// Private event handlers

	/*! Cookies.js - 0.4.0; Copyright (c) 2014, Scott Hamper; http://www.opensource.org/licenses/MIT */
	(function(e){"use strict";var b=function(a,d,c){return 1===arguments.length?b.get(a):b.set(a,d,c)};b._document=document;b._navigator=navigator;b.defaults={path:"/"};b.get=function(a){b._cachedDocumentCookie!==b._document.cookie&&b._renewCache();return b._cache[a]};b.set=function(a,d,c){c=b._getExtendedOptions(c);c.expires=b._getExpiresDate(d===e?-1:c.expires);b._document.cookie=b._generateCookieString(a,d,c);return b};b.expire=function(a,d){return b.set(a,e,d)};b._getExtendedOptions=function(a){return{path:a&& a.path||b.defaults.path,domain:a&&a.domain||b.defaults.domain,expires:a&&a.expires||b.defaults.expires,secure:a&&a.secure!==e?a.secure:b.defaults.secure}};b._isValidDate=function(a){return"[object Date]"===Object.prototype.toString.call(a)&&!isNaN(a.getTime())};b._getExpiresDate=function(a,d){d=d||new Date;switch(typeof a){case "number":a=new Date(d.getTime()+1E3*a);break;case "string":a=new Date(a)}if(a&&!b._isValidDate(a))throw Error("`expires` parameter cannot be converted to a valid Date instance"); return a};b._generateCookieString=function(a,b,c){a=a.replace(/[^#$&+\^`|]/g,encodeURIComponent);a=a.replace(/\(/g,"%28").replace(/\)/g,"%29");b=(b+"").replace(/[^!#$&-+\--:<-\[\]-~]/g,encodeURIComponent);c=c||{};a=a+"="+b+(c.path?";path="+c.path:"");a+=c.domain?";domain="+c.domain:"";a+=c.expires?";expires="+c.expires.toUTCString():"";return a+=c.secure?";secure":""};b._getCookieObjectFromString=function(a){var d={};a=a?a.split("; "):[];for(var c=0;c<a.length;c++){var f=b._getKeyValuePairFromCookieString(a[c]); d[f.key]===e&&(d[f.key]=f.value)}return d};b._getKeyValuePairFromCookieString=function(a){var b=a.indexOf("="),b=0>b?a.length:b;return{key:decodeURIComponent(a.substr(0,b)),value:decodeURIComponent(a.substr(b+1))}};b._renewCache=function(){b._cache=b._getCookieObjectFromString(b._document.cookie);b._cachedDocumentCookie=b._document.cookie};b._areEnabled=function(){var a="1"===b.set("cookies.js",1).get("cookies.js");b.expire("cookies.js");return a};b.enabled=b._areEnabled();"function"===typeof define&& define.amd?define(function(){return b}):"undefined"!==typeof exports?("undefined"!==typeof module&&module.exports&&(exports=module.exports=b),exports.Cookies=b):window.Cookies=b})();

	var TWO_WEEKS_IN_MILLISECONDS = 1000 * 60 * 60 * 24 * 14;

	// Register event handlers with Gigya
	gigya.accounts.addEventHandlers({

		onLogin: function(data) {
			Cookies.set(
				'gm_gigya_user',
				// @todo I'd like to encrypt this data so it's not obvious we're storing user data here
				JSON.stringify(data || {}),
				{expires: new Date(+new Date + TWO_WEEKS_IN_MILLISECONDS)}
			);
		},

		onLogout: function(data) {
			Cookies.expire('gm_gigya_user');
		}

	});

	// Public API

	/**
	 * Check if the current listener is logged in
	 * @returns {boolean}
	 */
	greaterMediaGigyaAuth.is_gigya_user_logged_in = function() {

		return ( undefined !== Cookies.get('gm_gigya_user') );

	}

	/**
	 * Get the current listener's Gigya user id
	 * @returns {string||null}
	 */
	greaterMediaGigyaAuth.gigya_user_id = function() {

		var cookie = Cookies.get('gm_gigya_user'), cookie_data;
		if(undefined === cookie) {
			return undefined;
		}

		cookie_data = JSON.parse(cookie);
		return cookie_data.UID || null;

	}

	return greaterMediaGigyaAuth;

}(GreaterMediaGigyaAuth || {}));

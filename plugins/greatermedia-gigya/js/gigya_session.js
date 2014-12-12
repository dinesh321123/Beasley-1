(function($) {

	/*
	 * Lite Version of Gigya Profile. This gets loaded on non profile
	 * pages and does not have the ability to modify the session.
	 *
	 */
	var GigyaSessionStore = function() {
		this.cookieValue = {};
	};

	GigyaSessionStore.prototype = {

		isEnabled: function() {
			return Cookies.enabled;
		},

		get: function(key) {
			return this.cookieValue[key];
		},

		load: function() {
			if (!this.isEnabled()) {
				return;
			}

			var cookieText   = Cookies.get(this.getCookieName());
			this.cookieValue = this.deserialize(cookieText);
		},

		getCookieName: function() {
			return 'gigya_profile';
		},

		deserialize: function(cookieText) {
			if (cookieText) {
				var cookieValue;

				try {
					if (atob) {
						cookieText = atob(cookieText);
					}
					cookieValue = JSON.parse(cookieText);
				} catch (err) {
					// ignore
				} finally {
					if (!this.isObject(cookieValue)) {
						cookieValue = {};
					}
				}

				return cookieValue;
			} else {
				return {};
			}
		},

		isObject: function(obj) {
			return (!!obj) && (obj.constructor === Object);
		},

	};

	var GigyaSession = function(store) {
		this.store   = store;
	};

	GigyaSession.prototype = {

		isEnabled: function() {
			return this.store.isEnabled();
		},

		isLoggedIn: function() {
			return !!this.store.get('UID');
		},

		getUserID: function() {
			return this.getUserField('UID');
		},

		getUserField: function(field) {
			if (this.isLoggedIn()) {
				return this.store.get(field);
			} else {
				return null;
			}
		}

	};

	var store   = new GigyaSessionStore();
	var session = new GigyaSession(store);

	store.load();

	window.is_gigya_user_logged_in = function() {
		return session.isEnabled() && session.isLoggedIn();
	};

	window.get_gigya_user_id = function() {
		return session.getUserID();
	};

	window.get_gigya_user_field = function(field) {
		return session.getUserField(field);
	};

}(jQuery));

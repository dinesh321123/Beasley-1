(function($) {

	var WpAjaxApi = function(config) {
		this.config = config;
	};

	WpAjaxApi.prototype = {

		nonceFor: function(action) {
			return this.config[action + '_nonce'];
		},

		urlFor: function(action) {
			var queryParams = {};
			queryParams[action + '_nonce'] = this.nonceFor(action);

			var url = this.config.ajax_url;
			url += url.indexOf('?') === -1 ? '?' : '&';
			url += $.param(queryParams);

			return url;
		},

		request: function(action, data) {
			if (!data) {
				data = {};
			}

			var url         = this.urlFor(action);
			var requestData = {
				'action': action,
				'action_data': JSON.stringify(data)
			};

			return $.post(url, requestData);
		},

	};

	window.WpAjaxApi = WpAjaxApi;

})(jQuery);

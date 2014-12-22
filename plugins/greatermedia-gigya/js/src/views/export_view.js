var ExportView = Backbone.View.extend({

	template: getTemplate('export'),

	initialize: function(options) {
		Backbone.View.prototype.initialize.call(this, options);
	},

	render: function() {
		var data = {};
		var html = this.template(data);

		this.$el.html(html);
		this.$el.css('visibility', 'visible');
	},

});

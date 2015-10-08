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

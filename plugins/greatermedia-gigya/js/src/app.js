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

		var toolbarView = new ToolbarView({
			el: $('#query_builder_toolbar'),
			collection: availableConstraints,
			activeConstraints: activeConstraints
		});

		var activeConstraintsView = new ActiveConstraintsView({
			el: $('#active_constraints'),
			collection: activeConstraints,
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
			el: $('#submitdiv #minor-publishing')
		});

		var exportMenuView = new ExportMenuView({
			el: $('#submitdiv #major-publishing-actions')
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


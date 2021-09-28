(function ($) {
	var $document = $(document);
	$document.ready(function () {
		$('#breaking_news_option').change(function() {
			$('#syndication_detached_action').val('no');
		});
	});
})(jQuery);

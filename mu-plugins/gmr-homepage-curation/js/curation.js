(function ($) {
	$(document).ready(function () {
		var dom_chanegd = false,
			save_button_clicked = false;

		$('#homepage-curation #submit').click(function() {
			save_button_clicked = true;
			return true;
		});

		$("#homepage-curation .post-finder > ul.list").bind("DOMSubtreeModified", function() {
			dom_chanegd = true;
		});

		$(window).on('beforeunload', function () {
			if (dom_chanegd && !save_button_clicked) {
				return 'You have not saved your changed.';
			}
		});
	});
})(jQuery);
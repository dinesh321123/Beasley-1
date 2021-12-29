(function ($) {
	var $document = $(document);
	$document.ready(function () {
		trigger_feild_display(!$("#acf-field_display_segmentation_cpt").is(":checked"));

		$("#acf-field_display_segmentation_cpt").click(function() {
			trigger_feild_display(!$(this).is(":checked"));
		});
		
		function trigger_feild_display(isHide = true) {
			if(isHide) {
				$('.acf-field-segmentation-ordering-cpt').addClass("acf-hidden");
			} else {
				$('.acf-field-segmentation-ordering-cpt').removeClass("acf-hidden");
			}
		}
	});
})(jQuery);

(function ($) {
	var $document = $(document);
	$document.ready(function () {
		$document.on('click', '#contest-rules-toggle', function(e) {
			const contestRules = document.getElementById('contest-rules');
			e.target.style.display = 'none';
			if (contestRules) {
				contestRules.style.display = 'block';
			}
		});
	});
})(jQuery);

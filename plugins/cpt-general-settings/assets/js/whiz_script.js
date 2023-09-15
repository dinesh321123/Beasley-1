(function ($) {
	var $document = $(document);
	$document.ready(function () {

		/**
		 * OutbrainWidget Function
		 *
		 * This function creates and initializes an Outbrain widget. It dynamically loads the Outbrain script.
		 *
		 * @param {string} url - The URL for the widget placement.
		 */
		function createOutbrainWidget(url) {
			// Check if the script element already exists
			if (!document.getElementById('outbrain-widget-script')) {
				// Load the Outbrain script dynamically
				var script = document.createElement('script');
				script.type = 'text/javascript';
				script.async = true;
				script.src = '//widgets.outbrain.com/outbrain.js';
				script.id = 'outbrain-widget-script';
			
				// Add the script to the document's head
				document.head.appendChild(script);
			
				// Ensure that the script is loaded before initializing the widget
				script.onload = function () {
					// Initialize the Outbrain widget
					window._Outbrain = window._Outbrain || [];
					window._Outbrain.push({
					widget: 'AR_1',
					placement: url,
					async: true,
					});
				};
			}
		}
		
		var outbrainUrl = general_whiz_object.page_url; // Replace with the desired URL
		createOutbrainWidget(outbrainUrl);

		$document.on('click', '#contest-rules-toggle', function(e) {
			const contestRules = document.getElementById('contest-rules');
			e.target.style.display = 'none';
			if (contestRules) {
				contestRules.style.display = 'block';
			}
		});
	});
})(jQuery);

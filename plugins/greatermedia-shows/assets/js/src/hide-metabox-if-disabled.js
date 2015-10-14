(function($){
	var $homepageSelect = $( document.getElementById( 'show-homepage' )),
		featuredMB = document.getElementById( 'show_featured' ),
		favoritesMB = document.getElementById( 'show_favorites' );

	var hideMetaboxes = function() {
		featuredMB.style.display = 'none';
		favoritesMB.style.display = 'none';
	};

	var showMetaboxes = function() {
		featuredMB.style.display = 'block';
		favoritesMB.style.display = 'block';
	};

	var checkMetaboxes = function() {
		var $selected = $homepageSelect.find( 'input:checked').first();

		if ( '1' === $selected.val() ) {
			showMetaboxes();
		} else {
			hideMetaboxes();
		}
	};

	// do this on page load
	checkMetaboxes();

	// Also do this when we change the state of the enabled/disabled radio, only once we click the OK button
	$homepageSelect.on( 'click', '.save-radio', checkMetaboxes );
})(jQuery);
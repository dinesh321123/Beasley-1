(function() {
	var $ = jQuery,
		$searchContainer = $( '#header__search--form '),
		$searchForm = $( '#header__search--form ' ).find( 'form' ),
		$searchBtn = $( '#header__search'),
		$searchInput = $( '#header-search' ),
		$overlay = $('.header-search-overlay-mask' );
	
	/**
	 * A function to show the header search when an event is targeted.
	 *
	 * @param e
	 */
	function showSearch(e) {
		e.preventDefault();
		
		if ( $searchContainer.hasClass( 'header__search--open' ) ) {
			return; 
		}
		
		$overlay.addClass( 'is-visible' )
		
		// Now, show the search form, but don't set focus until the transition
		// animation is complete. This is because Webkit browsers scroll to 
		// the element when it gets focus, and they scroll to it where it was
		// before the transition started.		
		if ( '0s' !== $searchContainer.css('transitionDuration') ) {
			$searchContainer.one('transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd', function () {
				$searchInput.focus().select();
			} );
		} else {
			$searchInput.focus().select();			
		}
		$searchContainer.addClass('header__search--open'); 
	}
	
	/**
	 * A function to hide the header search when an event is targeted.
	 *
	 * @param e
	 */
	function closeSearch(e) {
		e.preventDefault();
		
		if ( ! $searchContainer.hasClass( 'header__search--open' ) ) {
			return;
		}
		
		$searchContainer.removeClass( 'header__search--open' );
		$overlay.removeClass('is-visible');
		document.activeElement.blur();
	}
	
	/**
	 * Event listeners to run on click to show and close the search.
	 */
	$searchBtn.click( showSearch ); 
	
	/**
	 * Open search if user clicks on it.
	 */
	$searchInput.add( $searchForm.find( 'button[type=submit]' ) ).click( showSearch );
	
	function checkSearchField () {
		var $search_body = $searchContainer.find( '.header-search-body' );
		
		// Show the body only if there's text in the search field.
		if ( $searchInput.val().length ) {
			$search_body.addClass( 'is-visible' );
		} else {
			$search_body.removeClass( 'is-visible' );
		}
	}
	
	$searchInput.keyup( checkSearchField );
	
	checkSearchField(); 
	
	/**
	 * Close the search box when user presses escape.
	 */
	$(window).keydown(function (e) {
		if (e.keyCode === 27){
			closeSearch(e);
		}		
	});
	
	/**
	 * Handle enter key for Safari. 
	 */
	$searchForm.keydown( function ( e ) {
		if ( 13 === e.keyCode ) {
			$( this ).submit(); 
		}
	} );
	
	/**
	 * Close the search box (if open) if the user clicks on the overlay.
	 */
	$overlay.click(function (e) {
		closeSearch(e);
	});
	
	/**
	 * Close the search box (if open) if the user clicks the close button.
	 */
	$searchContainer.find( '.header__search--cancel' ).click( function ( e ) {
		e.preventDefault();
		closeSearch( e );
	} );
	
	/**
	 * Make "Search All Content" button trigger form submit.
	 */
	$searchContainer.find( '.header-search__search-all-btn' ).click( function () {
		$searchForm.submit(); 	
	} );
	
	/**
	 * PJAX workaround. PJAX is set to only handle links when they're clicked,
	 * so to get the form to work over PJAX we need to create a fake link and 
	 * then click it. Clunky but it is the quick fix for now. 
	 * 
	 * Note that we are calling click() on the DOM object, not the jQuery 
	 * object. This is the only way to get this to work on Safari. 
	 */
	$searchForm.submit( function ( e ) {
		e.preventDefault();		
		
		$( '<a></a>' )
			.attr( 'href', $( this ).attr( 'action' ) + '?s=' + $( this ).find( 'input[name=s]' ).val() )
			.appendTo( $( this ) )
			.get( 0 ).click() // Note we are triggering click on the DOM object, not the jQuery object.
		;
		
		closeSearch( e );
	} );
	
})();
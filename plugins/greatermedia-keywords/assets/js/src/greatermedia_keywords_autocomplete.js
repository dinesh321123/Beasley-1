( function( window, undefined ) {
	'use strict';

	jQuery( function( $ ) {
		$('#s').autocomplete({
			source: GMRKeywords
		});
		$('#header-search').autocomplete({
			source: GMRKeywords
		});
	});

} )( this );
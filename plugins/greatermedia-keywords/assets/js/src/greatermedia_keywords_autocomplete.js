( function( window, undefined ) {
	'use strict';

	jQuery( function( $ ) {

		//$('#s').autocomplete({
		//	source: GMRKeywords
		//});

		$('#header-search').autocomplete({
			source: GMRKeywords,
			appendTo: '.header__search--form',
			/*
			 * Nullify all the automaically added inline css things.
			 * we'll do our own gig, thanks.
			 */
			open: function () {
				$('.header__search--form .ui-menu').css({
					width  : '',
					display: '',
					left   : '',
					top    : ''
				});
			}
		});
	});

} )( this );
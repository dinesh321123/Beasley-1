/*! GreaterMedia Keywords - v0.0.1
 * http://wordpress.org/plugins
 * Copyright (c) 2015; * Licensed GPLv2+ */
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
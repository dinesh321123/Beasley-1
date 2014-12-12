/*! GreaterMedia Shows - v1.0.0
 * http://wordpress.org/plugins
 * Copyright (c) 2014; * Licensed GPLv2+ */
/**
 * Handles automatically selecting a user's show in the taxonomy metabox.
 *
 * @since 1.0.0
 */

/*global $:false, jQuery:false, wp:false */

var SHOW_JS;
var usersShow = SHOW_JS.usersShow;

( function( $, window, document ) {
	'use strict';

	$( document ).ready( function( $, usersShow ){
		set_default_show_terms( $ );
	});

	function set_default_show_terms( $, usersShow ) {
		// If there isn't already a show selected...
		if ( 0 === $( '#_showschecklist input[type=checkbox]:checked' ).length ) {
			$.each( SHOW_JS.usersShow, function( index, value ) {
				$( 'input:checkbox[value=' + value + ']' ).attr( 'checked', true );
			});
		}
	}
})( jQuery );

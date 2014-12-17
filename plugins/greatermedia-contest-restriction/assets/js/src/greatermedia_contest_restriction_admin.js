/**
 * GreaterMedia Contest Restriction
 * http://wordpress.org/plugins
 *
 * Copyright (c) 2014 10up
 * Licensed under the GPLv2+ license.
 */

/*global $:false, jQuery:false, alert:false, is_gigya_user_logged_in:false, console:false, get_gigya_user_field:false */

( function( window, undefined ) {
	'use strict';
	var $ =jQuery;
	$(function () {

		$('#restrict_number').on('change', function() {
			if($(this).is(':checked')){
				$('#max_entries').prop('disabled', false);
			} else {
				$('#max_entries').prop('disabled', true);
			}
		});

		$('#restrict_age').on('change', function() {
			if($(this).is(':checked')){
				$('#min_age').prop('disabled', false);
			} else {
				$('#min_age').prop('disabled', true);
			}
		});

	});

} )( this );
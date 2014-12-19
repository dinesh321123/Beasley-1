/**
 * GreaterMedia Contest Restriction
 * http://wordpress.org/plugins
 *
 * Copyright (c) 2014 10up
 * Licensed under the GPLv2+ license.
 */

( function( window, undefined ) {
	'use strict';
	var $ =jQuery;
	$(function () {
		var min_age = restrict_data.min_age;
		var confirm = false;
		var contest_form = $('.contest_entry_form');
		var contest_form_inputs = $(".contest_entry_form input");

		function disable_with_error( error_message ) {
			contest_form_inputs.prop("disabled", true);
			contest_form.prepend( '<div class="error">' + error_message + '</div>' );
		}

		// TODO: add link to login
		if( contest_form.hasClass('member_only') ) {
			if ( typeof is_gigya_user_logged_in !== 'undefined' && $.isFunction(is_gigya_user_logged_in)) {
				if( !is_gigya_user_logged_in() ) {
					disable_with_error( 'You must be logged in to enter the contest!' );
				} else {
					contest_form.removeClass('member_only');
				}
			} else {
				disable_with_error( 'You must be logged in to enter the contest!' );
			}
		}

		if( contest_form.hasClass('restrict_age') && !contest_form.hasClass('member_only') ) {
			if ( typeof is_gigya_user_logged_in !== 'undefined' && $.isFunction(is_gigya_user_logged_in)) {
				if ( is_gigya_user_logged_in() ) {
					var age = get_gigya_user_field( 'age' );
					if( age < min_age ) {
						disable_with_error( 'You must be at least ' + min_age + ' to enter the contest' );
					}
				} else {
					confirm = window.confirm( 'Please confirm that you are at least ' + min_age );
					if( !confirm ) {
						disable_with_error( '' );
					}
				}
			} else {
				confirm = window.confirm( 'Please confirm that you are at least ' + min_age );
				if( !confirm ) {
					disable_with_error( '' );
				}
			}
		}

		if( contest_form.hasClass('max_entries') ) {
			disable_with_error( 'This contest has reached maximum number of entries!' );
		}

	});

} )( this );
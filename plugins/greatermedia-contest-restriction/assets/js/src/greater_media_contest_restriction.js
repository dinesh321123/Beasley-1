/**
 * Greater Media Contest Restriction
 * http://wordpress.org/plugins
 *
 * Copyright (c) 2014 10up
 * Licensed under the GPLv2+ license.
 */

/*global $:false, jQuery:false, alert:false */

(function (window, undefined) {
	'use strict';
	var $ =jQuery;
	$(function () {
		$('#restrict_number').on('click', function() {
			$('.max_entries').slideToggle();
		});

		$('#restrict_age').on('click', function() {
			$('.min_age').slideToggle();
		});

		$('#start_date').datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1,
			onClose: function( selectedDate ) {
				$( "#end_date" ).datepicker( "option", "minDate", selectedDate );
			}
		});

		$('#end_date').datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1,
			onClose: function( selectedDate ) {
				$( "#start_date" ).datepicker( "option", "maxDate", selectedDate );
			}
		});
	});

})(jQuery);
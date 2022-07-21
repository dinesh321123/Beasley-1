/**
 * Greater Media
 *
 * Various theme specific functions
 *
 * Copyright (c) 2014 10up
 * Licensed under the GPLv2+ license.
 */
(function ($, window, document, undefined) {

	/**
	 * Global variables
	 */
	var body = document.querySelector('body');
	var html = document.querySelector('html');
	var header = document.getElementById('header');
	var footer = document.querySelector('.footer');
	var $table = $('table');
	var $tableTd = $('table td');

	/**
	 * Adds a class to a HTML table to make the table responsive
	 */
	function responsiveTables() {
		$table.addClass('responsive');
		$tableTd.removeAttr('width');
	}

	/**
	 * Function to add pop-up for social links
	 *
	 * @returns {boolean}
	 */
	function socialPopup() {
		var href = $(this).attr('href'),
			x = screen.width / 2 - 700 / 2,
			y = screen.height / 2 - 450 / 2;

		window.open(href, href, 'height=485,width=700,scrollbars=yes,resizable=yes,left=' + x + ',top=' + y);

		return false;
	}

	/**
	 * Toggles a target element for contest rules
	 *
	 * @param {MouseEvent} e
	 * @returns {boolean}
	 */
	function contestRulesToggle(e) {
		var target = $($(this).attr('data-target')).get(0),
			currentText = $(this).html(),
			newText = $(this).attr('data-alt-text');

		target.style.display = target.style.display !== 'none' ? 'none' : 'block';

		$(this).html(newText);
		$(this).attr('data-alt-text', currentText);

		return false;
	}

	/**
	 * Personality Toggle
	 */
	function personality_toggle() {
		var $button = $('.person-toggle'),
			start = $('.personality__meta').first().height(); // get the height of the meta before we start, basically tells us whether we're using the mobile or desktop height

		$button.on('click', function (e) {
			var $this = $(this),
				$parent = $this.parent().parent('.personality'),
				$meta = $this.siblings('.personality__meta'),
				curr = $meta.height(),
				auto = $meta.css('height', 'auto').height(),
				offset = '';

			$parent.toggleClass('open');
			// if( $parent.hasClass('open') ) {
			// 	$meta.height(curr).animate({height: auto * 0.69}, 1000); // the 0.69 adjusts for the difference in height due to the overflow: visible wrapping the text
			// } else {
			// 	$meta.height(curr).animate({height: start}, 1000);
			// }


			if ($this.hasClass('active')) {
				$this.text('More');
			} else {
				$this.text('Less');
			}
			$this.toggleClass('active');
		});
	}

	/**
	 * Init Functions
	 */
	responsiveTables();

	/**
	 * Functions called on Document Ready
	 */
	$(document).ready(function() {
		personality_toggle();
		$('.article__content').fitVids({customSelector: "div[id^='playerwrapper']"});
	});

	/**
	 * Functions specific to document events
	 */
	$(document).on('click', '.popup', socialPopup);

	$(document).on('click', '*[data-toggle="collapse"]', contestRulesToggle);

	/**
	 * Functions that run after the pjax:end event
	 */
	$(document).bind( 'pjax:end', function () {
		personality_toggle();
	});

})(jQuery, window, document);
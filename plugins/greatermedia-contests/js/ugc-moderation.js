/*! Greater Media Contests - v1.0.6
 * http://10up.com/
 * Copyright (c) 2016;
 * Licensed GPLv2+
 */
jQuery(function () {

	function do_scroll() {
		if (jQuery(window.location.hash).offset()) {
			jQuery(document.body).animate({
				'scrollTop': jQuery(window.location.hash).offset().top
			}, 500);
		}
	}

	if (window.location.href.indexOf('page=moderate-ugc')) {
		if (window.location.hash) {
			// If Twitter oembed content, delay scrolling until it's loaded
			if (jQuery('script[src="//platform.twitter.com/widgets.js"]').length) {
				setTimeout(function () {
					if (twttr.events.bind) {
						console.log('go');
						twttr.events.bind('loaded', do_scroll);
					}
				}, 100);
			}
			else {
				do_scroll();
			}
		}
	}

});

jQuery(function () {

	function append_extension(url, extension) {
		var parser = document.createElement('a');
		parser.href = url;

		var new_url = '//' +
			parser.host +
			parser.pathname +
			'.' + extension +
			parser.search +
			parser.hash;

		return new_url;

	}

	// AJAX-ify the "approve" button
	jQuery('a[name=approve]').click(
		function () {

			var approve_link = append_extension(this.href, 'json');
			var ugc_id = jQuery(this).parents('tr').data('ugc-id');

			console.log('approve link: ', approve_link);
			var req = jQuery.ajax(approve_link);
			req.done(function () {
				var row = jQuery('tr[data-ugc-id=' + ugc_id + ']');
				row.addClass('approved');
				row.find('a[name=approve]').replaceWith(GreaterMediaUGC.templates.approved);
				row.find('input[type=checkbox]').css('visibility', 'hidden');
				if (GreaterMediaAdminNotifier && GreaterMediaAdminNotifier.message) {
					// @TODO add listener name, contest name, etc. to this message & run it through translation
					// @TODO include "undo" link
					GreaterMediaAdminNotifier.message('Approved');
				}
			});

			return false;
		}

	);

	// AJAX-ify the "unapprove" button
	jQuery('a[name=unapprove]').click(
		function () {

			var unapprove_link = append_extension(this.href, 'json');
			var ugc_id = jQuery(this).parents('tr').data('ugc-id');

			console.log('unapprove link: ', unapprove_link);
			var req = jQuery.ajax(unapprove_link);
			req.done(function () {
				var row = jQuery('tr[data-ugc-id=' + ugc_id + ']');
				row.addClass('unapproved');
				row.find('a[name=unapprove]').replaceWith(GreaterMediaUGC.templates.unapproved);
				row.find('input[type=checkbox]').css('visibility', 'hidden');
				if (GreaterMediaAdminNotifier && GreaterMediaAdminNotifier.message) {
					// @TODO add listener name, contest name, etc. to this message & run it through translation
					// @TODO include "undo" link
					GreaterMediaAdminNotifier.message('Unapproved');
				}
			});

			return false;
		}

	);

	// AJAX-ify single gallery post deletion
	jQuery('.ugc-moderation-gallery-thumb a.trash').click(
		function () {

			var trash_link = append_extension(this.href, 'json');
			var thumb = jQuery(this).parents('.ugc-moderation-gallery-thumb');
			var self = this;

			var req = jQuery.ajax(trash_link);
			req.done(function () {
				thumb.addClass('removed');
				if (GreaterMediaAdminNotifier && GreaterMediaAdminNotifier.message) {
					// @TODO add listener name, contest name, etc. to this message & run it through translation
					// @TODO include "undo" link
					GreaterMediaAdminNotifier.message('Removed gallery image');
				}
			});

			return false;
		}

	);

});

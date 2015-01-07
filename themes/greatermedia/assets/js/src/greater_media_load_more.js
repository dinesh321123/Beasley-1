(function ($) {
	var __ready, reset_page = true, pagenums = {};

	__ready = function() {
		var $button = $('.posts-pagination--load-more');
		
		if ( ! $button.length ) {
			return; 
		}
				
		var sync = false,
			url = $button.data('url'),
			page = parseInt($button.data('page')); 
			loop_partial = greatermedia_load_more.loop_partial;

		if (reset_page) {
			pagenums[url] = !isNaN(page) && page > 0 ? page : 1;
		}
		
		// Hide the normal next/previous links
		$( '.posts-pagination--previous, .posts-pagination--next' ).hide();
		// Show our nice button. 
		$button.show(); 

		$button.click(function() {
			var $self = $(this);

			if (!sync) {
				sync = true;
				$self.removeClass('is-loaded');

				// let's use ?ajax=1 to distinguish AJAX and non AJAX requests
				// if we don't do it and enabled HTTP caching, then we might encounter
				// unpleasant condition when users see cached version of a page loaded by AJAX
				// instead of normal one.
				$.get(url.replace('{{page}}', ++pagenums[url]), {ajax: 1, loop_partial: loop_partial }).done(function(response) {
					sync = false;
					$self.addClass('is-loaded');

					$($('<div>' + $.trim(response) + '</div>').html()).insertBefore($button.parents('.posts-pagination'));
				}).fail(function() {
					$self.attr('disabled', 'disabled').text($self.data('not-found'));
				});
			}
			
			return false;
		});
	};

	$(document).bind('pjax:end', function(e, xhr) {
		reset_page = xhr !== null;
		__ready();
	});

	$(document).ready(__ready);
})(jQuery);
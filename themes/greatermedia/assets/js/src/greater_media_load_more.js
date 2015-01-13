(function ($) {
	var __ready, reset_page = true, pagenums = {};

	__ready = function() {
		$('.posts-pagination--load-more').each(function () {
					
			var $button = $(this);
			
			var loading = false,
				page_link_template = $button.data('page-link-template'),
				page = parseInt($button.data('page')),
				partial_slug = $button.data('partial-slug'),
				partial_name = $button.data('partial-name'),
				auto_load = $button.data('auto-load');
	
			if (reset_page) {
				pagenums[page_link_template] = !isNaN(page) && page > 0 ? page : 1;
			}
			
			// Hide the normal next/previous links
			$( '.posts-pagination--previous, .posts-pagination--next' ).hide();
			// Show our nice button. 
			$button.show(); 
			
			// If auto_load is set, create a Waypoint that will trigger the button
			// when it is reached. 
			var waypoint_context = null; 
			if ( auto_load ) {
				$button.waypoint({
					handler: function(direction) {
						// Store the Waypoint context so we can refresh it later.
						waypoint_context = this.context; 
						$button.trigger('click'); 
					},
					offset: 'bottom-in-view'
				});
			}
	
			$button.click(function() {
				var $self = $(this);
	
				if (!loading) {
					loading = true;
					$self.removeClass('is-loaded');
	
					// let's use ?ajax=1 to distinguish AJAX and non AJAX requests
					// if we don't do it and enabled HTTP caching, then we might encounter
					// unpleasant condition when users see cached version of a page loaded by AJAX
					// instead of normal one.
					$.get(page_link_template.replace('%d', ++pagenums[page_link_template]), {ajax: 1, partial_slug: partial_slug, partial_name: partial_name }).done(function(response) {
						loading = false;
						$self.addClass('is-loaded');
	
						$($('<div>' + $.trim(response) + '</div>').html()).insertBefore($button.parents('.posts-pagination'));
						
						// Refresh Waypoint context, if any. 
						if ( waypoint_context ) {
							waypoint_context.refresh(); 
						}						
					}).fail(function() {
						$self.attr('disabled', 'disabled').text($self.data('not-found'));
					});
				}
				
				return false;
			});
		}); 
	};

	$(document).bind('pjax:end', function(e, xhr) {
		reset_page = xhr !== null;
		__ready();
	});

	$(document).ready(__ready);
})(jQuery);
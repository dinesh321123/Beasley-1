/* globals tribe_ev:false ga */
(function ($, document) {
	var __ready, reset_page = true, pagenums = {};

	__ready = function() {
		$('.posts-pagination--load-more').each(function () {
			var $button = $(this),
				loading = false,
				page_link_template = $button.data('page-link-template'),
				page = parseInt($button.data('page')),
				partial_slug = $button.data('partial-slug'),
				partial_name = $button.data('partial-name'),
				auto_load = $button.data('auto-load');
	
			if (reset_page) {
				pagenums[page_link_template] = !isNaN(page) && page > 0 ? page : 1;
			}

			// If auto_load is set, create a Waypoint that will trigger the button
			// when it is reached. 
			var waypoint_context = null; 
			if ( auto_load ) {
				try {					
					$button.waypoint({
						handler: function(direction) {
							// Store the Waypoint context so we can refresh it later.
							waypoint_context = this.context; 
						$button.trigger('click'); 
						},
						offset: 'bottom-in-view'
					});
				} catch ( e ) {
					// Waypoints not supported, disable autoload. 
					auto_load = false; 
				}
			}

			var hide_button = function() {
				$button.hide();
				if (waypoint_context) {
					waypoint_context.destroy();
				}
			};
	
			$button.click(function() {
				var $self = $(this);
	
				if (!loading) {
					loading = true;
					$self.removeClass('is-loaded');

					// let's use ?ajax=1 to distinguish AJAX and non AJAX requests
					// if we don't do it and HTTP cache is enabled, then we might encounter
					// unpleasant condition when users see cached version of a page loaded by AJAX
					// instead of normal one.
					$.get(page_link_template.replace('%d', pagenums[page_link_template]), {ajax: 1, partial_slug: partial_slug, partial_name: partial_name }).done(function(response) {
						// We're done loading now.
						loading = false;
						$self.addClass('is-loaded');
						
						if ( response.post_count ) {
							// Add content to page. 
							$($('<div>' + $.trim(response.html) + '</div>').html()).insertBefore($button.parents('.posts-pagination'));							

							// Call Analytics
							if ( typeof window.ga === 'function' ) {
								var page = page_link_template.replace( '%d', pagenums[page_link_template] ).replace( location.href, '/' );
								window.ga( 'send', 'pageview', page );
							}

							// Increment page number
							pagenums[page_link_template]++;
							
							// Trigger event.
							$( document ).trigger( 'gmr_lazy_load_end' ); 
						}
												
						if ( ! response.post_count || pagenums[page_link_template] > response.max_num_pages ) {
							hide_button();
						} else if ( waypoint_context ) {
							// Refresh Waypoint context, if any.
							waypoint_context.refresh(); 
						}
					}).fail(function() {
						hide_button();
					});
				}
				
				return false;
			});
		}); 
	};

	if ( window.tribe_ev && window.tribe_ev.events ) {
		$( tribe_ev.events ).bind( 'tribe_ev_ajaxSuccess', __ready );
	}

	$(document).bind('pjax:end', function(e, xhr) {
		reset_page = xhr !== null;
		__ready();
	});

	$(document).ready(__ready);
})(jQuery, document);

/*! Breaking News - v0.1.0
 * http://wordpress.org/plugins
 * Copyright (c) 2015; * Licensed GPLv2+ */
(function( $ ){
	$( document ).ready(function($){
		synchronize_breaking_news_checboxes( $ );
	});

	function synchronize_breaking_news_checboxes( $ ) {
		var breakingNewsCheckbox = $( '#breaking-news-meta-fields' ).find( 'input#breaking_news_option' );
		var siteNotificationCheckbox = $( '#breaking-news-meta-fields' ).find( 'input#site_wide_notification_option' );

		breakingNewsCheckbox.bind( 'change', function() {
			if ( false === $( this ).is( ':checked' ) ) {
				siteNotificationCheckbox.removeAttr( 'checked' );
			}
		});

		siteNotificationCheckbox.bind( 'change', function() {
			if ( true === $( this ).is( ':checked' ) ) {
				breakingNewsCheckbox.attr('checked', 'checked');
			}
		});
	}
})( jQuery );

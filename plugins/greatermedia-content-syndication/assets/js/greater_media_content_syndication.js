/*! Greater Media Content Syndication - v0.0.1
 * http://wordpress.org/plugins
 * Copyright (c) 2015; * Licensed GPLv2+ */
/*global $:false, jQuery:false, wp:false, console:false, syndication_ajax:false, alert:false */
( function( window, undefined ) {
	'use strict';

	jQuery( function( $ ) {
		$(".subscription_terms").select2({
			placeholder: "Select term"
		});

		// perform syndication
		$("#syndicate_now").on( 'click', function(event) {
			event.preventDefault();
			var post_id = $(this).data( 'postid' );
			jQuery.ajax({
				type : "post",
				url : syndication_ajax.ajaxurl,
				data : { action: "syndicate-now" ,
					syndication_id : post_id ,
					syndication_nonce: syndication_ajax.syndication_nonce
				},
				beforeSend: function() {
					$('#syndication_status').html( 'Syndication Started' );
				},
				success: function(response) {
					$('#syndication_status').html( 'Syndication Finished' );
				}
			});
		});

		$( '#filter_metaboxes input[type=radio]' ).on( "click", function() {
			$('#filter_metaboxes input[type=radio]').each(function () {
				var el = '#' + $(this).data('enabled');
				if ( $(this).prop('checked') ) {
					$(el).select2( 'enable', true );
					$('#enabled_filter_taxonomy').val( $(this).data('enabled') );
				} else {
					$(el).select2( 'enable', false );
				}
			});
		});

		var syndicated_posts = $('ul.syndicated_posts li');
		syndicated_posts.hide().slice(0, 2).show();

		$("#show_all_syndicated_posts").click(function( event ){
			event.preventDefault();
			syndicated_posts.show('600');
		});
	});

} )( this );
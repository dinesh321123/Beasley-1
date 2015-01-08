/*! GreaterMedia Keywords - v0.0.1
 * http://wordpress.org/plugins
 * Copyright (c) 2015; * Licensed GPLv2+ */
( function( window, undefined ) {
	'use strict';

	jQuery( function( $ ) {
		$('#linked_content').select2();

		$('.submitdelete').on( 'click', function( event ) {
			event.preventDefault();
			var el = $(this);
			var post_id = el.data( 'postid');
			jQuery.ajax({
				type : "post",
				url : ajax_data.ajaxurl,
				data : {
					action: "delete_keyword" ,
					post_id : post_id ,
					delete_key_nonce: ajax_data.delete_key_nonce
				},
				success: function( success ) {
					if( success ) {
						el.closest('tr').addClass('delete').hide( 'drop' );
					} else {
						$('.form-table').first().prepend(
							'<div id="message" class="error"><p>Something gone wrong! Keyword is not deleted!</p></div>'
						);
					}
				}
			});
		});
	});

} )( this );
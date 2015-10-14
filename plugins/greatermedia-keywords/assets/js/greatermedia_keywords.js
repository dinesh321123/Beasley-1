/*! GreaterMedia Keywords - v0.0.1
 * http://wordpress.org/plugins
 * Copyright (c) 2015; * Licensed GPLv2+ */
// Silence jslint warning about _ being undefined.
/*global _, ajaxurl */

( function( window, undefined ) {
	'use strict';

	jQuery( function( $ ) {
		
		$( '.post-search' ).each( function () {
			
			var $post_search = $( this );
			var $search_item_container = $( '.post-search__list' );
			var search_item_template = _.template( $( '.post-search__list-item-template' ).html() );
	
			function do_search( term ) {
				$post_search.addClass( 'is-loading' );
				
				$.getJSON( ajaxurl, {
					action: 'greater_media/keywords/get_posts',
					s: term
				} )
					.done( function ( res ) {
						$search_item_container.empty();
						
						_.each( res.data, function ( item ) {
							$search_item_container.append( $( search_item_template( item ) ) );
						} );
						
						_.each( $('.post-search__list-item'), function ( item, index ) {
							if( index % 2 === 0 ) {
								$(item).addClass('alternate');
							}
						} );

						$post_search.removeClass( 'is-loading' );

						$('.post-search__list-item-input').on('click', function(event) {
							$('input:radio:not(:checked)').parent().removeClass("selected");
							$('input:radio:checked').parent().addClass("selected");
						});
					} )
				;
			}
	
			// Handle search field changes. 
			$( '#linked_content_search' ).keyup( _.debounce( function () {
				do_search( $( this ).val() );
			}, 500 ) );
			
			// Start off with a blank search which will just return recent posts.
			do_search( '' ); 
		} );
		

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
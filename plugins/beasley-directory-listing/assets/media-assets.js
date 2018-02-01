'use strict';

( function( $ ) {
	var $document = $( document );

	$document.on( 'click', '.select-image', function() {
		var $button = $( this ),
			$parent = $button.parent(),
			uploader = wp.media( {
				title: $button.attr( 'title' ),
				multiple: false,
				button: {
					text: 'Select Image'
				}
			} );

		uploader.on( 'select', function() {
			var attachment = uploader.state().get( 'selection' ).first().toJSON();

			$parent.find( 'input:first' ).val( attachment.id );
			$parent.find( 'div' ).css( 'background-image', 'url(\'' + attachment.url + '\')' );
		} );

		uploader.open();
	} );

	$document.on( 'click', '.clear-image', function() {
		var $parent = $( this ).parent();

		$parent.find( 'input' ).val( '' );
		$parent.find( 'div' ).css( 'background-image', 'url()' );
	} );
} )( jQuery );

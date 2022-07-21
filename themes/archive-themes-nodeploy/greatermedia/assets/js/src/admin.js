/**
 * Checks to see if the input field for the audio source has a value.
 * If so, then the image and the 'Remove upload' anchor are displayed.
 *
 * Otherwise, the standard anchor is rendered.
 *
 * @param    object    $    A reference to the jQuery object
 * @since    1.0.0
 */
function renderFileLocation( $ ) {

	/* If a thumbnail URL has been associated with this image
	 * Then we need to display the image and the reset link.
	 */
	if ( '' !== $.trim ( $( '#gmr_site_logo' ).val() ) ) {

		$( '#gmr_site_logo--location' ).removeClass( 'hidden' );

		$( '#gmr_site_logo--upload' )
			.hide();

		$( '#gmr_site_logo--remove' )
			.removeClass( 'hidden' );

	}

}

(function( $ ) {
	'use strict';

	$(function() {

		renderFileLocation( $ );

		$( '#gmr_site_logo--upload-btn' ).on( 'click', function( evt ) {

			evt.preventDefault();

			renderLogoUpload( $ );

		});

		$( '#gmr_site_logo-remove' ).on( 'click', function( evt ) {

			evt.preventDefault();

			resetLogoUpload( $ );

		});

	});

})( jQuery );

/**
 * Callback function for the 'click' event of the 'Logo Upload'
 * anchor in its meta box.
 *
 * Displays the media uploader for selecting an image.
 *
 * @param    object    $    A reference to the jQuery object
 * @since    0.1.0
 */
function renderLogoUpload( $ ) {
	'use strict';

	var file_frame, image_data, json;

	if ( undefined !== file_frame ) {

		file_frame.open();
		return;

	}

	file_frame = wp.media.frames.file_frame = wp.media({
		frame:    'post',
		state:    'insert',
		multiple: false
	});

	file_frame.on( 'insert', function() {

		json = file_frame.state().get( 'selection' ).first().toJSON();

		if ( 0 > $.trim( json.url.length ) ) {
			return;
		}

		$( '#gmr_site_logo--location' )
			.show()
			.removeClass( 'hidden' );

		$( '#gmr_site_logo--preview')
			.children( 'img' )
				.attr( 'src', json.url )
				.attr( 'alt', json.caption )
				.attr( 'title', json.title )
			.parent()
			.show()
			.removeClass( 'hidden' );

		$( '#gmr_site_logo--upload' )
			.hide();

		// Display the anchor for the removing the featured image
		$( '#gmr_site_logo--remove' )
			.show()
			.removeClass( 'hidden' );


		$( '#gmr_site_logo' ).val( json.url );
		$( '#gmr_logo_location' ).val( json.url );

	});

	file_frame.open();

}

/**
 * Callback function for the 'click' event of the 'Remove Logo'
 * anchor in its meta box.
 *
 * Resets the meta box by hiding the image and by hiding the 'Logo Upload'
 * container.
 *
 * @param    object    $    A reference to the jQuery object
 * @since    0.2.0
 */
function resetLogoUpload( $ ) {
	'use strict';

	// We add the 'hidden' class back to this anchor's parent
	$( '#gmr_site_logo--location' )
		.hide()
		.addClass( 'hidden' );

	$( '#gmr_site_logo--preview')
		.hide()
		.addClass( 'hidden' );

	$( '#gmr_site_logo--upload' )
		.show()
		.removeClass( 'hidden' );

	$( '#gmr_site_logo--remove' )
		.hide()
		.addClass( 'hidden' );

	// Finally, we reset the meta data input fields
	$( '#gmr_site_logo' )
		.val( '' );

}

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
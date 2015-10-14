/**
 * Greater Media Podcasts Admin js
 * http://wordpress.org/plugins
 *
 * Copyright (c) 2014 10up
 * Licensed under the GPLv2+ license.
 */

/**
 * Renders the Media Uploader when the 'Podcast' Upload button is clicked
 *
 * @param $
 */
function renderMediaUploader( $ ) {
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

		$( '#gmp-audio-location' )
			.show()
			.removeClass( 'hidden' );

		$( '#gmp-audio-upload-button' )
			.hide();

		// Display the anchor for the removing the featured image
		$( '#gmp-audio-remove-button' )
			.show()
			.removeClass( 'hidden' );

		$( '#gmp_audio_file' ).val( json.url );
		$( '#gmp_audio_file_location' ).val( json.url );

	});

	file_frame.open();

}

/**
 * Resets the 'Podcast' upload actions when the reset button is clicked.
 * This will hide the location field; show the upload button; hide the remove button;
 * and reset the value of `gmp_audio_file` field that saves to post meta
 *
 * @param $
 */
function resetUploadForm( $ ) {
	'use strict';

	// We add the 'hidden' class back to this anchor's parent
	$( '#gmp-audio-location' )
		.hide()
		.addClass( 'hidden' );

	$( '#gmp-audio-upload-button' )
		.show()
		.removeClass( 'hidden' );

	$( '#gmp-audio-remove-button' )
		.hide()
		.addClass( 'hidden' );

	// Finally, we reset the meta data input fields
	$( '#gmp_audio_file' )
		.val( '' );

}

/**
 * If there is a value to `gmp_audio_file`, the input field showing the
 * location will be shown; the upload button will be hidden, and the remove
 * button will be shown.
 *
 * @param $
 */
function renderFileLocation( $ ) {

	if ( '' !== $.trim ( $( '#gmp_audio_file' ).val() ) ) {

		$( '#gmp-audio-location' ).removeClass( 'hidden' );

		$( '#gmp-audio-upload-button' )
			.hide();

		$( '#gmp-audio-remove-button' )
			.removeClass( 'hidden' );

	}

}

/**
 * This calls all of the functions
 */
(function( $ ) {
	'use strict';

	$(function() {

		renderFileLocation( $ );

		$( '#gmp_audio_file_button' ).on( 'click', function( evt ) {

			evt.preventDefault();

			renderMediaUploader( $ );

		});

		$( '#gmp_audio_file_remove' ).on( 'click', function( evt ) {

			evt.preventDefault();

			resetUploadForm( $ );

		});

	});

})( jQuery );
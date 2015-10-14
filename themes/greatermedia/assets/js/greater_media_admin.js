(function($){
	var $body = $('body');

	// Media uploader
	$body.on( 'click', '.select-image', function(e) {
		var $this = $(this),
			$parent = $this.parents('.image-select-parent').first(),
			$image = $parent.find('img'),
			$field = $parent.find('.image-id-input'),
			frame;

		e.preventDefault();

		// Create the media frame.
		frame = wp.media.frames.chooseImage = wp.media({
			// Set the title of the modal.
			title: 'Choose an Image',

			// Tell the modal to show only images.
			library: {
				type: 'image'
			},

			// Customize the submit button.
			button: {
				// Set the text of the button.
				text: 'Select Image'
			}
		});

		// When an image is selected, run a callback.
		frame.on( 'select', function() {
			// Grab the selected attachment.
			var attachment = frame.state().get('selection').first(),
				sizes = attachment.get('sizes'),
				imageUrl = attachment.get('url');

			// Use thumbnail size if abailable for preview
			if ( "undefined" !== typeof sizes.thumbnail ) {
				imageUrl = sizes.thumbnail.url;
			}

			// set the hidden input's value
			$field.attr('value', attachment.id);

			// Show the image in the placeholder
			$image.attr('src', imageUrl);
		});

		frame.open();
	});

	$body.on( 'click', '.remove-image', function(e) {
		var $this = $(this),
			$parent = $this.parents('.image-select-parent').first(),
			$image = $parent.find('img'),
			$field = $parent.find('.image-id-input');

		e.preventDefault();

		$image.attr('src', '');
		$field.attr('value', '');
	});
})(jQuery);

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

/* globals wp:false */
(function ($, wp) {
	$(document).ready(function () {
		var imageFrame,
			selectedImage,
			selectedInput,
			nextButton;

		$('.select-fallback-image').click(function() {
			var $this = $(this);

			selectedImage = $this.data('img');
			selectedInput = $this.data('input');
			nextButton = $this.next();

			// if the frame already exists, open it
			if (imageFrame) {
				imageFrame.open();
				return false;
			}

			// set our settings
			imageFrame = wp.media({
				title: 'Choose Image',
				multiple: false,
				library: {
					type: 'image'
				},
				button: {
					text: 'Use This Image'
				}
			});

			// set up our select handler
			imageFrame.on( 'select', function() {
				var selection = imageFrame.state().get('selection');

				if ( ! selection ) {
					return;
				}

				// loop through the selected files
				selection.each( function( attachment ) {
					//console.log(attachment);
					var src = attachment.attributes.sizes.full.url;
					var id = attachment.id;

					$(selectedImage).attr('src', src);
					$(selectedInput).val(id);
					nextButton.show();
				} );
			});

			// open the frame
			imageFrame.open();

			return false;
		});

		// the remove image link, removes the image id from the hidden field and replaces the image preview
		$('.remove-fallback-image').click(function() {
			var $this = $(this);

			$($this.data('input')).val('');
			$($this.data('img')).attr('src', '');
			$this.hide();

			return false;
		});

	});
})(jQuery, wp);
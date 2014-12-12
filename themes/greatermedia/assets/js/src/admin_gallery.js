(function($){
	var $editButton = $('#manage-gallery-button'),
		$editButtonText = $('#manage-gallery-text'),
		$clearButton = $('#clear-gallery-button'),
		$input = $('#gmr-gallery-ids'),
		gallery = wp.media.gallery,
		$galleryImages = $('#gmr-gallery-images'),
		galleryItemTemplate = $('#gmr-gallery-item-template').text();

	$editButton.on( 'click', function(e) {
		e.preventDefault();

		var content = $input.val(),
			newGallery = content.length <= 0,
			frame;

		if ( content.length < 1 ) {
			frame = wp.media({
				frame: "post",
				state: "gallery",
				library : { type : 'image'},
				multiple: true
			});
			frame.open();
			//frame = gallery.edit( '[gallery]');
		} else {
			frame = gallery.edit( '[gallery ids="' + content + '"]' );
		}

		// Listen for the gallery to be updated - And save the 'shortcode' to the hidden input
		frame.state('gallery-edit').on( 'update', function( selection ) {
			updateImageIds( selection );

			updateGalleryPreview( selection );

			// Update the button text to 'Edit Gallery' if it is a new gallery and we have some IDs
			if ( newGallery ) {
				$editButtonText.text('Edit Gallery');
			}
		});
	});

	$clearButton.on('click', function(e) {
		e.preventDefault();

		// Clear the gallery IDs from the hidden input
		$input.val('');

		// Clear the preview
		$galleryImages.html('');

		// Change the gallery button to say "Create Gallery", since it now appears that we don't have a gallery
		$editButtonText.text('Create Gallery');
	});

	/**
	 * Update the image ids in the hidden input when the gallery is updated.
	 *
	 * @param selection
	 */
	var updateImageIds = function( selection ) {
		var ids = '';

		for ( var i = 0; i < selection.models.length; i++ ) {
			ids += selection.models[i].id + ',';
		}

		ids = ids.slice( 0, -1 );

		$input.val(ids);
	};

	/**
	 * Regenerate the gallery preview, with the new images.
	 *
	 * {{selected_class}} {{attachment_id}} {{thumbnail_url}} {{custom_caption}}
	 *
	 * @param selection
	 */
	var updateGalleryPreview = function( selection ) {
		var html = '';

		for ( var i = 0; i < selection.models.length; i++ ) {
			var attachmentId = selection.models[i].id,
				thumbnailUrl = selection.models[i].get('sizes').thumbnail.url;

			html += galleryItemTemplate
				.replace( /\{\{attachment_id\}\}/g, attachmentId )
				.replace( /\{\{thumbnail_url\}\}/g, thumbnailUrl );
		}

		$galleryImages.html(html);
	};
})(jQuery);
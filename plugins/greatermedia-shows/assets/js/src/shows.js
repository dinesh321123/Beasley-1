/**
 * GreaterMedia Shows
 * http://wordpress.org/plugins
 *
 * Copyright (c) 2014 10up
 * Licensed under the GPLv2+ license.
 */

/**
 * Callback function for the 'click' event of the 'Set Footer Image'
 * anchor in its meta box.
 *
 * Displays the media uploader for selecting an image.
 *
 * @since 0.1.0
 */

/*global $:false, jQuery:false, wp:false */

(function ($) {

	"use strict";

	$(function() {

		// the upload image button, saves the id and outputs a preview of the image
		var imageFrame;
		$('.meta_box_upload_image_button').click(function(event) {
			var $div = $('#show_logo .inside');
			
			// if the frame already exists, open it
			if ( imageFrame ) {
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
					
					$div.find('.meta_box_preview_image').attr('src', src);
					$div.find('.meta_box_upload_image').val(id);
				} );
			});
			
			// open the frame
			imageFrame.open();

			return false;
		});
		
		// the remove image link, removes the image id from the hidden field and replaces the image preview
		$('.meta_box_clear_image_button').click(function() {
			var $parent = $(this).parent();

			$parent.siblings('.meta_box_upload_image').val('');
			$parent.siblings('.meta_box_preview_image').attr('src', '');
			$(this).hide();
			return false;
		});
		
		// the file image button, saves the id and outputs the file name
		var fileFrame;
		$('.meta_box_upload_file_button').click(function(event) {
			event.preventDefault();
			
			var $self = $(event.target);
			var $div = $self.closest('div.meta_box_file_stuff');
			
			// if the frame already exists, open it
			if ( fileFrame ) {
				fileFrame.open();
				return;
			}
			
			// set our settings
			fileFrame = wp.media({
				title: 'Choose File',
				multiple: false,
				library: {
					type: 'file'
				},
				button: {
					text: 'Use This File'
				}
			});
			
			// set up our select handler
			fileFrame.on( 'select', function() {
				var selection = fileFrame.state().get('selection');
				
				if ( ! selection ) {
					return;
				}
				
				// loop through the selected files
				selection.each( function( attachment ) {
					//console.log(attachment);
					var src = attachment.attributes.url;
					var id = attachment.id;
					
					$div.find('.meta_box_filename').text(src);
					$div.find('.meta_box_upload_file').val(src);
					$div.find('.meta_box_file').addClass('checked');
				} );
			});
			
			// open the frame
			fileFrame.open();
		});
		
		// the remove image link, removes the image id from the hidden field and replaces the image preview
		$('.meta_box_clear_file_button').click(function() {
			$(this).parent().siblings('.meta_box_upload_file').val('');
			$(this).parent().siblings('.meta_box_filename').text('');
			$(this).parent().siblings('.meta_box_file').removeClass('checked');
			return false;
		});
		
		// function to create an array of input values
		function ids(inputs) {
			var a = [];
			for (var i = 0; i < inputs.length; i++) {
				a.push(inputs[i].val);
			}
			//$("span").text(a.join(" "));
		}
	});
} )(jQuery);
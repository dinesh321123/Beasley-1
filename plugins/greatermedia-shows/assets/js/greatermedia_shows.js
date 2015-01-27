/*! GreaterMedia Shows - v1.0.0
 * http://wordpress.org/plugins
 * Copyright (c) 2015; * Licensed GPLv2+ */
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
(function ($) {
	$(document).ready(function () {
		var hovered_show, original_color,
			popup_tmpl = $('#schedule-remove-popup').html();
			
		$('#schedule-table td > div').hover(function() {
			var $this = $(this);

			hovered_show = '.' + $this.attr('class');

			original_color = $this.css('background-color');
			$(hovered_show).css('background-color', $this.attr('data-hover-color'));
		}, function() {
			$(hovered_show).css('background-color', original_color);
		});

		$('.remove-show').click(function () {
			var link = $(this).attr('href');
			
			$('body').append(popup_tmpl.replace(/{url}/g, function() {
				return link;
			}));
			
			return false;
		});
		
		$('body').on('click', '.popup-wrapper .button-cancel', function() {
			$(this).parents('.popup-wrapper').remove();
			return false;
		});
	});
})(jQuery);

(function ($) {
	$(document).ready(function () {
		$('.mis-pub-radio').each(function() {
			var $this = $(this),
				$switchSelect = $this.find('.radio-select'),
				$editLink = $this.find('.edit-radio'),
				origin_value = $switchSelect.find('input:radio:checked').val();

			$editLink.click(function() {
				if ($switchSelect.is(':hidden')) {
					$switchSelect.slideDown('fast').find('input[type="radio"]').first().focus();
					$(this).hide();
				}
				return false;
			});

			$switchSelect.find('.cancel-radio').click(function() {
				$switchSelect.slideUp('fast', function() {
					$editLink.show().focus();

					$switchSelect.find('input:radio').each(function() {
						$(this).prop('checked', $(this).val() === origin_value);
					});
				});

				return false;
			});

			$switchSelect.find('.save-radio').click(function(e) {
				// Don't return false, so we can still listen for this to happen elsewhere
				e.preventDefault();

				var selected = $switchSelect.find('input:radio:checked');

				$switchSelect.slideUp('fast', function() {
					$editLink.show();

					origin_value = selected.val();
					$this.find('.radio-value').text(selected.parent().text());
				});
			});
		});
	});
})(jQuery);
(function($){
	var $homepageSelect = $( document.getElementById( 'show-homepage' )),
		featuredMB = document.getElementById( 'show_featured' ),
		favoritesMB = document.getElementById( 'show_favorites' );

	var hideMetaboxes = function() {
		featuredMB.style.display = 'none';
		favoritesMB.style.display = 'none';
	};

	var showMetaboxes = function() {
		featuredMB.style.display = 'block';
		favoritesMB.style.display = 'block';
	};

	var checkMetaboxes = function() {
		var $selected = $homepageSelect.find( 'input:checked').first();

		if ( '1' === $selected.val() ) {
			showMetaboxes();
		} else {
			hideMetaboxes();
		}
	};

	// do this on page load
	checkMetaboxes();

	// Also do this when we change the state of the enabled/disabled radio, only once we click the OK button
	$homepageSelect.on( 'click', '.save-radio', checkMetaboxes );
})(jQuery);
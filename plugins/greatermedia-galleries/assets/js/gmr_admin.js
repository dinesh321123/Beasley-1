(function ($) {
	$(document).ready(function () {
		var tmpl = $('#gallery-item-tmpl').html(),
			$add_button = $('.add-gallery-item'),
			uploader;
		
		if ($.fn.sortable) {
			$('.gallery-preview')
				.sortable({
					items: '> .gallery-image',
					containment: 'parent',
					cursor: 'move',
					distance: 20,
					placeholder: 'gallery-item gallery-item-placeholder',
					stop: function() {
						var editor = typeof tinymce !== 'undefined' && tinymce.get('content');
						editor.isNotDirty = false;
					}
				})
				.disableSelection();
		}

		uploader = wp.media({
			title: 'Add image to the gallery',
			multiple: true,
			library: {
				type: 'image'
			},
			button: {
				text: 'Select Image'
			}
		});

		uploader.on( 'select', function() {
			var selection = uploader.state().get('selection');

			if (selection) {
				selection.each(function(attachment) {
					var attributes = attachment.attributes,
						sizes = attributes.sizes,
						size = sizes.medium || sizes.full || sizes.thumbnail,
						new_item;

					new_item = tmpl
						.replace('%id%', attributes.id)
						.replace('%image%', size.url);

					$(new_item).insertBefore($add_button.parent());
				});
			}
		});

		$add_button.click(function() {
			var editor = typeof tinymce !== 'undefined' && tinymce.get('content');

			editor.isNotDirty = false;
			uploader.open();
			
			return false;
		});

		$(document).on('click', '.remove-gallery-item', function() {
			var editor = typeof tinymce !== 'undefined' && tinymce.get('content');
			
			editor.isNotDirty = false;
			$(this).parent().remove();
		});
	});
})(jQuery);
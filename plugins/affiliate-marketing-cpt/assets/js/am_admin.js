(function ($) {
	var $document = $(document);
	$document.ready(function () {
		$(".am_item_imagetype").click(function() {
			$('#' + $(this).val() + '_' + $(this).attr('data-postid')).hide();
			$('#' + $(this).attr('data-type-hide') + '_' + $(this).attr('data-postid')).show();
		});
		if ($('.set_custom_images').length > 0) {
			if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
					$document.on("click",".set_custom_images",function(e) {
					e.preventDefault();
					var button = $(this);
					var id = button.prev();
					var cur_row = $(this).parents('.content-row');
					wp.media.editor.send.attachment = function(props, attachment) {
						console.log(attachment);
						id.val(attachment.id);
						$(cur_row).find('.upload-preview').attr('src',attachment.url);
					};
					wp.media.editor.open(button);
					return false;
				});
			}
		}
		tinymce.init({ selector: '#tiny-editor' , branding: false });
	});
})(jQuery);

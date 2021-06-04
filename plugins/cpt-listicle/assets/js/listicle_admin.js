(function ($) {
	var $document = $(document);
	$document.ready(function () {
		/*
		*	function to delete items
		*/
		$document.on('click', '.content-delete', function(e) {
			e.preventDefault();
				if (
					$('.content-row').length > 1 &&
					confirm('Are you sure you want to delete this content?')
				) {
					$(this).parents('.content-row').remove();
				}
		});
		
		/*
		*	script to add new Items
		*/
		var startingContent = $(".content_count").val() - 1;
		$('#add_content').click(function(e) {
			e.preventDefault();
			startingContent++;
			var contentID = 'cpt_item_description_' + startingContent;
			var cpt_item_name = 'cpt_item_name_' + startingContent;
				contentRow = '<div class="content-row cpt-content-row">';
				contentRow += '<a class="content-delete" href="#" style="color:#a00;float:right;margin-top: 3px;text-decoration:none;font-size:20px;"><svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="-64 0 512 512" width="25px"><path d="m256 80h-32v-48h-64v48h-32v-80h128zm0 0" fill="#62808c"/><path d="m304 512h-224c-26.507812 0-48-21.492188-48-48v-336h320v336c0 26.507812-21.492188 48-48 48zm0 0" fill="#e76e54"/><path d="m384 160h-384v-64c0-17.671875 14.328125-32 32-32h320c17.671875 0 32 14.328125 32 32zm0 0" fill="#77959e"/><path d="m260 260c-6.246094-6.246094-16.375-6.246094-22.625 0l-41.375 41.375-41.375-41.375c-6.25-6.246094-16.378906-6.246094-22.625 0s-6.246094 16.375 0 22.625l41.375 41.375-41.375 41.375c-6.246094 6.25-6.246094 16.378906 0 22.625s16.375 6.246094 22.625 0l41.375-41.375 41.375 41.375c6.25 6.246094 16.378906 6.246094 22.625 0s6.246094-16.375 0-22.625l-41.375-41.375 41.375-41.375c6.246094-6.25 6.246094-16.378906 0-22.625zm0 0" fill="#fff"/></svg></a><h3 class="cpt-item-title">Item</h3>';
				contentRow += '<div class="cpt-form-group"><label  class="cptformtitle" for="' + cpt_item_name + '">Name</label><input name="cpt_item_name[]" type="text" id="' + cpt_item_name + '" ></div>';
				contentRow += '<input  name="cpt_item_order[]" type="hidden" value="' + startingContent + '">';
				contentRow += '<div class="cpt-form-group"><label  class="cptformtitle" for="' + contentID + '">Description</label><textarea name="cpt_item_description[]" class="tinytext" id="' + contentID + '" rows="10"></textarea></div>';
				contentRow += '</div>';
				
				$('.content-row').eq($('.content-row').length - 1).after(contentRow);
				wp.editor.initialize( contentID, 
					{ 
						tinymce: {
							wpautop  : true,
							theme    : 'modern',
							skin     : 'lightgray',
							language : 'en',
							formats  : {
								alignleft  : [
									{ selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: { textAlign: 'left' } },
									{ selector: 'img,table,dl.wp-caption', classes: 'alignleft' }
								],
								aligncenter: [
									{ selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: { textAlign: 'center' } },
									{ selector: 'img,table,dl.wp-caption', classes: 'aligncenter' }
								],
								alignright : [
									{ selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: { textAlign: 'right' } },
									{ selector: 'img,table,dl.wp-caption', classes: 'alignright' }
								],
								strikethrough: { inline: 'del' }
							},
							relative_urls       : false,
							remove_script_host  : false,
							convert_urls        : false,
							browser_spellcheck  : true,
							fix_list_elements   : true,
							entities            : '38,amp,60,lt,62,gt',
							entity_encoding     : 'raw',
							keep_styles         : false,
							paste_webkit_styles : 'font-weight font-style color',
							preview_styles      : 'font-family font-size font-weight font-style text-decoration text-transform',
							tabfocus_elements   : ':prev,:next',
							plugins    : 'charmap,hr,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpview',
							resize     : 'vertical',
							menubar    : false,
							indent     : false,
							toolbar1: 'formatselect bold italic | bullist numlist | blockquote | alignleft aligncenter alignright | link unlink | wp_more | spellchecker | fullscreen | wp_adv',
							// toolbar1   : 'bold, italic, strikethrough, bullist, numlist, blockquote, hr, alignleft, aligncenter, alignright, link, unlink, wp_more, spellchecker, fullscreen, wp_adv',
							toolbar2   : 'underline, alignjustify, forecolor, pastetext, removeformat, charmap, outdent,indent,undo,redo,wp_help',
							toolbar3   : '',
							toolbar4   : '',
							body_class : 'id post-type-post post-status-publish post-format-standard',
							wpeditimage_disable_captions: false,
							wpeditimage_html5_captions  : true
						},
						quicktags   : true,
						mediaButtons: true
					} );
		});
	});
})(jQuery);

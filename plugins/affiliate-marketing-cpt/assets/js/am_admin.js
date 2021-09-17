(function ($) {
	var $document = $(document);
	function reInitialize ( editorId ){
		wp.editor.remove( editorId );
		// alert( editorId );
		wp.editor.initialize( editorId,
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
				quicktags   : false,
				mediaButtons: false
			} );
		}
	function reorderCode (){
	}

	$document.ready(function () {
		// reorderCode();
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
	// Manage Ordering after add new Item
		$(".moveup").on("click", function() {
			var elem	= $(this).closest("div.am-content-row");
			var editorId = $(this).attr( "tiny-editorid" );
			if ( confirm('Are you sure you want to move this item?') ) {
				elem.prev().before(elem);
				reInitialize( editorId );
			}
		});
		
		$(".movedown").on("click", function() {
			var elem = $(this).closest("div.am-content-row");
			var editorId = $(this).attr( "tiny-editorid" );
			if ( confirm('Are you sure you want to move this item?') ) {
				elem.next().after(elem);
				reInitialize( editorId );
			}
		});
		
		$(".movetop").on("click", function() {
			var elem = $(this).closest("div.am-content-row");
			var editorId = $(this).attr( "tiny-editorid" );
			if ( confirm('Are you sure you want to move this item?') ) {
				elem.siblings().first().before(elem);
				reInitialize( editorId );
			}
		});
		
		$(".movebottom").on("click", function() {
			var elem = $(this).closest("div.am-content-row");
			var editorId = $(this).attr( "tiny-editorid" );
			if ( confirm('Are you sure you want to move this item?') ) {
				elem.siblings().last().after(elem);
				reInitialize( editorId );
			}
		});
	// Manage Content delete	
		$document.on('click', '.content-delete', function(e) {
			e.preventDefault();
			if( $( '.content-row' ).length === 1 ) {
				alert("One Item is required in Item box.");
			} else
			{
				if (confirm('Are you sure you want to delete this item?')) {
					$(this).parents('.content-row').remove();
				}
			}
		});
	});
})(jQuery);

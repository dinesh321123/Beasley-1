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
				quicktags   : true,
				mediaButtons: true
			} );
		}
	function reorderCode (){
		$(".moveup").on("click", function() {
			var elem	= $(this).closest("div.cpt-content-row");
			var editorId = $(this).attr( "tiny-editorid" );
			if ( confirm('Are you sure you want to move this item?') ) {
				elem.prev().before(elem);
				reInitialize( editorId );
			}
		});
		$(".movedown").on("click", function() {
			var elem = $(this).closest("div.cpt-content-row");
			var editorId = $(this).attr( "tiny-editorid" );
			if ( confirm('Are you sure you want to move this item?') ) {
				elem.next().after(elem);
				reInitialize( editorId );
			}
		});
		$(".movetop").on("click", function() {
			var elem = $(this).closest("div.cpt-content-row");
			var editorId = $(this).attr( "tiny-editorid" );
			if ( confirm('Are you sure you want to move this item?') ) {
				elem.siblings().first().before(elem);
				reInitialize( editorId );
			}
		});
		$(".movebottom").on("click", function() {
			var elem = $(this).closest("div.cpt-content-row");
			var editorId = $(this).attr( "tiny-editorid" );
			if ( confirm('Are you sure you want to move this item?') ) {
				elem.siblings().last().after(elem);
				reInitialize( editorId );
			}
		});
	}

	$document.ready(function () {
		reorderCode();
		/*
		*	function to delete items
		*/
		$document.on('click', '.content-delete', function(e) {
			e.preventDefault();
			if( $( '.content-row' ).length === 1 ) {
				alert("One Item required in Item box");
			} else
			{
				if ( confirm('Are you sure you want to delete this item?') ) {
					$(this).parents('.content-row').remove();
				}
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
				contentRow += '<div class="dir-btn-grp">';
				contentRow += '<button type="button" tiny-editorid="' + contentID + '" class="updownbtn movetop fa fa-angle-double-up dir-btn"></button>';
				contentRow += '<button type="button" tiny-editorid="' + contentID + '" class="updownbtn moveup fa fa-angle-up dir-btn"></button>';
				contentRow += '<button type="button" tiny-editorid="' + contentID + '" class="updownbtn movedown fa fa-angle-down dir-btn"></button>';
				contentRow += '<button type="button" tiny-editorid="' + contentID + '" class="updownbtn movebottom fa fa-angle-double-down dir-btn"></button>';
				contentRow += '<a class="content-delete dir-btn " href="#"><i class="fa fa-trash-o"></i></a>';
				contentRow += '</div>';
				contentRow += '<h3 class="cpt-item-title">Item</h3>';
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
			$(".moveup").on("click", function() {
				var elem	= $(this).closest("div.cpt-content-row");
				var editorId = $(this).attr( "tiny-editorid" );
				if ( confirm('Are you sure you want to move this item?') ) {
					elem.prev().before(elem);
					reInitialize( editorId );
				}
			});

			$(".movedown").on("click", function() {
				var elem = $(this).closest("div.cpt-content-row");
				var editorId = $(this).attr( "tiny-editorid" );
				if ( confirm('Are you sure you want to move this item?') ) {
					elem.next().after(elem);
					reInitialize( editorId );
				}
			});

			$(".movetop").on("click", function() {
				var elem = $(this).closest("div.cpt-content-row");
				var editorId = $(this).attr( "tiny-editorid" );
				if ( confirm('Are you sure you want to move this item?') ) {
					elem.siblings().first().before(elem);
					reInitialize( editorId );
				}
			});

			$(".movebottom").on("click", function() {
				var elem = $(this).closest("div.cpt-content-row");
				var editorId = $(this).attr( "tiny-editorid" );
				if ( confirm('Are you sure you want to move this item?') ) {
					elem.siblings().last().after(elem);
					reInitialize( editorId );
				}
			});
		});
	});
})(jQuery);

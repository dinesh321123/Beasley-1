function onAMMoveToclick(element) {
	var currentIntex = element.closest("div.am-content-row").index() + 1;
	jQuery("#am-current").val(currentIntex);
	jQuery("#am-note").html(`*Please enter between 1 to ${jQuery(".am-content-row").length ? jQuery(".am-content-row").length : 1}`);
	jQuery("#am-modal").dialog("open");
}

function rearrangeAMItems() {
	jQuery(".am-content-row").each(function (index, element) {
		var title = jQuery(element).find(".am-item-title").html(),
			text = title.split('.')[1];
		jQuery(element).find(".am-item-title").html( ( index + 1 ) + "." + text );
	});
}

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

	function validateInputRange() {
		$("#am-input").keydown(function () {
			var max = $(".am-content-row").length ? $(".am-content-row").length : 0;
			// Save old value.
			if (!$(this).val() || (parseInt($(this).val()) <= max && parseInt($(this).val()) >= 1))
			$(this).data("old", $(this).val());
		});
		$("#am-input").keyup(function () {
			var max = $(".am-content-row").length ? $(".am-content-row").length : 0;
			// Check correct, else revert back to old value.
			if (!$(this).val() || (parseInt($(this).val()) <= max && parseInt($(this).val()) >= 1))
			  ;
			else
			  $(this).val($(this).data("old"));
		});
	}

	$("#am-modal").dialog({
		draggable: false,
    	resizable: false,
		modal: true,
		autoOpen: false,
		dialogClass: 'afm-dialog',
		show: {effect: "blind", duration: 800}
	});
	$document.ready(function () {
		// reorderCode();
		validateInputRange();

		$("#am-submit").on("click", function() {
			var currentIntex = $("#am-current").val();
			var changeIndex = $("#am-input").val();
			if( (changeIndex && changeIndex > 0) && ( changeIndex !== currentIntex ) && (changeIndex <= $(".am-content-row").length)) {
				var currentEle = $(".am-content-row").eq(currentIntex-1);
				var currentEleId = $(currentEle).find('.movecustom').attr( "tiny-editorid" );
				$(".am-content-row").eq(currentIntex-1).remove();
				
				if(changeIndex == 1) {
					$(".am-content-row").eq(0).before(currentEle);	
				} else {
					$(".am-content-row").eq(changeIndex - 2).after(currentEle);
				}

				reInitialize( currentEleId );
				rearrangeAMItems();
				$("#am-input").val("");
				$("#am-modal").dialog("close");
			} else {
				$("#am-input").val("");
				alert('Please add correct index value!');
			}
		});

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
				rearrangeAMItems();
			}
		});

		$(".movedown").on("click", function() {
			var elem = $(this).closest("div.am-content-row");
			var editorId = $(this).attr( "tiny-editorid" );
			if ( confirm('Are you sure you want to move this item?') ) {
				elem.next().after(elem);
				reInitialize( editorId );
				rearrangeAMItems();
			}
		});

		$(".movetop").on("click", function() {
			var elem = $(this).closest("div.am-content-row");
			var editorId = $(this).attr( "tiny-editorid" );
			if ( confirm('Are you sure you want to move this item?') ) {
				elem.siblings().first().before(elem);
				reInitialize( editorId );
				rearrangeAMItems();
			}
		});

		$(".movebottom").on("click", function() {
			var elem = $(this).closest("div.am-content-row");
			var editorId = $(this).attr( "tiny-editorid" );
			if ( confirm('Are you sure you want to move this item?') ) {
				elem.siblings().last().after(elem);
				reInitialize( editorId );
				rearrangeAMItems();
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
					rearrangeAMItems();
				}
			}
		});
	});
})(jQuery);

(function ($) {
	var $document = $(document);
	$document.ready(function () {
		/* $(".ntdelbutton").click(function() {	
			// var remove_tag_data = current clicked tag;
			// var prior_tags_data = $( ".tp_ntdelbutton" ).val();
			// Then pass in ajax and unset the tag 
		}); */
		$("#tagsdiv-post_tag").remove();
		$(".tag-permissions-add").click(function() {
			var tags_data = $( '.tag-permissions-value' ).val();
			var prior_tags_data = $( '#tag_permissions_post_tag' ).val();
			ajaxExecuteToGetTags( tags_data, prior_tags_data, 'get' );
		});
		function removeTag(){
			$(".ntdelbutton").click(function() {
				var tags_data = $(this).val();
				var prior_tags_data = $( '#tag_permissions_post_tag' ).val();
				ajaxExecuteToGetTags( tags_data, prior_tags_data, 'remove' );
			});
		}
		function removeErrorAfterSomeTime(){
			// alert("Remove function called");
			setTimeout(function(){
				if ($('#errormsg').length > 0) {
				  $('#errormsg').remove();
				}
			  }, 10000)
		}
	function ajaxExecuteToGetTags( tags_data, prior_tags_data, activity ){
		$.ajax({
			type : 'POST',
			url : (ajaxurl) ? ajaxurl : my_ajax_object.url,
			data : { 
				action: 'is_tag_available',
				tags_data: tags_data,
				prior_tags_data: prior_tags_data,
				activity: activity
			},

			success : function( response ) {
				console.log(response);
				if( response.available_tag_html ){
					$( '#available-tagchecklist' ).empty();
					$( '#tag_permissions_post_tag' ).val( '' );
					
					$( '#available-tagchecklist' ).append( response.available_tag_html );
					$( '#tag_permissions_post_tag' ).val( response.available_tag_string );
					$( '#new-tag-post_tag' ).val('');
					removeTag();
				}
				if( response.not_available_tag_string && response.not_available_tag_string.length > 0 ) {
					$( '#error_msg' ).append( '<div id="errormsg">'+  response.not_available_tag_string +' tag not available to use</div>' );
					removeErrorAfterSomeTime();
				}
			},
			error : function(r) {
				$('#error_msg').prev().append('<div id="errormsg"><p class="error">There was an error. Please reload the page.</p></div>');
				removeErrorAfterSomeTime();
			}
		});
	}

	});
})(jQuery);

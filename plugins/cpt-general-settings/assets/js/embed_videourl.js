(function ($) {
	var $document = $(document);
	$document.ready(function () {
		$document.on('change', '.embed_field_url', function(e) {
			var getPostid			=	$('.embed_field_mediaid').val();
			var getEmbedVideoUrl	=	$(this).val();
			$( '#embed_field_spinner' ).addClass( 'is-active' );
			console.log(getEmbedVideoUrl);
			console.log(getPostid);
			console.log(ajaxurl);
			$.ajax({
				type : 'POST',
				url : (ajaxurl) ? ajaxurl : my_ajax_object.url,
				data : {
					action: 'validate_embed_videourl',
					getPostid: getPostid,
					getEmbedVideoUrl: getEmbedVideoUrl
				},
				success : function( response ) {
					alert(response.data.message);
					console.log(response.data);
					$( '#embed_field_spinner' ).removeClass( 'is-active' );
				},
				error : function( error ) {
					alert(error.data.message);
					console.log(error.data);
					// $('#error_msg').prev().append('<div id="errormsg"><p class="error">There was an error. Please reload the page.</p></div>');
				}
			});
		});
	});
})(jQuery);

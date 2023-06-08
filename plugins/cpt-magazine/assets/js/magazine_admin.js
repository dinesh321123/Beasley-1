const request = (action, data) => new Promise((resolve, reject) => {
	wp.ajax.send(action, {
		type: 'GET',
		data,
		success: resolve,
		error: reject,
	});
});

(function ($) {
	var $document = $(document);
	$document.ready(function () {
		var magazine_post_submit = false;
		$( '#post' ).submit( function( ) {
			var category_featured_post_meta_box = $( '#post' ).find("input[name='category_featured_post_meta_box']").val();
			if(!category_featured_post_meta_box) {
				showErrorMsgMagazine('Featured Post for category is required.');
				return false;
			}

			if(magazine_post_submit == true) {
				return true;
			} else {
				var post_id = $( '#post' ).find('#post_ID').val();
				var selectedCat = $('#acf-field_select_category_magazine_cpt').val();
				if (selectedCat)  {
					console.log('selectedCat: ', selectedCat);
					request('validate_magazine_feilds', { selectedCat: selectedCat, post_id: post_id }).then((success) => {
						if(success && success.alreadyExist) {
							showErrorMsgMagazine('Selected Category already exist.');
							magazine_post_submit = false;
						} else {
							magazine_post_submit = true;
							$( '#post' ).submit();
						}
					}).catch((error) => {
						console.log(error);
						showErrorMsgMagazine('Some Error during while saving, Make sure correct parameters are selected.');
						magazine_post_submit = false;
					});
				}
				return magazine_post_submit;
			}
		} );
	});

	function showErrorMsgMagazine(message = '') {
		if($('.wp-header-end').length > 0) {
			$('.wp-header-end').after('<div class="error magazine-cat-admin-error"><p>'+message+'</p></div>');
			setTimeout(() => {
				$('.magazine-cat-admin-error').remove();
			}, 5000);
		} else {
			alert(message);
		}
	}
})(jQuery);

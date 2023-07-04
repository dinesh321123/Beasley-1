(function ($) {
	var $document = $(document);
	$document.ready(function () {
		var clicked = false;
		if( duplicateListicleData.post_type == 'listicle_cpt' && duplicateListicleData.currunt_page == 'edit.php' ){
			$('.duplicate a').on("click", function (e) {
				if(clicked===false){
					clicked=true;
				}else{
					e.preventDefault();
				}
			});
		}
	});
})(jQuery);

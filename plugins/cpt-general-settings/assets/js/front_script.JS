function scrollToSegmentation(type, item, heading_item = null) {
	var gotoID = null;
	var pagiStyleHeight = 0;
	var headerStyleHeight = 0;
	var headerContainer = document.getElementsByClassName( 'header-and-news-container' );
	var paginationHeadSection = document.getElementsByClassName( 'pagination-head-section' );

	if (headerContainer[0]) {
		var headerStyle = window.getComputedStyle(headerContainer[0]);
		headerStyleHeight = headerStyle.height ? Math.ceil(parseFloat(headerStyle.height)) : 0;
	}
	if (paginationHeadSection[0]) {
		var pagiStyle = window.getComputedStyle(paginationHeadSection[0]);
		pagiStyleHeight = pagiStyle.height ? Math.ceil(parseFloat(pagiStyle.height)) : 0;
	}

	if(item) {
		gotoID = document.getElementById(jQuery.trim(type) + '-segment-item-' + item);
	}
	if(heading_item) {
		gotoID = document.getElementById(jQuery.trim(type) + '-segment-header-item-' + heading_item);
	}
	if(gotoID) {
		var headerOffset = ( headerStyleHeight + pagiStyleHeight );
		var gotoIDPosition = gotoID.getBoundingClientRect().top;
		var offsetPosition = gotoIDPosition + window.pageYOffset - headerOffset;
	
		window.scrollTo({
			top: offsetPosition,
			behavior: "smooth"
		});
	}
}

// Add alt parameter to auto genrated images for lighthouse issue
var checkTritonPixeltimes = 0;
var checkTritonPixel = setInterval(function() {
    checkTritonPixeltimes += 1;
    var triton_pixel_image = document.getElementsByClassName('triton-pixel');
    if(triton_pixel_image.length > 0) {
        for (var idx = 0; idx < triton_pixel_image.length; idx++) {
            if(triton_pixel_image[idx] && triton_pixel_image[idx].tagName == "IMG") {
                triton_pixel_image[idx].alt = "";
            }
        }
        clearInterval(checkTritonPixel);
    }
    if(checkTritonPixeltimes > 10) {
        clearInterval(checkTritonPixel);
    }
}, 500);

// Add alt parameter to auto genrated images for lighthouse issue
var checkResetPixeltimes = 0;
var checkResetPixel = setInterval(function() {
	checkResetPixeltimes += 1;
	var reset_pixel_image = document.getElementById('resetPixelContainer');
	if(reset_pixel_image) {
		var reset_pixel_image_nodes = reset_pixel_image.childNodes;
		if(reset_pixel_image_nodes.length) {
			for(var i=0; i<reset_pixel_image_nodes.length; i++) {
				if (reset_pixel_image_nodes[i].tagName == 'IMG') {
					reset_pixel_image_nodes[i].alt = "Reset Pixel Image";
				 }
			}
		}
		clearInterval(checkResetPixel);
	}
	if(checkResetPixeltimes > 10) {
		clearInterval(checkResetPixel);
	}
}, 500);
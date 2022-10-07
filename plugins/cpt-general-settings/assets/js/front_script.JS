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

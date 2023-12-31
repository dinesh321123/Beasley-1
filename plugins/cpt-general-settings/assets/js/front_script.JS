document.addEventListener('DOMContentLoaded', () => {
	// Find all elements with class "media-image-with-attr"
	let mediaImages = document.querySelectorAll(".media-image-with-attr");

	// Function to retrieve image attribution
	async function getImageAttribution(image) {
	  if ( !image.hasAttribute("data-media-image-attribution") ) {
		return;
	  }

	  // Mapping object for class names to float classes
	  const floatClassMap = {
		alignright: "media-right-align",
		alignleft: "media-left-align",
		aligncenter: "media-center-align",
		alignnone: "media-none-align"
	  };

	  // Convert classList to an array
	  const classList = Array.from(image.classList);

	  // Find the matching float class from the classList
	  const float = classList.find(className => floatClassMap.hasOwnProperty(className));

	  try {
		if (image.hasAttribute("data-media-image-attribution")) {
		  // Create container div for image and attribution
		  const containerDiv = document.createElement("div");
		  containerDiv.className = "media-image-with-attr-container";

		  // Add the float class to the containerDiv
		  // If float is undefined or doesn't have a corresponding float class, add an empty string
		  if(floatClassMap[float]) {
			containerDiv.classList.add(floatClassMap[float]);
		  }

		  // Create div for attribution text
		  const attributionDiv = document.createElement("div");
		  attributionDiv.className = "media-image-attr";
		  attributionDiv.innerHTML = image.getAttribute("data-media-image-attribution");

		  // Clone the image element
		  const clonedImage = image.cloneNode(true);

		  // Append cloned image and attribution div to container div
		  containerDiv.appendChild(clonedImage);
		  containerDiv.appendChild(attributionDiv);

		  // Replace the original image with the container div
		  image.parentNode.replaceChild(containerDiv, image);
		}
	  } catch (error) {
		console.error('Error Attribution: ', error);
	  }
	}

	if (mediaImages.length > 0) {
	  // Iterate over each image element
	  mediaImages.forEach(async (image) => {
		await getImageAttribution(image);
	  });
	}
});

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

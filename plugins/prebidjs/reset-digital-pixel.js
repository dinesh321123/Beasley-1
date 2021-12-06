window['fireResetPixel'] = function(pageUrl) {

	function getPixelContainer() {
		var pixelContainer = document.querySelector('#resetPixelContainer');
		return pixelContainer;
	}

	function clearPixels() {
		var pixelContainer = getPixelContainer();
		if (pixelContainer) {
			pixelContainer.parentNode.removeChild(pixelContainer)
		}
	}

	function createPixelContainer() {
		var pixelContainer = document.createElement('span');
		pixelContainer.setAttribute('id', 'resetPixelContainer');
		document.body.appendChild(pixelContainer);
		return pixelContainer;
	}

	function insertPixel(url) {
		var pixelContainer = getPixelContainer();

		if (! pixelContainer) {
			pixelContainer = createPixelContainer();
		}

		var pixel = document.createElement('img');

		pixel.setAttribute('style', 'width:1px; height:1px;');
		pixel.setAttribute('src', url);
		pixelContainer.appendChild(pixel);
	}

	clearPixels();

	var metas = document.getElementsByTagName('meta');
	var meta_keywords="";
	var meta_title=document.title;
	var meta_description="";
	var meta_uemail="";
	for (var i=0; i<metas.length; i++) {
		if(metas[i].hasAttribute("name") && metas[i].hasAttribute("content")){
			if(metas[i].name=="keywords" && metas[i].content!=""){
				meta_keywords=metas[i].content;
			}
			if(metas[i].name=="description" && metas[i].content!=""){
				meta_description=metas[i].content;
			}
		}
	}
	var dds = document.getElementsByTagName("dd");
	var dts = document.getElementsByTagName("dt");
	if(dds[1] && dts[1] && dts[1].innerHTML=="Email"){
		meta_uemail=dds[1].innerHTML;
	}

	var docLocation = pageUrl ? pageUrl : document.location.href;

	insertPixel('https://meta.resetdigital.co/smart?px=1000164&tp=gif&k='+encodeURIComponent(meta_keywords)+'&t='+encodeURIComponent(meta_title)+'&d='+encodeURIComponent(meta_description)+'&email='+encodeURIComponent(meta_uemail)+'&purl='+encodeURIComponent(docLocation));
	insertPixel('https://bpi.rtactivate.com/tag/?id=20784&user_id=00002500A2BB4866');
	insertPixel('https://x.bidswitch.net/sync?dsp_id=447&user_id=00002500A2BB4866&expires=90');
	insertPixel('https://x.bidswitch.net/sync?ssp=resetdigital&user_id=00002500A2BB4866&expires=90');
}

if (typeof window['manualFireResetPixel'] == 'undefined') {
	fireResetPixel();
}



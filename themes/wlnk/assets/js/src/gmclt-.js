var GMCLT = GMCLT || {};

var apiUrl = 'http://api2.1079thelink.com';

var gmcltStationName = 'WLNK';
var gmcltStationID = 1;

var gmcltPlayerBackgroundImages = [];
var gmcltPlayerBackgroundCurrentIndex = 0;

jQuery(document).ready(function(){
	if($(window).width() >= 768) {
		GMCLT.PlayerBackground.init();
	}
});

GMCLT.PlayerBackground = function() {
	
	var init = function() {
		jQuery.getJSON(apiUrl + '/playerBackground/player.cfc?method=getPlayerBackgrounds&station=wlnk&callback=?',
	
			function (gmcltPlayerBackgroundImageObject) {
				gmcltPlayerBackgroundImages = gmcltPlayerBackgroundImageObject;
				changePlayerBackgroundImage();
			})
			.fail(function() {
			   //do nothing. Not catastrophic
			});
	};
	
	var changePlayerBackgroundImage = function() {
		var theUrl = 'url(' + gmcltPlayerBackgroundImages[gmcltPlayerBackgroundCurrentIndex].img + ')';
		
		jQuery('.live-stream').css({ "background-image": theUrl });
		if (gmcltPlayerBackgroundCurrentIndex < gmcltPlayerBackgroundImages.length-1) {
			gmcltPlayerBackgroundCurrentIndex = gmcltPlayerBackgroundCurrentIndex+1;
		}
		else {
			gmcltPlayerBackgroundCurrentIndex = 0;
		}
		
		setTimeout(changePlayerBackgroundImage, 5000);
		
	}
	
	var oPublic =
	    {
	      init: init
	    };
    return oPublic;
	 
}();
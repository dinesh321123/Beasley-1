var GMCLT = GMCLT || {};

GMCLT.Traffic = function() {
	
  	var init = function() {
  		google.maps.event.addDomListener(window, 'load', initialize);
  	};

	var initialize = function() {
		var myLatlng = new google.maps.LatLng(35.2269, -80.8433);
		var mapOptions = {
			zoom: 11,
		    scrollwheel: false,
		    //draggable: false,
		    center: myLatlng
		}

		trafficmap = new google.maps.Map(document.getElementById('traffic-map-canvas'), mapOptions);

		var trafficLayer = new google.maps.TrafficLayer();
		trafficLayer.setMap(trafficmap);
		getTrafficIncidents();
		getTrafficCameras();
	};

	var getTrafficIncidents = function() {
		trafficObject = new Object();
			
		infowindow = new google.maps.InfoWindow({
	        content: 'Test content'
	    });
			
		jQuery.getJSON('http://site.gmclt.com/api/Traffic.cfc?method=getActiveIncidents&callback=?',
			
			function (trafficObject) {
					
				for (var i=0;i<trafficObject.length;i++) { 
					var id = trafficObject[i].incidentId;
					var location = new google.maps.LatLng(trafficObject[i].lat, trafficObject[i].lng);
					var markerimage = trafficObject[i].marker;
					var title = trafficObject[i].headline;
					var zindex = trafficObject[i].zindex;
					
					//create marker
					window['marker' + id] = new google.maps.Marker({
						position: location,
						map: trafficmap,
						icon: markerimage,
						title: title,
						zIndex: zindex
					});

					//Add listener for click
					google.maps.event.addListener(window['marker' + id], 'click', buildIncidentClickHandler(trafficObject[i]));
					
				}
			});
	};

	var buildIncidentClickHandler =	function(i) {
		return function() {
			var id = i.incidentId;
			var body = i.body;
			var title = i.headline;
			var markerimage = i.marker;
			var startdate = i.startdate;
			var starttime = i.starttime;
			var enddate = i.enddate;
			var endtime = i.endtime;
			
			if (startdate == enddate) {
				var activedatestring = startdate + ' at ' + starttime + ' - ' + endtime;
			}
			else {
				var activedatestring = startdate + ' at ' + starttime + ' - ' + enddate + ' at ' + endtime;
			}
			
			infowindow.setContent("<h3><img src='" + markerimage + "' align='left' style='padding: 10px;' vspace='2' hspace='2'>" + title + "</h3><p class='attribution'>" + activedatestring + "</p><p>" + body + "</p>");
			infowindow.open(trafficmap,window['marker' + id]);
		};
	};

	var	getTrafficCameras = function() {
		cameraObject = new Object();
		
		infowindow = new google.maps.InfoWindow({
	        content: 'Test content'
	    });
		
		jQuery.getJSON('http://site.gmclt.com/api/Traffic.cfc?method=getActiveCameras&callback=?',
		
			function (cameraObject) {
				
				for (var i=0;i<cameraObject.length;i++) { 
					var id = cameraObject[i].webId;
					var location = new google.maps.LatLng(cameraObject[i].latitude, cameraObject[i].longitude);
					var title = cameraObject[i].name;
					
					//create marker
					window['camera' + id] = new google.maps.Marker({
						position: location,
						map: trafficmap,
						icon: 'http://content.gmclt.com/traffic/trafficCamera.png',
						title: title,
						zIndex: 1
					});

					//Add listener for click
					google.maps.event.addListener(window['camera' + id], 'click', buildCameraClickHandler(cameraObject[i]));
					
				}
			});
	};

	var buildCameraClickHandler = function(i) {
		return function() {
			var id = i.webId;
			var orientation = i.orientation;
			var name = i.name;
			var refreshRate = i.refreshRate;
			var provider = i.provider;
			var fullImage = i.fullImage;
			
			infowindow.setContent("<img src='" + fullImage + "' style='padding: 10px;' vspace='2' hspace='2'><h2>" + name + "</h2><p class='attribution'>" + orientation + "</p><p>" + provider + "</p>");
			infowindow.open(trafficmap,window['camera' + id]);
		};

  };

  	var oPublic =
	    {
	      init: init
	    };
	    return oPublic;
 
 }();
  
GMCLT.Weather = function() {
	 
 	var init = function() {
		//alert('hello');
		//handlebarsExample();
		populateWeatherData('USNC0121');	
		jQuery('#gmcltWX_search').keydown(function (e){
		    if(e.keyCode == 13){
		        searchWeatherLocations();
		    }
		}); 
		jQuery('#gmcltWX_searchsubmit').click(function(){
			searchWeatherLocations();
			
		});
	};
	
	var populateWeatherData = function(locationId) {
		
		var wxDataObject;
		var wxConditionsSource = jQuery("#currentConditions-template").html(); 
		var wxConditionsTemplate = Handlebars.compile(wxConditionsSource); 
		var wxForecastFullSource = jQuery("#forecastFull-template").html(); 
		var wxForecastFullTemplate = Handlebars.compile(wxForecastFullSource);
		var wxForecastSource = jQuery("#forecast-template").html(); 
		var wxForecastTemplate = Handlebars.compile(wxForecastSource); 
		
		jQuery.getJSON('http://api.gmclt.com/weather/weather.cfc?method=getWeatherData&locationId=' + locationId + '&callback=?',
		
			function (wxDataObject) {
				jQuery('#gmcltWX_currentContent').html(wxConditionsTemplate(wxDataObject));
				jQuery('#gmcltWX_forecastFullContent').html(wxForecastFullTemplate(wxDataObject));
				jQuery('#gmcltWX_forecastContent').html(wxForecastTemplate(wxDataObject));
				jQuery('.gmcltWX_search').show();
				jQuery('.gmcltWX_loading').hide();
			});
		
		
		
	};
	
	var searchWeatherLocations = function() {
		var searchQuery = jQuery.trim(jQuery('#gmcltWX_search').val());
		var wxSearchSource = jQuery("#searchResults-template").html(); 
		var wxSearchTemplate = Handlebars.compile(wxSearchSource);
		
		if (searchQuery.length) {
			jQuery('.gmcltWX_loading').show();
			jQuery('#gmcltWX_currentContent').html('');
			jQuery('#gmcltWX_forecastFullContent').html('');
			jQuery('#gmcltWX_forecastContent').html('');
			jQuery.getJSON('http://api.gmclt.com/weather/weather.cfc?method=searchLocations&queryString=' + searchQuery + '&callback=?',
		
			function (wxSearchObject) {
				if (wxSearchObject.match) {
					populateWeatherData(wxSearchObject.results[0].locationId);
					jQuery('#gmcltWX_search').val('');
				}
				else {
					jQuery('#gmcltWX_currentContent').html(wxSearchTemplate(wxSearchObject));
					jQuery('.gmcltWX_loading').hide();
					jQuery('#gmcltWX_search').val('');
				}
			});
			
		}
		
	}
	
	var oPublic =
	    {
	      init: init,
	      populateWeatherData: populateWeatherData
	    };
    return oPublic;
	 
}();
var trafficmap;
var infowindow;

GMCLT.Traffic = function() {
	
  	var init = function() {
		var myLatlng = new google.maps.LatLng(35.2269, -80.8433);
		var mapOptions = {
			zoom: 11,
		    scrollwheel: false,
		    //draggable: false,
		    center: myLatlng
		};

		trafficmap = new google.maps.Map(document.getElementById('gmclt_trafficMapCanvas'), mapOptions);

		trafficLayer = new google.maps.TrafficLayer();
		trafficLayer.setMap(trafficmap);
		getTrafficIncidents();
	};

	var getTrafficIncidents = function() {
		var trafficListSource = jQuery("#list-template").html(); 
		var trafficListTemplate = Handlebars.compile(trafficListSource);
		
		infowindow = new google.maps.InfoWindow({
	        content: 'Test content'
	    });
			
		jQuery.getJSON(apiUrl + '/traffic/traffic.cfc?method=getActiveIncidents&callback=?',
			
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
						icon: iconPath + markerimage,
						title: title,
						zIndex: zindex
					});

					//Add listener for click
					google.maps.event.addListener(window['marker' + id], 'click', buildIncidentClickHandler(trafficObject[i]));
					
					jQuery('#gmclt_trafficList').html(trafficListTemplate(trafficObject));
					jQuery('#gmclt_trafficListLoading').hide();
					
				}
				jQuery('#gmcltTraffic_mapLoading').hide();
			})
			.fail(function() {
			   trafficError('map');
			});
	};

	var buildIncidentClickHandler =	function(i) {
		return function() {
			var id = i.incidentId;
			var body = i.body;
			var title = i.headline;
			var markerimage = i.marker;
			var dateString = i.dateString;
			
			infowindow.setContent("<h3><img src='" + iconPath + markerimage + "' align='left' style='padding: 10px;' vspace='2' hspace='2'>" + title + "</h3><p class='attribution'>" + dateString + "</p><p>" + body + "</p>");
			infowindow.open(trafficmap,window['marker' + id]);
		};
	};

  	var trafficError = function(area) {
		var trafficErrorSource = jQuery("#error-template").html(); 
		var trafficErrorTemplate = Handlebars.compile(trafficErrorSource);
		jQuery('#traffic-map-canvas').html(trafficErrorTemplate());
		jQuery('#gmcltTraffic_' + area + 'Loading').hide();
	};
	
	var oPublic =
	    {
	      init: init
	    };
	    return oPublic;
 
 }();
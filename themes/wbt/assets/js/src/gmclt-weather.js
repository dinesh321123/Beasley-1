jQuery(document).ready(function(){
	GMCLT.Weather.currentConditionsSubnav();
});

GMCLT.Weather = function() {

 	var init = function() {
		populateWeatherData('USNC0121');
		//listen for search
		jQuery('#gmclt_wxSearch').keydown(function (e){
		    if(e.keyCode == 13){
		        searchWeatherLocations();
		    }
		});
		jQuery('#gmclt_wxSearchsubmit').click(function(){
			searchWeatherLocations();

		});
	};

	var currentConditionsSubnav = function() {
		var index = jQuery("div.secondary-link:contains('Weather')").parents().eq(1).attr('id');

		if (index) {

			var cookieWx = Cookies.get('gmcltWx');

			if (cookieWx) {
				var currentConditions = cookieWx.split(',');
				populateCurrentConditionsSubnav(currentConditions[0],currentConditions[1],index);
			}
			else {
				jQuery.getJSON(apiUrl + '/weather/v2/weather.cfc?method=getCurrentMini&callback=?',

				function (wxConditionsDataObject) {
					populateCurrentConditionsSubnav(wxConditionsDataObject.TempF,wxConditionsDataObject.IconCode,index);
					Cookies.set('gmcltWx', wxConditionsDataObject.TempF + ',' + wxConditionsDataObject.IconCode, { expires: 900 });
				})
				.fail(function() {
				   //do nothing. Not catastrophic
				});
			}
		}

	}

	var populateCurrentConditionsSubnav = function(TempF,IconCode,index) {
		var htmlString = '<a href="/weather"><div class="secondary-link"><img style="height: 35px; display: inline; margin-top: -10px; vertical-align: top;" class="gmclt_wxHeaderIcon" src="/wp-content/themes/wbt/images/wxv2/60/wx_' + IconCode +  '.png"> ' + TempF + '&deg;</div></a>';
		jQuery('#' + index).html(htmlString);
	};

	var populateWeatherData = function(locationId) {
		//init handlebars templates
		var wxConditionsSource = jQuery("#currentConditions-template").html();
		var wxConditionsTemplate = Handlebars.compile(wxConditionsSource);
		var wxForecastFullSource = jQuery("#forecastFull-template").html();
		var wxForecastFullTemplate = Handlebars.compile(wxForecastFullSource);
		var wxForecastSource = jQuery("#forecast-template").html();
		var wxForecastTemplate = Handlebars.compile(wxForecastSource);

		jQuery.getJSON(apiUrl + '/weather/v2/weather.cfc?method=getWeatherData&locationId=' + locationId + '&callback=?',

			function (wxDataObject) {
				jQuery('#gmclt_narrowColumnContent').html(wxConditionsTemplate(wxDataObject));
				jQuery('#gmcltWX_forecastFullContent').html(wxForecastFullTemplate(wxDataObject));
				jQuery('#gmcltWX_forecastContent').html(wxForecastTemplate(wxDataObject));
				jQuery('.gmclt_wxSearch').show();
				jQuery('.gmclt_wxLoading').hide();
				jQuery('#gmclt_radarMapCanvas').show();
				initializeRadarMap(wxDataObject.location + ', ' + wxDataObject.state);
			})
			.fail(function() {
			   wxError();
			});

	};

	var searchWeatherLocations = function() {
		var searchQuery = jQuery.trim(jQuery('#gmclt_wxSearch').val());
		var wxSearchSource = jQuery("#searchResults-template").html();
		var wxSearchTemplate = Handlebars.compile(wxSearchSource);

		if (searchQuery.length) {
			jQuery('.gmclt_wxLoading').show();
			jQuery('#gmclt_narrowColumnContent').html('');
			jQuery('#gmcltWX_forecastFullContent').html('');
			jQuery('#gmcltWX_forecastContent').html('');
			jQuery('#gmclt_radarMapCanvas').hide();
			jQuery.getJSON(apiUrl + '/weather/v2/weather.cfc?method=searchLocations&queryString=' + searchQuery + '&callback=?',

			function (wxSearchObject) {
				if (wxSearchObject.match) {
					populateWeatherData(wxSearchObject.results[0].locationId);
					jQuery('#gmclt_wxSearch').val('');
				}
				else {
					jQuery('#gmclt_narrowColumnContent').html(wxSearchTemplate(wxSearchObject));
					jQuery('.gmclt_wxLoading').hide();
					jQuery('#gmclt_wxSearch').val('');
				}

			})
			.fail(function() {
			   wxError();
			});

		}

	};

	function initializeRadarMap(address) {
		geocoder = new google.maps.Geocoder();
	  	geocoder.geocode( { 'address': address}, function(results, status) {
	    	if (status == google.maps.GeocoderStatus.OK) {
				var mapOptions = {
					zoom: 8,
					scrollwheel: false,
					center: results[0].geometry.location,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};

				var radarMap = new google.maps.Map(document.getElementById('gmclt_radarMapCanvas'), mapOptions);

				//get radar overlays
				var tileNEX = new google.maps.ImageMapType({
					getTileUrl: function(tile, zoom) {
						return "http://mesonet.agron.iastate.edu/cache/tile.py/1.0.0/nexrad-n0q-900913/" + zoom + "/" + tile.x + "/" + tile.y +".png?"+ (new Date()).getTime();
					},
					tileSize: new google.maps.Size(256, 256),
					opacity:0.60,
					name : 'NEXRAD',
					isPng: true
				});

				//add radar overlays
				radarMap.overlayMapTypes.push(null); // create empty overlay entry
				radarMap.overlayMapTypes.setAt("1",tileNEX);
			}
    	});

    }

	var stormwatchMap;
	// Default State
	var GMCLTstate = 'NC';

	var stormwatchInit = function() {
		//google.maps.event.addDomListener(window, 'load', initializeStormwatchMap);
		initializeStormwatchMap();
		google.maps.visualRefresh = true;

		jQuery('#gmclt_selectState').change(function () {
			jQuery( "#gmclt_selectState option:selected" ).each(function() {
				GMCLTstate = jQuery( this ).val();
				initializeStormwatchMap();
			});
		}).change();

	};

	function initializeStormwatchMap() {
		geocoder = new google.maps.Geocoder();
	  	geocoder.geocode( { 'address': GMCLTstate}, function(results, status) {
	    	if (status == google.maps.GeocoderStatus.OK) {
				var mapOptions = {
					zoom: 7,
					scrollwheel: false,
					center: results[0].geometry.location,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};

				stormwatchMap = new google.maps.Map(document.getElementById('gmclt_stormwatchMapCanvas'), mapOptions);

				//get radar overlays
				var tileNEX = new google.maps.ImageMapType({
					getTileUrl: function(tile, zoom) {
						return "http://mesonet.agron.iastate.edu/cache/tile.py/1.0.0/nexrad-n0q-900913/" + zoom + "/" + tile.x + "/" + tile.y +".png?"+ (new Date()).getTime();
					},
					tileSize: new google.maps.Size(256, 256),
					opacity:0.60,
					name : 'NEXRAD',
					isPng: true
				});

				//add initial state alerts
				addAlertBoxes(GMCLTstate);

				//add radar overlays
				stormwatchMap.overlayMapTypes.push(null); // create empty overlay entry
				stormwatchMap.overlayMapTypes.setAt("1",tileNEX);

				//add additional state alerts
				var stateList = 'NC,SC,VA,WV,TN,GA,AL,AK,AZ,AR,CA,CO,CT,DE,DC,FL,HI,ID,IL,IN,IA,KS,KY,LA,ME,MD,MA,MI,MN,MS,MO,MT,NE,NV,NH,NJ,NM,NY,ND,OH,OK,OR,PA,RI,SD,TX,UT,VT,WA,WI,WY';
				var stateARRAY = stateList.split(',');

				for (var i=0; i < stateARRAY.length; i++){
					if (stateARRAY[i] != GMCLTstate) {
					addAlertBoxes(stateARRAY[i]);
					}
				}


			}
			else {
				wxError();
				//alert("Geocode was not successful for the following reason: " + status);
      		}
    	});

    }

    function addAlertBoxes(state) {

		jQuery.getJSON(apiUrl + '/weather/weather.cfc?method=getWeatherAlerts&filter=byState&location=' + state + '&callback=?',

			function (GMCLTMapObject) {
				for (var i=0; i < GMCLTMapObject.length; i++){

					var countyId = GMCLTMapObject[i].countyId;

					//create new array for the coordinates
					var myCoords = [];

					//push Google LatLng object into array
					for (var j=0; j < GMCLTMapObject[i].countyPolygon.length; j++){
						myCoords.push(new google.maps.LatLng(GMCLTMapObject[i].countyPolygon[j].lat,GMCLTMapObject[i].countyPolygon[j].lng));
					}

					//create the polygon outlining the affected county
					window['alertZone' + countyId] = new google.maps.Polygon({
						paths: myCoords,
						strokeColor: '#FF0000',
						strokeOpacity: 0.8,
						strokeWeight: 1,
						fillColor: '#FF0000',
						fillOpacity: 0.35
					});

					window['alertZone' + countyId].setMap(stormwatchMap);

					//add listener for click on the polygon
					google.maps.event.addListener(window['alertZone' + countyId], 'click', buildPolyClickHandler(countyId));

				}
				jQuery('.gmcltWX_mapLoading').hide();

			})
			.fail(function() {
			   wxError();
			});


    }

    function buildPolyClickHandler(countyId) {
	    return function() {

	    	var wxAlertSource = jQuery("#alert-template").html();
			var wxAlertTemplate = Handlebars.compile(wxAlertSource);

	    	jQuery.getJSON(apiUrl + '/weather/weather.cfc?method=getWeatherAlerts&filter=byCounty&location=' + countyId + '&callback=?',

				function (GMCLTDetailsObject) {

					jQuery('#gmclt_narrowColumnContent').html(wxAlertTemplate(GMCLTDetailsObject));

				})
				.fail(function() {
				   wxError();
				});
	    };
    }

	var wxError = function() {
		var wxErrorSource = jQuery("#error-template").html();
		var wxErrorTemplate = Handlebars.compile(wxErrorSource);
		jQuery('#gmclt_narrowColumnContent').html(wxErrorTemplate());
		jQuery('.gmclt_wxLoading').hide();
		jQuery('.gmclt_wxSearch').hide();
	};

	var oPublic =
	    {
	      init: init,
	      stormwatchInit: stormwatchInit,
	      populateWeatherData: populateWeatherData,
	      currentConditionsSubnav: currentConditionsSubnav
	    };
    return oPublic;

}();

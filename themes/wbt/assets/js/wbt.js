var GMCLT = GMCLT || {};

var apiUrl = 'http://api2.wbt.com';

var gmcltStationName = 'WBT';
var gmcltStationID = 2;

 ( function( window, undefined ) {
	'use strict';

    Livefyre.require([
        'streamhub-wall#3',
        'streamhub-sdk#2'
    ], function(LiveMediaWall, SDK) {
        var wall = window.wall = new LiveMediaWall({
            el: document.getElementById("wbt-social-column"),
            initial: 4,
            collection: new (SDK.Collection)({
                "network": "gmcharlotte.fyre.co",
                "siteId": "364558",
                "articleId": "custom-1443014747260"
            })
        });
    });

    jQuery('.live-links--more__btn').attr('href', '/social/');

 } )( this );
if (typeof Handlebars != "undefined") {
	
}

GMCLT.AdIndex = function() {
	
	var $advertiserSearch = jQuery( document.getElementById( 'gmclt_advertiserSearch' ) );
	var $wideColumnContent = jQuery( document.getElementById( 'gmclt_wideColumnContent' ) );
	var $categorySelect = '';
	var $categoryDropdown = jQuery( document.getElementById( 'gmclt_categoryDropdown' ) );

	var init = function() {
		Handlebars.registerHelper("inc", function(value, options)
		{
		    return parseInt(value) + 1;
		});
		
		Handlebars.registerHelper("analytics", function(eventCode)
		{
		    ga('send', {'hitType': 'event', 'eventCategory': 'Advertising', 'eventAction': 'Impression', 'eventLabel': eventCode});
		   return '';
		});
		
		
		//listen for search
		$advertiserSearch.keydown(function (e){
		    if(e.keyCode == 13){
		        searchAdvertisers(0, 'search');
		    }
		}); 
		jQuery('#gmclt_searchSubmit').click(function(){
			searchAdvertisers(0, 'search');
			
		});
		populateCategories();
		
		var urlVars = [], hash;
		    var q = document.URL.split('?')[1];
		    if(q != undefined){
		        q = q.split('&');
		        for(var i = 0; i < q.length; i++){
		            hash = q[i].split('=');
		            urlVars.push(hash[1]);
		            urlVars[hash[0]] = hash[1];
		        }
		}
		
		if (urlVars['advertiserId']) {
			searchAdvertisers(urlVars['advertiserId'], 'direct');
		}
	};	
	
	var searchAdvertisers = function(id,mode) {
		
		var searchQuery = jQuery.trim( $advertiserSearch.val() );
		var adSearchSource = jQuery("#searchResults-template").html(); 
		var adSearchTemplate = Handlebars.compile(adSearchSource);
		var proceed = false;
		var url = '';
		
		switch (mode) {
			case 'search':
				url = apiUrl + '/adIndex/adIndex.cfc?method=searchIndex&query=' + searchQuery + '&mode=' + mode + '&station=' + gmcltStationName + '&callback=?';
				if (searchQuery.length) {
					proceed = true;
				}
				break;
			case 'category':
				url = apiUrl + '/adIndex/adIndex.cfc?method=searchIndex&categoryId=' + id + '&mode=' + mode +'&station=' + gmcltStationName + '&callback=?';
				proceed = true;
				break;
			case 'direct':
				url = apiUrl + '/adIndex/adIndex.cfc?method=searchIndex&advertiserId=' + id + '&mode=' + mode +'&station=' + gmcltStationName + '&callback=?';
				$categoryDropdown.hide();
				jQuery('.gmclt_searchBar').hide();
				proceed = true;
				break;
		}
		
		if (proceed) {
			jQuery('.gmclt_searching').show();
			$wideColumnContent.html('');
			jQuery.getJSON(url,
		
			function (searchObject) {
				$wideColumnContent.html(adSearchTemplate(searchObject));
				jQuery('.gmclt_searching').hide();
				$advertiserSearch.val('');
				if (mode != 'category') {
					$categorySelect.val('0');
				} 
				
			})
			.fail(function() {
			   searchError();
			});
			
		}
	};
	
	var populateCategories = function() {
		var categorySource = jQuery("#category-template").html(); 
		var categoryTemplate = Handlebars.compile(categorySource);
		
		jQuery.getJSON(apiUrl + '/adIndex/adIndex.cfc?method=getCategories&stationId=' + gmcltStationID + '&callback=?',
	
		function (categoryObject) {
			$categoryDropdown.html(categoryTemplate(categoryObject));
			$categorySelect = jQuery( document.getElementById( 'gmclt_categorySelect' ) );
			$categorySelect.change(function() 
				{
				  searchAdvertisers($(this).attr('value'), 'category');
				});
		})
			.fail(function() {
			   //do nothing. Not catastrophic
			});
	};
	
	var searchError = function() {
		var searchErrorSource = jQuery("#error-template").html(); 
		var searchErrorTemplate = Handlebars.compile(searchErrorSource);
		$wideColumnContent.html(searchErrorTemplate());
		jQuery('.gmclt_searching').hide();
		jQuery('.gmclt_searchBar').hide();
	};
	
	var oPublic =
	    {
	      init: init
	    };
	    return oPublic;
 
}();
jQuery(document).ready(function(){
	GMCLT.Stocks.stockQuoteSubnav();
});

GMCLT.Stocks = function() {
	
	var stockObject;
	var currentStock = 0;
	var index = jQuery("div.secondary-link:contains('Stocks')").parents().eq(1).attr('id');
	 
 	var init = function() {
		
	};
	
	var stockQuoteSubnav = function() {
		
		if (index) {
		
			jQuery.getJSON(apiUrl + '/stocks/stocks.cfc?method=getStocksMini&callback=?',
	
			function (stockDataObject) {
				stockObject = stockDataObject;
				window.setInterval("GMCLT.Stocks.populateStockQuote()", 4000);
			})
			.fail(function() {
			   //do nothing. Not catastrophic
			});
			
		}
		
	}
	
	var populateStockQuote = function() {
		var htmlString = '<a href="/category/business-news/"><div class="secondary-link"><img class="gmclt_stocksHeaderIcon" src="/wp-content/themes/wbt/images/stocks/' + stockObject[currentStock].arrow + '.png"> ' + stockObject[currentStock].shortName + ': ' + stockObject[currentStock].change + '</div></a>';
		jQuery('#' + index).html(htmlString);
		
		if (stockObject.length == currentStock+1) {
			currentStock = 0;
		}
		else {
			currentStock = currentStock+1;
		}
	};
	
	var oPublic =
	    {
	      init: init,
	      stockQuoteSubnav: stockQuoteSubnav,
	      populateStockQuote: populateStockQuote
	    };
    return oPublic;
	 
}();
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
				jQuery.getJSON(apiUrl + '/weather/weather.cfc?method=getCurrentMini&callback=?',
		
				function (wxConditionsDataObject) {
					populateCurrentConditionsSubnav(wxConditionsDataObject.temperature,wxConditionsDataObject.graphicCode,index);
					Cookies.set('gmcltWx', wxConditionsDataObject.temperature + ',' + wxConditionsDataObject.graphicCode, { expires: 900 });
				})
				.fail(function() {
				   //do nothing. Not catastrophic
				});
			}
		}
		
	}
	
	var populateCurrentConditionsSubnav = function(temperature,graphicCode,index) {
		var htmlString = '<a href="/weather"><div class="secondary-link"><img class="gmclt_wxHeaderIcon" src="/wp-content/themes/wbt/images/wx/' + graphicCode +  '.png"> ' + temperature + '&deg;</div></a>';
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
		
		jQuery.getJSON(apiUrl + '/weather/weather.cfc?method=getWeatherData&locationId=' + locationId + '&callback=?',
		
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
			jQuery.getJSON(apiUrl + '/weather/weather.cfc?method=searchLocations&queryString=' + searchQuery + '&callback=?',
		
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
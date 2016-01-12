if (typeof Handlebars != "undefined") {
	
}

GMCLT.AdIndex = function() {
	
	

	var init = function() {
		
		$advertiserSearch = jQuery( document.getElementById( 'gmclt_advertiserSearch' ) );
		$wideColumnContent = jQuery( document.getElementById( 'gmclt_wideColumnContent' ) );
		$categorySelect = '';
		$categoryDropdown = jQuery( document.getElementById( 'gmclt_categoryDropdown' ) );
		
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
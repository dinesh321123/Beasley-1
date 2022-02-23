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
		var htmlString = '<a href="/category/business/"><div class="secondary-link"><img style="height: 17px; width: 13px; display: inline; vertical-align: top;" class="gmclt_stocksHeaderIcon" src="/wp-content/themes/wbt/images/stocks/' + stockObject[currentStock].arrow + '.png"> ' + stockObject[currentStock].shortName + ': ' + stockObject[currentStock].change + '</div></a>';
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
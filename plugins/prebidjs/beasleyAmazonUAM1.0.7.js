
	//load the apstag.js library
	!function(a9,a,p,s,t,A,g){if(a[a9])return;function q(c,r){a[a9]._Q.push([c,r])}a[a9]={init:function(){q("i",arguments)},fetchBids:function(){q("f",arguments)},setDisplayBids:function(){},targetingKeys:function(){return[]},_Q:[]};A=p.createElement(s);A.async=!0;A.src=t;g=p.getElementsByTagName(s)[0];g.parentNode.insertBefore(A,g)}("apstag",window,document,"script","//c.amazon-adsystem.com/aax2/apstag.js");

	window.apstagIsInitialized = false;
	window.lastReturnedAmazonUAMBids = [];

	window.initializeAPS = () => {
		if (!window.apstagIsInitialized && window.bbgiconfig.amazon_uam_pubid) {
			window.apstagIsInitialized = true;
			//initialize the apstag.js library on the page to allow bidding
			apstag.init({
				pubID: window.bbgiconfig.amazon_uam_pubid, //enter your pub ID here as shown above, it must within quotes
				adServer: 'googletag'
			});
		}
	};





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
                "network": "gmcharlotte-int-0.fyre.co",
                "siteId": "305174",
                "articleId": "201509161917:designer-app-1442429572714"
            })
        });
    });

    jQuery('.live-links--more__btn').attr('href', '/social/');

 } )( this );
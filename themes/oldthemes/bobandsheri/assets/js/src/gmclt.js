var GMCLT = GMCLT || {};

var apiUrl = 'http://api2.bobandsheri.com';

var gmcltStationName = 'BOBANDSHERI';
var gmcltStationID = 3;

 ( function( window, undefined ) {
	'use strict';

    Livefyre.require([
        'streamhub-wall#3',
        'streamhub-sdk#2'
    ], function(LiveMediaWall, SDK) {
        var wall = window.wall = new LiveMediaWall({
            el: document.getElementById("bobandsheri-social-column"),
            initial: 4,
            collection: new (SDK.Collection)({
                "network": "gmcharlotte.fyre.co",
                "siteId": "377346",
                "articleId": "designer-app-1444422907332"
            })
        });
    });

    jQuery('.live-links--more__btn').attr('href', '/social/');

 } )( this );
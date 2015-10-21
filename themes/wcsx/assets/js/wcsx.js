( function( window, undefined ) {
	'use strict';

    Livefyre.require([
        'streamhub-wall#3',
        'streamhub-sdk#2'
    ], function(LiveMediaWall, SDK) {
        var wall = window.wall = new LiveMediaWall({
            el: document.getElementById("wcsx-social-stream"),
            initial: 4,
            collection: new (SDK.Collection)({
                "network": "gmdetroit.fyre.co",
                "siteId": "364561",
                "articleId": "custom-1436386065807"
            })
        });
    });

    jQuery('.live-links--more__btn').attr('href', '/social/');

 } )( this );

/**
 * WMGC
 * http://wordpress.org/themes
 *
 * Copyright (c) 2014 10up
 * Licensed under the GPLv2+ license.
 */
 
 ( function( window, undefined ) {
	'use strict';

    Livefyre.require([
        'streamhub-wall#3',
        'streamhub-sdk#2'
    ], function(LiveMediaWall, SDK) {
        var wall = window.wall = new LiveMediaWall({
            el: document.getElementById("wmgc-social-stream"),
            initial: 4,
            collection: new (SDK.Collection)({
                "network": "gmdetroit.fyre.co",
                "siteId": "364562",
                "articleId": "custom-1432038152149"
            })
        });
    });

    jQuery('.live-links--more__btn').attr('href', '/social/');

 } )( this );
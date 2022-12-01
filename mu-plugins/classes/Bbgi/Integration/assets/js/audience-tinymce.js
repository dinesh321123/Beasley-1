(function() {
    tinymce.PluginManager.add('audience-io-button', function( editor, url ) {
        editor.addButton( 'audience-io-button', {
            text: tinyMCE_object.button_name,
            icon: 'code',
            image : tinyMCE_object.image,
            onclick: function() {
                editor.windowManager.open( {
                    title: tinyMCE_object.button_title,
                    body: [

                        {
                            type   : 'textbox',
                            name   : 'textbox',
                            label  : 'Widget URL',
                            tooltip: 'Audience IO shortcode',
                            value  : ''
                        }
                    ],
                    onsubmit: function( e ) {
                        editor.insertContent( '[audience-promo widget-url="' + e.data.textbox + '" ]');
                    }
                });
            },
        });
    });

})();

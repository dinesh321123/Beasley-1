(function() {
    tinymce.PluginManager.add('newslettersignup-button', function( editor, url ) {
            editor.addButton( 'newslettersignup-button', {
            text: tinyMCE_object_NSF.button_name,
            icon: 'code',
            image : tinyMCE_object_NSF.image,
            onclick: function() {
                editor.windowManager.open( {
                    title: tinyMCE_object_NSF.button_title,
                    body: [

                        {
                            type   : 'textbox',
                            name   : 'label',
                            label  : 'Label',
                            tooltip: 'News letter signup label',                           
                            style: 'width: 300px',
                            value  : tinyMCE_object_NSF.label,
                        },
                        {
                            type   : 'textbox',
                            name   : 'description',
                            label  : 'Description',
                            tooltip: 'News letter signup description',
                            value  : tinyMCE_object_NSF.description
                        },
                        {
                            type   : 'textbox',
                            name   : 'color',
                            label  : 'Text-color',
                            tooltip: 'News letter signup color',
                            value  : tinyMCE_object_NSF.text_color
                        },
                        {
                            type   : 'textbox',
                            name   : 'checkbox_content',
                            label  : 'Terms and Privacy Policy Content',
                            tooltip: 'Terms and Privacy Policy Content',
                            value  : tinyMCE_object_NSF.checkbox_content
                        },
                        {
                            type   : 'textbox',
                            name   : 'logo',
                            label  : 'Logo',
                            tooltip: 'add logo URL',
                            value  : tinyMCE_object_NSF.logo
                        },
                        {
                            type   : 'textbox',
                            name   : 'subscription_attributes',
                            label  : 'Subscription Attributes',
                            tooltip: 'add subscription_attributes',
                            value  : tinyMCE_object_NSF.subscription_attributes
                        },
                        {
                            type   : 'textbox',
                            name   : 'subscription_id',
                            label  : 'Subscription ID',
                            tooltip: 'add subscription ID',
                            value  :  tinyMCE_object_NSF.subscription_id
                        },

                    ],
                    onsubmit: function( e ) {
                        editor.insertContent( '[newsletter-signup label="' + e.data.label + '" description="' + e.data.description + '" color="' + e.data.color + '" checkbox_content="' + e.data.checkbox_content + '" logo="' + e.data.logo + '" subscription_attributes="' + e.data.subscription_attributes + '" subscription_id="' + e.data.subscription_id + '" ]');
                    }
                });
            },
        });
    });

})();

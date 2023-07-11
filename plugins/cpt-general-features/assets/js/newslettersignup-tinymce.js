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
                            label  : 'label',
                            tooltip: 'News letter signup label',
                            value  : 'Join the Family'
                        },
                        {
                            type   : 'textbox',
                            name   : 'description',
                            label  : 'description',
                            tooltip: 'News letter signup description',
                            value  : 'Get Our Latest Articles in Your Inbox'
                        },
                        {
                            type   : 'textbox',
                            name   : 'color',
                            label  : 'color',
                            tooltip: 'News letter signup color',
                            value  : '#000000'
                        },
                        {
                            type   : 'textbox',
                            name   : 'checkbox_content',
                            label  : 'Terms and Condition content',
                            tooltip: 'Terms and Condition content',
                            value  : "By clicking 'Subscribe' I agree to the website\'s terms of Service and Privacy Policy. I understand I can unsubscribe at any time."
                        },
                        {
                            type   : 'textbox',
                            name   : 'logo',
                            label  : 'logo',
                            tooltip: 'add logo URL',
                            value  : tinyMCE_object_NSF.logo
                        },
                        {
                            type   : 'textbox',
                            name   : 'subscription_attributes',
                            label  : 'subscription Attributes',
                            tooltip: 'add subscription_attributes',
                            value  : tinyMCE_object_NSF.subscription_attributes
                        },
                        {
                            type   : 'textbox',
                            name   : 'subscription_id',
                            label  : 'subscription ID',
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

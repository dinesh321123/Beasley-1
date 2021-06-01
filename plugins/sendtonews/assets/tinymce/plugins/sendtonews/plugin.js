/**
 * TinyMCE JavaScript asset.
 *
 * @author STN Video <wpadmin@stnvideo.com>
 * @package SendtoNews
 * @version 1.0.0
 */

( function( $ ) {

    tinymce.create( 'tinymce.plugins.sendtonews', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init: function ( ed, url ) {
            // Register the editor button w/ options
            ed.addButton( 'sendtonews', {
                title: 'Select STN Video Player', // TODO: Localize String - https://benandjacq.com/2015/07/how-to-internationalize-tinymce-plugins-in-wordpress-plugins/
                cmd: 'sendtonews',
                image: url + '/logo.png',
            });

            // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
            ed.addCommand( 'sendtonews', function () {
                let initialState = 'sendtonews-overview';
                if ( localStorage.getItem( 'sendtonews-used-once' ) == "true" ) {
                    initialState = localStorage.getItem( 'sendtonews-modal-tab' );
                }

                localStorage.removeItem( 'sendtonews-selected-player' );

                // Instantiate new STN Video Media Modal.
                frame = new S2N_Modal.frame({

                    // Set unique ID for the modal.
                    id: 'sendtonews-modal-tinymce-' + ed.id,

                    // Set the title of the modal.
                    title: 'STN Video Player Selector', // TODO: SUPPORT I18N

                    // Customize the submit button.
                    button: {
                        // Set the text of the button.
                        text: 'Close', // TODO: SUPPORT I18N
                    },

                    // Indicate the initial state of the modal.
                    state: initialState
                });

                // Open modal frame.
                frame.open();

                // Handle Video Insertion click event, throttle to single action.
                $( '#sendtonews-modal-tinymce-' + ed.id ).one( 'click', '.insert-video', function( e ) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Retrieve type and key data from insert button.
                    let embedType = $( this ).data( 'embedtype' );
                    let embedKey = $( this ).data( 'embedkey' );

                    // Fallback to single video type player.
                    if ( ! embedType )
                    {
                        embedType = 'single';
                    }

                    // Insert Shortcode into the editor instance.
                    if ( embedKey )
                    {
                        ed.insertContent( '[sendtonews key="' + embedKey + '" type="' + embedType + '"]' );
                    } else {
                        alert( 'ERROR: Cannot Embed Video, Missing Video Embed Key.' );
                    }

                    // Cleanup tooltips and close media frame.
                    $(this).tooltip('hide');
                    frame.close();
                });

                // Handle Video Insertion click event, throttle to single action.
                $( '#sendtonews-modal-tinymce-' + ed.id ).one( 'click', '.insert-player', function( e ) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Retrieve type and key data from insert button.
                    let embedType = $( this ).data( 'embedtype' );
                    let embedKey = $( this ).data( 'embedkey' );

                    // Fallback to float video type player.
                    if ( ! embedType )
                    {
                        embedType = 'player';
                    }

                    // Insert Shortcode into the editor instance.
                    if ( embedKey )
                    {
                        ed.insertContent( '[sendtonews key="' + embedKey + '" type="' + embedType + '"]' );
                    } else {
                        alert( 'ERROR: Cannot Embed Video, Missing Video Embed Key.' );
                    }

                    // Cleanup tooltips and close media frame.
                    $(this).tooltip('hide');
                    frame.close();
                });

            });
        }
    });

    // Add S2N TinyMCE Editor Button.
    tinymce.PluginManager.add( 'sendtonews', tinymce.plugins.sendtonews );
} )( jQuery );

/**
 * TinyMCE JavaScript asset.
 *
 * @author Vimeo Video <wpadmin@vvs.com>
 * @package VimeoVideoSelector
 * @version 1.0.1.2
 */
( function( $ ) {
    tinymce.create( 'tinymce.plugins.vimeovideoselector', {
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
            ed.addButton( 'vimeovideoselector', {
                title: 'Select Vimeo Video Player', // TODO: Localize String - https://benandjacq.com/2015/07/how-to-internationalize-tinymce-plugins-in-wordpress-plugins/
                cmd: 'vimeovideoselector',
                image: url + '/logo.png',
            });
            // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
            ed.addCommand( 'vimeovideoselector', function () {
                let initialState = 'vimeovideoselector-vimeo';
                if ( localStorage.getItem( 'vimeovideoselector-used-once' ) == "true" ) {
                    initialState = localStorage.getItem( 'vimeovideoselector-modal-tab' );
                }
                localStorage.removeItem( 'vimeovideoselector-selected-player' );
                // Instantiate new Vimeo Video Media Modal.
                frame = new VVPS_Modal_V.frame({
                    // Set unique ID for the modal.
                    id: 'vimeovideoselector-modal-tinymce-' + ed.id,
                    // Set the title of the modal.
                    title: 'Vimeo Video Player Selector', // TODO: SUPPORT I18N
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
                $( '#vimeovideoselector-modal-tinymce-' + ed.id ).one( 'click', '.insert-video', function( e ) {
                    e.preventDefault();
                    e.stopPropagation();

                    let embedKey = $( this ).data( 'video_id' );
                    if ( embedKey )
                    {
                        ed.insertContent( '[vimeovideoselector width="100%" height="auto" key="' + embedKey + '"]');
                    } else {
                        alert( 'ERROR: Cannot Embed Video, Missing Video Embed Key.' );
                    }
                    frame.close();
                });
            });
        }
    });
    // Add VVPS TinyMCE Editor Button.
    tinymce.PluginManager.add( 'vimeovideoselector', tinymce.plugins.vimeovideoselector );
} )( jQuery );

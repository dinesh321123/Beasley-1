/**
 * Gutenberg block type.
 * STN Video block for gutenberg.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.2
 */

// Player Type Text Mapping for Notices
let playerTextMapping = {
    'single'  : {
        'name'   : wp.i18n.__( 'Single Video Responsive Player', 'stnvideo' ),
        'notice' : wp.i18n.__( 'To avoid logging and issues with ads the above is just a preview and not the actual STN Video player. The actual player will be displayed on the front-end of your website.', 'stnvideo' )
    },
    'float'   : {
        'name'   : wp.i18n.__( 'Single Video Float Player', 'stnvideo' ),
        'notice' : wp.i18n.__( 'To avoid logging and issues with ads the above is just a preview and not the actual STN Video player. The actual player will be displayed on the front-end of your website.', 'stnvideo' )
    },
    'amp'     : {
        'name'   : wp.i18n.__( 'Single Video AMP Player', 'stnvideo' ),
        'notice' : wp.i18n.__( 'WARNING: The AMP embed will only load on pages that support AMP.  To avoid logging and issues with ads the above is just a preview and not the actual STN Video player. The actual player will be displayed on the front-end of your website.', 'stnvideo' )
    },
    'amplist' : {
        'name'   : wp.i18n.__( 'Playlist AMP Player', 'stnvideo' ),
        'notice' : wp.i18n.__( 'WARNING: The AMP embed will only load on pages that support AMP.  To avoid logging and issues with ads the above is just a preview and not the actual STN Video player. The actual player will be displayed on the front-end of your website.', 'stnvideo' )
    },
    'barker' : {
        'name'   : wp.i18n.__( 'Playlist Barker Player', 'stnvideo' ),
        'notice' : wp.i18n.__( 'To avoid logging and issues with ads the above is just a preview and not the actual STN Video player. The actual player will be displayed on the front-end of your website.', 'stnvideo' )
    },
    'full' : {
        'name'   : wp.i18n.__( 'Smart Match Full Player', 'stnvideo' ),
        'notice' : wp.i18n.__( 'Smart Match players work by reading the contents of your page to select videos for your playlist. This feature requires public content so will only function on the front-end of your website. After publishing the Smart Match player on a page, refresh that page to activate the Smart Match functionality. The menu icon in the top right hand corner of the player will turn from white to green after Smart Match has successfully read your page.', 'stnvideo' )
    },
    'player' : {
        'name'   : wp.i18n.__( 'Smart Match Float Player', 'stnvideo' ),
        'notice' : wp.i18n.__( 'Smart Match players work by reading the contents of your page to select videos for your playlist. This feature requires public content so will only function on the front-end of your website. After publishing the Smart Match player on a page, refresh that page to activate the Smart Match functionality. The menu icon in the top right hand corner of the player will turn from white to green after Smart Match has successfully read your page.', 'stnvideo' )
    }
};

// Custom Icon Element.
const s2nIcon = wp.element.createElement(
    'svg',
    {
        id: 'sendtonews-icon',
        className: 'sendtonews-icon',
        viewBox: '0 0 693 685.1',
        height: 8,
        width: 24,
        x: '0px',
        y: '0px',
        style: { enableBackground: 'new 0 0 693 685.1' },
        xmlns: 'http://www.w3.org/2000/svg',
        xmlnsXlink: 'http://www.w3.org/1999/xlink',
        xmlSpace: 'preserve'
    },
    wp.element.createElement(
        'style',
        {},
        '.st0{fill:#FFFFFF;} .st1{fill:#95E5E5;} .st2{fill:#FCAF38;} .st3{fill:#0F3D61;}'
    ),
    wp.element.createElement(
        'g',
        {
            id: 'STN_Video'
        },
        wp.element.createElement(
            'circle',
            {
                className: 'st0',
                cx: '350.5',
                cy: '342.6',
                r: "342.6"
            }
        ),
        wp.element.createElement(
            'g',
            {},
            wp.element.createElement(
                'g',
                {},
                wp.element.createElement(
                    'path',
                    {
                        className: 'st1',
                        d: `M572.3,342.5v0.2c0,1.3,0,2.6-0.1,3.9c-0.1,1.4-0.2,2.8-0.4,4.1c-0.2,1.4-0.4,2.7-0.6,4.1c0,0.1,0,0.2,0,0.3
                            c-0.3,1.3-0.5,2.5-0.8,3.8c-0.5,2-1,4-1.7,5.9c0,0,0,0,0,0c-0.7,2-1.4,3.8-2.3,5.7c-0.3,0.6-0.5,1.3-0.9,1.8
                            c-0.3,0.6-0.6,1.3-0.9,1.8l0,0c-0.3,0.6-0.7,1.2-1,1.8c-0.3,0.6-0.7,1.2-1,1.8c-0.6,1-1.3,2.1-1.9,3.1c-1.7,2.5-3.5,4.8-5.5,7
                            c-0.6,0.7-1.1,1.3-1.8,1.9c-0.4,0.4-0.8,0.8-1.2,1.2c-0.8,0.8-1.5,1.5-2.3,2.2c-0.8,0.7-1.6,1.4-2.5,2.1c-0.8,0.7-1.7,1.4-2.6,2
                            c-0.9,0.7-1.8,1.3-2.7,1.9l-2.3,1.5l-0.8,0.5c-10.7,6.4-23,9.7-35.3,9.7c-13.2,0-26.5-3.8-38.2-11.7l-82.1-55.4l-2-1.4l-2.9-2
                            l-49.9-33.6l-102.4-69.1c-0.1,0-0.1-0.1-0.1-0.1l-4.8-3.2c-31.3-21.1-39.6-63.7-18.5-95c21.1-31.3,63.7-39.6,95-18.5L456.4,228
                            c0,0,0.1,0,0.1,0.1l43.7,29.5l41,27.7c0.2,0.1,0.3,0.2,0.4,0.3c0.2,0.1,0.3,0.2,0.4,0.3c0,0,0,0,0,0c1.2,0.8,2.4,1.7,3.5,2.6
                            c0.2,0.1,0.4,0.3,0.5,0.4c0.8,0.6,1.6,1.3,2.3,1.9c1.3,1,2.5,2.2,3.6,3.3l0.1,0.1c0.9,0.9,1.7,1.8,2.6,2.7c0.3,0.4,0.6,0.8,1,1.1
                            c0.6,0.7,1.2,1.4,1.7,2.1c0.2,0.2,0.4,0.4,0.5,0.6c0.5,0.7,1.1,1.4,1.5,2.1c0.4,0.5,0.7,0.9,1,1.4c0,0,0,0,0,0
                            c1.5,2.3,2.9,4.7,4.2,7.1c0.5,1.1,1.1,2.2,1.6,3.3c0,0,0,0,0,0.1c0.5,1.3,1,2.5,1.5,3.7c0.5,1.3,0.9,2.6,1.3,3.9
                            c0.4,1.3,0.8,2.6,1.1,3.9c0.3,1.3,0.6,2.6,0.8,3.8c0,0.2,0,0.3,0.1,0.5c0.2,1.3,0.4,2.6,0.5,3.8c0.2,1.3,0.3,2.7,0.4,4.1
                            C572.2,339.8,572.3,341.1,572.3,342.5z`
                    }
                )
            ),
            wp.element.createElement(
                'g',
                {},
                wp.element.createElement(
                    'path',
                    {
                        className: 'st2',
                        d: `M573.2,342.5v0.2c0,1.3,0,2.6-0.1,3.9c-0.1,1.4-0.2,2.8-0.4,4.1c-0.2,1.4-0.4,2.7-0.6,4.1c0,0.1,0,0.2,0,0.3
                            c-0.3,1.3-0.5,2.5-0.8,3.8c-1,4-2.3,7.9-4,11.7c-0.5,1.3-1.2,2.5-1.8,3.7l0,0c-0.6,1.2-1.3,2.4-2,3.6c-0.6,1-1.3,2.1-1.9,3.1
                            c-1.7,2.5-3.5,4.8-5.5,7c-1,1.1-1.9,2.1-2.9,3.1c-3.1,3-6.4,5.8-10.1,8.3l-2.3,1.5l-0.8,0.5l-22.3,15.1c0,0,0,0,0,0
                            c0,0,0,0-0.1,0c0,0,0,0-0.1,0v0c0,0-0.1,0.1-0.3,0.2l-78.3,52.8l-9.2,6.2L298.8,564c-11.7,7.9-25.1,11.7-38.2,11.7
                            c-13.7,0-27.3-4.1-38.8-12.1c-0.4-0.3-0.8-0.5-1-0.8c-6.5-4.6-12.3-10.4-17-17.4c-7.4-11-11.2-23.3-11.6-35.7v-6.6
                            c1.3-20.5,11.7-40.3,30.1-52.7l76.6-51.6l2.8-1.9l39.2-26.4l6.1-4.1c0.1,0,0.2-0.1,0.3-0.2l35.2-23.8l0.3-0.2l83.8-56.5
                            c23.6-15.9,53.6-15.1,75.9-0.5c0.3,0.2,0.5,0.3,0.7,0.5c1.2,0.8,2.4,1.7,3.5,2.6c1,0.8,2,1.5,2.9,2.3c1.3,1,2.5,2.2,3.6,3.3
                            l0.1,0.1c1.2,1.2,2.4,2.5,3.5,3.8c0.6,0.7,1.2,1.4,1.7,2.1c0.2,0.2,0.4,0.4,0.5,0.6c0.5,0.7,1.1,1.4,1.5,2.1c0.4,0.5,0.7,1,1,1.5
                            c2.3,3.4,4.2,6.9,5.8,10.5c0.5,1.3,1,2.5,1.5,3.7c0.5,1.3,0.9,2.6,1.3,3.9c0.4,1.3,0.8,2.6,1.1,3.9c0.3,1.3,0.6,2.6,0.8,3.8
                            c0,0.2,0,0.3,0.1,0.5c0.2,1.3,0.4,2.6,0.5,3.8c0.2,1.3,0.3,2.7,0.4,4.1C573.2,339.8,573.2,341.1,573.2,342.5z`
                    }
                )
            ),
            wp.element.createElement(
                'path',
                {
                    className: 'st3',
                    d: `M429.7,475.7L298.8,564c-11.7,7.9-25.1,11.7-38.2,11.7c-13.7,0-27.3-4.1-38.8-12.1c-0.4-0.3-0.8-0.5-1-0.8
                        c-6.5-4.6-12.3-10.4-17-17.4c-7.4-11-11.2-23.3-11.6-35.7V329.7L301.7,397l102.1,62.8c0,0,0.1,0,0.1,0.1l22.8,14.1L429.7,475.7z`
                }
            )
        )
    )
);

wp.blocks.registerBlockType( 'sendtonews/playerselector', {
    /**
     * Block title.
     * @var string
     * @since 0.1.0
     */
    title: wp.i18n.__( 'STN Video Player Selector', 'stnvideo' ),
    /**
     * Block description.
     * @var string
     * @since 0.1.0
     */
    description: wp.i18n.__( 'Select a STN Video Player to embed into the article.', 'stnvideo' ),
    /**
     * Block icon.
     * @var string
     * @since 0.1.0
     */
    icon: s2nIcon,
    /**
     * Block category.
     * @var string
     * @since 0.1.0
     */
    category: 'embed',
    /**
     * Keywords.
     * @var array
     * @since 0.1.0
     */
    keywords: [
        wp.i18n.__( 'stnvideo', 'stnvideo' ),
        wp.i18n.__( 'sendtonews', 'stnvideo' ),
        wp.i18n.__( 'sportstonews', 'stnvideo' ),
        wp.i18n.__( 'stn', 'stnvideo' ),
        wp.i18n.__( 'video', 'stnvideo' ),
        wp.i18n.__( 'media', 'stnvideo' ),
        wp.i18n.__( 'sports', 'stnvideo' ),
    ],
    /**
     * Attributes / properties.
     * @var dictionary
     * @since 0.1.0
     */
    attributes:
    {
        /**
         * Player Name for preview.
         * @since 0.6.0
         * @var string
         */
        embedName:
        {
            type: 'string'
        },
        /**
         * Player Key to display.
         * @since 0.4.0
         * @var string
         */
        embedKey:
        {
            type: 'string'
        },
        /**
         * Player Embed Type to display.
         * @since 0.4.0
         * @var string
         */
        embedType:
        {
            type: 'string'
        },
        /**
         * Player Video for preview.
         * @since 0.6.0
         * @var string
         */
        embedVideo:
        {
            type: 'string'
        },
    },
    /**
     * Returns the editor display block and HTML markup.
     * The "edit" property must be a valid function.
     * @since 0.1.0
     *
     * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
     *
     * @param {object} props
     *
     * @return {object} element
     */
    edit: function( props ) {

        /**
         * TODO: Using props.isSelected when can suppress the input/text/button
         * leaving just the video. Implementing works until you do a replace
         * as the isSelected isn't present on the new element.
         * Reference: https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#isselected
         */

        let invalid        = false;
        let invalidMessage = wp.i18n.__( 'Unknown Player Issue', 'stnvideo' );

        let holderElement = wp.element.createElement(
            'div',
            {
                className: props.className + '-holder',
                key: props.className + '-holder',
            },
            ''
        );

        if ( props.attributes.embedKey && props.attributes.embedType ) {
            let previewPlayer = '';
            let imagePreview  = wp.element.createElement(
                'img',
                {
                    className: 's2n-player-sample-image ' + props.className + '-player-sample-image',
                    key: props.className + '-player-sample-image',
                    src: sendtonews_block_i18n.plugin_url + "/assets/images/player.jpg",
                }
            );

            switch( props.attributes.embedType ) {
                case 'single':
                case 'float':
                case 'amp':
                    if ( props.attributes.embedVideo ) {
                        previewPlayer = wp.element.createElement(
                            'video',
                            {
                                className: 's2n-player-sample-video ' + props.className + '-player-sample-video',
                                key: props.className + '-player-sample-video',
                                src: props.attributes.embedVideo,
                                autoPlay: true,
                                controls: 'controls',
                                crossOrigin: 'anonymous',
                                muted: 'muted',
                                controlsList: 'nodownload',
                                disablePictureInPicture: true,
                                playsInline: true,
                                onContextMenu: e => e.preventDefault(),
                            }
                        );
                    } else {
                        previewPlayer = imagePreview;
                    }
                    break;
                case 'amplist':
                case 'barker':
                case 'full':
                case 'player':
                    previewPlayer = imagePreview;
                    break;
                default:
                    invalid        = true;
                    invalidMessage = wp.i18n.__( 'Invalid Player Type', 'stnvideo' );
                    break;
            }

            if ( ! invalid ) {
                holderElement = wp.element.createElement(
                    'div', {
                        className: props.className + '-holder',
                        key: props.className + '-holder'
                    },
                    wp.element.createElement(
                        'div',
                        {
                            key: props.className + '-invalid-notice'
                        },
                    ),
                    wp.element.createElement(
                        'div',
                        {
                            className: 'notice notice-info inline ' + props.className + '-playername-notice',
                            key: props.className + '-playername-notice',
                        },
                        wp.element.createElement(
                            'h4',
                            {
                                className: props.className + '-playername',
                                key: props.className + '-playername',
                            },
                            (
                                props.attributes.embedName ?
                                props.attributes.embedName :
                                playerTextMapping[props.attributes.embedType]['name']
                            )
                        )
                    ),
                    wp.element.createElement(
                        'div',
                        {
                            className: 's2n-player-sample ' + props.className + '-player-sample',
                            key: props.className + '-player-sample',
                        },
                        previewPlayer
                    ),
                    wp.element.createElement(
                        'div',
                        {
                            className: 'notice notice-warning inline ' + props.className + '-player-notice',
                            key: props.className + '-player-notice',
                        },
                        wp.element.createElement(
                            'p',
                            {
                                className: props.className + '-player-notice-text',
                                key: props.className + '-player-notice-text',
                            },
                            playerTextMapping[props.attributes.embedType]['notice']
                        )
                    )
                );
            }

        } else if ( props.attributes.embedKey ) {
            invalid        = true;
            invalidMessage = wp.i18n.__( 'Missing Player Type', 'stnvideo' );
        } else if ( props.attributes.embedType ) {
            invalid        = true;
            invalidMessage = wp.i18n.__( 'Missing Player Key', 'stnvideo' );
        }

        if ( invalid ) {
            holderElement = wp.element.createElement(
                'div', {
                    className: props.className + '-holder',
                    key: props.className + '-holder'
                },
                wp.element.createElement(
                    'div',
                    {
                        className: 'notice notice-error inline ' + props.className + '-invalid-notice',
                        key: props.className + '-invalid-notice'
                    },
                    invalidMessage
                ),
                wp.element.createElement(
                    'div',
                    {
                        key: props.className + '-playername-notice'
                    },
                    wp.element.createElement(
                        'h4',
                        {
                            key: props.className + '-playername'
                        }
                    )
                ),
                wp.element.createElement(
                    'div',
                    {
                        key: props.className + '-player-sample'
                    }
                ),
                wp.element.createElement(
                    'div',
                    {
                        key: props.className + '-player-notice'
                    },
                    wp.element.createElement(
                        'p',
                        {
                            key: props.className + '-player-notice-text'
                        }
                    )
                )
            );
        }

        return [
            holderElement,
            wp.element.createElement( wp.components.Button, {
                className: props.className + '-button',
                key: props.className + '-button',
                isPrimary: true,
                islarge: 'true',
                style: {width: '100%', justifyContent: 'center', height: 45, fontSize: 15},
                children: (
                    props.attributes.embedKey ?
                    wp.i18n.__( 'Replace Player', 'stnvideo' ) :
                    wp.i18n.__( 'Select Player', 'stnvideo' )
                ),
                onClick: ( event ) => {
                    // Get reference to current block element.
                    let $block = jQuery( '[data-block=' + props.clientId + ']' );
                    localStorage.setItem( 'sendtonews-selected-player', $block.find('.sendtonews-embedName input')[0].defaultValue );

                    let initialState = 'sendtonews-overview';
                    if ( localStorage.getItem( 'sendtonews-selected-player' )[0] == "[" )  {
                        initialState = 'sendtonews-smartmatch';
                    } else if ( localStorage.getItem( 'sendtonews-selected-player' ).length > 0 ) {
                        initialState = 'sendtonews-video-library';
                    } else if ( localStorage.getItem( 'sendtonews-used-once' ) == "true" ) {
                        initialState = localStorage.getItem( 'sendtonews-modal-tab' );
                    }

                    // Instantiate new STN Video Media Modal.
                    frame = new S2N_Modal.frame({

                        // Set unique ID for the modal.
                        id: 'sendtonews-modal-' + props.clientId,

                        // Set the title of the modal.
                        title: wp.i18n.__( 'STN Video Player Selector', 'stnvideo' ),

                        // Customize the action button.
                        button: {
                            // Set the text of the action button.
                            text: wp.i18n.__( 'Close', 'stnvideo' ),

                        },

                        // Indicate the initial state of the modal.
                        state: initialState
                    });

                    // Open modal frame.
                    frame.open();

                    // Handle Video Insertion click event, throttle to single action.
                    jQuery( '#sendtonews-modal-' + props.clientId ).one( 'click', '.insert-video', function( e ) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Retrieve name, type, key and video data from insert video button.
                        let embedName   = jQuery( this ).data( 'embedname' );
                        let embedType   = jQuery( this ).data( 'embedtype' );
                        let embedKey    = jQuery( this ).data( 'embedkey' );
                        let embedVideo  = jQuery( this ).data( 'embedvideo' );

                        // Map input references.
                        let $blockEmbedNameInput  = $block.find( '.sendtonews-embedName input' );
                        let $blockEmbedTypeInput  = $block.find( '.sendtonews-embedType input' );
                        let $blockEmbedKeyInput   = $block.find( '.sendtonews-embedKey input' );
                        let $blockEmbedVideoInput = $block.find( '.sendtonews-embedVideo input' );

                        // Update property attributes.
                        props.setAttributes({
                            embedName:  embedName,
                            embedType:  embedType,
                            embedKey:   embedKey,
                            embedVideo: embedVideo
                        });

                        // Update inputs and trigger change.
                        $blockEmbedNameInput.val( embedName ).trigger( 'change' );
                        $blockEmbedKeyInput.val( embedKey ).trigger( 'change' );
                        $blockEmbedTypeInput.val( embedType ).trigger( 'change' );
                        $blockEmbedVideoInput.val( embedVideo ).trigger( 'change' );

                        // Replace view with player information.
                        $block.find( '.wp-block-sendtonews-playerselector-holder' ).html( '<div class="notice notice-info inline ' + props.className + '-playername-notice"><h4 class="' + props.className + '-playername">' + embedName + '</h4></div>' );

                        // Insert player embed screenshot.
                        $block.find( '.wp-block-sendtonews-playerselector-holder' ).append( '<div class="s2n-player-sample ' + props.className + '-player-sample"><video class="video" autoplay controls="controls" crossorigin="anonymous" muted="muted" controlsList="nodownload" disablePictureInPicture playsinline onContextMenu="return false;"><source src="' + embedVideo + '" /></div>' );

                        // Insert player notice.
                        $block.find( '.wp-block-sendtonews-playerselector-holder' ).append( '<div class="notice notice-warning inline' + props.className + '-player-notice"><p class="' + props.className + '-player-notice-text">' + playerTextMapping[embedType]['notice'] + '</p></div>' );

                        // Update button text to indicate action is to replace.
                        $block.find( '.wp-block-sendtonews-playerselector' ).text( wp.i18n.__( 'Replace Player', 'stnvideo' ) );

                        // Cleanup tooltips and close media frame.
                        jQuery( this ).tooltip('hide');
                        frame.close();
                    });

                    jQuery( '#sendtonews-modal-' + props.clientId ).one( 'click', '.insert-player', function( e ) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Retrieve name, type and key data from insert player button.
                        let embedName = jQuery( this ).data( 'embedname' );
                        let embedType = jQuery( this ).data( 'embedtype' );
                        let embedKey  = jQuery( this ).data( 'embedkey' );

                        // Map input references.
                        let $blockEmbedNameInput  = $block.find( '.sendtonews-embedName input' );
                        let $blockEmbedTypeInput  = $block.find( '.sendtonews-embedType input' );
                        let $blockEmbedKeyInput   = $block.find( '.sendtonews-embedKey input' );
                        let $blockEmbedVideoInput = $block.find( '.sendtonews-embedVideo input' );

                        // Update property attributes.
                        props.setAttributes({
                            embedName:  embedName,
                            embedType:  embedType,
                            embedKey:   embedKey,
                            embedVideo: ''
                        });

                        // Update inputs and trigger change.
                        $blockEmbedNameInput.val( embedName ).trigger( 'change' );
                        $blockEmbedKeyInput.val( embedKey ).trigger( 'change' );
                        $blockEmbedTypeInput.val( embedType ).trigger( 'change' );
                        $blockEmbedVideoInput.val( '' ).trigger( 'change' );

                        // Replace view with player information.
                        $block.find( '.wp-block-sendtonews-playerselector-holder' ).html( '<div class="notice notice-info inline ' + props.className + '-playername-notice"><h4 class="' + props.className + '-playername">' + embedName + '</h4></div>' );

                        // Insert player embed screenshot.
                        $block.find( '.wp-block-sendtonews-playerselector-holder' ).append( '<div class="s2n-player-sample ' + props.className + '-player-sample"><img class="s2n-player-sample-image ' + props.className + '-player-sample-image" src="' + sendtonews_block_i18n.plugin_url + '/assets/images/player.jpg" /></div>' );

                        // Insert player notice.
                        $block.find( '.wp-block-sendtonews-playerselector-holder' ).append( '<div class="notice notice-warning inline' + props.className + '-player-notice"><p class="' + props.className + '-player-notice-text">' + playerTextMapping[embedType]['notice'] + '</p></div>' );

                        // Update button text to indicate action is to replace.
                        $block.find( '.wp-block-sendtonews-playerselector' ).text( wp.i18n.__( 'Replace Player', 'stnvideo' ) );

                        // Cleanup tooltips and close media frame.
                        jQuery( this ).tooltip('hide');
                        frame.close();
                    });

                    jQuery( '#sendtonews-modal-' + props.clientId ).one( 'click', '.insert-player-key-type', function( e ) {
                        e.preventDefault();
                        e.stopPropagation();

                        let type = document.getElementById( "specifiedType" );

                        // Retrieve name, type and key data from insert player button.
                        let embedName = type.options[type.selectedIndex].text + " Video Player";
                        let embedType = document.getElementById( "specifiedType" ).value
                        let embedKey  = document.getElementById( "specifiedKey" ).value

                        // Map input references.
                        let $blockEmbedNameInput  = $block.find( '.sendtonews-embedName input' );
                        let $blockEmbedTypeInput  = $block.find( '.sendtonews-embedType input' );
                        let $blockEmbedKeyInput   = $block.find( '.sendtonews-embedKey input' );
                        let $blockEmbedVideoInput = $block.find( '.sendtonews-embedVideo input' );

                        // Update property attributes.
                        props.setAttributes({
                            embedName:  embedName,
                            embedType:  embedType,
                            embedKey:   embedKey,
                            embedVideo: ''
                        });

                        // Update inputs and trigger change.
                        $blockEmbedNameInput.val( embedName ).trigger( 'change' );
                        $blockEmbedKeyInput.val( embedKey ).trigger( 'change' );
                        $blockEmbedTypeInput.val( embedType ).trigger( 'change' );
                        $blockEmbedVideoInput.val( '' ).trigger( 'change' );

                        // Replace view with player information.
                        $block.find( '.wp-block-sendtonews-playerselector-holder' ).html( '<div class="notice notice-info inline ' + props.className + '-playername-notice"><h4 class="' + props.className + '-playername">' + embedName + '</h4></div>' );

                        // Insert player embed screenshot.
                        $block.find( '.wp-block-sendtonews-playerselector-holder' ).append( '<div class="s2n-player-sample ' + props.className + '-player-sample"><img class="s2n-player-sample-image ' + props.className + '-player-sample-image" src="' + sendtonews_block_i18n.plugin_url + '/assets/images/player.jpg" /></div>' );

                        // Insert player notice.
                        if ( typeof playerTextMapping[embedType] !== 'undefined' ) {
                            $block.find( '.wp-block-sendtonews-playerselector-holder' ).append( '<div class="notice notice-warning inline' + props.className + '-player-notice"><p class="' + props.className + '-player-notice-text">' + playerTextMapping[embedType]['notice'] + '</p></div>' );
                        }

                        // Update button text to indicate action is to replace.
                        $block.find( '.wp-block-sendtonews-playerselector' ).text( wp.i18n.__( 'Replace Player', 'stnvideo' ) );

                        // Cleanup tooltips and close media frame.
                        jQuery( this ).tooltip( 'hide' );
                        frame.close();

                        // Update the key value in player key input field
                        document.getElementById('sendtonews-embedKey-' + props.clientId).value = embedKey;
                    });
                }
            }),
            wp.element.createElement( wp.components.TextControl, {
              label: wp.i18n.__( 'Player Name', 'stnvideo' ),
              hideLabelFromVision: true,
              className: 'sendtonews-embedName',
              key: 'sendtonews-embedName',
              type: 'hidden',
              value: ( props.attributes.embedName ? props.attributes.embedName : '' ),
            }),
            wp.element.createElement( wp.components.TextControl, {
              label: wp.i18n.__( 'Player Video', 'stnvideo' ),
              hideLabelFromVision: true,
              className: 'sendtonews-embedVideo',
              key: props.attributes.embedKey + '-sendtonews-embedVideo',
              type: 'hidden',
              value: ( props.attributes.embedVideo ? props.attributes.embedVideo : '' ),
            }),
        ];

    },
    /**
     * Returns the HTML markup that will be rendered in live post.
     * The "save" property must be a valid function.
     * @since 0.1.0
     *
     * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
     *
     * @param {object} props
     *
     * @return {object} element
     */
    save: function( props ) {

        return wp.element.createElement(
            'div',
            {
                className: props.className,
            },
            (
                props.attributes.embedKey ?
                '[sendtonews key="' + props.attributes.embedKey + '" type="' + ( props.attributes.embedType ? props.attributes.embedType : 'single' ) + '"]' :
                '[sendtonews]'
            )
        );
    },
} );

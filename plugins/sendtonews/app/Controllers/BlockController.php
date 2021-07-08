<?php

namespace SendtoNews\Controllers;

use WPMVC\MVC\Controller;
use SendtoNews\Models\Settings;

/**
 * BlockController controller to handle the STN Video block and methods.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.1
 */
class BlockController extends Controller
{
    /**
     * Renders the STN Video block.
     * @since 0.1.0
     * @since 0.1.2 Use S2N_PLAYER_SELECTOR_URL constant.
     *
     * @global SendtoNews\Main $sendtonews
     */
    public function register()
    {
    	// Main app object
    	global $sendtonews;

        // Get version.
        $version = $sendtonews->config->get( 'version' );

        // Confirm API verification.
        $verified = $sendtonews->{ '_c_return_AdminController@verified' }();

        // Default Block to unverified version.
        $block_script = S2N_PLAYER_SELECTOR_URL . '/assets/blocks/sendtonews/editorverify.js';
        $block_script_dependencies = [
            'underscore',
            'backbone',
            'wp-blocks',
            'wp-element',
            'wp-i18n',
        ];
        $block_style_dependencies = [
            'wp-edit-blocks',
        ];

        if ( $verified )
        {
            // Set Block to verified version.
            $block_script = S2N_PLAYER_SELECTOR_URL . '/assets/blocks/sendtonews/editor.js';
            $block_script_dependencies = [
                'jquery',
                'jquery-ui-tooltip',
                'underscore',
                'backbone',
                'wp-blocks',
                'wp-element',
                'wp-i18n',
                'media-widgets',
                'sendtonews-oneui-bootstrap',
                'sendtonews-oneui-simplebar',
                'sendtonews-oneui-scrolllock',
                'sendtonews-oneui-jqappear',
                'sendtonews-oneui-jscookie',
                'sendtonews-oneui-app',
                'sendtonews-plugins',
                'sendtonews-utils',
                'sendtonews-stories',
                'sendtonews-loader',
                'sendtonews-modal',
            ];
            $block_style_dependencies = [
                'wp-edit-blocks',
                'sendtonews-oneui',
                'sendtonews-stn',
                'sendtonews-stories',
                'sendtonews-plugins'
            ];
        }

    	// Register block script.
        wp_register_script(
            'sendtonews-block',
            $block_script,
            $block_script_dependencies,
            $version,
            true
        );

        // Internationalization (i18n) of Block script.
        // @link https://pascalbirchler.com/internationalization-in-wordpress-5-0/
        wp_set_script_translations( 'sendtonews-block', 'stnvideo' );

        // Default CID to empty in case Settings unavailable.
        $cid = '';

        // Retrieve settings.
        $settings = Settings::find();

        // Check settings exist.
        if ( ! empty( $settings ) )
        {
            // Retrieve Company ID from settings.
            $cid = $settings->cid;
        }

        // Pass the Company ID and Plugin URL through to the block editor.
        wp_localize_script( 'sendtonews-block', 'sendtonews_block_i18n', array(
            'cid'        => $cid,
            'plugin_url' => S2N_PLAYER_SELECTOR_URL,
        ) );

        // Register block stylesheet.
        wp_register_style(
            'sendtonews-block',
            S2N_PLAYER_SELECTOR_URL . '/assets/blocks/sendtonews/editor.css',
            $block_style_dependencies,
            $version
        );

        // Register sendtonews block
        register_block_type( 'sendtonews/playerselector', [
            'editor_script' => 'sendtonews-block',
            'editor_style'  => 'sendtonews-block',
        ] );
    }
}

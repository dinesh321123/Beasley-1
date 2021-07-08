<?php

namespace SendtoNews\Controllers;

use WPMVC\MVC\Controller;
use SendtoNews\Models\Settings;

/**
 * ModalController controller to handle the STN Video Player Selector popup modal.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.1
 */
class ModalController extends Controller
{
    /**
     * Passes a toggle via localize to enable the Video Library view.
     * @since 0.6.0
     *
     * @hook admin_enqueue_scripts
     */
    public function enable_library()
    {
        // Use localize to enable the Video Library.
        wp_localize_script( 'sendtonews-modal', 'sendtonews_model_i18n', array( 'enable_library' => true ) );
    }

    /**
     * Enqueues the necessary modal scripts/styles.
     * @since 0.2.0
     * @since 0.3.0 Replaced Thickbox completely with the Media Modal.
     *
     * @hook admin_enqueue_scripts
     */
    public function enqueue()
    {
        // Default CID & AUTH to empty in case Settings unavailable.
        $cid      = '';
        $authcode = '';

        // Retrieve settings.
        $settings = Settings::find();

        // Check settings exist.
        if ( ! empty( $settings ) )
        {
            // Retrieve Company ID & AUTHCODE from settings.
            $cid      = $settings->cid;
            $authcode = $settings->authcode;
        }

        // Enqueue media scripts and widgets.
        wp_enqueue_media();
        wp_enqueue_script( 'media-widgets' );

        // Enqueue custom styles.
        wp_enqueue_style( 'sendtonews-oneui' );
        wp_enqueue_style( 'sendtonews-stn' );
        wp_enqueue_style( 'sendtonews-stories' );
        wp_enqueue_style( 'sendtonews-plugins' );

        // Enqueue custom scripts.
        wp_enqueue_script( 'sendtonews-oneui-bootstrap' );
        wp_enqueue_script( 'sendtonews-oneui-simplebar' );
        wp_enqueue_script( 'sendtonews-oneui-scrolllock' );
        wp_enqueue_script( 'sendtonews-oneui-jqappear' );
        wp_enqueue_script( 'sendtonews-oneui-jscookie' );
        wp_enqueue_script( 'sendtonews-oneui-app' );
        wp_enqueue_script( 'sendtonews-plugins' );
        wp_enqueue_script( 'sendtonews-utils' );
        wp_enqueue_script( 'sendtonews-stories' );
        wp_enqueue_script( 'sendtonews-loader' );
        wp_enqueue_script( 'sendtonews-modal' );

        /*********************
         * Localize Custom Scripts
         *
         * Pass parameters to the scripts.
         *********************/

        // Pass CID & AUTHCODE to the utils script for use with the API.
        wp_localize_script( 'sendtonews-utils', 'sendtonews_utils_i18n', array(
            'cid'      => $cid,
            'authcode' => $authcode,
            'api_url'  => S2N_API_URL,
        ) );

        // Pass plugin path for noThumbnail image.
        wp_localize_script( 'sendtonews-stories', 'sendtonews_stories_i18n', array(
            'plugin_url' => S2N_PLAYER_SELECTOR_URL,
        ) );

        // Pass CID & AUTHCODE to the loader script for use with the API.
        wp_localize_script( 'sendtonews-loader', 'sendtonews_loader_i18n', array(
            'cid'          => $cid,
            'authcode'     => $authcode,
            'api_url'      => S2N_API_URL
        ) );
    }

    /**
     * Renders the overview template.
     * @since 0.3.0
     *
     * @hook admin_footer-{context}
     */
    public function render_overview_template()
    {
        // Render the popup modal skeleton for the Overview tab.
        $this->view->show( 'admin.modals.overview', [ 'enable_library' => true ] );
    }

    /**
     * Renders the Smart Match template.
     * @since 0.3.0
     *
     * @hook admin_footer-{context}
     */
    public function render_smartmatch_template()
    {
        // Render the popup modal skeleton for the Smart Match Player tab.
        $this->view->show( 'admin.modals.smartmatch' );
    }

    /**
     * Renders the help template.
     * @since 0.3.0
     *
     * @hook admin_footer-{context}
     */
    public function render_help_template()
    {
        // Render the popup modal skeleton for the Help tab.
        $this->view->show( 'admin.modals.help' );
    }

    /**
     * Renders the video library template.
     * @since 0.3.0
     *
     * @hook admin_footer-{context}
     */
    public function render_library_template()
    {
        // Render the popup modal skeleton for the Video Library tab.
        $this->view->show( 'admin.modals.stories' );
    }

}

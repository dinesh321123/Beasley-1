<?php

namespace SendtoNews\Controllers;

use WPMVC\Log;
use WPMVC\Request;
use WPMVC\MVC\Controller;
use SendtoNews\Models\Settings;

/**
 * AdminController controller to handle admin settings and methods.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.1
 */
class AdminController extends Controller
{
    /**
     * Admin menu settings key.
     * @since 0.1.0
     * @var string
     */
    const ADMIN_MENU_SETTINGS = 'sendtonews-settings';

    /**
     * Register the Settings menu page.
     * @since 0.1.0
     */
    public function menu()
    {
        add_submenu_page(
            'options-general.php',
            __( 'STN Video Settings', 'stnvideo' ),
            __( 'STN Video', 'stnvideo' ),
            'manage_options',
            self::ADMIN_MENU_SETTINGS,
            [ &$this, 'view_settings' ]
        );
    }

    /**
     * Adds the settings link to the plugin action links.
     * @since 0.1.0
     *
     * @filter plugin_action_links_[basename]
     *
     * @param array $links Plugin action links.
     *
     * @return array Plugin action links.
     */
    public function plugin_links( $links = [] )
    {
        return array_merge( [
            '<a href="'
                . admin_url( 'options-general.php?page=' . self::ADMIN_MENU_SETTINGS )
                . '">'
                . __( 'Settings' )
                . '</a>'
        ], $links );
    }

    /**
     * Display the Settings menu page.
     * @since 0.1.0
     *
     * @global SendtoNews\Main $sendtonews
     */
    public function view_settings()
    {
        global $sendtonews;

        // Provide an enqueue hook for Settings.
        do_action( 'sendtonews_settings_enqueue' );

        // Render Settings view.
        $settings = Settings::find();
        $this->view->show(
            'admin.forms.settings',
            [
                'notice'   => $sendtonews->message,
                'error'    => Request::input( 'error' ),
                'settings' => $settings,
            ]
        );
    }

    /**
     * Auth settings verification.
     * @since 0.4.0
     * @since 0.9.0 Saved boolean flag to the db for auth handshake.
     *
     * @return boolean Flag indicating authentication was verified.
     */
    public function verify()
    {
        // Retrieve Settings Data.
        $settings = Settings::find();

        if ( empty( $settings ) )
        {
            delete_option( 'sendtonews_auth_verified' );
            return false;
        }

        // Retrieve CID and AUTHCODE from settings.
        $cid      = $settings->cid;
        $authcode = $settings->authcode;

        if ( empty( $cid ) || empty( $authcode ) )
        {
            delete_option( 'sendtonews_auth_verified' );
            return false;
        }

        // AUTH API verification.
        $response = wp_remote_post( S2N_API_URL . 'auth/verifycode', array(
            'method' => 'POST',
            'body'   => array(
                'cid'      => $cid,
                'authcode' => $authcode,
            ),
        ));

        if ( is_wp_error( $response ) )
        {
            delete_option( 'sendtonews_auth_verified' );
            return false;
        }
        else
        {
            // Retrieve remote response body.
            $response_body = wp_remote_retrieve_body( $response );
            if ( empty( $response_body ) )
            {
                delete_option( 'sendtonews_auth_verified' );
                return false;
            }

            // JSON decode response body.
            $response_body = json_decode( $response_body );

            if ( $response_body->success )
            {
                update_option( 'sendtonews_auth_verified', true, true );
                return true;
            }
            else
            {
                delete_option( 'sendtonews_auth_verified' );
                return false;
            }
        }
    }

    /**
     * Retrieve the Auth verification status.
     * @since 0.9.0
     *
     * @return boolean Flag indicating authentication was verified.
     */
    public function verified()
    {
        return get_option( 'sendtonews_auth_verified', false );
    }

    /**
     * Displays notice if Auth verify fails.
     * @since 0.1.1
     */
    public function notice()
    {
        // Display plugin authentication notice.
        $this->view->show( 'admin.notices.verify' );
    }

    /**
     * Saves settings.
     * @since 0.1.0
     *
     * @global SendtoNews\Main $sendtonews
     *
     * @return boolean Flag indicating save operation success.
     */
    public function save()
    {
        // Check the admin referrer / verify the nonce.
        if (
            isset( $_POST['sendtonews_settings_nonce'] ) &&
            wp_verify_nonce( $_POST['sendtonews_settings_nonce'], 'save_sendtonews_settings' )
        )
        {
            global $sendtonews;

            // Retrieve settings data and request data.
            $model    = Settings::find();
            $notice   = Request::input( 'notice' );
            $cid      = Request::input( 'cid' );
            $authcode = Request::input( 'authcode' );

            // Check if action already complete.
            if ( ! empty( $notice ) ) return $notice;

            if ( $_POST )
            {
                try
                {
                    // Save settings.
                    $model->cid      = $cid ? $cid : '';
                    $model->authcode = $authcode ? $authcode : '';

                    // Filter model object before save.
                    $model = apply_filters( 'sendtonews_settings_before_save', $model );

                    // Save model.
                    $model->save();

                    // Clear model.
                    //$model->clear();

                    // Trigger verify action on save.
                    $this->verify();

                    // Return success notice.
                    $sendtonews->message = __( 'Settings saved.', 'stnvideo' );

                }
                catch (Exception $e)
                {
                    Log::error($e);
                }
            }
        }
    }
}

<?php

namespace VimeoVideoSelector\Controllers;

require_once( __DIR__ . '/../Models/Settings.php' );
use Vimeo\Vimeo;
use WPMVCVVS\Request;
use WPMVCVVS\MVC\Controller;
use VimeoVideoSelector\Models\Settings;

/**
 * AdminController controller to handle admin settings and methods.
 *
 * @author Vimeo Video
 * @copyright Vimeo Video <https://www.vvs.com>
 * @package VimeoVideoSelector
 * @version 1.0.1.2
 */
class AdminController extends Controller
{
    /**
     * Admin menu settings key.
     * @since 0.1.0
     * @var string
     */
    const ADMIN_MENU_SETTINGS = 'vimeovideoselector-settings';

    /**
     * Register the Settings menu page.
     * @since 0.1.0
     */
    public function menu()
    {
        // add_submenu_page(
        //     'options-general.php',
        //     __( 'Vimeo Video Settings', 'vvs' ),
        //     __( 'Vimeo Video', 'vvs' ),
        //     'manage_options',
        //     self::ADMIN_MENU_SETTINGS,
        //     [ &$this, 'view_settings' ]
        // );
        add_menu_page(
            __( 'Vimeo Video Settings', 'vvs' ),
            __( 'Vimeo Video', 'vvs' ),
            'manage_options',
            self::ADMIN_MENU_SETTINGS,
            [ &$this, 'view_settings' ],
            '', // Icon
            100 // Position of the menu item in the menu.
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
    public function settings_enqueue()
    {
        wp_enqueue_script( 'chosen_script', plugin_dir_url('vimeovideoselector') . 'vimeovideoselector/assets/js/chosen.jquery.min.js', array('jquery'), '1.8.7' );
        wp_enqueue_style( 'chosen_style', plugin_dir_url('vimeovideoselector') . 'vimeovideoselector/assets/css/chosen.min.css', array(), '1.8.7' );
    }

    /**
     * Display the Settings menu page.
     * @since 0.1.0
     * 
     * @global VimeoVideoSelector\Main $vimeovideoselector
     */
    public function view_settings()
    {
        global $vimeovideoselector;

        // Provide an enqueue hook for Settings.
        do_action( 'vimeovideoselector_settings_enqueue' );

        // Render Settings view.
        $settings = Settings::find();
        $this->view->show(
            'admin.forms.settings',
            [
                'notice'   => $vimeovideoselector->message,
                'error'    => $vimeovideoselector->error,
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
    public function verify($client_id,$client_secret)
    {
        global $vimeovideoselector;
       
        if ( empty( $client_id ) || empty( $client_secret ) )
        {
            delete_site_option( 'vimeovideoselector_auth_verified' );
            return false;
        }

        $vimeo = new Vimeo($client_id,$client_secret);
    
        // AUTH API verification.
        $response = $vimeo->clientCredentials();

        if ( is_wp_error( $response ) )
        {
            delete_site_option( 'vimeovideoselector_auth_verified' );
            return false;
        }
        else
        {
            // Retrieve remote response body.
            $response_body = wp_remote_retrieve_body( $response );
            if ( empty( $response_body ) || !empty($response_body['error']))
            {
                $vimeovideoselector->error =  __($response_body['developer_message'], 'vvs' );
                delete_site_option( 'vimeovideoselector_auth_verified' );
                return false;
            }
            else
            {
                update_site_option( 'vimeovideoselector_auth_verified', true, true );
                // Return success notice.
                $vimeovideoselector->message = __( 'Settings saved.', 'vvs' );
                return $response_body['access_token'];
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
        return get_site_option( 'vimeovideoselector_auth_verified', false );
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
     * @global VimeoVideoSelector\Main $vimeovideoselector
     * 
     * @return boolean Flag indicating save operation success.
     */
    public function save()
    {
        // Check the admin referrer / verify the nonce.
        if (isset( $_POST['vimeovideoselector_settings_nonce'] ) && wp_verify_nonce( $_POST['vimeovideoselector_settings_nonce'], 'save_vimeovideoselector_settings' ))
        {
            // Retrieve settings data and request data.
            $model    = Settings::find();
            $notice   = Request::input( 'notice' );
            $error   = Request::input( 'error' );
            $client_id      = Request::input( 'client_id' );
            $client_secret = Request::input( 'client_secret' );
            $vvs_is_active = $_POST['vvs_is_active'];
            $channel = Request::input( 'channel' );

            // Check if action already complete.
            if ( ! empty( $notice ) ) return $notice;
            if ( ! empty( $error ) ) return $error;

            if ( $_POST )
            {
                try
                {
                    // Save settings.
                    $model->client_id      = $client_id ? $client_id : '';
                    $model->client_secret = $client_secret ? $client_secret : '';
                    $model->channel = $channel ? $channel : '';
                    $model->vvs_is_active = $vvs_is_active ? $vvs_is_active : '';

                    // Trigger verify action on save.
                    $access_token = $this->verify($client_id,$client_secret);
                    $model->access_token = $access_token ? $access_token : '';

                    // Filter model object before save.
                    $model = apply_filters( 'vimeovideoselector_settings_before_save', $model );

                    // Save model.
                    $model->save();

                    // Clear model.
                    //$model->clear();
                }
                catch (Exception $e)
                {
                    
                }
            }
        }
    }
}
<?php

namespace SendtoNews;

use WPMVC\Bridge;
use SendtoNews\Models\Settings;

/**
 * Main class.
 * Bridge between WordPress and STN Video.
 * Class contains declaration of hooks and filters.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.2
 */
class Main extends Bridge
{
    /**
     * Declaration of public WordPress hooks.
     * @since 0.1.0
     * @since 0.1.1 Introduced oEmbed Support.
     * @since 1.0.1 Suppress undesired calls from triggering the plugin.
     */
    public function init()
    {
        if (
            ( defined( 'DOING_CRON' )    && DOING_CRON )    || // Avoid Cron calls.
            ( defined( 'WP_CLI' )        && WP_CLI )        || // Avoid WP-CLI calls
            ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) || // Avoid wp-install calls.
            ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) // Avoid Cron calls.
        )
        {
            // Bail on undesired calls.
            return;
        }

        // Check for old STN Video oEmbed plugin.
        if ( ! function_exists( 's2n_oembed_provider' ) )
        {
            // Register oEmbed provider.
            $this->add_action( 'init', 'oEmbedController@register' );
        }
        else
        {
            // Display notice to indicate old plugin can be deactivated.
            $this->add_action( 'admin_notices', 'oEmbedController@notice' );
        }

        // Register [sendtonews] shortcode.
        // @TODO: Migrate to [stnvideo] shortcode.
        //$this->add_shortcode( 'sendtonews', 'ShortcodeController@show' );

        // Register SendtoNews widget.
        // @TODO: Migrate to STNVideo widget.
        $this->add_widget( 'SendtoNews' );

        // Register SendtoNews block.
        // @TODO: Migrate to STNVideo block.
        if ( function_exists( 'register_block_type' ) )
        {
            $this->add_action( 'init', 'BlockController@register' );
        }
    }

    /**
     * Declaration of admin only WordPress hooks.
     * For WordPress admin dashboard.
     * @since 0.1.0
     * @since 0.1.1 Use S2N_PLAYER_SELECTOR_BASENAME for plugin_basename.
     * @since 0.4.0 Use API Verification Method.
     * @since 0.9.0 Update API Verification to throttle requests.
     * @since 1.0.1 Suppress WP-CLI calls from triggering the plugin.
     *
     * @global SendtoNews\Main $sendtonews
     */
    public function on_admin()
    {
        if (
            ( defined( 'DOING_CRON' )    && DOING_CRON )    || // Avoid Cron calls.
            ( defined( 'WP_CLI' )        && WP_CLI )        || // Avoid WP-CLI calls
            ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) || // Avoid wp-install calls.
            ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) || // Avoid Cron calls.
            ( ! is_user_logged_in() ) // Avoid Non-logged-in calls.
        )
        {
            // Bail on undesired calls.
            return;
        }

        global $sendtonews;

        // Save the settings menu if there's any request information.
        $sendtonews->{ '_c_void_AdminController@save' }();

        // Register the 'Settings' menu item for STN Video.
        $this->add_action( 'admin_menu', 'AdminController@menu' );

        // Add 'Settings' link to the Plugins listing.
        $this->add_filter( 'plugin_action_links_' . S2N_PLAYER_SELECTOR_BASENAME, 'AdminController@plugin_links' );

        // Handle plugin verification.
        $verified = $sendtonews->{ '_c_return_AdminController@verified' }();

        if ( ! $verified )
        {
            // Check to verify the API authenticaion.
            $verified = $sendtonews->{ '_c_void_AdminController@verify' }();
        }

        if ( $verified )
        {
            // Plugin version used to register custom scripts.
            $version = $this->config->get( 'version' );

            // Register custom styles.
            $this->add_asset( 'css/oneui.css', false, [], 'all', true, $version, 'sendtonews-oneui' );
            $this->add_asset( 'css/sendtonews.css', false, [], 'all', true, $version, 'sendtonews-stn' );
            $this->add_asset( 'css/stories.css', false, [], 'all', true, $version, 'sendtonews-stories' );
            $this->add_asset( 'css/jqplugins.css', false, [], 'all', true, $version, 'sendtonews-plugins' );

            // Register vendor scripts.
            $this->add_asset( 'js/vendor/bootstrap.bundle.min.js', false, ['jquery','jquery-ui-tooltip'], true, true, $version, 'sendtonews-oneui-bootstrap' );
            $this->add_asset( 'js/vendor/simplebar.min.js', false, ['jquery','jquery-ui-tooltip'], true, true, $version, 'sendtonews-oneui-simplebar' );
            $this->add_asset( 'js/vendor/jquery-scrollLock.min.js', false, ['jquery','jquery-ui-tooltip'], true, true, $version, 'sendtonews-oneui-scrolllock' );
            $this->add_asset( 'js/vendor/jquery.appear.min.js', false, ['jquery','jquery-ui-tooltip'], true, true, $version, 'sendtonews-oneui-jqappear' );
            $this->add_asset( 'js/vendor/js.cookie.min.js', false, ['jquery','jquery-ui-tooltip'], true, true, $version, 'sendtonews-oneui-jscookie' );
            $this->add_asset( 'js/vendor/oneui.app.min.js', false, ['jquery','jquery-ui-tooltip', 'sendtonews-oneui-bootstrap', 'sendtonews-oneui-simplebar', 'sendtonews-oneui-scrolllock', 'sendtonews-oneui-jqappear', 'sendtonews-oneui-jscookie'], true, true, $version, 'sendtonews-oneui-app' );

            // Register custom scripts.
            $this->add_asset( 'js/plugins.js', false, ['jquery','jquery-ui-tooltip'], true, true, $version, 'sendtonews-plugins' );
            $this->add_asset( 'js/utils.js', false, ['jquery','jquery-ui-tooltip'], true, true, $version, 'sendtonews-utils' );
            $this->add_asset( 'js/stories.js', false, ['jquery','jquery-ui-tooltip'], true, true, $version, 'sendtonews-stories' );
            $this->add_asset( 'js/loader.js', false, ['jquery', 'jquery-ui-tooltip', 'sendtonews-plugins'], true, true, $version, 'sendtonews-loader' );
            $this->add_asset( 'js/modal.js', false, ['jquery','jquery-ui-tooltip'], true, true, $version, 'sendtonews-modal' );

            // Only setup TinyMCE plugin if user has edit permissions and supports Rich Editing (WYSIWYG).
            if ( ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) && user_can_richedit() )
            {
                // Register the TinyMCE plugin.
                $this->add_filter( 'mce_external_plugins', 'TinyMCEController@register_plugin' );

                // Add the TinyMCE button.
                $this->add_filter( 'mce_buttons', 'TinyMCEController@add_button' );
            }

            // Enable the Video Library on the post editor.
            $this->add_action( 'admin_print_scripts-post.php', 'ModalController@enable_library' );
            $this->add_action( 'admin_print_scripts-post-new.php', 'ModalController@enable_library' );

            // Enqueue media modal scripts for editors.
            $this->add_action( 'admin_print_scripts-post.php', 'ModalController@enqueue', 20 );
            $this->add_action( 'admin_print_scripts-post-new.php', 'ModalController@enqueue', 20 );

            // Render the Overview Modal Template for post editing (TinyMCE & Block).
            $this->add_action( 'admin_footer-post.php', 'ModalController@render_overview_template' );
            $this->add_action( 'admin_footer-post-new.php', 'ModalController@render_overview_template' );

            // Render the SmartMatch Modal Template for post editing (TinyMCE & Block).
            $this->add_action( 'admin_footer-post.php', 'ModalController@render_smartmatch_template' );
            $this->add_action( 'admin_footer-post-new.php', 'ModalController@render_smartmatch_template' );

            // Render the Help Modal Template for post editing (TinyMCE & Block).
            $this->add_action( 'admin_footer-post.php', 'ModalController@render_help_template' );
            $this->add_action( 'admin_footer-post-new.php', 'ModalController@render_help_template' );

            // Render the Video Library Modal Template for post editing (TinyMCE & Block).
            $this->add_action( 'admin_footer-post.php', 'ModalController@render_library_template' );
            $this->add_action( 'admin_footer-post-new.php', 'ModalController@render_library_template' );
        }
        else
        {
            if ( current_user_can( 'manage_options' ) )
            {
                // Display notice to indicate failed verification.
                $this->add_action( 'admin_notices', 'AdminController@notice' );
            }
        }
    }
}

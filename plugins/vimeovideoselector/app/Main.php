<?php

namespace VimeoVideoSelector;

use WPMVCVVS\Bridge;
use VimeoVideoSelector\Models\Settings;

/**
 * Main class.
 * Bridge between WordPress and Vimeo Video.
 * Class contains declaration of hooks and filters.
 *
 * @author Vimeo Video
 * @copyright Vimeo Video <https://www.vvs.com>
 * @package VimeoVideoSelector
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

        // Register [vimeovideoselector] shortcode.
        // @TODO: Migrate to [vvs] shortcode.
        $this->add_shortcode( 'vimeovideoselector', 'ShortcodeController@show' );
    }

    /**
     * Declaration of admin only WordPress hooks.
     * For WordPress admin dashboard.
     * @since 0.1.0
     * @since 0.1.1 Use VVPS_PLAYER_SELECTOR_BASENAME for plugin_basename.
     * @since 0.4.0 Use API Verification Method.
     * @since 0.9.0 Update API Verification to throttle requests.
     * @since 1.0.1 Suppress WP-CLI calls from triggering the plugin.
     *
     * @global VimeoVideoSelector\Main $vimeovideoselector
     */
    public function console_log($output, $with_script_tags = true) {
    	$js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
		if ($with_script_tags) {
			$js_code = '<script>' . $js_code . '</script>';
		}
			echo $js_code;
	}

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

        global $vimeovideoselector;

        // Save the settings menu if there's any request information.
        $vimeovideoselector->{ '_c_void_AdminController@save' }();

        // Register the 'Settings' menu item for Vimeo Video.
        if ( ! is_multisite() ) {
            $this->add_action( 'admin_menu', 'AdminController@menu' );
            // Add 'Settings' link to the Plugins listing.
            $this->add_filter( 'plugin_action_links_' . VVPS_PLAYER_SELECTOR_BASENAME, 'AdminController@plugin_links' );
        }
        else{
            $this->add_action( 'network_admin_menu', 'AdminController@menu' );
        }
        $this->add_action( 'vimeovideoselector_settings_enqueue', 'AdminController@settings_enqueue' );


        // Handle plugin verification.

        $settings = Settings::find();
        // $this->console_log(get_current_blog_id());
        // $this->console_log($settings->vvs_is_active);

        if(!empty( $settings ) && (in_array(get_current_blog_id(), $settings->vvs_is_active))){
            $verified = $vimeovideoselector->{ '_c_return_AdminController@verified' }();
            if ( $verified )
            {
                // Plugin version used to register custom scripts.
                $version = $this->config->get( 'version' );

                // Register custom styles.
                $this->add_asset( 'css/vimeovideoselector.css', false, [], 'all', true, $version, 'vimeovideoselector-stn' );
                $this->add_asset( 'css/stories.css', false, [], 'all', true, $version, 'vimeovideoselector-stories' );

               // Register custom scripts.
                $this->add_asset( 'js/modal.js', false, ['jquery','jquery-ui-tooltip'], true, true, $version, 'vimeovideoselector-modal' );

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

                // Render the Vimeo Modal Template for post editing (TinyMCE & Block).
                $this->add_action( 'admin_footer-post.php', 'ModalController@render_vimeo_template' );
                $this->add_action( 'admin_footer-post-new.php', 'ModalController@render_vimeo_template' );

                // Render the Help Modal Template for post editing (TinyMCE & Block).
                $this->add_action( 'admin_footer-post.php', 'ModalController@render_help_template' );
                $this->add_action( 'admin_footer-post-new.php', 'ModalController@render_help_template' );

                // Ajax for vimeo videos search and pagination.
                $this->add_action( 'wp_ajax_vimeo_action', 'ModalController@render_vimeo_search' );
                //$this->add_action( 'wp_ajax_nopriv_vimeo_action', 'ModalController@render_vimeo_search' );
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
}

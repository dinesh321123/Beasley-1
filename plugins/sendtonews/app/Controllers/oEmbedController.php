<?php

namespace SendtoNews\Controllers;

use WPMVC\MVC\Controller;

/**
 * oEmbedController controller to handle the STN Video oEmbed and methods.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.1
 */
class oEmbedController extends Controller
{
    /**
     * Registers the STN Video oEmbed.
     * @since 0.1.1
     */
    public function register()
    {
        // Ensure wp_oembed_add_provider function is available.
        if ( ! function_exists( 'wp_oembed_add_provider' ) )
        {
            require_once ABSPATH . WPINC . '/embed.php';
        }

        // Whitelist STN Video as an oEmbed service.
        wp_oembed_add_provider( '#https?://embed.sendtonews\.com/.*#i', 'https://embed.sendtonews.com/services/oembed', true );
    }

    /**
     * Displays notice if STN Video oEmbed plugin installed.
     * @since 0.1.1
     */
    public function notice()
    {
        if ( is_plugin_active_for_network( 'sendtonews-oembed/sendtonews-oembed.php' ) )
        {
            // Display oEmbed plugin deactivation notice when the old plugin is Network Active.
            $this->view->show( 'admin.notices.oembednetworkactive' );
        }
        else
        {
            // Display oEmbed plugin deactivation notice.
            $this->view->show( 'admin.notices.oembed' );
        }
    }
}

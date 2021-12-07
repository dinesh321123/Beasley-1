<?php

namespace VimeoVideoSelector\Controllers;

use WPMVC\MVC\Controller;

/**
 * TinyMCEController controller for registering the TinyMCE plugin and button.
 * Enables the Vimeo Video Player Selector for the classic editor (WYSIWYG).
 *
 * @author Vimeo Video
 * @copyright Vimeo Video <https://www.vvs.com>
 * @package VimeoVideoSelector
 * @version 1.0.1.2
 */
class TinyMCEController extends Controller
{
    /**
     * Register the TinyMCE plugin integration.
     *
     * @since 0.2.0
     *
     * @hook mce_external_plugins
     */
    public function register_plugin( $plugins )
    {

        $plugins['vimeovideoselector'] = assets_url( 'tinymce/plugins/vimeovideoselector/plugin.js', __FILE__ );
        return $plugins;
    }

    /**
     * Add the TinyMCE button.
     *
     * @since 0.2.0
     *
     * @hook mce_buttons
     */
    public function add_button( $buttons )
    {
        $buttons[] = "vimeovideoselector";
        return $buttons;
    }
}

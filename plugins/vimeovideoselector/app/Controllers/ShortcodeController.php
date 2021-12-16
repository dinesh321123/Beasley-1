<?php

namespace VimeoVideoSelector\Controllers;

use WPMVCVVS\MVC\Controller;
use VimeoVideoSelector\Models\Settings;

/**
 * ShortcodeController controller to handle the [vimeovideoselector] shortcode and methods.
 *
 * @author Vimeo Video
 * @copyright Vimeo Video <https://www.vvs.com>
 * @package VimeoVideoSelector
 * @version 1.0.1.2
 */
class ShortcodeController extends Controller
{
    /**
     * Renders the [vimeovideoselector] shortcode.
     * @since 0.1.0
     * @since 0.3.0 Supports additional player types ('single', 'float', 'player', 'full', 'barker', 'amp', 'amplist').
     *
     * @hook vimeovideoselector
     *
     * @global VimeoVideoSelector\Main $vimeovideoselector Main class.
     *
     * @return string Shortcode contents.
     */
    public function show( $atts )
    {
        // Check for shortcode attributes.
        if ( empty( $atts ) ) {
            $error = __( 'Missing Shortcode Attributes', 'vvs' );
            return $this->view->get( 'public.embeds.invalid', [
                'error' => $error,
            ]);
        }
        // Retrieve settings.
        $settings = Settings::find();

        // Check settings exist.
        if ( empty( $settings ) ) {
            $error = __( 'Missing Required Setting', 'vvs' );
            return $this->view->get( 'public.embeds.invalid', [
                'error' => $error,
            ]);
        }

        // Retrieve Company ID from settings.
        $client_id = $settings->client_id;

        // Check CID setting exists.
        if ( empty( $client_id ) ) {
            $error = __( 'Missing Required CLIENT ID Setting', 'vvs' );
            return $this->view->get( 'public.embeds.invalid', [
                'error' => $error,
            ]);
        }

        // Check for required shortcode 'key' attribute.
        if ( ! array_key_exists( 'key', $atts ) || empty( $atts['key'] ) ) {
            $error = __( 'Missing Required Key Shortcode Attribute', 'vvs' );
            return $this->view->get( 'public.embeds.invalid', [
                'error' => $error,
            ]);
        }
        return $this->view->get( 'public.embeds.player', ['key' => $atts['key'], 'width' => $atts['width'], 'height' => $atts['height'] ]);
    }
}

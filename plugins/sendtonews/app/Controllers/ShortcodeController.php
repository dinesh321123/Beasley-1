<?php

namespace SendtoNews\Controllers;

use WPMVC\MVC\Controller;
use SendtoNews\Models\Settings;

/**
 * ShortcodeController controller to handle the [sendtonews] shortcode and methods.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.0
 */
class ShortcodeController extends Controller
{
    /**
     * Renders the [sendtonews] shortcode.
     * @since 0.1.0
     * @since 0.3.0 Supports additional player types ('single', 'float', 'player', 'full', 'barker', 'amp', 'amplist').
     *
     * @hook sendtonews
     *
     * @global SendtoNews\Main $sendtonews Main class.
     *
     * @return string Shortcode contents.
     */
    public function show( $atts )
    {
        // Check for shortcode attributes.
        if ( empty( $atts ) ) {
            $error = __( 'Missing Shortcode Attributes', 'stnvideo' );
            return $this->view->get( 'public.embeds.invalid', [
                'error' => $error,
            ]);
        }

        if ( array_key_exists( 'cid', $atts ) && ! empty( $atts['cid'] ) ) {
            // Retrieve Company ID from shortcode attributes.
            $cid = $atts['cid'];
        } else {
            // Retrieve settings.
            $settings = Settings::find();

            // Check settings exist.
            if ( empty( $settings ) ) {
                $error = __( 'Missing Required Setting', 'stnvideo' );
                return $this->view->get( 'public.embeds.invalid', [
                    'error' => $error,
                ]);
            }

            // Retrieve Company ID from settings.
            $cid = $settings->cid;

            // Check CID setting exists.
            if ( empty( $cid ) ) {
                $error = __( 'Missing Required Company ID Setting', 'stnvideo' );
                return $this->view->get( 'public.embeds.invalid', [
                    'error' => $error,
                ]);
            }
        }

        // Check for required shortcode 'key' attribute.
        if ( ! array_key_exists( 'key', $atts ) || empty( $atts['key'] ) ) {
            $error = __( 'Missing Required Key Shortcode Attribute', 'stnvideo' );
            return $this->view->get( 'public.embeds.invalid', [
                'error' => $error,
            ]);
        }

        // Check for required shortcode 'type' attribute.
        if ( ! array_key_exists( 'type', $atts ) || empty( $atts['type'] ) ) {
            $error = __( 'Missing Required Type Shortcode Attribute', 'stnvideo' );
            return $this->view->get( 'public.embeds.invalid', [
                'error' => $error,
            ]);
        }

        // Confirm 'type' is valid and supported.
        if ( ! in_array( $atts['type'], array( 'single', 'float', 'player', 'full', 'barker', 'amp', 'amplist' ) ) ) {
            $error = __( 'Invalid  Type Shortcode Attribute', 'stnvideo' );
            return $this->view->get( 'public.embeds.invalid', [
                'error' => $error,
            ]);
        }

        if ( is_customize_preview() ) {
            // Render Placeholder.
            return $this->view->get( 'public.embeds.placeholder' );
        }

        // Render Embed.
        return $this->view->get( 'public.embeds.' . $atts['type'], [
            'cid' => $cid,
            'key' => $atts['key'],
        ]);
    }
}

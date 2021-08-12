<?php

use SendtoNews\Models\Settings;

/**
 * STN Video Widget.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.1
 */
class SendtoNews extends WP_Widget
{
    /**
     * MAIN class reference for WordPress MVC.
     *
     * @since 0.1.0
     * @var object
     */
    protected $main;

    /**
     * Whether or not the widget has been registered yet.
     *
     * @since 0.2.1
     * @var bool
     */
    protected $registered = false;

    /**
     * Widget Constructor.
     * @since 0.1.0
     *
     * @global SendtoNews\Main $sendtonews Main class.
     */
    public function __construct( $id = '', $name = '', $args = array() )
    {
        global $sendtonews;
        $this->main = $sendtonews;

        parent::__construct(
            'sendtonews', // Widget ID.
            'STN Video', // Widget name.
            [
                'classname'   => 'widget_sendtonews', // Widget class name.
                'description' => __( 'STN Video Player Selector.', 'stnvideo' ), // Widget description.
            ]
        );
    }

    /**
     * Add hooks while registering all widget instances of this widget class.
     *
     * @since 0.2.1
     *
     * @param integer $number Optional. The unique order number of this widget instance
     *                        compared to other instances of the same class. Default -1.
     *
     * @global SendtoNews\Main $sendtonews
     */
    public function _register_one( $number = -1 )
    {
        global $sendtonews;

        parent::_register_one($number);

        if ( $this->registered )
        {
            return;
        }

        $this->registered = true;

        // Confirm API verification.
        $verified = $sendtonews->{ '_c_return_AdminController@verified' }();

        if ( $verified )
        {
            // Note that the widgets component in the customizer will also do
            // the 'admin_print_scripts-widgets.php' action in WP_Customize_Widgets::print_scripts().
            add_action( 'admin_print_scripts-widgets.php', array( $this, 'enqueue_admin_scripts' ), 20 );

            if ( $this->is_preview() )
            {
                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_preview_scripts' ) );
            }

            // Note that the widgets component in the customizer will also do
            // the 'admin_footer-widgets.php' action in WP_Customize_Widgets::print_footer_scripts().
            add_action( 'admin_footer-widgets.php', array( $this, 'render_widget_overview_template' ) );
            add_action( 'admin_footer-widgets.php', array( $this, 'render_smartmatch_template' ) );
            add_action( 'admin_footer-widgets.php', array( $this, 'render_help_template' ) );
        }
    }

    /**
     * Widget display.
     * Renders what will be inside the widget when displayed.
     * @since 0.1.0
     *
     * @param array $args     Arguments for the widget.
     * @param class $instance Parameters.
     */
    public function widget( $args, $instance )
    {
        echo $args['before_widget'];

        // Retrieve settings.
        $settings = Settings::find();

        // Check settings exist.
        if ( empty( $settings ) )
        {
            $error = __( 'Missing Required Settings', 'stnvideo' );
            $this->main->view( 'public.embeds.invalid', [
                'error' => $error,
            ] );
        }
        else
        {
            // Retrieve Company ID from settings.
            $cid = $settings->cid;

            // Check CID setting exists.
            if ( empty( $cid ) )
            {
                $error = __( 'Missing Required Company ID Setting', 'stnvideo' );
                $this->main->view( 'public.embeds.invalid', [
                    'error' => $error,
                ] );
            }
            else
            {
                // Check for widget instance.
                if ( empty( $instance ) )
                {
                    $error = __( 'Missing Widget Instance', 'stnvideo' );
                    $this->main->view( 'public.embeds.invalid', [
                        'error' => $error,
                    ] );
                }
                else
                {
                    // Check for required widget 'key' value.
                    if ( ! array_key_exists( 'key', $instance ) || empty( $instance['key'] ) )
                    {
                        $error = __( 'Missing Required Key Widget Value', 'stnvideo' );
                        $this->main->view( 'public.embeds.invalid', [
                            'error' => $error,
                        ] );
                    }
                    else
                    {
                        // Check for required widget 'type' value.
                        if ( ! array_key_exists( 'type', $instance ) || empty( $instance['type'] ) )
                        {
                            $error = __( 'Missing Required Type Widget Value', 'stnvideo' );
                            $this->main->view( 'public.embeds.invalid', [
                                'error' => $error,
                            ] );
                        }
                        else
                        {
                            // Confirm 'type' is valid and supported.
                            if ( ! in_array( $instance['type'], array( 'single', 'float', 'player', 'full', 'barker', 'amp', 'amplist' ) ) )
                            {
                                $error = __( 'Invalid Type Widget Value', 'stnvideo' );
                                $this->main->view( 'public.embeds.invalid', [
                                    'error' => $error,
                                ] );
                            }
                            else
                            {
                                if ( is_customize_preview() ) {
                                    // Render Placeholder.
                                    $this->main->view( 'public.embeds.placeholder' );
                                } else {
                                    // Render Embed.
                                    $this->main->view( 'public.embeds.' . $instance['type'], [
                                        'cid' => $cid,
                                        'key' => $instance['key'],
                                    ] );
                                }
                            }
                        }
                    }
                }
            }
        }

        echo $args['after_widget'];
    }

    /**
     * Widget form.
     * Renders the form displayed for widget setting in admin dashboard.
     * @since 0.1.0
     *
     * @param array $instance Widget instance.
     *
     * @global SendtoNews\Main $sendtonews
     */
    public function form( $instance )
    {
        global $sendtonews;

        // Grab values from widget instance or revert to defaults.
        $name = ! empty( $instance['name'] ) ? $instance['name'] : '';
        $type = ! empty( $instance['type'] ) ? $instance['type'] : '';
        $key  = ! empty( $instance['key'] )  ? $instance['key']  : '';

        // Default CID to empty in case Settings unavailable.
        $cid = '';

        // Confirm API verification.
        $verified = $sendtonews->{ '_c_return_AdminController@verified' }();

        // Retrieve settings.
        $settings = Settings::find();

        // Check settings exist.
        if ( ! empty( $settings ) )
        {
            // Retrieve Company ID from settings.
            $cid = $settings->cid;
        }

        if ( $verified )
        {
            // Render Widget Form
            $this->main->view( 'admin.forms.widget', [
                'widget'   => $this,
                'instance' => $instance,
                'cid'      => $cid,
                'name'     => $name,
                'type'     => $type,
                'key'      => $key,
            ] );
        }
        else
        {
            // Render Verify Settings Notice in Widget Form
            $this->main->view( 'admin.forms.widgetverify', [
                'widget'   => $this,
                'instance' => $instance,
                'cid'      => $cid,
                'name'     => $name,
                'type'     => $type,
                'key'      => $key,
            ] );
        }
    }

    /**
     * Widget update.
     * Called when user updates settings at widget setting in admin dashboard.
     * @since 0.1.0
     *
     * @param array $new_instance Widget instance.
     * @param array $old_instance Widget instance.
     *
     * @return array The updated Widget instance.
     */
    public function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;

        // Update Instance
        $instance['name'] = ( ! empty( $new_instance['name'] ) ) ? $new_instance['name'] : '';
        $instance['type'] = ( ! empty( $new_instance['type'] ) ) ? $new_instance['type'] : '';
        $instance['key']  = ( ! empty( $new_instance['key'] ) ) ? $new_instance['key'] : '';

        return $instance;
    }

    /**
     * Enqueue preview scripts.
     *
     * These scripts normally are enqueued just-in-time when a video shortcode is used.
     * In the customizer, however, widgets can be dynamically added and rendered via
     * selective refresh, and so it is important to unconditionally enqueue them in
     * case a widget does get added.
     *
     * @since 4.8.0
     */
    public function enqueue_preview_scripts() {}

    /**
     * Loads the required scripts and styles for the widget control.
     *
     * @since 4.8.0
     *
     * @global SendtoNews\Main $sendtonews
     */
    public function enqueue_admin_scripts()
    {
        global $sendtonews;

        // Default CID & AUTH to empty in case Settings unavailable.
        $cid      = '';
        $authcode = '';

        // Retrieve version & settings.
        $version  = $sendtonews->config->get( 'version' );
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

        /*********************
         * Enqueue Custom Scripts
         *********************/

         // Enqueue S2N OneUI Styles.
        wp_enqueue_style(
            'sendtonews-oneui',
            S2N_PLAYER_SELECTOR_URL . 'assets/css/oneui.css',
            array(),
            $version,
            'all'
        );

         // Enqueue S2N Styles.
        wp_enqueue_style(
            'sendtonews-stn',
            S2N_PLAYER_SELECTOR_URL . 'assets/css/sendtonews.css',
            array(),
            $version,
            'all'
        );

         // Enqueue S2N Stories Styles.
        wp_enqueue_style(
            'sendtonews-stories',
            S2N_PLAYER_SELECTOR_URL . 'assets/css/stories.css',
            array(),
            $version,
            'all'
        );

        // Enqueue S2N Plugins Styles.
        wp_enqueue_style(
            'sendtonews-plugins',
            S2N_PLAYER_SELECTOR_URL . 'assets/css/jqplugins.css',
            array(),
            $version,
            'all'
        );

        /*********************
         * Enqueue Custom Scripts
         *********************/

        // Enqueue S2N Bootstrap Script.
        wp_enqueue_script(
            'sendtonews-oneui-bootstrap',
            S2N_PLAYER_SELECTOR_URL . 'assets/js/vendor/bootstrap.bundle.min.js',
            array(
                'jquery',
                'jquery-ui-tooltip',
            ),
            $version,
            true
        );

        // Enqueue S2N Simplebar Script.
        wp_enqueue_script(
            'sendtonews-oneui-simplebar',
            S2N_PLAYER_SELECTOR_URL . 'assets/js/vendor/simplebar.min.js',
            array(
                'jquery',
                'jquery-ui-tooltip',
            ),
            $version,
            true
        );

        // Enqueue S2N Scroll Lock Script.
        wp_enqueue_script(
            'sendtonews-oneui-scrolllock',
            S2N_PLAYER_SELECTOR_URL . 'assets/js/vendor/jquery-scrollLock.min.js',
            array(
                'jquery',
                'jquery-ui-tooltip',
            ),
            $version,
            true
        );

        // Enqueue S2N jQuery Appear Script.
        wp_enqueue_script(
            'sendtonews-oneui-jqappear',
            S2N_PLAYER_SELECTOR_URL . 'assets/js/vendor/jquery.appear.min.js',
            array(
                'jquery',
                'jquery-ui-tooltip',
            ),
            $version,
            true
        );

        // Enqueue S2N Plugins Script.
        wp_enqueue_script(
            'sendtonews-oneui-jscookie',
            S2N_PLAYER_SELECTOR_URL . 'assets/js/vendor/js.cookie.min.js',
            array(
                'jquery',
                'jquery-ui-tooltip',
            ),
            $version,
            true
        );

        // Enqueue S2N OneUI App Script.
        wp_enqueue_script(
            'sendtonews-oneui-app',
            S2N_PLAYER_SELECTOR_URL . 'assets/js/vendor/oneui.app.min.js',
            array(
                'jquery',
                'jquery-ui-tooltip',
                'sendtonews-oneui-bootstrap',
                'sendtonews-oneui-simplebar',
                'sendtonews-oneui-scrolllock',
                'sendtonews-oneui-jqappear',
                'sendtonews-oneui-jscookie',
            ),
            $version,
            true
        );

        // Enqueue S2N Plugins Script.
        wp_enqueue_script(
            'sendtonews-plugins',
            S2N_PLAYER_SELECTOR_URL . 'assets/js/plugins.js',
            array(
                'jquery',
                'jquery-ui-tooltip',
            ),
            $version,
            true
        );

        // Enqueue S2N Utils Script.
        wp_enqueue_script(
            'sendtonews-utils',
            S2N_PLAYER_SELECTOR_URL . 'assets/js/utils.js',
            array(
                'jquery',
                'jquery-ui-tooltip',
            ),
            $version,
            true
        );

        // Enqueue S2N Stories Script.
        wp_enqueue_script(
            'sendtonews-stories',
            S2N_PLAYER_SELECTOR_URL . 'assets/js/stories.js',
            array(
                'jquery',
                'jquery-ui-tooltip',
            ),
            $version,
            true
        );

        // Enqueue S2N Loader Script.
        wp_enqueue_script(
            'sendtonews-loader',
            S2N_PLAYER_SELECTOR_URL . 'assets/js/loader.js',
            array(
                'jquery',
                'jquery-ui-tooltip',
                'sendtonews-plugins'
            ),
            $version,
            true
        );

        // Enqueue S2N Modal Script.
        wp_enqueue_script(
            'sendtonews-modal',
            S2N_PLAYER_SELECTOR_URL . 'assets/js/modal.js',
            array(
                'jquery',
                'jquery-ui-tooltip',
            ),
            $version,
            true
        );

        // Enqueue S2N Widget Script.
        wp_enqueue_script(
            'sendtonews-widget',
            S2N_PLAYER_SELECTOR_URL . 'assets/js/widget.js',
            array(
                'jquery',
                'jquery-ui-tooltip',
                'editor',
                'media-upload',
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
            ),
            $version,
            true
        );

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

        // Use localize to disable the Video Library.
        wp_localize_script( 'sendtonews-modal', 'sendtonews_model_i18n', array(
            'enable_library' => false,
        ) );

        // Use localize for translation as well as to pass the plugin URL.
        wp_localize_script( 'sendtonews-widget', 'sendtonews_widget_i18n', array(
            'title'      => __( 'SendtoNews Player Selector', 'stnvideo' ),
            'save'       => __( 'Save', 'stnvideo' ),
            'close'      => __( 'Close', 'stnvideo' ),
            'plugin_url' => S2N_PLAYER_SELECTOR_URL,
        ) );
    }

    /**
     * Renders the overview template for the Widget context.
     * @since 0.9.1
     *
     * @hook admin_footer-{context}
     *
     * @global SendtoNews\Main $sendtonews
     */
    public function render_widget_overview_template()
    {
        global $sendtonews;

        // Render the popup modal skeleton for the Overview tab.
        $sendtonews->view( 'admin.modals.overview', [ 'enable_library' => false ] );
    }

    /**
     * Renders the Smart Match template.
     * @since 0.9.1
     *
     * @hook admin_footer-{context}
     *
     * @global SendtoNews\Main $sendtonews
     */
    public function render_smartmatch_template()
    {
        global $sendtonews;

        // Render the popup modal skeleton for the Smart Match Player tab.
        $sendtonews->view( 'admin.modals.smartmatch' );
    }

    /**
     * Renders the help template.
     * @since 0.9.1
     *
     * @hook admin_footer-{context}
     *
     * @global SendtoNews\Main $sendtonews
     */
    public function render_help_template()
    {
        global $sendtonews;

        // Render the popup modal skeleton for the Help tab.
        $sendtonews->view( 'admin.modals.help' );
    }
}

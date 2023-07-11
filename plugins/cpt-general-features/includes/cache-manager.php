<?php
/**
 * Cache Manager Class
 *
 * Manages cache-related functionality, including adding a clear cache button to the admin bar,
 * handling the button click event, and displaying cache cleared messages.
 *
 * @package YourThemeOrPlugin
 * @since 1.0.0
 */

 if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class Cache_Manager {

    /**
	 * Constructor.
	 */
    function __construct() {
        add_action( 'wp_loaded', array( $this, 'wp_init_cache' ) );
    }

    /**
     * Initializes the cache manager by adding necessary actions.
     */
    public function wp_init_cache() {
        
        // Check if user has the manage_cache capability
        if (current_user_can('manage_cache')) {
            add_action( 'wp_before_admin_bar_render', array( $this, 'add_clear_cache_button_to_admin_bar' ), 21 );
            add_action( 'wp_ajax_clear_cache', array( $this, 'handle_clear_cache_button_click' ) );
            add_action( 'admin_notices', array( $this, 'show_cache_cleared_message' ) );
        }
    }    

    /**
     * Adds a clear cache button to the admin bar.
     */
    public function add_clear_cache_button_to_admin_bar() {
        global $wp_admin_bar;

        $wp_admin_bar->add_menu(
            array(
                'id'     => 'cache-parent',
                'title'  => __( 'Cache', GFF_TEXT_DOMAIN ),
                // 'href'   => '#',
                'meta'  => array(
                    'title' => __( 'Cache', GFF_TEXT_DOMAIN ),
                ),
            )
        );

        // Add the clear cache button to the admin bar
        $wp_admin_bar->add_menu(array(
            'parent' => 'cache-parent',
            'id'    => 'clear-cache',
            'title' => __( 'Clear Cache', GFF_TEXT_DOMAIN ),
            'href'  => wp_nonce_url(admin_url('admin-ajax.php?action=clear_cache'), 'clear_cache'),
            'meta'  => array(
                'title' => __( 'Clear the site cache', GFF_TEXT_DOMAIN ),
            ),
        ));

    }

    /**
     * Handles the clear cache button click event.
     */
    public function handle_clear_cache_button_click() {
        // Verify the nonce for security
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'clear_cache')) {
            wp_die( __( 'Invalid request!', GFF_TEXT_DOMAIN ) );
        }

        // Check if user has the manage_cache capability
		if ( ! current_user_can( 'manage_cache' ) ) {
			wp_die( __( 'You do not have permission to clear the cache.', GFF_TEXT_DOMAIN ) );
		}

        // Store the referring URL
		$referrer = wp_get_referer();

        // Add your cache clearing logic here
        // ...

        // Set a transient to display the cache cleared message on the dashboard
        set_transient( 'cache_cleared_message', true );

        // Redirect the user back to the referring URL
		if ( ! empty( $referrer ) && ! wp_safe_redirect( $referrer ) ) {
			wp_safe_redirect( admin_url() );
		}
        exit;
    }

    /**
     * Displays the cache cleared message as an admin notice.
     */
    public function show_cache_cleared_message() {
        // Check if the cache cleared message transient exists
        if ( get_transient( 'cache_cleared_message' ) ) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Cache cleared successfully!', GFF_TEXT_DOMAIN ) . '</p></div>';

            // Delete the transient after displaying the message
            delete_transient( 'cache_cleared_message' );
        }
    }

}

new Cache_Manager();
<?php
/**
 * Custom Capability for BBGI
 *
 * This code defines a custom capability, 'manage_cache', and restricts its usage for the 'administrator' role
 * in WordPress. It prevents administrators from managing the cache directly.
 *
 * @package YourThemeOrPlugin
 */

// Ensures that this file is not accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    die( "Please don't try to access this file directly." );
}

class BBGI_Capability {

    /**
     * Constructor function for the BBGI_Capability class.
     * Adds an action hook to initialize the custom capability.
     */
    function __construct() {
        add_action( 'wp_loaded', array( $this, 'wp_init_bbgi_capability' ) );
    }

    /**
     * Initializes the custom capability 'manage_cache' for the 'administrator' role.
     */
    public function wp_init_bbgi_capability() {
        
        // The roles for which the 'manage_cache' capability should be added
        $roles = [ 'administrator' ];

        foreach ( $roles as $role ) {
            $role_obj = get_role( $role );

            // Check if the role object is an instance of the WP_Role class
            if ( is_a( $role_obj, \WP_Role::class ) ) {

                // Adds the 'manage_cache' capability with a value of false to restrict cache management
                $role_obj->add_cap( 'manage_cache', false );
            
            }
        }

    }

}

new BBGI_Capability();
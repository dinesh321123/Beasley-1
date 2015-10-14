<?php

/**
 * Adjusted remote IP address. Uses IP address provided by CloudFlare if available.
 *
 * @param string $remote_ip The initial IP address.
 * @return string CloudFlare IP address if available, otherwise initial IP address.
 */
function adjust_remote_ip_addrses( $remote_ip ) {
	// lets use IP address from CloudFlare if available
	if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) && filter_var( $_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP ) ) {
		return $_SERVER['HTTP_CF_CONNECTING_IP'] ;
	}
	
	return $remote_ip;
}
add_filter( 'restricted_site_access_remote_ip', 'adjust_remote_ip_addrses' );
<?php
/**
 * Redirects HTTPS requests on the frontend to the corresponding
 * non-http version.
 */

class FrontEndHttpRedirector {

	function enable() {
		add_action( 'init', array( $this, 'run' ) );
	}

	function run() {
		if ( $this->needs_redirect() ) {
			$this->redirect();
		}
	}

	function redirect() {
		wp_safe_redirect( $this->get_redirect_url() );
		die();
	}

	function needs_redirect() {
		return ! $this->is_login_page() && ! is_admin() && is_ssl();
	}

	function is_login_page() {
		return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) );
	}

	function get_redirect_url() {
		$domain   = $_SERVER['SERVER_NAME'];
		$path     = $_SERVER['REQUEST_URI'];

		return 'http://' . $domain . $path;
	}

}

$frontend_http_redirector = new FrontEndHttpRedirector();
$frontend_http_redirector->enable();

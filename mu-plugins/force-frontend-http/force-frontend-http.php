<?php
/**
 * Redirects HTTPS requests on the frontend to the corresponding
 * non-http version.
 */

class FrontEndHttpRedirector {

	function enable() {
		add_action( 'wp', array( $this, 'run' ) );
	}

	function run() {
		if ( $this->needs_redirect() ) {
			$this->redirect();
		}
	}

	function redirect() {
		$html = <<<HTML
<script type="text/javascript">
	location.href = location.href.replace('https://', 'http://');
</script>
HTML;

		echo $html;
		die();
	}

	function needs_redirect() {
		return ! $this->is_login_page() && ! is_admin() && is_ssl() && ! $this->is_ugc_moderation();
	}

	function is_login_page() {
		return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) );
	}

	// Redirecting in the admin on UGC moderation breaks moderation process - avoid these
	function is_ugc_moderation() {
		$ugc_action = get_query_var( 'ugc_action' );
		return ! empty( $ugc_action );
	}

}

$frontend_http_redirector = new FrontEndHttpRedirector();
$frontend_http_redirector->enable();

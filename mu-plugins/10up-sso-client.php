<?php
/*
Plugin Name: 10up SSO Client
Plugin URI:
Description:
Author: 10up Inc
Author URI: https://10up.com/
Version: 1.0.0
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

if ( ! defined( 'TENUPSSO_PROXY_URL' ) ) {
	define( 'TENUPSSO_PROXY_URL', 'http://ssoproxy.10uplabs.com/wp-login.php' );
}

function tenup_sso_add_login_errors( WP_Error $errors ) {
	global $tenup_login_failed;

	if ( ! defined( 'TENUPSSO_PROXY_URL' ) ) {
		$errors->add( '10up-sso', 'Define <b><code style="white-space:nowrap">TENUPSSO_PROXY_URL</code></b> in the <b><code style="white-space:nowrap">wp-config.php</code></b> with absolute URL of the proxy&apos;s <b><code style="white-space:nowrap">wp-login.php</code></b> file.' );
	}

	if ( $tenup_login_failed ) {
		$error_code = filter_input( INPUT_GET, 'error' );
		switch ( $error_code ) {
			case 'invalid_email_domain':
				$errors->add( '10up-sso-login', 'The email address is not allowed.' );
				break;
			default:
				$errors->add( '10up-sso-login', 'Login failed.' );
				break;
		}
	}

	return $errors;
}
add_filter( 'wp_login_errors', 'tenup_sso_add_login_errors' );

function tenup_sso_process_client_login() {
	global $tenup_login_failed;

	if ( ! defined( 'TENUPSSO_PROXY_URL' ) ) {
		return;
	}

	$email = filter_input( INPUT_GET, 'email', FILTER_VALIDATE_EMAIL );

	if ( ! empty( $_GET['error'] ) ) {
		$tenup_login_failed = true;
	} elseif ( ! empty( $email ) ) {
		$verify = add_query_arg( array(
			'action' => '10up-verify',
			'email'  => urlencode( $email ),
			'nonce'  => urlencode( filter_input( INPUT_GET, 'nonce' ) ),
		), TENUPSSO_PROXY_URL );

		$response = wp_remote_get( $verify );
		if ( wp_remote_retrieve_response_code( $response ) != 200 ) {
			wp_redirect( wp_login_url() );
			exit;
		}

		$user_id = false;
		$user = get_user_by( 'email', $email );
		if ( ! $user ) {
			$short_email = str_replace( '@get10up.com', '@10up.com', $email );
			$user = get_user_by( 'email', $short_email );
		}

		if ( ! $user ) {
			$default_role = defined( 'TENUPSSO_DEFAULT_ROLE' )
				? TENUPSSO_DEFAULT_ROLE
				: 'administrator';

			$user_id = wp_insert_user( array(
				'user_login'   => current( explode( '@', $email ) ),
				'user_pass'    => wp_generate_password(),
				'user_email'   => $email,
				'display_name' => filter_input( INPUT_GET, 'full_name' ),
				'first_name'   => filter_input( INPUT_GET, 'first_name' ),
				'last_name'    => filter_input( INPUT_GET, 'last_name' ),
				'role'         => $default_role,
			) );

			if ( ! is_wp_error( $user_id ) ) {
				add_user_meta( $user_id, '10up-sso', 1 );

				if ( is_multisite() ) {
					add_user_to_blog( get_current_blog_id(), $user_id, $default_role );
					if ( defined( 'TENUPSSO_GRANT_SUPER_ADMIN' ) && filter_var( TENUPSSO_GRANT_SUPER_ADMIN, FILTER_VALIDATE_BOOLEAN ) ) {
						require_once ABSPATH . 'wp-admin/includes/ms.php';
						grant_super_admin( $user_id );
					}
				}

				$user = get_user_by( 'id', $user_id );
			}
		} else {
			$user_id = $user->ID;
		}

		if ( ! empty( $user_id ) ) {
			add_filter( 'auth_cookie_expiration', 'tenup_sso_change_cookie_expiration', 1000 );
			wp_set_auth_cookie( $user_id );
			remove_filter( 'auth_cookie_expiration', 'tenup_sso_change_cookie_expiration', 1000 );

			$redirect_to = admin_url();
			$requested_redirect_to = '';
			if ( isset( $_REQUEST['redirect_to'] ) ) {
				$redirect_to = $requested_redirect_to = $_REQUEST['redirect_to'];
			}

			$redirect_to = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );
			if ( empty( $redirect_to ) ) {
				// If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
				if ( is_multisite() && ! get_active_blog_for_user( $user->ID ) && ! is_super_admin( $user->ID ) ) {
					$redirect_to = user_admin_url();
				} elseif ( is_multisite() && ! $user->has_cap( 'read' ) ) {
					$redirect_to = get_dashboard_url( $user->ID );
				} elseif ( ! $user->has_cap( 'edit_posts' ) ) {
					$redirect_to = admin_url( 'profile.php' );
				} else {
					// Just in case everything else fails, go home...
					$redirect_to = home_url();
				}
			}

			wp_safe_redirect( $redirect_to );
			exit;
		}

		$tenup_login_failed = true;
	} else {
		$redirect_url = wp_login_url();
		if ( isset( $_REQUEST['redirect_to'] ) ) {
			$redirect_url = add_query_arg( 'redirect_to', $_REQUEST['redirect_to'], $redirect_url );
		}

		$proxy_url = add_query_arg( array(
			'action'   => '10up-login',
			'redirect' => urlencode( $redirect_url ),
		), TENUPSSO_PROXY_URL );

		wp_redirect( $proxy_url );
		exit;
	}
}
add_action( 'login_form_10up-login', 'tenup_sso_process_client_login' );

function tenup_sso_update_login_form() {
	$google_login = add_query_arg( 'action', '10up-login', wp_login_url() );
	if ( isset( $_REQUEST['redirect_to'] ) ) {
		$google_login = add_query_arg( 'redirect_to', $_REQUEST['redirect_to'], $google_login );
	}

	?><script type="text/javascript">
		(function() {
			document.getElementById('loginform').insertAdjacentHTML(
				'afterbegin',
				'<div id="tenup_sso" class="tenup-sso">' +
					'<a href="<?php echo esc_url( $google_login ); ?>" class="button button-hero button-primary">' +
						'Login with 10up account' +
					'</a>' +
					'<span class="tenup-sso-or"><span>or</span></span>' +
				'</div>'
			);
		})();
	</script><?php
}
add_action( 'login_form', 'tenup_sso_update_login_form' );

function tenup_sso_render_login_form_styles() {
	?><style>
		.tenup-sso {
			margin-bottom: 1em;
			font-weight: normal;
			overflow: hidden;
			text-align: center;
		}

		.tenup-sso .button-primary {
			float: none;
			text-transform: capitalize;
		}

		.tenup-sso-or {
			margin: 2em 0;
			width: 100%;
			display: block;
			border-bottom: 1px solid rgba(0,0,0,0.13);
			text-align: center;
			line-height: 1;
		}

		.tenup-sso-or span {
			position: relative;
			top: 0.5em;
			background: white;
			padding: 0 1em;
			color: #72777c;
		}
	</style><?php
}
add_action( 'login_head', 'tenup_sso_render_login_form_styles' );

function tenup_sso_preven_standard_login( $user ) {
	// do nothing if proxy URL is not provided or direct login is allowed
	if ( ! defined( 'TENUPSSO_PROXY_URL' ) || defined( 'TENUPSSO_ALLOW_DIRECT_LOGIN' ) ) {
		return $user;
	}

	// check if user logged in with 10up account and prevent it
	if ( ! is_wp_error( $user ) ) {
		$is_10up_sso = get_user_meta( $user->ID, '10up-sso', true );
		if ( filter_var( $is_10up_sso, FILTER_VALIDATE_BOOLEAN ) ) {
			return new WP_Error( 'tenup-sso', 'Please, use "Login with 10up account" button to enter the site.' );
		}
	}

	return $user;
}
// @todo add this back once all bugs are ironed out
//add_filter( 'authenticate', 'tenup_sso_preven_standard_login', 999 );

function tenup_sso_change_cookie_expiration() {
	return DAY_IN_SECONDS;
}

function tenup_sso_check_user_blog() {
	if ( ! is_user_logged_in() || is_network_admin() ) {
		return;
	}

	$user_id = get_current_user_id();
	$has_10up_sso = get_user_meta( $user_id, '10up-sso', true );
	if ( ! filter_var( $has_10up_sso, FILTER_VALIDATE_BOOLEAN ) ) {
		return;
	}

	$current_blog = get_current_blog_id();
	$blogs = get_blogs_of_user( $user_id );
	$user_blogs = wp_list_filter( $blogs, array( 'userblog_id' => $current_blog ) );
	if ( ! empty( $user_blogs ) ) {
		return;
	}

	if ( is_multisite() ) {
		$default_role = defined( 'TENUPSSO_DEFAULT_ROLE' )
				? TENUPSSO_DEFAULT_ROLE
				: 'administrator';

		add_user_to_blog( $current_blog, $user_id, $default_role );
		wp_cache_delete( $user_id, 'user_meta' );
	}
}
add_action( 'admin_page_access_denied', 'tenup_sso_check_user_blog' );
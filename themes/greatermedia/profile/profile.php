<?php
	get_header();
	$profile_page = get_query_var( 'profile_page', 'login' );

/**
 * 'join',
 * 'login',
 * 'logout',
 * 'account',
 * 'forgot-password',
 * 'cookies-required',
 */

	if ( 'login' === $profile_page ) {
		$option_key = 'login';
		$defaults = array (
			'heading' => 'Login',
			'message' => 'Membership gives you access to all areas of the site, including full membership-only contests and the ability to submit content to share with the site and other members.'
		);
	}

	if ( 'join' === $profile_page ) {
		$option_key = 'join';
		$defaults = array(
			'heading' => 'Join',
			'message' => 'Membership gives you access to all areas of the site, including full membership-only contests and the ability to submit content to share with the site and other members.'
		);
	}

	if ( 'account' === $profile_page ) {
		$option_key = 'profile';
		$defaults = array (
			'heading' => 'Manage your Account',
			'message' => 'Help us get to know you better, manage your communication preferences, or change your password.'
		);
	}

	if ( 'forgot-password' === $profile_page ) {
		$option_key = 'password';
		$defaults = array (
			'heading' => 'Password Reset',
			'message' => 'Forgot your password? No worries, it happens. We\'ll send you a password reset email.'
		);
	}

if ( 'cookies-required' === $profile_page ) {
	$option_key = 'cookies';
	$defaults = array (
		'heading' => 'Cookies required',
		'message' => 'It doesn\'t look like your browser is letting us set a cookie. These small bits of information are stored in your browser and allow us to ensure you stay logged in. They are required to use the site and can generally be authorized in your browser\'s preferences or settings screen.'
	);
}

$page_heading = get_option( "gmr_{$option_key}_page_heading" );
$page_message = get_option( "gmr_{$option_key}_page_message" );

if ( empty( $page_heading ) ) {
	$page_heading = $defaults['heading'];
}

if ( empty( $page_message ) ) {
	$page_message = $defaults['message'];
}


?>


	<div class="gigya-screen-set" id="GMR-RegistrationLogin" style="display:none">
		<div class="gigya-screen cf" id="gigya-login-screen" data-responsive="true">
			<h2>Login to Your Account</h1>
			<h3>Login with your social network</h3>
			<div class="gigya-social-login">
				<param name="buttonsStyle" value="standard" />
				<param name="enabledProviders" value="facebook,twitter,google" />
				<param name="showTermsLink" value="false" />
				<param name="hideGigyaLink" value="true" />
				<param name="width" value="280" />
				<param name="useHTML" value="true" />
				<param name="buttonSize" value="40px" />
			</div>

			<h3>Or, login with email</h3>

			<form class="gigya-login-form">
				<span class="gigya-error-msg" data-bound-to="loginID" ></span>
				<input type="text" name="loginID" placeholder="Email" />

				<span class="gigya-error-msg" data-bound-to="password" ></span>
				<input type="password" name="password" placeholder="Password" />

				<span class="gigya-error-msg" data-bound-to="gigya-login-form"></span>
				<input type="submit" name="submit" value="Login" />
			</form>
		</div>
	</div>

	<main class="main" role="main">

		<div class="container profile-page__container">

			<div class="profile-page__sidebar">

				<h1><?php echo esc_html( $page_heading ); ?></h1>
				<?php echo apply_filters( 'the_content', $page_message ); ?>

			</div>

			<div id="profile-content" class="profile-page__content">

			</div>

		</div>

	</main>

<?php get_footer(); ?>

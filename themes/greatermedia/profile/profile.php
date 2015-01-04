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

	if ( 'account' === $profile_page ) {
		$option_key = 'profile';
		$defaults = array (
			'heading' => 'Manage your Account',
			'message' => 'Help us get to know you better, manage your communication preferences, or change your password.'
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

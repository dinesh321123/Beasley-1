<?php get_header(); ?>

<?php

$page_heading = 'Login';
$page_message = 'Membership gives you access to all areas of the site, including full membership-only contests and the ability to submit content to share with the site and other members.';
$months = array(
	'January',
	'February',
	'March',
	'April',
	'May',
	'June',
	'July',
	'August',
	'September',
	'October',
	'November',
	'December',
);

$emma_groups = get_option( 'emma_groups' );
$emma_groups = json_decode( $emma_groups, true );
if ( ! $emma_groups ) {
	$emma_groups = array();
}

$state_names = array(
	array( 'label' => 'Alabama', 'value' => 'AL' ),
	array( 'label' => 'Alaska', 'value' => 'AK' ),
	array( 'label' => 'Arizona', 'value' => 'AZ' ),
	array( 'label' => 'Arkansas', 'value' => 'AR' ),
	array( 'label' => 'California', 'value' => 'CA' ),
	array( 'label' => 'Colorado', 'value' => 'CO' ),
	array( 'label' => 'Connecticut', 'value' => 'CT' ),
	array( 'label' => 'Delaware', 'value' => 'DE' ),
	array( 'label' => 'District of Columbia', 'value' => 'DC' ),
	array( 'label' => 'Florida', 'value' => 'FL' ),
	array( 'label' => 'Georgia', 'value' => 'GA' ),
	array( 'label' => 'Hawaii', 'value' => 'HI' ),
	array( 'label' => 'Idaho', 'value' => 'ID' ),
	array( 'label' => 'Illinois', 'value' => 'IL' ),
	array( 'label' => 'Indiana', 'value' => 'IN' ),
	array( 'label' => 'Iowa', 'value' => 'IA' ),
	array( 'label' => 'Kansas', 'value' => 'KS' ),
	array( 'label' => 'Kentucky', 'value' => 'KY' ),
	array( 'label' => 'Louisiana', 'value' => 'LA' ),
	array( 'label' => 'Maine', 'value' => 'ME' ),
	array( 'label' => 'Maryland', 'value' => 'MD' ),
	array( 'label' => 'Massachusetts', 'value' => 'MA' ),
	array( 'label' => 'Michigan', 'value' => 'MI' ),
	array( 'label' => 'Minnesota', 'value' => 'MN' ),
	array( 'label' => 'Mississippi', 'value' => 'MS' ),
	array( 'label' => 'Missouri', 'value' => 'MO' ),
	array( 'label' => 'Montana', 'value' => 'MT' ),
	array( 'label' => 'Nebraska', 'value' => 'NE' ),
	array( 'label' => 'Nevada', 'value' => 'NV' ),
	array( 'label' => 'New Hampshire', 'value' => 'NH' ),
	array( 'label' => 'New Jersey', 'value' => 'NJ' ),
	array( 'label' => 'New Mexico', 'value' => 'NM' ),
	array( 'label' => 'New York', 'value' => 'NY' ),
	array( 'label' => 'North Carolina', 'value' => 'NC' ),
	array( 'label' => 'North Dakota', 'value' => 'ND' ),
	array( 'label' => 'Ohio', 'value' => 'OH' ),
	array( 'label' => 'Oklahoma', 'value' => 'OK' ),
	array( 'label' => 'Oregon', 'value' => 'OR' ),
	array( 'label' => 'Pennsylvania', 'value' => 'PA' ),
	array( 'label' => 'Rhode Island', 'value' => 'RI' ),
	array( 'label' => 'South Carolina', 'value' => 'SC' ),
	array( 'label' => 'South Dakota', 'value' => 'SD' ),
	array( 'label' => 'Tennessee', 'value' => 'TN' ),
	array( 'label' => 'Texas', 'value' => 'TX' ),
	array( 'label' => 'Utah', 'value' => 'UT' ),
	array( 'label' => 'Vermont', 'value' => 'VT' ),
	array( 'label' => 'Virginia', 'value' => 'VA' ),
	array( 'label' => 'Washington', 'value' => 'WA' ),
	array( 'label' => 'West Virginia', 'value' => 'WV' ),
	array( 'label' => 'Wisconsin', 'value' => 'WI' ),
	array( 'label' => 'Wyoming', 'value' => 'WY' ),
	array( 'label' => 'Armed Forces Americas', 'value' => 'AA' ),
	array( 'label' => 'Armed Forces Europe', 'value' => 'AE' ),
	array( 'label' => 'Armed Forces Pacific', 'value' => 'AP' ),
);

function get_gigya_verify_email_message() {
	if ( array_key_exists( 'errorCode', $_GET ) ) {
		$error_code = $_GET['errorCode'];
		preg_match( '~^(\d+)~', $error_code, $matches );
		$error_num = trim( $matches[1] );

		if ( $error_num === '0' ) {
			return 'Your email was verified successfully and your Account has been activated.';
		} else {
			$message = substr( $error_code, strlen( $error_num ) );
			$message = str_replace( '\"', '', $message );
			$message = 'Error: Could not verify your email: ' . $message;
			$message = esc_html( $message );

			return "<span class='error'>$message</span>";
		}
	} else {
		return '';
	}
}

?>

	<div
		class="gigya-screen-set"
		id="GMR-CustomScreenSet"
		style="display:none"
		data-on-pending-registration-screen="gigya-register-complete-screen"
		data-on-pending-verification-screen="gigya-resend-verification-code-screen">
		<div class="gigya-screen" id="gigya-login-screen" data-responsive="true">
			<h2>Login to Your Account</h2>
			<h3>Login with your social network</h3>
			<div class="gigya-social-login" data-on-success-screen="gigya-login-success-screen">
				<param name="buttonsStyle" value="fullLogoColored" />
				<param name="enabledProviders" value="facebook,twitter,google" />
				<param name="showTermsLink" value="false" />
				<param name="hideGigyaLink" value="true" />

				<param name="width" value="180" />
				<param name="height" value="150" />
<!--
				<param name="width" value="400" />
				<param name="height" value="150" />
-->
				<param name="useHTML" value="true" />
				<param name="buttonSize" value="40px" />
				<param name="lastLoginIndication" value="none" />
			</div>

			<h3>Or, login with email</h3>

			<form class="gigya-login-form" id="gigya-login-form" data-on-success-screen="gigya-login-success-screen">
				<span class="gigya-error-msg login-error-msg" data-bound-to="gigya-login-form"></span>
				<span class="gigya-error-msg" data-bound-to="loginID" ></span>
				<input type="text" name="loginID" placeholder="Email" />

				<span class="gigya-error-msg" data-bound-to="password" ></span>
				<input type="password" name="password" placeholder="Password" />

				<a href="#" class="link-button" data-switch-screen="gigya-forgot-password-screen">Forgot Password?</a>
				<input type="submit" name="submit" value="Login" />

			</form>

			<h3 class="new-account-msg">Don't have an account?
			<a href="#" class="link-button" data-switch-screen="gigya-register-screen">Register here.</a>
			</h3>
		</div>

		<div class="gigya-screen" id="gigya-forgot-password-screen" data-responsive="true">
			<h2>Password Reset</h2>
			<h3>Please enter your email address to reset your password.</h3>

			<form class="gigya-reset-password-form" id="gigya-reset-password-form" data-on-success-screen="gigya-forgot-password-sent-screen">
				<span class="gigya-error-msg login-error-msg" data-bound-to="gigya-reset-password-form"></span>
				<span class="gigya-error-msg" data-bound-to="loginID" ></span>
				<input type="text" name="loginID" placeholder="Email" />

				<a href="#" class="link-button" data-switch-screen="gigya-login-screen">&laquo; Back</a>
				<input type="submit" name="submit" value="Submit" />
			</form>
		</div>

		<div class="gigya-screen" id="gigya-logout-screen" data-responsive="true">
			<h2>Logging you out ...</h2>
		</div>

		<div class="gigya-screen" id="gigya-login-success-screen" data-responsive="true">
			<h2>Login successful.</h2>
		</div>

		<div class="gigya-screen" id="gigya-forgot-password-sent-screen" data-responsive="true">
			<h2>Password Reset</h2>
			<h3>An email regarding your password change has been sent to your email address.</h3>

			<a href="#" class="link-button" data-switch-screen="gigya-login-screen">&laquo; Back to Login</a>
		</div>

		<div class="gigya-screen" id="gigya-register-screen" data-responsive="true">
			<h2>Create Your Account</h2>
			<h3>Register with your social network</h3>
			<div class="gigya-social-login">
				<param name="buttonsStyle" value="fullLogoColored" />
				<param name="enabledProviders" value="facebook,twitter,google" />
				<param name="showTermsLink" value="false" />
				<param name="hideGigyaLink" value="true" />

				<param name="width" value="180" />
				<param name="height" value="150" />
<!--
				<param name="width" value="400" />
				<param name="height" value="150" />
-->
				<param name="useHTML" value="true" />
				<param name="buttonSize" value="40px" />
				<param name="lastLoginIndication" value="none" />
			</div>

			<h3>Or, create new account</h3>

			<form class="gigya-register-form" id="gigya-register-form">
				<span class="gigya-error-msg login-error-msg" data-bound-to="gigya-register-form"></span>

				<span class="gigya-error-msg" data-bound-to="email" ></span>
				<input type="text" name="email" placeholder="Email" />

				<span class="gigya-error-msg" data-bound-to="profile.firstName" ></span>
				<input type="text" name="profile.firstName" placeholder="First Name" />

				<span class="gigya-error-msg" data-bound-to="profile.lastName" ></span>
				<input type="text" name="profile.lastName" placeholder="Last Name" />

				<span class="gigya-error-msg" data-bound-to="password" ></span>
				<input type="password" name="password" placeholder="Password" />

				<span class="gigya-error-msg" data-bound-to="passwordRetype" ></span>
				<input type="password" name="passwordRetype" placeholder="Re-Enter Password" />

				<a href="#" class="link-button" data-switch-screen="gigya-login-screen">&laquo; Login</a>
				<input type="submit" name="submit" value="Register" />
			</form>
		</div>

		<div class="gigya-screen" id="gigya-register-complete-screen" data-responsive="true">
			<h2>Complete Your Profile</h2>

			<form class="gigya-profile-form" id="gigya-profile-form" data-on-success-screen="gigya-register-success-screen">
				<span class="gigya-error-msg login-error-msg" data-bound-to="gigya-profile-form"></span>

				<span class="gigya-error-msg" data-bound-to="profile.email" ></span>
				<input type="text" name="profile.email" placeholder="Email" />

				<span class="gigya-error-msg" data-bound-to="profile.firstName" ></span>
				<input type="text" name="profile.firstName" placeholder="First Name"/>

				<span class="gigya-error-msg" data-bound-to="profile.lastName" ></span>
				<input type="text" name="profile.lastName" placeholder="Last Name" />

				<span class="gigya-error-msg" data-bound-to="profile.birthYear" ></span>
				<label>Year of Birth:</label>
				<select name="profile.birthYear">
					<?php foreach ( range( 1920, date( 'Y' ) ) as $year ) { ?>
						<option value="<?php echo esc_attr( $year ); ?>"><?php echo esc_html( $year ); ?></option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.birthMonth" ></span>
				<label>Month of Birth:</label>
				<select name="profile.birthMonth">
					<?php foreach ( $months as $month_num => $month_name ) { ?>
						<option value="<?php echo esc_attr( $month_num + 1 ); ?>">
						<?php echo esc_html( $month_name ); ?>
						</option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.birthDay" ></span>
				<label>Day of Birth:</label>
				<select name="profile.birthDay">
					<?php foreach ( range( 1, 31 ) as $day_num ) { ?>
						<option value="<?php echo esc_attr( $day_num ); ?>">
						<?php echo esc_html( $day_num ); ?>
						</option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.zip" ></span>
				<input type="text" name="profile.zip" placeholder="ZIP Code" />

				<span class="gigya-error-msg" data-bound-to="profile.gender" ></span>
				<label class="gender-label">Gender:</label>
				<label class="inline-label"><input type="radio" name="profile.gender" value="m" />Male</label>
				<label class="inline-label"><input type="radio" name="profile.gender" value="f" />Female</label>

				<span class="gigya-error-msg" data-bound-to="profile.state" ></span>
				<label>State:</label>
				<select name="profile.state">
					<?php foreach ( $state_names as $state ) { ?>
						<option value="<?php echo esc_attr( $state['value'] ); ?>"><?php echo esc_html( $state['label'] ); ?></option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.city" ></span>
				<label>City:</label>
				<input type="text" name="profile.city" />

				<h2>Email Subscriptions</h2>

				<ul class="member-groups-list">
					<?php foreach ( $emma_groups as $emma_group ) { ?>
						<li>
							<input
								type="checkbox"
								name="data.<?php echo esc_attr( $emma_group['field_key'] ) ?>"
								checked="checked" />
							<label class="label-email-list">
								<?php echo esc_html( $emma_group['group_name'] ) ?>
							</label>
						</li>
					<?php } ?>
				</ul>

				<h2>Radio Listening Questions:</h2>

				<label>On a typical day, about how much time would you say that you spend listening to the radio?</label>
				<select name="data.listeningFrequency">
					<option value="0">less than 1 hour</option>
					<option value="1">1 to 3 hours</option>
					<option value="2">more than 3 hours</option>
				</select>

				<label>When you're listening to the radio, about what percentage of time do you spend listening to 102.9 WMGK?</label>
				<select name="data.listeningLoyalty">
					<?php foreach ( range( 0, 100, 10 ) as $percent ) { ?>
						<option value="<?php echo esc_attr( $percent ); ?>"><?php echo esc_html( $percent . '%' ); ?></option>
					<?php } ?>
				</select>

				<input type="submit" name="submit" value="Join the Club" />
			</form>
		</div>

		<div class="gigya-screen" id="gigya-register-success-screen" data-responsive="true">
			<h2>Account created successfully.</h2>
		</div>

		<div class="gigya-screen" id="gigya-update-profile-screen" data-responsive="true">
			<h2>Your Profile</h2>

			<form class="gigya-profile-form" id="gigya-profile-form" data-on-success-screen="gigya-update-profile-success-screen">
				<span class="gigya-error-msg login-error-msg" data-bound-to="gigya-profile-form"></span>
				<span class="gigya-error-msg" data-bound-to="profile.email" ></span>

				<label>Email:
					<a href="#"
						class="link-button verify-email-link"
						data-switch-screen="gigya-resend-verification-code-update-screen">Verify Email</a>
				</label>
				<input type="text" name="profile.email" />

				<span class="gigya-error-msg" data-bound-to="profile.firstName" ></span>
				<label>First Name:</label>
				<input type="text" name="profile.firstName" />

				<span class="gigya-error-msg" data-bound-to="profile.lastName" ></span>
				<label>Last Name:</label>
				<input type="text" name="profile.lastName" />

				<span class="gigya-error-msg" data-bound-to="profile.birthYear" ></span>
				<label>Year of Birth:</label>
				<select name="profile.birthYear">
					<?php foreach ( range( 1920, date( 'Y' ) ) as $year ) { ?>
						<option value="<?php echo esc_attr( $year ); ?>"><?php echo esc_html( $year ); ?></option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.birthMonth" ></span>
				<label>Month of Birth:</label>
				<select name="profile.birthMonth">
					<?php foreach ( $months as $month_num => $month_name ) { ?>
						<option value="<?php echo esc_attr( $month_num + 1 ); ?>">
						<?php echo esc_html( $month_name ); ?>
						</option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.birthDay" ></span>
				<label>Day of Birth:</label>
				<select name="profile.birthDay">
					<?php foreach ( range( 1, 31 ) as $day_num ) { ?>
						<option value="<?php echo esc_attr( $day_num ); ?>">
						<?php echo esc_html( $day_num ); ?>
						</option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.zip" ></span>
				<label>ZIP Code:</label>
				<input type="text" name="profile.zip" />

				<span class="gigya-error-msg" data-bound-to="profile.gender" ></span>
				<label class="gender-label">Gender:</label>
				<label class="inline-label"><input type="radio" name="profile.gender" value="m" />Male</label>
				<label class="inline-label"><input type="radio" name="profile.gender" value="f" />Female</label>

				<span class="gigya-error-msg" data-bound-to="profile.state" ></span>
				<label>State:</label>
				<select name="profile.state">
					<?php foreach ( $state_names as $state ) { ?>
						<option value="<?php echo esc_attr( $state['value'] ); ?>"><?php echo esc_html( $state['label'] ); ?></option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.city" ></span>
				<label>City:</label>
				<input type="text" name="profile.city" />

				<h2>Password</h2>
				<a href="#" data-switch-screen="gigya-change-password-screen">Change your password.</a>

				<h2>Email Subscriptions</h2>

				<ul class="member-groups-list">
					<?php foreach ( $emma_groups as $emma_group ) { ?>
						<li>
							<input
								type="checkbox"
								name="data.<?php echo esc_attr( $emma_group['field_key'] ) ?>" />
							<label class="label-email-list">
								<?php echo esc_html( $emma_group['group_name'] ) ?>
							</label>
						</li>
					<?php } ?>
				</ul>

				<a href="#" class="link-button logout-button">&laquo; Logout</a>
				<input type="submit" name="submit" value="Update Profile" />
			</form>
		</div>

		<div class="gigya-screen" id="gigya-change-password-screen" data-responsive="true">
			<h2>Change Your Password</h2>

			<form class="gigya-profile-form" id="gigya-profile-form" data-on-success-screen="gigya-change-password-success-screen">
				<span class="gigya-error-msg login-error-msg" data-bound-to="gigya-profile-form"></span>

				<span class="gigya-error-msg" data-bound-to="password" ></span>
				<label>Enter your current password:</label>
				<input type="password" name="password" />

				<span class="gigya-error-msg" data-bound-to="newPassword" ></span>
				<label>Choose a new password:</label>
				<input type="password" name="newPassword" />

				<span class="gigya-error-msg" data-bound-to="passwordRetype" ></span>
				<label>Retype new password:</label>
				<input type="password" name="passwordRetype" />

				<a href="#" class="link-button" data-switch-screen="gigya-update-profile-screen">&laquo; Back</a>
				<input type="submit" name="submit" value="Update" />
			</form>
		</div>

		<div class="gigya-screen" id="gigya-update-profile-success-screen" data-responsive="true">
			<h2>Profile Updated Successfully.</h2>

			<a href="#" class="link-button" data-switch-screen="gigya-update-profile-screen">&laquo; Back</a>
		</div>

		<div class="gigya-screen" id="gigya-change-password-success-screen" data-responsive="true">
			<h2>Password Changed Successfully.</h2>

			<a href="#" class="link-button" data-switch-screen="gigya-update-profile-screen">&laquo; Back</a>
		</div>

		<div class="gigya-screen" id="gigya-reset-link-password-screen" data-responsive="true">
			<h2>Reset Your Password</h2>

			<form class="gigya-profile-form" id="gigya-reset-link-password-form">
				<span class="gigya-error-msg reset-link-password-error-msg"></span>

				<span class="gigya-error-msg" data-bound-to="newPassword" ></span>
				<label>Choose a new password:</label>
				<input type="password" name="newPassword" />

				<span class="gigya-error-msg" data-bound-to="passwordRetype" ></span>
				<label>Confirm new password:</label>
				<input type="password" name="passwordRetype" />

				<input type="submit" name="submit" value="Reset" />
			</form>

			<a href="#" class="link-button" data-switch-screen="gigya-login-screen">&laquo; Back</a>
		</div>

		<div class="gigya-screen" id="gigya-reset-link-password-progress-screen" data-responsive="true">
			<h2>Resetting Password ...</h2>
		</div>

		<div class="gigya-screen" id="gigya-reset-link-password-success-screen" data-responsive="true">
			<h2>Password Reset Successfully</h2>

			<a href="#" class="link-button" data-switch-screen="gigya-login-screen">&laquo; Back to Login</a>
		</div>

		<div class="gigya-screen" id="gigya-resend-verification-code-screen" data-responsive="true">
			<h2>Email Verification</h2>
			<h3>Your email has not been verified. Please check your inbox for the verification email.</h3>

			<form class="gigya-resend-verification-code-form" id="gigya-resend-verification-code-form" data-on-success-screen="gigya-resend-verification-code-success-screen">
				<span class="gigya-error-msg login-error-msg" data-bound-to="gigya-resend-verification-code-form"></span>

				<span class="gigya-error-msg" data-bound-to="email" placeholder="Email:" ></span>
				<input type="text" name="email" placeholder="Email" id="resend-email" />

				<a href="#" class="link-button" data-switch-screen="gigya-login-screen">&laquo; Back</a>
				<input type="submit" name="submit" value="Resend Verification" />
			</form>
		</div>

		<div class="gigya-screen" id="gigya-resend-verification-code-success-screen" data-responsive="true">
			<h2>Email Verification</h2>
			<h3>A confirmation email has been sent to you with a link to activate your account.</h3>

			<a href="#" class="link-button" data-switch-screen="gigya-login-screen">&laquo; Back to Login</a>
		</div>

		<div class="gigya-screen" id="gigya-resend-verification-code-update-screen" data-responsive="true">
			<h2>Email Verification</h2>
			<h3>Your email has not been verified. Please check your inbox for the verification email.</h3>

			<form class="gigya-resend-verification-code-form" id="gigya-resend-verification-code-form" data-on-success-screen="gigya-resend-verification-code-update-success-screen">
				<span class="gigya-error-msg login-error-msg" data-bound-to="gigya-resend-verification-code-form"></span>

				<span class="gigya-error-msg" data-bound-to="email" placeholder="Email:" ></span>
				<input type="text" name="email" placeholder="Email" id="resend-email" />

				<a href="#" class="link-button" data-switch-screen="gigya-update-profile-screen">&laquo; Back</a>
				<input type="submit" name="submit" value="Resend Verification" />
			</form>
		</div>

		<div class="gigya-screen" id="gigya-resend-verification-code-update-success-screen" data-responsive="true">
			<h2>Email Verification</h2>
			<h3>A confirmation email has been sent to you with a link to activate your account.</h3>

			<a href="#" class="link-button" data-switch-screen="gigya-update-profile-screen">&laquo; Back</a>
		</div>

		<div class="gigya-screen" id="gigya-verify-email-screen" data-responsive="true">
			<h2>Email Verification</h2>
			<h3><?php echo get_gigya_verify_email_message(); ?></h3>

			<a href="#" class="link-button" data-switch-screen="gigya-login-screen">&laquo; Back to Login</a>
		</div>


	</div><!--end screenset -->

	<main class="main" role="main">

		<div class="container profile-page__container">

			<div class="profile-page__sidebar">

				<h1 class="profile-header">
					<span class="profile-header-text"></span>
					<span class="profile-header-sep" style="display:none">/</span>
					<a class="profile-header-link" href="#" style="display:none">REGISTER</a>
				</h1>
				<span class="profile-message"></span>

			</div>

			<div id="profile-content" class="profile-page__content">

			</div>

		</div>

	</main>

<?php get_footer(); ?>

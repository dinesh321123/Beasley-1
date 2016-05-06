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

function emma_filter_active_groups( $group ) {
	if ( ! array_key_exists( 'group_active', $group ) ) {
		// old settings before this feature, assumes all such groups are
		// active by default
		return true;
	} else if ( filter_var( $group['group_active'], FILTER_VALIDATE_BOOLEAN ) === true ) {
		return true;
	} else {
		return false;
	}
}

$emma_groups = get_option( 'emma_groups' );
$emma_groups = json_decode( $emma_groups, true );
if ( ! $emma_groups ) {
	$emma_groups = array();
} else {
	$emma_groups = array_filter( $emma_groups, 'emma_filter_active_groups' );
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
				<label for="login-email">Email</label><input type="text" name="loginID" id="login-email" placeholder="Email" />

				<span class="gigya-error-msg" data-bound-to="password" ></span>
				<label for="login-password">Password</label><input type="password" name="password" id="login-password" placeholder="Password" />

				<a href="#" class="link-button" data-switch-screen="gigya-forgot-password-screen">Forgot Password?</a>
				<input type="submit" name="submit" class="gigya-input-submit-button" value="Login" />

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
				<label for="password-reset-email">Email</label><input type="text" name="loginID" id="password-reset-email" placeholder="Email" />

				<a href="#" class="link-button" data-switch-screen="gigya-login-screen">&laquo; Back</a>
				<input type="submit" name="submit" class="gigya-input-submit-button" value="Submit" />
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
				<label for="register-email">Email</label><input type="text" name="email" id="register-email" placeholder="Email" />

				<span class="gigya-error-msg" data-bound-to="profile.firstName" ></span>
				<label for="register-firstName">First Name</label><input type="text" name="profile.firstName" id="register-firstName" placeholder="First Name" />

				<span class="gigya-error-msg" data-bound-to="profile.lastName" ></span>
				<label for="register-lastName">Last Name</label><input type="text" name="profile.lastName" id="register-lastName" placeholder="Last Name" />

				<span class="gigya-error-msg" data-bound-to="password" ></span>
				<label for="register-password">Password</label><input type="password" name="password" id="register-password" placeholder="Password" />
				<div class="profile__helper">Passwords must be a minimum of 6 characters</div>

				<span class="gigya-error-msg" data-bound-to="passwordRetype" ></span>
				<label for="register-password-confirm">Re-Enter Password</label><input type="password" name="passwordRetype" id="register-password-confirm" placeholder="Re-Enter Password" />
				<div class="profile__helper">Passwords must be a minimum of 6 characters</div>

				<a href="#" class="link-button" data-switch-screen="gigya-login-screen">&laquo; Login</a>
				<input type="submit" name="submit" class="gigya-input-submit-button" value="Register" />
			</form>
		</div>

		<div class="gigya-screen" id="gigya-register-complete-screen" data-responsive="true">
			<h2>Complete Your Profile</h2>

			<form class="gigya-profile-form" id="gigya-profile-form" data-on-success-screen="gigya-register-success-screen">
				<span class="gigya-error-msg login-error-msg" data-bound-to="gigya-profile-form"></span>

				<span class="gigya-error-msg" data-bound-to="profile.email" ></span>
				<label for="register-complete-email">Email</label><input type="text" name="profile.email" id="register-complete-email" placeholder="Email" />

				<span class="gigya-error-msg" data-bound-to="profile.firstName" ></span>
				<label for="register-complete-firstName">First Name</label><input type="text" name="profile.firstName" id="register-complete-firstName" placeholder="First Name"/>

				<span class="gigya-error-msg" data-bound-to="profile.lastName" ></span>
				<label for="register-complete-lastName">Last Name</label><input type="text" name="profile.lastName" id="register-complete-lastName" placeholder="Last Name" />

				<span class="gigya-error-msg" data-bound-to="profile.birthYear" ></span>
				<label for="register-complete-birthYear">Year of Birth:</label>
				<select name="profile.birthYear" id="register-complete-birthYear">
					<?php foreach ( range( 1920, date( 'Y' ) ) as $year ) { ?>
						<option value="<?php echo esc_attr( $year ); ?>"><?php echo esc_html( $year ); ?></option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.birthMonth" ></span>
				<label for="register-complete-birthMonth">Month of Birth:</label>
				<select name="profile.birthMonth" id="register-complete-birthMonth">
					<?php foreach ( $months as $month_num => $month_name ) { ?>
						<option value="<?php echo esc_attr( $month_num + 1 ); ?>">
						<?php echo esc_html( $month_name ); ?>
						</option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.birthDay" ></span>
				<label for="register-complete-birthDay">Day of Birth:</label>
				<select name="profile.birthDay" id="register-complete-birthDay">
					<?php foreach ( range( 1, 31 ) as $day_num ) { ?>
						<option value="<?php echo esc_attr( $day_num ); ?>">
						<?php echo esc_html( $day_num ); ?>
						</option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.gender" ></span>
				<label class="gender-label">Gender:</label>
				<label class="inline-label" for="register-complete-gender-male"><input type="radio" name="profile.gender" id="register-complete-gender-male" value="m" />Male</label>
				<label class="inline-label" for="register-complete-gender-female"><input type="radio" name="profile.gender" id="register-complete-gender-female" value="f" />Female</label>

				<span class="gigya-error-msg" data-bound-to="profile.country" ></span>
				<label class="country-label" for="profile-country">Country:</label>
				<select name="profile.country" id="profile-country" >
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.state" ></span>
				<label for="profile-state">State / Province:</label>
				<select name="profile.state" id="profile-state">
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.city" ></span>
				<label for="profile-city">City:</label>
				<input type="text" name="profile.city" id="profile-city" />

				<span class="gigya-error-msg" data-bound-to="profile.zip" id="profile-zip-error"></span>
				<label for="register-complete-zip">ZIP Code</label>
				<input type="text" name="profile.zip" id="register-complete-zip" placeholder="ZIP Code" />

				<h2>Email Subscriptions</h2>

				<ul class="member-groups-list">
					<?php foreach ( $emma_groups as $emma_group ) { ?>
					<?php
						$emma_group_id          = $emma_group['group_id'];
						$emma_group_name        = $emma_group['group_name'];
						$emma_group_description = empty( $emma_group['group_description'] ) ? $emma_group_name : $emma_group['group_description'];
						$field_key              = $emma_group['field_key'];
						$checked              	= ( true === $emma_group['opt_in_default'] ) ? true : false;

					?>
						<li>
							<input
								type="checkbox"
								name="data.<?php echo esc_attr( $field_key ) ?>" <?php checked( $checked ); ?>
								id="emma_group_<?php echo esc_attr( $emma_group_id ); ?>" />
							<label class="label-email-list" for="emma_group_<?php echo esc_attr( $emma_group_id ); ?>">
								<?php echo esc_html( $emma_group_description ) ?>
							</label>
						</li>
					<?php } ?>
				</ul>

				<h2>Radio Listening Questions:</h2>

				<label for="register-complete-listening-frequency">On a typical day, about how much time would you say that you spend listening to the radio?</label>
				<span class="gigya-error-msg" data-bound-to="data.listeningFrequency" ></span>
				<select name="data.listeningFrequency" id="register-complete-listening-frequency">
					<option disabled selected value>Select One</option>
					<option value="0">less than 1 hour</option>
					<option value="1">1 to 3 hours</option>
					<option value="2">more than 3 hours</option>
				</select>

				<label for="register-complete-listening-loyalty">When you're listening to the radio, about what percentage of time do you spend listening to <?php echo get_bloginfo( 'name' ); ?>?</label>
				<span class="gigya-error-msg" data-bound-to="data.listeningLoyalty" ></span>
				<select name="data.listeningLoyalty" id="register-complete-listening-loyalty">
					<option disabled selected value>Select One</option>
					<?php foreach ( range( 0, 100, 10 ) as $percent ) { ?>
						<option value="<?php echo esc_attr( $percent ); ?>"><?php echo esc_html( $percent . '%' ); ?></option>
					<?php } ?>
				</select>

				<input type="submit" name="submit" class="gigya-input-submit-button" value="Join the Club" />
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

				<label for="profile-update-email">Email:
					<a href="#"
						class="link-button verify-email-link"
						data-switch-screen="gigya-resend-verification-code-update-screen">Verify Email</a>
				</label>
				<input type="text" name="profile.email" id="profile-update-email" />

				<span class="gigya-error-msg" data-bound-to="profile.firstName" ></span>
				<label for="profile-update-firstName">First Name:</label>
				<input type="text" name="profile.firstName" id="profile-update-firstName" />

				<span class="gigya-error-msg" data-bound-to="profile.lastName" ></span>
				<label for="profile-update-lastName">Last Name:</label>
				<input type="text" name="profile.lastName" id="profile-update-lastName" />

				<span class="gigya-error-msg" data-bound-to="profile.birthYear" ></span>
				<label for="profile-update-birthYear">Year of Birth:</label>
				<select name="profile.birthYear" id="profile-update-birthYear">
					<?php foreach ( range( 1920, date( 'Y' ) ) as $year ) { ?>
						<option value="<?php echo esc_attr( $year ); ?>"><?php echo esc_html( $year ); ?></option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.birthMonth" ></span>
				<label for="profile-update-birthMonth">Month of Birth:</label>
				<select name="profile.birthMonth" id="profile-update-birthMonth">
					<?php foreach ( $months as $month_num => $month_name ) { ?>
						<option value="<?php echo esc_attr( $month_num + 1 ); ?>">
						<?php echo esc_html( $month_name ); ?>
						</option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.birthDay" ></span>
				<label for="profile-update-birthDay">Day of Birth:</label>
				<select name="profile.birthDay" id="profile-update-birthDay">
					<?php foreach ( range( 1, 31 ) as $day_num ) { ?>
						<option value="<?php echo esc_attr( $day_num ); ?>">
						<?php echo esc_html( $day_num ); ?>
						</option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.gender" ></span>
				<label class="gender-label">Gender:</label>
				<label class="inline-label" for="profile-update-gender-male">
				<input type="radio" name="profile.gender" id="profile-update-gender-male" value="m" />Male</label>
				<label class="inline-label" for="profile-update-gender-female">
				<input type="radio" name="profile.gender" id="profile-update-gender-female" value="f" />Female</label>

				<span class="gigya-error-msg" data-bound-to="profile.country" ></span>
				<label class="country-label" for="profile-update-country">Country:</label>
				<select name="profile.country" id="profile-update-country" >
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.state" ></span>
				<label for="profile-update-state">State / Province:</label>
				<select name="profile.state" id="profile-update-state">
				</select>

				<span class="gigya-error-msg" data-bound-to="profile.city" ></span>
				<label for="profile-update-city">City:</label>
				<input type="text" name="profile.city" id="profile-update-city" />


				<span class="gigya-error-msg" data-bound-to="profile.zip" id="profile-update-zip-error"></span>
				<label for="profile-update-zip">ZIP Code:</label>
				<input type="text" name="profile.zip" id="profile-update-zip" />



				<h2>Password</h2>
				<a href="#" data-switch-screen="gigya-change-password-screen">Change your password.</a>

				<h2>Email Subscriptions</h2>

				<ul class="member-groups-list">
					<?php foreach ( $emma_groups as $emma_group ) { ?>
					<?php
						$emma_group_id          = $emma_group['group_id'];
						$emma_group_name        = $emma_group['group_name'];
						$emma_group_description = empty( $emma_group['group_description'] ) ? $emma_group_name : $emma_group['group_description'];
						$field_key              = $emma_group['field_key'];
					?>
						<li>
							<input
								type="checkbox"
								name="data.<?php echo esc_attr( $field_key ) ?>"
								id="emma_group_<?php echo esc_attr( $emma_group_id ); ?>" />
							<label class="label-email-list" for="emma_group_<?php echo esc_attr( $emma_group_id ); ?>">
								<?php echo esc_html( $emma_group_description ) ?>
							</label>
						</li>
					<?php } ?>
				</ul>

				<div class="nielsen-research">
					<h2>Nielsen Research</h2>
					<p>Our properties may feature Nielsen proprietary measurement software, which will allow you to contribute to market research, such as Nielsen TV Ratings. To learn more about the information that Nielsen software may collect and your choices with regard to it, please see the Nielsen Digital Measurement Privacy Policy at <a href="http://www.nielsen.com/digitalprivacy">http://www.nielsen.com/digitalprivacy</a>.</p>

					<input type="checkbox" id="nielsen_optout" name="data.nielsen_optout" class="nielsen-opt-out" />
					<label class="label-nielsen-opt-out" for="nielsen_optout">Opt-out of Nielsen's Online Measurement Research.</label>
				</div>

				<a href="#" class="link-button logout-button">&laquo; Logout</a>
				<input type="submit" name="submit" class="gigya-input-submit-button" value="Update Profile" />
			</form>
		</div>

		<div class="gigya-screen" id="gigya-change-password-screen" data-responsive="true">
			<h2>Change Your Password</h2>

			<form class="gigya-profile-form" id="gigya-profile-form" data-on-success-screen="gigya-change-password-success-screen">
				<span class="gigya-error-msg login-error-msg" data-bound-to="gigya-profile-form"></span>

				<span class="gigya-error-msg" data-bound-to="password" ></span>
				<label for="password-change-current">Enter your current password:</label>
				<input type="password" name="password" id="password-change-current" />

				<span class="gigya-error-msg" data-bound-to="newPassword" ></span>
				<label for="password-change-new">Choose a new password:</label>
				<input type="password" name="newPassword" id="password-change-new" />
				<div class="profile__helper">Passwords must be a minimum of 6 characters</div>

				<span class="gigya-error-msg" data-bound-to="passwordRetype" ></span>
				<label for="password-change-confirm">Retype new password:</label>
				<input type="password" name="passwordRetype" id="password-change-confirm" />
				<div class="profile__helper">Passwords must be a minimum of 6 characters</div>

				<a href="#" class="link-button" data-switch-screen="gigya-update-profile-screen">&laquo; Back</a>
				<input type="submit" name="submit" class="gigya-input-submit-button" value="Update" />
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
				<label for="password-reset-new">Choose a new password:</label>
				<input type="password" name="newPassword" id="password-reset-new" />
				<div class="profile__helper">Passwords must be a minimum of 6 characters</div>

				<span class="gigya-error-msg" data-bound-to="passwordRetype" ></span>
				<label for="password-reset-confirm">Confirm new password:</label>
				<input type="password" name="passwordRetype" id="password-reset-confirm" />
				<div class="profile__helper">Passwords must be a minimum of 6 characters</div>

				<input type="submit" name="submit" class="gigya-input-submit-button" value="Reset" />
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
				<label for="resend-email">Email</label><input type="text" name="email" placeholder="Email" id="resend-email" />

				<a href="#" class="link-button" data-switch-screen="gigya-login-screen">&laquo; Back</a>
				<input type="submit" name="submit" class="gigya-input-submit-button" value="Resend Verification" />
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
				<label for="resend-email">Email</label><input type="text" name="email" placeholder="Email" id="resend-email" />

				<a href="#" class="link-button" data-switch-screen="gigya-update-profile-screen">&laquo; Back</a>
				<input type="submit" name="submit" class="gigya-input-submit-button" value="Resend Verification" />
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

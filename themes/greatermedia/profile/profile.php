<?php get_header(); ?>

<?php

$page_heading = 'Login';
$page_message = 'Membership gives you access to all areas of the site, including full membership-only contests and the ability to submit content to share with the site and other members.';

?>

	<div class="gigya-screen-set" id="GMR-CustomScreenSet" style="display:none" data-on-pending-registration-screen="gigya-register-complete-screen">
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

				<input type="submit" name="submit" value="Login" />
			</form>
		</div>

		<div class="gigya-screen" id="gigya-logout-screen" data-responsive="true">
			<h2>Logging you out ...</h2>
		</div>

		<div class="gigya-screen" id="gigya-login-success-screen" data-responsive="true">
			<h2>Login successful.</h2>
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

				<span class="gigya-error-msg" data-bound-to="firstName" ></span>
				<input type="text" name="firstName" placeholder="First Name" />

				<span class="gigya-error-msg" data-bound-to="lastName" ></span>
				<input type="text" name="lastName" placeholder="Last Name" />

				<span class="gigya-error-msg" data-bound-to="password" ></span>
				<input type="password" name="password" placeholder="Password" />

				<span class="gigya-error-msg" data-bound-to="passwordRetype" ></span>
				<input type="password" name="passwordRetype" placeholder="Re-Enter Password" />

				<input type="submit" name="submit" value="Register" />
			</form>
		</div>

		<div class="gigya-screen" id="gigya-register-complete-screen" data-responsive="true">
			<h2>Complete Your Profile</h2>

			<form class="gigya-profile-form" id="gigya-profile-form" data-on-success-screen="gigya-register-success-screen">
				<span class="gigya-error-msg login-error-msg" data-bound-to="gigya-profile-form"></span>

				<span class="gigya-error-msg" data-bound-to="email" ></span>
				<input type="text" name="email" placeholder="Email" />

				<span class="gigya-error-msg" data-bound-to="firstName" ></span>
				<input type="text" name="firstName" placeholder="First Name"/>

				<span class="gigya-error-msg" data-bound-to="lastName" ></span>
				<input type="text" name="lastName" placeholder="Last Name" />

				<span class="gigya-error-msg" data-bound-to="birthYear" ></span>
				<label>Year of Birth:</label>
				<select name="birthYear">
					<?php foreach ( range( 1920, date( 'Y' ) ) as $year ) { ?>
						<option value="<?php echo esc_attr( $year ); ?>"><?php echo esc_html( $year ); ?></option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="zip" ></span>
				<input type="text" name="zip" placeholder="Postcode" />

				<span class="gigya-error-msg" data-bound-to="gender" ></span>
				<label>Gender:</label>
				<select name="gender">
					<option value="m">Male</option>
					<option value="f">Female</option>
					<option value="u">Unknown</option>
				</select>

				<h2>Email Subscriptions</h2>

				<ul class="member-groups-list">
					<li>
						<input type="checkbox" name="data.vipGroup" checked="checked" />
						<label class="label-email-list">VIP Newsletter</label>
					</li>
					<li>
						<input type="checkbox" name="data.bigFrigginDealGroup" checked="checked">
						<label class="label-email-list">Big Friggin' Deal</label>
					</li>
					<li>
						<input type="checkbox" name="data.birthdayGreetingsGroup" checked="checked">
						<label class="label-email-list">Birthday Greetings</label>
					</li>
				</ul>

				<h2>Radio Listening Questions:</h2>

				<label>On a typical day, about how much time would you say that you spend listening to the radio?</label>
				<select name="data.listeningFrequency">
					<option value="0">less than 1 hour</option>
					<option value="1">1 to 3 hours</option>
					<option value="2">more than 3 hours</option>
				</select>

				<label>When you're listening to the radio, about what percentage of time do you spend listening to 93.3 WMMR?</label>
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

			<form class="gigya-profile-form" id="gigya-profile-form">
				<span class="gigya-error-msg login-error-msg" data-bound-to="gigya-profile-form"></span>
				<span class="gigya-error-msg" data-bound-to="email" ></span>

				<label>Email:</label>
				<input type="text" name="email" />

				<span class="gigya-error-msg" data-bound-to="firstName" ></span>
				<label>First Name:</label>
				<input type="text" name="firstName" />

				<span class="gigya-error-msg" data-bound-to="lastName" ></span>
				<label>Last Name:</label>
				<input type="text" name="lastName" />

				<span class="gigya-error-msg" data-bound-to="lastName" ></span>
				<label>Year of Birth:</label>
				<select name="birthYear">
					<?php foreach ( range( 1920, date( 'Y' ) ) as $year ) { ?>
						<option value="<?php echo esc_attr( $year ); ?>"><?php echo esc_html( $year ); ?></option>
					<?php } ?>
				</select>

				<span class="gigya-error-msg" data-bound-to="zip" ></span>
				<label>Postcode:</label>
				<input type="text" name="zip" />

				<span class="gigya-error-msg" data-bound-to="gender" ></span>
				<label>Gender:</label>
				<select name="gender">
					<option value="m">Male</option>
					<option value="f">Female</option>
					<option value="u">Unknown</option>
				</select>

				<h2>Email Subscriptions</h2>

				<ul class="member-groups-list">
					<li>
						<input type="checkbox" name="data.vipGroup" />
						<label class="label-email-list">VIP Newsletter</label>
					</li>
					<li>
						<input type="checkbox" name="data.bigFrigginDealGroup">
						<label class="label-email-list">Big Friggin' Deal</label>
					</li>
					<li>
						<input type="checkbox" name="data.birthdayGreetingsGroup">
						<label class="label-email-list">Birthday Greetings</label>
					</li>
				</ul>

				<input type="submit" name="submit" value="Update Profile" />
			</form>
		</div>

	</div><!-- end screenset --!>

	<main class="main" role="main">

		<div class="container profile-page__container">

			<div class="profile-page__sidebar">

				<!-- WIP - needs to mirror the JS -->
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

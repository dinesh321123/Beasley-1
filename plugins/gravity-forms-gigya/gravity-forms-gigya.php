<?php

/*
Plugin Name: Greater Media Gravity Forms Integration
Author: 10up, Thaicloud
License: gpl
*/

class GMR_Gform {

	public static function hooks() {

		// Enqueue js
		add_action( 'wp_enqueue_scripts', array( __CLASS__, "enqueue_scripts" ) );

		// Apply hide class to disable single contest form
		add_filter( "gform_form_tag", array( __CLASS__, "gmi_form_tag" ) );

		// Show Gigya login/registration form before Gravity Form on single contest page
		add_filter( "gform_pre_render", array( __CLASS__, "gmi_pre_render_form" ) );

		// When a Gravity form is submitted it fires this hook callback. Send (allthethings) to Gigya & CPTs
		add_action( "gform_after_submission", array( __CLASS__, "gmi_after_submission" ), 10, 2 );

		// Sets up the HTML for selecting the Gigya demographic fields in Gravity Forms
		add_action( "gform_field_advanced_settings", array( __CLASS__, "gmi_gigya_profile_settings" ), 10, 2 );

		// If Gigya demographic field was set, apply key to GF 'parameter name' field
		add_filter( "gform_admin_pre_render", array( __CLASS__, "apply_demographic_setting" ), 1 );

		// Outputs scripts for gravity forms
		add_action( "gform_editor_js", array( __CLASS__, "editor_script" ) );

		// Add a help tooltip for the gigya profile fields
		add_filter( "gform_tooltips", array( __CLASS__, "add_encryption_tooltips" ) );

		// Set hidden fields for nabbing gigya user data
		add_filter( "gform_submit_button", array( __CLASS__, "gigya_hidden_fields" ), 10, 5 );

		// Add custom Gigya Field section to GForm UI
		// add_filter( "gform_add_field_buttons", array( __CLASS__, "add_gigya_fields" ), 10, 2 );

		// Add Gigya Field prefix to custom field titles
		// add_filter( "gform_field_type_title", array( __CLASS__, "assign_title" ), 10, 2 );

		// Handle each custom field type
		// add_action( "gform_field_input" , array( __CLASS__, "custom_field_input" ), 10, 5 );

	}

	public static function enqueue_scripts() {

		// greatermedia.dev / 10uplabs apiKey
		$dev_key = '3_e_T7jWO0Vjsd9y0WJcjnsN6KaFUBv6r3VxMKqbitvw-qKfmaUWysQKa1fra5MTb6';

		?>
		<script type='text/javascript' src='http://cdn.gigya.com/JS/socialize.js?apiKey=<?php echo $dev_key; ?>'>
			{ enabledProviders: 'facebook,twitter,linkedin,yahoo,messenger' }
		</script>
		<?php
		wp_enqueue_script( 'gravity-forms-gigya', plugins_url( null, __FILE__ ) . "/js/gravity-forms-gigya.js", array( 'jquery' ) );

	}

	/**
	 * Fade out form on single contest page
	 *
	 * * @param  string $form_tag
	 */
	public static function gmi_form_tag( $form_tag ) {
		if ( is_singular( GreaterMediaContests::CPT_SLUG ) ) {
			$form_tag = str_replace( "<form ", "<form class='hide' ", $form_tag );
		}

		return $form_tag;
	}

	/**
	 * Show Gigya login/registration form before Gravity Form on single contest page
	 *
	 * * @param  array $form Gravity Forms form object {@link http://www.gravityhelp.com/documentation/page/Developer_Docs#Form_Object}
	 */
	public static function gmi_pre_render_form( $form ) {
		if ( is_singular( GreaterMediaContests::CPT_SLUG ) ) {
			?>
			<div id="gigya-login-wrap">

			<div id="gigya-buttons" class="gigya-buttons">
				<a href="#" class="register">Create an Account</a>
				<a href="#" class="login">Login</a>
			</div>

			<div id="gigya-controls"></div>

			<div class="gigya-screen-set" id="Contest-login" style="display: none;" data-on-pending-registration-screen="gigya-complete-registiration-screen"
			     data-on-pending-verification-screen="gigya-email-verification-screen" data-on-pending-tfa-registration-screen="gigya-tfa-registration-screen"
			     data-on-pending-tfa-verification-screen="gigya-tfa-verification-screen" data-on-pending-password-change-screen="gigya-password-change-required-screen"
			     data-on-existing-login-identifier-screen="gigya-link-account-screen" data-on-pending-recent-login-screen="gigya-recent-login-screen"
			     data-width="760" data-responsive="true">
			<div class="gigya-screen" id="gigya-login-screen" data-caption="Login" data-width="700"
			     style="max-width: 700px;">
				<form class="gigya-login-form">
					<div class="gigya-layout-row"></div>
					<div class="gigya-layout-row">
						<div class="gigya-layout-cell">
							<h2 class="gigya-composite-control gigya-composite-control-header">Login with your social network:</h2>

							<div class="gigya-composite-control gigya-spacer"
							     data-units="2" style="height: 20px;"></div>
							<div class="gigya-composite-control gigya-composite-control-social-login">
								<div class="gigya-social-login">
									<param name="width" value="300">
									<param name="height" value="100">
									<param name="buttonsStyle" value="fullLogo">
									<param name="buttonSize" value="35">
									<param name="showWhatsThis" value="false">
									<param name="showTermsLink" value="false">
									<param name="hideGigyaLink" value="true">
								</div>
							</div>
						</div>
						<div class="gigya-layout-cell">
							<h2 class="gigya-composite-control gigya-composite-control-header">Or, login here:</h2>

							<div class="gigya-composite-control gigya-spacer" data-units="2"
							     style="height: 20px;"></div>
							<div class="gigya-composite-control gigya-composite-control-textbox">
								<label class="gigya-label">
									<span class="gigya-label-text">Email:</span>
								</label>
								<input type="text" name="loginID" class="gigya-input-text" tabindex="1" autocomplete="off"
								       style="cursor: auto; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QsPDhss3LcOZQAAAU5JREFUOMvdkzFLA0EQhd/bO7iIYmklaCUopLAQA6KNaawt9BeIgnUwLHPJRchfEBR7CyGWgiDY2SlIQBT/gDaCoGDudiy8SLwkBiwz1c7y+GZ25i0wnFEqlSZFZKGdi8iiiOR7aU32QkR2c7ncPcljAARAkgckb8IwrGf1fg/oJ8lRAHkR2VDVmOQ8AKjqY1bMHgCGYXhFchnAg6omJGcBXEZRtNoXYK2dMsaMt1qtD9/3p40x5yS9tHICYF1Vn0mOxXH8Uq/Xb389wff9PQDbQRB0t/QNOiPZ1h4B2MoO0fxnYz8dOOcOVbWhqq8kJzzPa3RAXZIkawCenHMjJN/+GiIqlcoFgKKq3pEMAMwAuCa5VK1W3SAfbAIopum+cy5KzwXn3M5AI6XVYlVt1mq1U8/zTlS1CeC9j2+6o1wuz1lrVzpWXLDWTg3pz/0CQnd2Jos49xUAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
								<span class="gigya-error-msg" data-bound-to="loginID"></span>
							</div>
							<div class="gigya-composite-control gigya-composite-control-password">
								<label class="gigya-label">
									<label for="password" class="gigya-label">
                <span class="gigya-label-text">Password: <a data-switch-screen="gigya-forgot-password-screen" class="forgotPassword">Forgot password</a>
                </span>

										<div class="gigya-clear"></div>
									</label>
								</label>
								<input type="password" name="password" class="gigya-input-password" tabindex="1"
								       autocomplete="off" style="cursor: pointer; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QsPDhss3LcOZQAAAU5JREFUOMvdkzFLA0EQhd/bO7iIYmklaCUopLAQA6KNaawt9BeIgnUwLHPJRchfEBR7CyGWgiDY2SlIQBT/gDaCoGDudiy8SLwkBiwz1c7y+GZ25i0wnFEqlSZFZKGdi8iiiOR7aU32QkR2c7ncPcljAARAkgckb8IwrGf1fg/oJ8lRAHkR2VDVmOQ8AKjqY1bMHgCGYXhFchnAg6omJGcBXEZRtNoXYK2dMsaMt1qtD9/3p40x5yS9tHICYF1Vn0mOxXH8Uq/Xb389wff9PQDbQRB0t/QNOiPZ1h4B2MoO0fxnYz8dOOcOVbWhqq8kJzzPa3RAXZIkawCenHMjJN/+GiIqlcoFgKKq3pEMAMwAuCa5VK1W3SAfbAIopum+cy5KzwXn3M5AI6XVYlVt1mq1U8/zTlS1CeC9j2+6o1wuz1lrVzpWXLDWTg3pz/0CQnd2Jos49xUAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
								<span class="gigya-error-msg" data-bound-to="password"></span>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell">
									<div class="gigya-error-display gigya-composite-control gigya-composite-control-form-error"
									     data-bound-to="gigya-login-form">
										<div class="gigya-error-msg gigya-form-error-msg" data-bound-to="gigya-login-form"
										     style=""></div>
									</div>
								</div>
								<div class="gigya-layout-cell">
									<div class="gigya-composite-control gigya-composite-control-submit">
										<input type="submit" class="gigya-input-submit" value="Submit" tabindex="3">
									</div>
								</div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row"></div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell"></div>
						<div class="gigya-layout-cell">
							<div class="gigya-composite-control gigya-spacer" data-units="6" style="height: 60px;"></div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row"></div>
					<div class="gigya-clear"></div>
				</form>
			</div>
			<div class="gigya-screen" id="gigya-tfa-verification-screen" data-caption="Authenticate your account"
			     data-width="350">
				<div class="gigya-layout-row">
					<div class="gigya-composite-control gigya-composite-control-tfa-widget gigya-composite-control-tfa-verify">
						<div class="gigya-tfa">
							<param name="mode" value="verify">
						</div>
					</div>
				</div>
				<div class="gigya-layout-row">
					<div class="gigya-layout-cell"></div>
					<div class="gigya-layout-cell"></div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row ">
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row"></div>
				<div class="gigya-layout-row ">
					<div class="gigya-layout-cell"></div>
					<div class="gigya-layout-cell"></div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row ">
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row"></div>
				<div class="gigya-clear"></div>
			</div>
			<div class="gigya-screen" id="gigya-password-change-required-screen" data-caption="Mandatory Password"
			     data-width="350">
				<form class="gigya-profile-form">
					<div class="gigya-layout-row">
						<label class="gigya-composite-control gigya-composite-control-label" style="display: block;">For security reasons your password needs to be changed</label>

						<div class="gigya-composite-control gigya-composite-control-password"
						     style="display: block;">
							<label class="gigya-label">
								<span class="gigya-label-text">Enter your current password:</span>
							</label>
							<input type="password" name="password" class="gigya-input-password" tabindex="1"
							       autocomplete="off" style="background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QsPDhss3LcOZQAAAU5JREFUOMvdkzFLA0EQhd/bO7iIYmklaCUopLAQA6KNaawt9BeIgnUwLHPJRchfEBR7CyGWgiDY2SlIQBT/gDaCoGDudiy8SLwkBiwz1c7y+GZ25i0wnFEqlSZFZKGdi8iiiOR7aU32QkR2c7ncPcljAARAkgckb8IwrGf1fg/oJ8lRAHkR2VDVmOQ8AKjqY1bMHgCGYXhFchnAg6omJGcBXEZRtNoXYK2dMsaMt1qtD9/3p40x5yS9tHICYF1Vn0mOxXH8Uq/Xb389wff9PQDbQRB0t/QNOiPZ1h4B2MoO0fxnYz8dOOcOVbWhqq8kJzzPa3RAXZIkawCenHMjJN/+GiIqlcoFgKKq3pEMAMwAuCa5VK1W3SAfbAIopum+cy5KzwXn3M5AI6XVYlVt1mq1U8/zTlS1CeC9j2+6o1wuz1lrVzpWXLDWTg3pz/0CQnd2Jos49xUAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
							<span class="gigya-error-msg" data-bound-to="password"></span>
						</div>
						<div class="gigya-composite-control gigya-composite-control-password" style="display: block;">
							<label class="gigya-label">
								<span class="gigya-label-text">Choose a new password:</span>
							</label>
							<input type="password" name="newPassword" class="gigya-input-password" tabindex="2"
							       style="background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACIUlEQVQ4EX2TOYhTURSG87IMihDsjGghBhFBmHFDHLWwSqcikk4RRKJgk0KL7C8bMpWpZtIqNkEUl1ZCgs0wOo0SxiLMDApWlgOPrH7/5b2QkYwX7jvn/uc//zl3edZ4PPbNGvF4fC4ajR5VrNvt/mo0Gr1ZPOtfgWw2e9Lv9+chX7cs64CS4Oxg3o9GI7tUKv0Q5o1dAiTfCgQCLwnOkfQOu+oSLyJ2A783HA7vIPLGxX0TgVwud4HKn0nc7Pf7N6vV6oZHkkX8FPG3uMfgXC0Wi2vCg/poUKGGcagQI3k7k8mcp5slcGswGDwpl8tfwGJg3xB6Dvey8vz6oH4C3iXcFYjbwiDeo1KafafkC3NjK7iL5ESFGQEUF7Sg+ifZdDp9GnMF/KGmfBdT2HCwZ7TwtrBPC7rQaav6Iv48rqZwg+F+p8hOMBj0IbxfMdMBrW5pAVGV/ztINByENkU0t5BIJEKRSOQ3Aj+Z57iFs1R5NK3EQS6HQqF1zmQdzpFWq3W42WwOTAf1er1PF2USFlC+qxMvFAr3HcexWX+QX6lUvsKpkTyPSEXJkw6MQ4S38Ljdbi8rmM/nY+CvgNcQqdH6U/xrYK9t244jZv6ByUOSiDdIfgBZ12U6dHEHu9TpdIr8F0OP692CtzaW/a6y3y0Wx5kbFHvGuXzkgf0xhKnPzA4UTyaTB8Ph8AvcHi3fnsrZ7Wore02YViqVOrRXXPhfqP8j6MYlawoAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
							<span class="gigya-error-msg" data-bound-to="newPassword"></span>

							<div class="gigya-password-strength" data-bound-to="newPassword" data-on-focus-bubble="true"></div>
						</div>
						<div class="gigya-composite-control gigya-composite-control-password" style="display: block;">
							<label class="gigya-label">
								<span class="gigya-label-text">Re-enter new password:</span>
							</label>
							<input type="password" name="passwordRetype" class="gigya-input-password" tabindex="3"
							       style="background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACIUlEQVQ4EX2TOYhTURSG87IMihDsjGghBhFBmHFDHLWwSqcikk4RRKJgk0KL7C8bMpWpZtIqNkEUl1ZCgs0wOo0SxiLMDApWlgOPrH7/5b2QkYwX7jvn/uc//zl3edZ4PPbNGvF4fC4ajR5VrNvt/mo0Gr1ZPOtfgWw2e9Lv9+chX7cs64CS4Oxg3o9GI7tUKv0Q5o1dAiTfCgQCLwnOkfQOu+oSLyJ2A783HA7vIPLGxX0TgVwud4HKn0nc7Pf7N6vV6oZHkkX8FPG3uMfgXC0Wi2vCg/poUKGGcagQI3k7k8mcp5slcGswGDwpl8tfwGJg3xB6Dvey8vz6oH4C3iXcFYjbwiDeo1KafafkC3NjK7iL5ESFGQEUF7Sg+ifZdDp9GnMF/KGmfBdT2HCwZ7TwtrBPC7rQaav6Iv48rqZwg+F+p8hOMBj0IbxfMdMBrW5pAVGV/ztINByENkU0t5BIJEKRSOQ3Aj+Z57iFs1R5NK3EQS6HQqF1zmQdzpFWq3W42WwOTAf1er1PF2USFlC+qxMvFAr3HcexWX+QX6lUvsKpkTyPSEXJkw6MQ4S38Ljdbi8rmM/nY+CvgNcQqdH6U/xrYK9t244jZv6ByUOSiDdIfgBZ12U6dHEHu9TpdIr8F0OP692CtzaW/a6y3y0Wx5kbFHvGuXzkgf0xhKnPzA4UTyaTB8Ph8AvcHi3fnsrZ7Wore02YViqVOrRXXPhfqP8j6MYlawoAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
							<span class="gigya-error-msg" data-bound-to="passwordRetype"></span>
						</div>
					</div>
					<div class="gigya-layout-row">
						<div class="gigya-layout-cell"></div>
						<div class="gigya-layout-cell"></div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row"></div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell">
							<div class="gigya-error-display gigya-composite-control gigya-composite-control-form-error"
							     data-bound-to="gigya-profile-form" style="display: block;">
								<div class="gigya-error-msg gigya-form-error-msg" data-bound-to="gigya-profile-form"
								     style=""></div>
							</div>
						</div>
						<div class="gigya-layout-cell">
							<div class="gigya-composite-control gigya-composite-control-submit" style="display: block;">
								<input type="submit" class="gigya-input-submit" value="Submit" tabindex="4">
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row"></div>
					<div class="gigya-clear"></div>
				</form>
			</div>
			<div class="gigya-screen" id="gigya-recent-login-screen" data-caption="Recent Login"
			     data-width="700" style="max-width: 700px;">
				<form class="gigya-login-form">
					<div class="gigya-layout-row">
						<h2 class="gigya-composite-control gigya-composite-control-header" data-wizard-text-bold="bold"
						    style="font-weight: bold;">Please sign-in again to make changes to your account</h2>

						<div class="gigya-composite-control gigya-spacer"
						     data-units="1" style="height: 10px;"></div>
					</div>
					<div class="gigya-layout-row">
						<div class="gigya-layout-cell">
							<h2 class="gigya-composite-control gigya-composite-control-header">Login with your social network:</h2>

							<div class="gigya-composite-control gigya-spacer"
							     data-units="2" style="height: 20px;"></div>
							<div class="gigya-composite-control gigya-composite-control-social-login">
								<div class="gigya-social-login">
									<param name="width" value="300">
									<param name="height" value="100">
									<param name="buttonsStyle" value="fullLogo">
									<param name="buttonSize" value="35">
									<param name="showWhatsThis" value="false">
									<param name="showTermsLink" value="false">
									<param name="hideGigyaLink" value="true">
								</div>
							</div>
						</div>
						<div class="gigya-layout-cell">
							<h2 class="gigya-composite-control gigya-composite-control-header">Or, login here:</h2>

							<div class="gigya-composite-control gigya-spacer" data-units="2"
							     style="height: 20px;"></div>
							<div class="gigya-composite-control gigya-composite-control-textbox">
								<label class="gigya-label">
									<span class="gigya-label-text">Email:</span>
								</label>
								<input type="text" name="loginID" class="gigya-input-text" tabindex="1" autocomplete="off"
								       style="background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QsPDhss3LcOZQAAAU5JREFUOMvdkzFLA0EQhd/bO7iIYmklaCUopLAQA6KNaawt9BeIgnUwLHPJRchfEBR7CyGWgiDY2SlIQBT/gDaCoGDudiy8SLwkBiwz1c7y+GZ25i0wnFEqlSZFZKGdi8iiiOR7aU32QkR2c7ncPcljAARAkgckb8IwrGf1fg/oJ8lRAHkR2VDVmOQ8AKjqY1bMHgCGYXhFchnAg6omJGcBXEZRtNoXYK2dMsaMt1qtD9/3p40x5yS9tHICYF1Vn0mOxXH8Uq/Xb389wff9PQDbQRB0t/QNOiPZ1h4B2MoO0fxnYz8dOOcOVbWhqq8kJzzPa3RAXZIkawCenHMjJN/+GiIqlcoFgKKq3pEMAMwAuCa5VK1W3SAfbAIopum+cy5KzwXn3M5AI6XVYlVt1mq1U8/zTlS1CeC9j2+6o1wuz1lrVzpWXLDWTg3pz/0CQnd2Jos49xUAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
								<span class="gigya-error-msg" data-bound-to="loginID"></span>
							</div>
							<div class="gigya-composite-control gigya-composite-control-password">
								<label class="gigya-label">
									<label for="password" class="gigya-label">
                <span class="gigya-label-text">Password: <a data-switch-screen="gigya-forgot-password-screen" class="forgotPassword">Forgot password</a>
                </span>

										<div class="gigya-clear"></div>
									</label>
								</label>
								<input type="password" name="password" class="gigya-input-password" tabindex="1"
								       autocomplete="off" style="background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QsPDhss3LcOZQAAAU5JREFUOMvdkzFLA0EQhd/bO7iIYmklaCUopLAQA6KNaawt9BeIgnUwLHPJRchfEBR7CyGWgiDY2SlIQBT/gDaCoGDudiy8SLwkBiwz1c7y+GZ25i0wnFEqlSZFZKGdi8iiiOR7aU32QkR2c7ncPcljAARAkgckb8IwrGf1fg/oJ8lRAHkR2VDVmOQ8AKjqY1bMHgCGYXhFchnAg6omJGcBXEZRtNoXYK2dMsaMt1qtD9/3p40x5yS9tHICYF1Vn0mOxXH8Uq/Xb389wff9PQDbQRB0t/QNOiPZ1h4B2MoO0fxnYz8dOOcOVbWhqq8kJzzPa3RAXZIkawCenHMjJN/+GiIqlcoFgKKq3pEMAMwAuCa5VK1W3SAfbAIopum+cy5KzwXn3M5AI6XVYlVt1mq1U8/zTlS1CeC9j2+6o1wuz1lrVzpWXLDWTg3pz/0CQnd2Jos49xUAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
								<span class="gigya-error-msg" data-bound-to="password"></span>
							</div>
							<div class="gigya-composite-control gigya-composite-control-captcha-widget">
								<div class="gigya-captcha-wrapper gigya-error-display" data-error-flags="captchaNeeded"
								     data-bound-to="gigya-login-form">
									<div class="gigya-captcha">
										<param name="theme" value="white">
									</div>
									<span class="gigya-error-msg" data-bound-to="captchaText"></span>
								</div>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell">
									<div class="gigya-error-display gigya-composite-control gigya-composite-control-form-error"
									     data-bound-to="gigya-login-form">
										<div class="gigya-error-msg gigya-form-error-msg" data-bound-to="gigya-login-form"
										     style=""></div>
									</div>
								</div>
								<div class="gigya-layout-cell">
									<div class="gigya-composite-control gigya-composite-control-submit">
										<input type="submit" class="gigya-input-submit" value="Submit" tabindex="3">
									</div>
								</div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row"></div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell"></div>
						<div class="gigya-layout-cell"></div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row"></div>
					<div class="gigya-clear"></div>
				</form>
			</div>
			<div data-caption="Registration" id="gigya-register-screen" class="gigya-screen"
			     data-width="700" data-on-pending-verification-screen="gigya-thank-you-screen" style="max-width: 700px;">
				<form class="gigya-register-form" _lpchecked="1">
					<div class="gigya-layout-row"></div>
					<div class="gigya-layout-row">
						<div class="gigya-layout-row">
							<div class="gigya-layout-cell" style="min-width: 300px;">
								<div class="gigya-layout-row">
									<h2 class="gigya-composite-control gigya-composite-control-header" style="text-align: left;">Login with your social network</h2>
								</div>
								<div class="gigya-layout-row">
									<div class="gigya-layout-cell"></div>
									<div class="gigya-layout-cell"></div>
									<div class="gigya-clear"></div>
								</div>
								<div class="gigya-layout-row">
									<div class="gigya-composite-control gigya-composite-control-social-login">
										<div class="gigya-social-login">
											<param name="width" value="333">
											<param name="height" value="100">
											<param name="buttonStyle" value="fullLogo">
											<param name="buttonSize" value="">
											<param name="showWhatsThis" value="false">
											<param name="showTermsLink" value="false">
											<param name="hideGigyaLink" value="true">
											<param name="buttonsStyle" value="fullLogo">
											<param name="mode" value="standard">
										</div>
									</div>
								</div>
							</div>
							<div class="gigya-layout-cell">
								<div class="gigya-layout-row">
									<h2 class="gigya-composite-control gigya-composite-control-header" style="display: block;">Or, create new account</h2>

									<div class="gigya-composite-control gigya-spacer" data-units="2"
									     style="height: 20px;"></div>
									<div class="gigya-composite-control gigya-composite-control-textbox" style="display: block;">
										<label class="gigya-label">
											<span class="gigya-label-text">Email:</span>
											<label class="gigya-required-display" data-bound-to="email">*</label>
										</label>
										<input type="text" class="gigya-input-text" name="email" data-display-name=""
										       tabindex="1" autocomplete="off" style="cursor: auto; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QsPDhss3LcOZQAAAU5JREFUOMvdkzFLA0EQhd/bO7iIYmklaCUopLAQA6KNaawt9BeIgnUwLHPJRchfEBR7CyGWgiDY2SlIQBT/gDaCoGDudiy8SLwkBiwz1c7y+GZ25i0wnFEqlSZFZKGdi8iiiOR7aU32QkR2c7ncPcljAARAkgckb8IwrGf1fg/oJ8lRAHkR2VDVmOQ8AKjqY1bMHgCGYXhFchnAg6omJGcBXEZRtNoXYK2dMsaMt1qtD9/3p40x5yS9tHICYF1Vn0mOxXH8Uq/Xb389wff9PQDbQRB0t/QNOiPZ1h4B2MoO0fxnYz8dOOcOVbWhqq8kJzzPa3RAXZIkawCenHMjJN/+GiIqlcoFgKKq3pEMAMwAuCa5VK1W3SAfbAIopum+cy5KzwXn3M5AI6XVYlVt1mq1U8/zTlS1CeC9j2+6o1wuz1lrVzpWXLDWTg3pz/0CQnd2Jos49xUAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
										<span class="gigya-error-msg" data-bound-to="email"></span>
									</div>
								</div>
								<div class="gigya-layout-row">
									<div class="gigya-layout-cell">
										<div class="gigya-composite-control gigya-composite-control-textbox" style="display: block;">
											<label class="gigya-label">
												<span class="gigya-label-text">First name:</span>
												<label class="gigya-required-display" data-bound-to="profile.firstName"
												       style="display: inline-block;">*</label>
											</label>
											<input type="text" class="gigya-input-text" name="profile.firstName" data-display-name=""
											       tabindex="2" style="cursor: auto; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
											<span class="gigya-error-msg" data-bound-to="profile.firstName"></span>
										</div>
									</div>
									<div class="gigya-layout-cell">
										<div class="gigya-composite-control gigya-composite-control-textbox" style="display: block;">
											<label class="gigya-label">
												<span class="gigya-label-text">Last name:</span>
												<label class="gigya-required-display" data-bound-to="profile.lastName"
												       style="display: inline-block;">*</label>
											</label>
											<input type="text" class="gigya-input-text" name="profile.lastName" data-display-name=""
											       tabindex="3">
											<span class="gigya-error-msg" data-bound-to="profile.lastName"></span>
										</div>
									</div>
									<div class="gigya-clear"></div>
									<div class="gigya-clear"></div>
								</div>
								<div class="gigya-layout-row">
									<div class="gigya-layout-cell">
										<div class="gigya-composite-control gigya-composite-control-password" style="display: block;">
											<label class="gigya-label">
												<span class="gigya-label-text">Password:</span>
												<label class="gigya-required-display" data-bound-to="password">*</label>
											</label>
											<input type="password" name="password" class="gigya-input-password" data-display-name=""
											       tabindex="4" autocomplete="off" style="background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QsPDhss3LcOZQAAAU5JREFUOMvdkzFLA0EQhd/bO7iIYmklaCUopLAQA6KNaawt9BeIgnUwLHPJRchfEBR7CyGWgiDY2SlIQBT/gDaCoGDudiy8SLwkBiwz1c7y+GZ25i0wnFEqlSZFZKGdi8iiiOR7aU32QkR2c7ncPcljAARAkgckb8IwrGf1fg/oJ8lRAHkR2VDVmOQ8AKjqY1bMHgCGYXhFchnAg6omJGcBXEZRtNoXYK2dMsaMt1qtD9/3p40x5yS9tHICYF1Vn0mOxXH8Uq/Xb389wff9PQDbQRB0t/QNOiPZ1h4B2MoO0fxnYz8dOOcOVbWhqq8kJzzPa3RAXZIkawCenHMjJN/+GiIqlcoFgKKq3pEMAMwAuCa5VK1W3SAfbAIopum+cy5KzwXn3M5AI6XVYlVt1mq1U8/zTlS1CeC9j2+6o1wuz1lrVzpWXLDWTg3pz/0CQnd2Jos49xUAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
											<span class="gigya-error-msg" data-bound-to="password"></span>

											<div class="gigya-password-strength" data-bound-to="password" data-on-focus-bubble="true"></div>
										</div>
									</div>
									<div class="gigya-layout-cell">
										<div class="gigya-composite-control gigya-composite-control-password" style="display: block;">
											<label for="password" class="gigya-label">
												<span class="gigya-label-text">Re-enter Password:</span>
												<label class="gigya-required-display" data-bound-to="password">*</label>
											</label>
											<input type="password" class="gigya-input-password" name="passwordRetype" tabindex="5"
											       data-display-name="" style="background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACIUlEQVQ4EX2TOYhTURSG87IMihDsjGghBhFBmHFDHLWwSqcikk4RRKJgk0KL7C8bMpWpZtIqNkEUl1ZCgs0wOo0SxiLMDApWlgOPrH7/5b2QkYwX7jvn/uc//zl3edZ4PPbNGvF4fC4ajR5VrNvt/mo0Gr1ZPOtfgWw2e9Lv9+chX7cs64CS4Oxg3o9GI7tUKv0Q5o1dAiTfCgQCLwnOkfQOu+oSLyJ2A783HA7vIPLGxX0TgVwud4HKn0nc7Pf7N6vV6oZHkkX8FPG3uMfgXC0Wi2vCg/poUKGGcagQI3k7k8mcp5slcGswGDwpl8tfwGJg3xB6Dvey8vz6oH4C3iXcFYjbwiDeo1KafafkC3NjK7iL5ESFGQEUF7Sg+ifZdDp9GnMF/KGmfBdT2HCwZ7TwtrBPC7rQaav6Iv48rqZwg+F+p8hOMBj0IbxfMdMBrW5pAVGV/ztINByENkU0t5BIJEKRSOQ3Aj+Z57iFs1R5NK3EQS6HQqF1zmQdzpFWq3W42WwOTAf1er1PF2USFlC+qxMvFAr3HcexWX+QX6lUvsKpkTyPSEXJkw6MQ4S38Ljdbi8rmM/nY+CvgNcQqdH6U/xrYK9t244jZv6ByUOSiDdIfgBZ12U6dHEHu9TpdIr8F0OP692CtzaW/a6y3y0Wx5kbFHvGuXzkgf0xhKnPzA4UTyaTB8Ph8AvcHi3fnsrZ7Wore02YViqVOrRXXPhfqP8j6MYlawoAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
											<span class="gigya-error-msg" data-bound-to="passwordRetype"></span>
										</div>
									</div>
									<div class="gigya-clear"></div>
									<div class="gigya-clear"></div>
								</div>
								<div class="gigya-layout-row">
									<div class="gigya-composite-control gigya-composite-control-multi-choice" style="display: block;">
										<label class="gigya-label">
											<span class="gigya-label-text">How often do you listen to this station?</span>
											<label class="gigya-required-display"
											       data-bound-to="data.listeningFrequency" style="display: none;">*</label>
										</label>
										<span class="gigya-error-msg"></span>

										<div class="gigya-multi-choice-item">
											<input type="radio" class="gigya-input-radio" name="data.listeningFrequency" value="Several times daily">
											<label>Several times daily</label>
										</div>
										<div class="gigya-multi-choice-item">
											<input type="radio" class="gigya-input-radio" name="data.listeningFrequency" value="Once per day">
											<label>Once per day</label>
										</div>
										<div class="gigya-multi-choice-item">
											<input type="radio" class="gigya-input-radio" name="data.listeningFrequency" value="Several times per week">
											<label>Several times per week</label>
										</div>
										<div class="gigya-multi-choice-item">
											<input type="radio" class="gigya-input-radio" name="data.listeningFrequency" value="Once per week or less">
											<label>Once per week or less</label>
										</div>
									</div>
									<div class="gigya-composite-control gigya-composite-control-multi-choice">
										<label class="gigya-label">
											<span class="gigya-label-text">What is your listening loyalty?</span>
											<label class="gigya-required-display" data-bound-to="data.listeningLoyalty"
											       style="display: none;">*</label>
										</label>
										<span class="gigya-error-msg"></span>

										<div class="gigya-multi-choice-item">
											<input type="radio" class="gigya-input-radio" name="data.listeningLoyalty" value="Only this station">
											<label>Only this station</label>
										</div>
										<div class="gigya-multi-choice-item">
											<input type="radio" class="gigya-input-radio" name="data.listeningLoyalty" value="This and 2-3 other stations">
											<label>This and 2-3 other stations</label>
										</div>
										<div class="gigya-multi-choice-item">
											<input type="radio" class="gigya-input-radio" name="data.listeningLoyalty" value="More than 3 other stations">
											<label>More than 3 other stations</label>
										</div>
									</div>
								</div>
								<div class="gigya-layout-row ">
									<div class="gigya-layout-cell ">
										<div class="gigya-layout-row ">
											<div class="gigya-layout-cell"></div>
											<div class="gigya-layout-cell"></div>
											<div class="gigya-clear"></div>
										</div>
									</div>
									<div class="gigya-layout-cell ">
										<div class="gigya-layout-row ">
											<div class="gigya-layout-cell">
												<div class="gigya-error-display gigya-composite-control gigya-composite-control-form-error"
												     data-bound-to="gigya-register-form" style="display: block;">
													<div class="gigya-error-msg gigya-form-error-msg" data-bound-to="gigya-register-form"
													     style=""></div>
												</div>
											</div>
											<div class="gigya-layout-cell"></div>
											<div class="gigya-clear"></div>
										</div>
									</div>
									<div class="gigya-clear"></div>
								</div>
								<div class="gigya-layout-row">
									<div class="gigya-composite-control gigya-composite-control-checkbox">
										<input type="checkbox" class="gigya-input-checkbox" name="data.subscribe" data-display-name="">
										<label class="gigya-label">
											<span class="gigya-label-text">Subscribe to our newsletter</span>
											<label class="gigya-required-display" data-bound-to="data.subscribe"
											       style="display: none;">*</label>
										</label>
									</div>
									<div class="gigya-composite-control gigya-composite-control-checkbox">
										<input type="checkbox" class="gigya-input-checkbox" name="data.terms" data-display-name="">
										<label class="gigya-label">
											<span class="gigya-label-text">I have read and understood the Terms of Use</span>
											<label class="gigya-required-display"
											       data-bound-to="data.terms" style="display: inline;">*</label>
										</label>
									</div>
									<div class="gigya-composite-control gigya-spacer" data-units="6" style="height: 60px;"></div>
									<div class="gigya-composite-control gigya-composite-control-submit" style="display: block;">
										<input type="submit" class="gigya-input-submit" value="Submit" tabindex="8">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="gigya-clear"></div>
				</form>
			</div>
			<div class="gigya-screen" id="gigya-complete-registiration-screen" data-caption="Complete your registration"
			     data-width="350" style="max-width: 350px;">
				<form class="gigya-profile-form">
					<div class="gigya-layout-row">
						<label class="gigya-composite-control gigya-composite-control-label" style="display: block;">We still need some details from you...</label>

						<div class="gigya-composite-control gigya-spacer"
						     data-units="1" style="height: 10px;"></div>
						<div class="gigya-composite-control gigya-composite-control-dropdown" style="display: block;">
							<label class="gigya-label">
								<span class="gigya-label-text">Year of birth:</span>
								<label class="gigya-required-display" data-bound-to="profile.birthYear"
								       style="display: none;">*</label>
							</label> <select name="profile.birthYear" tabindex="2">
								<option value="1920">1920</option>
								<option value="1921">1921</option>
								<option value="1922">1922</option>
								<option value="1923">1923</option>
								<option value="1924">1924</option>
								<option value="1925">1925</option>
								<option value="1926">1926</option>
								<option value="1927">1927</option>
								<option value="1928">1928</option>
								<option value="1929">1929</option>
								<option value="1930">1930</option>
								<option value="1931">1931</option>
								<option value="1932">1932</option>
								<option value="1933">1933</option>
								<option value="1934">1934</option>
								<option value="1935">1935</option>
								<option value="1936">1936</option>
								<option value="1937">1937</option>
								<option value="1938">1938</option>
								<option value="1939">1939</option>
								<option value="1940">1940</option>
								<option value="1941">1941</option>
								<option value="1942">1942</option>
								<option value="1943">1943</option>
								<option value="1944">1944</option>
								<option value="1945">1945</option>
								<option value="1946">1946</option>
								<option value="1947">1947</option>
								<option value="1948">1948</option>
								<option value="1949">1949</option>
								<option value="1950">1950</option>
								<option value="1951">1951</option>
								<option value="1952">1952</option>
								<option value="1953">1953</option>
								<option value="1954">1954</option>
								<option value="1955">1955</option>
								<option value="1956">1956</option>
								<option value="1957">1957</option>
								<option value="1958">1958</option>
								<option value="1959">1959</option>
								<option value="1960">1960</option>
								<option value="1961">1961</option>
								<option value="1962">1962</option>
								<option value="1963">1963</option>
								<option value="1964">1964</option>
								<option value="1965">1965</option>
								<option value="1966">1966</option>
								<option value="1967">1967</option>
								<option value="1968">1968</option>
								<option value="1969">1969</option>
								<option value="1970">1970</option>
								<option value="1971">1971</option>
								<option value="1972">1972</option>
								<option value="1973">1973</option>
								<option value="1974">1974</option>
								<option value="1975">1975</option>
								<option value="1976">1976</option>
								<option value="1977">1977</option>
								<option value="1978">1978</option>
								<option value="1979">1979</option>
								<option value="1980">1980</option>
								<option value="1981">1981</option>
								<option value="1982">1982</option>
								<option value="1983">1983</option>
								<option value="1984">1984</option>
								<option value="1985">1985</option>
								<option value="1986">1986</option>
								<option value="1987">1987</option>
								<option value="1988">1988</option>
								<option value="1989">1989</option>
								<option value="1990">1990</option>
								<option value="1991">1991</option>
								<option value="1992">1992</option>
								<option value="1993">1993</option>
								<option value="1994">1994</option>
								<option value="1995">1995</option>
								<option value="1996">1996</option>
								<option value="1997">1997</option>
								<option value="1998">1998</option>
								<option value="1999">1999</option>
								<option value="2000">2000</option>
								<option value="2001">2001</option>
								<option value="2002">2002</option>
								<option value="2003">2003</option>
								<option value="2004">2004</option>
							</select>
          <span
	          class="gigya-error-msg" data-bound-to="profile.birthYear"></span>
						</div>
						<div class="gigya-composite-control gigya-composite-control-textbox" style="display: block;">
							<label class="gigya-label">
								<span class="gigya-label-text">Postcode:</span>
								<label class="gigya-required-display" data-bound-to="profile.zip"
								       style="display: none;">*</label>
							</label>
							<input type="text" value="" name="profile.zip" class="gigya-input-text" tabindex="3">
							<span class="gigya-error-msg" data-bound-to="profile.zip"></span>
						</div>
					</div>
					<div class="gigya-layout-row">
						<div class="gigya-layout-cell">
							<div class="gigya-error-display gigya-composite-control gigya-composite-control-form-error"
							     data-bound-to="gigya-profile-form" style="display: block;">
								<div class="gigya-error-msg gigya-form-error-msg" data-bound-to="gigya-profile-form"
								     style=""></div>
							</div>
						</div>
						<div class="gigya-layout-cell">
							<div class="gigya-composite-control gigya-composite-control-submit" style="display: block;">
								<input type="submit" class="gigya-input-submit" value="Submit" tabindex="5">
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row"></div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell"></div>
						<div class="gigya-layout-cell"></div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row"></div>
					<div class="gigya-clear"></div>
				</form>
			</div>
			<div class="gigya-screen" id="gigya-tfa-registration-screen" data-caption="Keep your account secure"
			     data-width="350">
				<div class="gigya-layout-row">
					<div class="gigya-composite-control gigya-composite-control-tfa-widget gigya-composite-control-tfa-register">
						<div class="gigya-tfa">
							<param name="mode" value="register">
						</div>
					</div>
				</div>
				<div class="gigya-layout-row">
					<div class="gigya-layout-cell"></div>
					<div class="gigya-layout-cell"></div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row ">
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row"></div>
				<div class="gigya-layout-row ">
					<div class="gigya-layout-cell"></div>
					<div class="gigya-layout-cell"></div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row ">
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row"></div>
				<div class="gigya-clear"></div>
			</div>
			<div class="gigya-screen" id="gigya-link-account-screen" data-caption="Already a member"
			     data-width="400">
				<form class="gigya-link-accounts-form">
					<div class="gigya-layout-row">
						<label class="gigya-composite-control gigya-composite-control-label" style="display: block;">We found your email in our system.
							<br>Please provide your site password to link to your existing account.</label>
						<label
							class="gigya-composite-control gigya-composite-control-label" style="display: block;">Or
							<a data-switch-screen="gigya-register-screen">click here</a> to create new account</label>

						<div
							class="gigya-composite-control gigya-spacer" data-units="1" style="height: 10px;"></div>
						<div class="gigya-composite-control gigya-composite-control-textbox" style="display: block;">
							<label class="gigya-label">
								<span class="gigya-label-text">Email:</span>
							</label>
							<input type="text" name="loginID" class="gigya-input-text" tabindex="1" autocomplete="off"
							       style="background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QsPDhss3LcOZQAAAU5JREFUOMvdkzFLA0EQhd/bO7iIYmklaCUopLAQA6KNaawt9BeIgnUwLHPJRchfEBR7CyGWgiDY2SlIQBT/gDaCoGDudiy8SLwkBiwz1c7y+GZ25i0wnFEqlSZFZKGdi8iiiOR7aU32QkR2c7ncPcljAARAkgckb8IwrGf1fg/oJ8lRAHkR2VDVmOQ8AKjqY1bMHgCGYXhFchnAg6omJGcBXEZRtNoXYK2dMsaMt1qtD9/3p40x5yS9tHICYF1Vn0mOxXH8Uq/Xb389wff9PQDbQRB0t/QNOiPZ1h4B2MoO0fxnYz8dOOcOVbWhqq8kJzzPa3RAXZIkawCenHMjJN/+GiIqlcoFgKKq3pEMAMwAuCa5VK1W3SAfbAIopum+cy5KzwXn3M5AI6XVYlVt1mq1U8/zTlS1CeC9j2+6o1wuz1lrVzpWXLDWTg3pz/0CQnd2Jos49xUAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
							<span class="gigya-error-msg" data-bound-to="loginID"></span>
						</div>
						<div class="gigya-composite-control gigya-composite-control-password" style="display: block;">
							<label class="gigya-label">
          <span class="gigya-label-text">Password: <a style="float: right; font-weight: normal;                            margin-right: 5px;"
                                                      data-switch-screen="gigya-forgot-password-screen">Forgot password</a>
          </span>
							</label>
							<input type="password" name="password" class="gigya-input-password" tabindex="2"
							       autocomplete="off" style="background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QsPDhss3LcOZQAAAU5JREFUOMvdkzFLA0EQhd/bO7iIYmklaCUopLAQA6KNaawt9BeIgnUwLHPJRchfEBR7CyGWgiDY2SlIQBT/gDaCoGDudiy8SLwkBiwz1c7y+GZ25i0wnFEqlSZFZKGdi8iiiOR7aU32QkR2c7ncPcljAARAkgckb8IwrGf1fg/oJ8lRAHkR2VDVmOQ8AKjqY1bMHgCGYXhFchnAg6omJGcBXEZRtNoXYK2dMsaMt1qtD9/3p40x5yS9tHICYF1Vn0mOxXH8Uq/Xb389wff9PQDbQRB0t/QNOiPZ1h4B2MoO0fxnYz8dOOcOVbWhqq8kJzzPa3RAXZIkawCenHMjJN/+GiIqlcoFgKKq3pEMAMwAuCa5VK1W3SAfbAIopum+cy5KzwXn3M5AI6XVYlVt1mq1U8/zTlS1CeC9j2+6o1wuz1lrVzpWXLDWTg3pz/0CQnd2Jos49xUAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
							<span class="gigya-error-msg" data-bound-to="password"></span>
						</div>
					</div>
					<div class="gigya-layout-row">
						<div class="gigya-layout-cell">
							<div class="gigya-error-display gigya-composite-control gigya-composite-control-form-error"
							     data-bound-to="gigya-link-accounts-form" style="display: block;">
								<div class="gigya-error-msg gigya-form-error-msg" data-bound-to="gigya-link-accounts-form"
								     style=""></div>
							</div>
						</div>
						<div class="gigya-layout-cell">
							<div class="gigya-composite-control gigya-composite-control-submit" style="display: block;">
								<input type="submit" class="gigya-input-submit" value="Submit" tabindex="3">
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row"></div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell"></div>
						<div class="gigya-layout-cell"></div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row"></div>
					<div class="gigya-clear"></div>
				</form>
			</div>
			<div class="gigya-screen" id="gigya-forgot-password-screen" data-caption="Forgot password"
			     data-width="350">
				<form class="gigya-reset-password-form" data-on-success-screen="gigya-forgot-password-success-screen">
					<div class="gigya-layout-row">
						<label class="gigya-composite-control gigya-composite-control-label" style="display: block;">Please enter your email address to reset your password</label>

						<div class="gigya-composite-control gigya-spacer"
						     data-units="1" style="height: 10px;"></div>
						<div class="gigya-composite-control gigya-composite-control-textbox" style="display: block;">
							<label class="gigya-label">
								<span class="gigya-label-text">Email:</span>
							</label>
							<input type="text" name="loginID" class="gigya-input-text" tabindex="1" autocomplete="off"
							       style="background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QsPDhss3LcOZQAAAU5JREFUOMvdkzFLA0EQhd/bO7iIYmklaCUopLAQA6KNaawt9BeIgnUwLHPJRchfEBR7CyGWgiDY2SlIQBT/gDaCoGDudiy8SLwkBiwz1c7y+GZ25i0wnFEqlSZFZKGdi8iiiOR7aU32QkR2c7ncPcljAARAkgckb8IwrGf1fg/oJ8lRAHkR2VDVmOQ8AKjqY1bMHgCGYXhFchnAg6omJGcBXEZRtNoXYK2dMsaMt1qtD9/3p40x5yS9tHICYF1Vn0mOxXH8Uq/Xb389wff9PQDbQRB0t/QNOiPZ1h4B2MoO0fxnYz8dOOcOVbWhqq8kJzzPa3RAXZIkawCenHMjJN/+GiIqlcoFgKKq3pEMAMwAuCa5VK1W3SAfbAIopum+cy5KzwXn3M5AI6XVYlVt1mq1U8/zTlS1CeC9j2+6o1wuz1lrVzpWXLDWTg3pz/0CQnd2Jos49xUAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
							<span class="gigya-error-msg" data-bound-to="loginID"></span>
						</div>
					</div>
					<div class="gigya-layout-row">
						<div class="gigya-layout-cell">
							<div class="gigya-error-display gigya-composite-control gigya-composite-control-form-error"
							     data-bound-to="gigya-reset-password-form" style="display: block;">
								<div class="gigya-error-msg gigya-form-error-msg" data-bound-to="gigya-reset-password-form"
								     style=""></div>
							</div>
						</div>
						<div class="gigya-layout-cell">
							<div class="gigya-composite-control gigya-composite-control-submit" style="display: block;">
								<input type="submit" class="gigya-input-submit" value="Submit" tabindex="2">
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row">
						<div class="gigya-composite-control gigya-spacer" data-units="8" style="height: 80px; display: block;"></div>
						<label class="gigya-composite-control gigya-composite-control-label" style="text-align: right; display: block;">To login with a different account
							<a data-switch-screen="gigya-login-screen">click here </a>
						</label>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell"></div>
						<div class="gigya-layout-cell"></div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row"></div>
					<div class="gigya-clear"></div>
				</form>
			</div>
			<div data-width="300" data-caption="Password reset" id="gigya-forgot-password-success-screen"
			     class="gigya-screen">
				<div class="gigya-layout-row">
					<div style="height: 40px;" data-units="4" class="gigya-composite-control gigya-spacer"></div>
					<label class="gigya-composite-control gigya-composite-control-label gigya-message">An email regarding your password change has been sent to your email address.</label>

					<div
						class="gigya-composite-control gigya-spacer" data-units="5" style="height: 50px;"></div>
				</div>
				<div class="gigya-layout-row">
					<div class="gigya-layout-cell"></div>
					<div class="gigya-layout-cell"></div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row ">
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row"></div>
				<div class="gigya-layout-row ">
					<div class="gigya-layout-cell"></div>
					<div class="gigya-layout-cell"></div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row ">
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row"></div>
				<div class="gigya-clear"></div>
			</div>
			<div class="gigya-screen" id="gigya-email-verification-screen" data-caption="Your email was not verified"
			     data-width="350">
				<form class="gigya-resend-verification-code-form" data-on-success-screen="gigya-thank-you-screen">
					<div class="gigya-layout-row">
						<label class="gigya-composite-control gigya-composite-control-label" style="display: block;">We have not verified that the email belongs to you. Please check your inbox for
							the verification email.
							<br>
							<br>To resend the verification email, please enter your email address and click Submit.</label>

						<div
							class="gigya-composite-control gigya-spacer" data-units="1" style="height: 10px;"></div>
						<div class="gigya-composite-control gigya-composite-control-textbox" style="display: block;">
							<label class="gigya-label">
								<span class="gigya-label-text">Email:</span>
							</label>
							<input type="text" name="email" class="gigya-input-text" tabindex="1" autocomplete="off"
							       style="background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QsPDhss3LcOZQAAAU5JREFUOMvdkzFLA0EQhd/bO7iIYmklaCUopLAQA6KNaawt9BeIgnUwLHPJRchfEBR7CyGWgiDY2SlIQBT/gDaCoGDudiy8SLwkBiwz1c7y+GZ25i0wnFEqlSZFZKGdi8iiiOR7aU32QkR2c7ncPcljAARAkgckb8IwrGf1fg/oJ8lRAHkR2VDVmOQ8AKjqY1bMHgCGYXhFchnAg6omJGcBXEZRtNoXYK2dMsaMt1qtD9/3p40x5yS9tHICYF1Vn0mOxXH8Uq/Xb389wff9PQDbQRB0t/QNOiPZ1h4B2MoO0fxnYz8dOOcOVbWhqq8kJzzPa3RAXZIkawCenHMjJN/+GiIqlcoFgKKq3pEMAMwAuCa5VK1W3SAfbAIopum+cy5KzwXn3M5AI6XVYlVt1mq1U8/zTlS1CeC9j2+6o1wuz1lrVzpWXLDWTg3pz/0CQnd2Jos49xUAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
							<span class="gigya-error-msg" data-bound-to="email"></span>
						</div>
					</div>
					<div class="gigya-layout-row">
						<div class="gigya-layout-cell">
							<div class="gigya-error-display gigya-composite-control gigya-composite-control-form-error"
							     data-bound-to="gigya-resend-verification-code-form" style="display: block;">
								<div class="gigya-error-msg gigya-form-error-msg" data-bound-to="gigya-resend-verification-code-form"
								     style=""></div>
							</div>
						</div>
						<div class="gigya-layout-cell">
							<div class="gigya-composite-control gigya-composite-control-submit" style="display: block;">
								<input type="submit" class="gigya-input-submit" value="Submit" tabindex="2">
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row">
						<div class="gigya-composite-control gigya-spacer" data-units="8" style="height: 80px; display: block;"></div>
						<label class="gigya-composite-control gigya-composite-control-label" style="text-align: right; display: block;">To login with a different account
							<a data-switch-screen="gigya-login-screen">click here </a>
						</label>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell"></div>
						<div class="gigya-layout-cell"></div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row ">
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-layout-cell ">
							<div class="gigya-layout-row ">
								<div class="gigya-layout-cell"></div>
								<div class="gigya-layout-cell"></div>
								<div class="gigya-clear"></div>
							</div>
						</div>
						<div class="gigya-clear"></div>
					</div>
					<div class="gigya-layout-row"></div>
					<div class="gigya-clear"></div>
				</form>
			</div>
			<div class="gigya-screen" id="gigya-thank-you-screen" data-caption="Thank you for registering!"
			     data-width="300">
				<div class="gigya-layout-row">
					<div class="gigya-composite-control gigya-spacer" data-units="4" style="height: 40px; display: block;"></div>
					<label class="gigya-composite-control gigya-composite-control-label gigya-message">Thank you!</label>

					<div
						class="gigya-composite-control gigya-spacer" data-units="5" style="height: 50px; display: block;"></div>
				</div>
				<div class="gigya-layout-row">
					<div class="gigya-layout-cell"></div>
					<div class="gigya-layout-cell"></div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row ">
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row"></div>
				<div class="gigya-layout-row ">
					<div class="gigya-layout-cell"></div>
					<div class="gigya-layout-cell"></div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row ">
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-layout-cell ">
						<div class="gigya-layout-row ">
							<div class="gigya-layout-cell"></div>
							<div class="gigya-layout-cell"></div>
							<div class="gigya-clear"></div>
						</div>
					</div>
					<div class="gigya-clear"></div>
				</div>
				<div class="gigya-layout-row"></div>
				<div class="gigya-clear"></div>
			</div>
			</div>
			</div>
		<?php
		}

		return $form;
	}

	/**
	 * When a Gravity form is submitted it fires this hook callback. The hook contains the entries and the form.
	 * The $form array contains all the $field data, which is then used to extract data from $entry
	 *
	 * @param  array $entry Gravity Forms entry object {@link http://www.gravityhelp.com/documentation/page/Developer_Docs#Entry_Object}
	 * @param  array $form  Gravity Forms form object {@link http://www.gravityhelp.com/documentation/page/Developer_Docs#Form_Object}
	 */
	public static function gmi_after_submission( $entry, $form ) {

		// Build up this array from gigya data
		$gigya_profile = array();

		// Go through each field and get data associated with each
		foreach ( $form['fields'] as $field ) {

			$value = self::_gmi_normalize_entry_value( $field['id'], $entry, $field );

			// Don't store empty values!
			if ( empty( $value ) ) {
				continue;
			}

			if ( isset( $field['gigyaDemographic'] ) && ! empty( $field['gigyaDemographic'] ) ) {
				$gigya_profile[ $field['gigyaDemographic'] ] = $value;
			}

		} // endforeach

		$gigya_json = json_encode( $gigya_profile );

		?>
		<script type="text/javascript">
			document.addEventListener("DOMContentLoaded", updateGigyaProfile, false);
			function updateGigyaProfile(e) {
				gigya.accounts.setAccountInfo({data: <?php echo $gigya_json; ?>, callback: updateProfileAlert});
			}
			function updateProfileAlert(response) {
				console.log(response);
			}
		</script><?php

		// Generate Contest Entry if on contest and gigya user data exists
		if ( is_singular( GreaterMediaContests::CPT_SLUG ) && ( ! empty( $_POST['gigya_name'] ) && ! empty( $_POST['gigya_UID'] ) ) ) {
			$gf_contest_entry = GreaterMediaContestEntry::create_for_data( get_the_ID(), esc_html( $_POST['gigya_name'] ), esc_html( $_POST['gigya_UID'] ), 'gravity-forms', get_permalink() );
			$gf_contest_entry->save();
		}

	}

	/**
	 * Get the value from $entry for a given $id.
	 * Needs $field for context.
	 *
	 * @param  integer|string $id    Gravity forms field id. sometimes an integer, sometimes a string.
	 * @param  array          $entry Gravity Forms entry object
	 * @param  array          $field A subset representing a single field in the Gravity Forms Form object
	 *
	 * @return string       normalized entry value
	 */
	public static function _gmi_normalize_entry_value( $id, $entry, $field ) {

		// If this field has "inputs" that means we need to go get all that have been set.
		// It's a select, check, or radio list.

		if ( isset( $field['inputs'] ) ) {
			$inputs = array();
			foreach ( $field['inputs'] as $input ) {

				$value = $entry[ (string) $input['id'] ];

				$value = maybe_unserialize( $value );

				// Don't store keys if the value is empty
				if ( empty( $value ) ) {
					continue;
				}

				// No need to store key here- only results go to Gigya
				$inputs[] = $value;

			}

			// Single result, no array
			if ( count( $inputs ) === 1 ) {
				$inputs = $inputs[0];
			}

			return $inputs;
		} else {
			return maybe_unserialize( $entry[ $field['id'] ] );
		}
	}

	/**
	 * Sets up the HTML for selecting the Gigya demographic fields in Gravity Forms
	 *
	 * @param  integer $position where in the form it should be shown
	 * @param  integer $form_id  a form ID, in case you want to restrict to certain forms
	 */
	public static function gmi_gigya_profile_settings( $position, $form_id ) {

		$gigya_fields = self::gmi_get_gigya_fields();

		//create settings on position 50 (right after Admin Label)
		if ( $position == 0 && ! empty ( $gigya_fields ) ) {
			?>
			<li class="gigya_setting field_setting">
				<label for="field_admin_label">
					<?php _e( "Submit to Gigya Demographic Field", "gravityforms" ); ?>
					<?php gform_tooltip( "gigya_demographic_fields" ) ?>
				</label>
				<select id="gigya_demographic" onChange="SetFieldProperty('gigyaDemographic', jQuery(this).val());">
					<option value="">Don't add this field's value to user's profile</option>
					<?php foreach ( $gigya_fields as $predefined_field_key => $predefined_field_value ): ?>
						<option value="<?php echo $predefined_field_key; ?>"><?php echo $predefined_field_key; ?></option>
					<?php endforeach; ?>
				</select>
			</li>
		<?php
		}
	}

	/**
	 * If Gigya demographic field was set, apply key to GF 'parameter name' field
	 *
	 * @param array of all form data
	 */
	public static function apply_demographic_setting( $form ) {
		foreach ( $form["fields"] as $key => $field ) {
			if ( ! empty( $field['gigyaDemographic'] ) ) {
				$form["fields"][ $key ]['allowsPrepopulate'] = 1;
				$form["fields"][ $key ]['inputName']         = esc_html( $field['gigyaDemographic'] );
			}
		}

		return $form;
	}

	/**
	 * Returns an array of Gigya profile fields
	 * @return array gigya fields
	 */
	public static function gmi_get_gigya_fields() {
		$gigya_fields = get_site_option( "gf_prebuilt_fields" );

		return $gigya_fields;
	}

	/**
	 * Outputs scripts for gravity forms. Gravity Forms uses javascript to save fields and show the previously saved value
	 * Note that the actual field (output in gigya_profile_settings() ) has no name="" attribute.
	 */
	public static function editor_script() {

		$gigya_fields = self::gmi_get_gigya_fields();
		if ( ! empty ( $gigya_fields ) ) {
			foreach ( $gigya_fields as $predefined_field_key => $predefined_field_value ) {
				// Scalable way to process additional field types
				switch ( $predefined_field_value['type'] ) {
					case "dropdown":
						$type = 'select';
						break;
					case "checkbox":
						$type = 'checkbox';
						break;
					default:
						$type = 'text';
				}
				?>
				<script type='text/javascript'>
					fieldSettings["<?php echo $predefined_field_key; ?>"] = fieldSettings["<?php echo $type; ?>"];
				</script>
			<?php
			}
		}
		?>
		<script type='text/javascript'>

			//binding to the load field settings event to initialize the checkbox
			jQuery(document).bind("gform_load_field_settings", function (event, field, form) {
				jQuery('.gigya_setting').show();
				if (field["gigyaDemographic"]) {
					jQuery('.gigya_setting').find('[value=' + field["gigyaDemographic"] + ']').attr('selected', 'selected');
				}
			});

		</script>
	<?php
	}

	/**
	 * Add a help tooltip for the gigya profile fields
	 *
	 * @param array $tooltips array of tooltips.
	 */
	public static function add_encryption_tooltips( $tooltips ) {
		$tooltips["form_field_encrypt_value"] = "<h6>Encryption</h6>Check this box to encrypt this field's data";

		return $tooltips;
	}

	/**
	 * Set hidden fields for nabbing gigya user data
	 *
	 * @param array $form
	 */
	public static function gigya_hidden_fields( $button ) {
		$hidden_fields = '<input type="hidden" id="gigya_UID" name="gigya_UID"> ';
		$hidden_fields .= '<input type="hidden" id="gigya_name" name="gigya_name"> ';

		return $hidden_fields . $button;
	}

	/**
	 * Add custom Gigya Field section to GForm UI
	 */
	public static function add_gigya_fields( $field_groups ) {

		$gigya_fields = self::gmi_get_gigya_fields();
		if ( ! empty ( $gigya_fields ) ) {

			// Create Gigya Field settings
			array_unshift( $field_groups, array( 'name' => 'gigya_fields', 'label' => 'Gigya Demographic Fields' ) );

			// Create buttons for each predefined demographic field
			foreach ( $field_groups as &$group ) {
				if ( $group["name"] == "gigya_fields" ) {

					foreach ( $gigya_fields as $predefined_field_key => $predefined_field_value ) {

						$group["fields"][] = array(
							"class"   => "button",
							"value"   => __( $predefined_field_key, "gravityforms" ),
							"title"   => "button",
							"onclick" => "StartAddField( '" . $predefined_field_key . "' );"
						);
					}
				}
			}
		}

		return $field_groups;
	}

	/**
	 * Add Gigya Field prefix to custom field titles
	 */
	public static function assign_title( $title, $type ) {

		$gigya_fields = self::gmi_get_gigya_fields();
		if ( ! empty ( $gigya_fields ) ) {
			foreach ( $gigya_fields as $predefined_field_key => $predefined_field_value ) {
				if ( $type == $predefined_field_key ) {
					return __( 'Gigya Field: ' . $type, 'gravityforms' );
				}
			}
		}
	}

	/**
	 * Handle each custom field type
	 * TODO: Add dynamic handling of allowed field types (select, checkbox & text
	 */
	public static function custom_field_input( $input, $field, $value, $lead_id, $form_id ) {

		$gigya_fields = self::gmi_get_gigya_fields();
		if ( ! empty ( $gigya_fields ) ) {
			foreach ( $gigya_fields as $predefined_field_key => $predefined_field_value ) {
				if ( $field["type"] == $predefined_field_key ) {
					$tabindex  = GFCommon::get_tabindex();
					$css_class = isset( $field['cssClass'] ) ? $field['cssClass'] : ”;
				switch ( $predefined_field_value['type'] ) {
					case "dropdown":
						$display = "<div class='ginput_container'><select name='.$id.' id='.$field_id.' class='.$css_class.' '. $tabindex.' >";
						foreach ( $predefined_field_value as $key => $dropdown_option ) {
							$display .= '<option value="' . $dropdown_option . '">' . $dropdown_option . '</option>';
						}
						$display .= "</select></div>";

						return $display;
						break;
					case "checkbox":
						$type = 'checkbox';
						break;
					default:
						$type = 'text';
				}
			}
			}
		}

		return $input;
	}

}

GMR_Gform::hooks();













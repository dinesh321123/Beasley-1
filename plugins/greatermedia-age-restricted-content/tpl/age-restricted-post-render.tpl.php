<div class="age-restricted-shield" id="age-restricted-shield-<?php echo esc_attr( $post->ID ); ?>">
	<p>
		The following is restricted to members <?php echo esc_html( $age_restriction ); ?>
	</p>

	<?php if ( ! is_gigya_user_logged_in() ) : ?>
		<p>
			<a class="age-restricted-login-btn" href="<?php echo esc_url( gigya_profile_path( 'login', array( 'dest' => $_SERVER['REQUEST_URI'] ) ) ); ?>">
				Log in to verify your age
			</a>
		</p>
	<?php endif; ?>
</div>
<div class="login-restricted-shield login-restricted-shield--inline" id="login-restricted-shield-<?php the_ID(); ?>">
	<p>
		This content is restricted to members only
	</p>

	<p>
		<a class="login-restricted-login-btn" href="<?php echo esc_url( gigya_profile_path( 'login', array( 'dest' => $_SERVER['REQUEST_URI'] ) ) ); ?>">
			Log in to view this content
		</a>
	</p>
</div>
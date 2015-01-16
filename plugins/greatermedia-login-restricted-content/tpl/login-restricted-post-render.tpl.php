<div class="login-restricted-shield" id="login-restricted-shield-<?php echo esc_attr( $post->ID ); ?>">
	<p>
		The following is restricted to members only
	</p>

	<p>
		<a class="login-restricted-login-btn" href="<?php echo esc_url( gigya_profile_path( 'login', array( 'dest' => $_SERVER['REQUEST_URI'] ) ) ); ?>">
			Log in to view this content
		</a>
	</p>
</div>
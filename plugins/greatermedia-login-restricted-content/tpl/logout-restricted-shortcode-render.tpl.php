<div class="logout-restricted-shield logout-restricted-shield--inline" id="logout-restricted-shield-<?php the_ID(); ?>">
	<p>
		The following is restricted to non members only
	</p>

	<p>
		<a class="logout-restricted-login-btn" href="<?php echo esc_url( gigya_profile_path( 'logout', array( 'dest' => $_SERVER['REQUEST_URI'] ) ) ); ?>">
			Log out to view this content
		</a>
	</p>
</div>
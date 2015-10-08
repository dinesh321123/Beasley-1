<div class="age-restricted-shield age-restricted-shield--inline" id="age-restricted-shield-<?php the_ID(); ?>">
	<p>
		This content is restricted to members
		<?php if ( '18plus' === $age_restriction ) : ?>
			18+
		<?php elseif ( '21plus' === $age_restriction ) : ?>
			21+
		<?php endif; ?>
	</p>
	
	<?php if ( ! is_gigya_user_logged_in() ) : ?>
		<p>
			<a class="age-restricted-login-btn" href="<?php echo esc_url( gigya_profile_path( 'login', array( 'dest' => $_SERVER['REQUEST_URI'] ) ) ); ?>">
				Log in to verify your age
			</a>
		</p>
	<?php endif; ?>
</div>
<span class="login-restricted-shield" id="login-restricted-shield-<?php echo esc_attr( $post->ID ); ?>">
	<p>You must be at least
		<?php if ( '18plus' === $age_restriction ) : ?>
			18
		<?php elseif ( '21plus' === $age_restriction ) : ?>
			21
		<?php endif; ?>
		years old to view this content.
		<?php if ( ! is_gigya_user_logged_in()) : ?>
			Please <a href="<?php echo esc_url( $login_url ); ?>"><?php _e( 'sign in' ); ?></a> to continue.
		<?php endif; ?>
	</p>
</span>
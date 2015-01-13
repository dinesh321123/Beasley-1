<section class="col__inner--left">
	<?php the_content(); ?>
</section>

<section class="col__inner--right">
	<dl class="contest__submission--entries">
		<dt>Submitted By</dt>
		<dd><?php echo esc_html( gmr_contest_submission_get_author() ); ?></dd>

		<dt>Submitted On</dt>
		<dd><?php echo get_the_date( '' ); ?></dd>
		
		<?php
		
		$contest_entry_id = get_post_meta( get_the_ID(), 'contest_entry_id', true );
		if ( $contest_entry_id ) :
			$fields = GreaterMediaFormbuilderRender::parse_entry( get_post()->post_parent, $contest_entry_id );
			if ( ! empty( $fields ) ) :
				foreach ( $fields as $field ) :
					if ( 'file' != $field['type'] && 'email' != $field['type'] ) :
						?><dt>
							<?php echo esc_html( $field['label'] ); ?>
						</dt>
						<dd>
							<?php 
							
							$value = is_array( $field['value'] ) ? implode( ', ', $field['value'] ) : $field['value'];
							if ( strlen( $value ) > 200 ) {
								$value = substr( $value, 0, 200 ) . '&hellip;';
							}
							
							echo esc_html( $value );

							?>
						</dd><?php
					endif;
				endforeach;
			endif;
		endif;

		?>
	</dl>

	<?php if ( function_exists( 'is_gigya_user_logged_in' ) ) : ?>
		<?php if ( is_gigya_user_logged_in() ) : ?>
			<div>
				<a class="contest__submission--vote" href="#" data-id="<?php echo esc_attr( get_post_field( 'post_name', null ) ); ?>">
					<i class="fa fa-thumbs-o-up"></i> Upvote
				</a>

				<a class="contest__submission--unvote" href="#" data-id="<?php echo esc_attr( get_post_field( 'post_name', null ) ); ?>">
					<i class="fa fa-thumbs-o-down"></i> Downvote
				</a>
			</div>
		<?php else : ?>
			<p>
				You must be logged in to vote for the submission!
				<a href="<?php echo esc_url( gmr_contests_get_login_url( parse_url( get_permalink( get_post_field( 'post_parent', null ) ), PHP_URL_PATH ) ) ) ?>">Sign in here</a>.
			</p>
		<?php endif; ?>
	<?php endif; ?>
</section>
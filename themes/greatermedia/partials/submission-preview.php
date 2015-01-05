<section class="col__inner--left">
	<?php the_content(); ?>
</section>

<section class="col__inner--right">
	<?php
	$contest_entry_id = get_post_meta( get_the_ID(), 'contest_entry_id', true );
	if ( $contest_entry_id ) :
		$fields = GreaterMediaFormbuilderRender::parse_entry( get_post()->post_parent, $contest_entry_id );
		if ( ! empty( $fields ) ) : ?>
			<dl class="contest__submission--entries">
				<?php foreach ( $fields as $field ) : ?>
					<dt><?php echo esc_html( $field['label'] ); ?></dt>
					<dd>
						<?php echo esc_html( is_array( $field['value'] ) ? implode( ', ', $field['value'] ) : $field['value'] ); ?>
					</dd>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>
	<?php endif; ?>

	<p class="contest__submission--author">
		Submitted by <?php echo esc_html( gmr_contest_submission_get_author() ); ?> On <?php echo get_the_date( '', $contest_entry_id ); ?>
	</p>

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
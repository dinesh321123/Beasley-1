<div class="meta">
	<div class="author-meta">
		<?php if ( ! is_singular( 'contest' ) ) : ?>
			<span class="author-avatar">
				<?php if ( is_singular() ) : ?>
					<?php $avatar = get_avatar( get_the_author_meta( 'ID' ), 40 ); ?>
					<?php if ( $avatar ) : ?>
						<?php echo $avatar ?>
					<?php else: ?>
						<img class="avatar avatar-40 photo" src="https://2.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=96&d=mm&r=g"
							 height="40" width="40" alt="Placeholder Shilloutte User Image">
					<?php endif; ?>
				<?php endif; ?>
			</span>

			<span class="author-meta-name">
				<?php the_author_meta( 'display_name' ); ?>
			</span>
		<?php endif; ?>

		<span class="author-meta-date">
			<?php ee_the_date(); ?>
		</span>
	</div>

	<?php $sponsored_by = ee_get_sponsored_by(get_the_id()) ?>
	<?php if ( $sponsored_by !== '' ) : ?>
		<?php $sponsor_url = ee_get_sponsor_url(get_the_id()) ?>
		<div class="sponsor-meta">
			<?php if ( $sponsor_url === '' ) : ?>
				<?php echo esc_html_e( $sponsored_by, 'bbgi' ); ?>
			<?php else : ?>
				<a class="sponsor-meta" href='<?php echo $sponsor_url ?>' target='_blank'><?php echo esc_html_e( $sponsored_by, 'bbgi' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="share-wrap-icons">
		<span class="label">Share</span>
		<?php ee_the_share_buttons( get_permalink(), get_the_title() ); ?>
	</div>


</div>

<div class="meta">
	<div class="author-meta">
		<?php $avatar = get_avatar( get_the_author_meta( 'ID' ), 40 ); ?>
			<span class="author-avatar">
				<?php if( is_singular() && $avatar ) : ?>
					<?php echo $avatar ?>
				<?php else: ?>
					<img src="http://2.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=96&d=mm&r=g" alt="Placeholder Shilloutte User Image">
				<?php endif; ?>
			</span>

		<span class="author-meta-name"><?php the_author_meta( 'display_name' ); ?></span>
		<span class="author-meta-date"><?php ee_the_date(); ?></span>
	</div>

	<?php ee_the_share_buttons( get_permalink(), get_the_title() ); ?>
</div>

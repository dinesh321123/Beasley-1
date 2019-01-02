<div class="meta">
	<div class="author-meta">
		<?php $avatar = get_avatar( get_the_author_meta( 'ID' ), 40 ); ?>
		<?php if( is_single() && $avatar ) : ?>
			<span class="author-avatar">
				<?php echo get_avatar( get_the_author_meta( 'ID' ), 40 ); ?>
			</span>
		<?php endif; ?>

		<span class="author-meta-name"><?php the_author_meta( 'display_name' ); ?></span>
		<span class="author-meta-date"><?php ee_the_date(); ?></span>
	</div>
	
	<?php ee_the_share_buttons( get_permalink(), get_the_title() ); ?>
</div>

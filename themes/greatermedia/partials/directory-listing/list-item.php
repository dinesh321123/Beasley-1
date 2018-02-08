<div class="directory-list-item">
	<div class="directory-list-item__thumbnail">
		<?php the_post_thumbnail( 'gm-related-post' ); ?>
	</div>
	<div class="directory-list-item__content">
		<h3 class="directory-list-item__title">
			<a href="<?php echo esc_url( get_permalink() ) ?>">
				<?php the_title(); ?>
			</a>
		</h3>
		<?php the_excerpt(); ?>
	</div>
	<div class="inquire">
		<a class="inquire__link" href="<?php echo esc_url( get_permalink() ); ?>">INQUIRE</a>
	</div>
</div>
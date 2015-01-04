<li class="contest__submission">
	<a class="contest__submission--link" href="<?php the_permalink(); ?>">
		<span class="contest__submission--rating">
			<i class="fa fa-thumbs-o-up"></i> <?php echo number_format( get_post_field( 'menu_order', null ), 0 ); ?>
		</span>

		<?php the_post_thumbnail(); ?>

		<span class="contest__submission--author"><?php echo esc_html( gmr_contest_submission_get_author() ); ?></span>
		<span class="contest__submission--date"><?php echo esc_html( get_the_date() ); ?></span>
	</a>
</li>
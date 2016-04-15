<li class="<?php echo gmr_contests_submission_class( 'contest__submission' ); ?>">
	<a class="contest__submission--link" href="<?php the_permalink(); ?>">
		<span class="contest__submission--winner"><i class="fa fa-trophy"></i></span>

		<?php if ( gmr_contests_can_show_vote_count( get_the_ID() ) ) { ?>
			<span class="contest__submission--rating">
				<i class="fa fa-thumbs-o-up"></i>
				<b><?php echo number_format( get_post_field( 'menu_order', null ), 0 ); ?></b>
			</span>
		<?php } ?>

		<?php the_post_thumbnail( 'gmr-featured-secondary' ); ?>

		<span class="contest__submission--author"><?php echo esc_html( gmr_contest_submission_get_author() ); ?></span>
		<span class="contest__submission--date"><?php echo esc_html( get_the_date() ); ?></span>
	</a>
</li>
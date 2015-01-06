<div class="posts-pagination<?php echo is_front_page() ? ' front-pagination' : ''; ?>">
	<div class="posts-pagination--previous">
		<?php next_posts_link( '<i class="fa fa-angle-double-left"></i>Previous' ); ?>
	</div>
	
	<?php get_template_part( 'partials/load-more' ); ?>

	<div class="posts-pagination--next">
		<?php previous_posts_link( 'Next<i class="fa fa-angle-double-right"></i>' ); ?>
	</div>
</div>
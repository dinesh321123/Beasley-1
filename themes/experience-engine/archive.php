<?php

get_header(); ?>

<?php if ( ee_is_first_page() ): ?>
	<div class="archive-title content-wrap">
		<?php if ( is_post_type_archive( 'podcast' ) ): ?>
			<svg xmlns="http://www.w3.org/2000/svg" width="26" height="29">
				<path d="M16.325 17.145a5.31 5.31 0 0 0 1.984-4.138 5.327 5.327 0 0 0-5.32-5.321 5.326 5.326 0 0 0-5.321 5.32 5.31 5.31 0 0 0 1.984 4.139 5.31 5.31 0 0 0-1.984 4.138v7.095h10.641v-7.095a5.31 5.31 0 0 0-1.984-4.138zm-.38 8.868h-5.912v-4.73a2.96 2.96 0 0 1 2.956-2.956 2.96 2.96 0 0 1 2.956 2.956v4.73zm-2.956-10.05a2.96 2.96 0 0 1-2.956-2.956 2.96 2.96 0 0 1 2.956-2.956 2.96 2.96 0 0 1 2.956 2.956 2.96 2.96 0 0 1-2.956 2.956z"/>
				<path d="M22.197 3.81A12.92 12.92 0 0 0 13.001 0C9.526 0 6.26 1.353 3.803 3.81c-5.07 5.07-5.07 13.322 0 18.393l.836.836 1.673-1.672-.837-.836c-4.149-4.15-4.149-10.9 0-15.05a10.576 10.576 0 0 1 7.526-3.116c2.842 0 5.514 1.107 7.524 3.116 4.15 4.15 4.15 10.9 0 15.05l-.836.836 1.672 1.672.836-.836c5.07-5.072 5.07-13.323 0-18.393z"/>
			</svg>
		<?php endif; ?>
		<?php the_archive_title( '<h1>', '</h1>' ); ?>
	</div>
<?php endif; ?>

<div class="archive-tiles -grid content-wrap">
	<?php while ( have_posts() ) :
		the_post();
		get_template_part( 'partials/tile', get_post_type() );
	endwhile; ?>
</div>

<?php ee_load_more();

get_footer();

<?php

get_header(); ?>

<?php if ( ee_is_first_page() ): ?>
	<div class="archive-title content-wrap">
		<?php if ( is_post_type_archive( 'podcast' ) ): ?>
			<svg xmlns="http://www.w3.org/2000/svg" width="26" height="29" aria-labelledby="podcast-icon-title podcast-icon-desc">
				<title id="podcast-icon-title">
					<?php esc_html( __( 'Podcast Archive Title Icon' ) ); ?>
				</title>
				<desc id="podcast-icon-desc">
					<?php esc_html( __( 'Icon showing radiating signal from an antenna' ) ); ?>
				</desc>
				<path d="M16.325 17.145a5.31 5.31 0 0 0 1.984-4.138 5.327 5.327 0 0 0-5.32-5.321 5.326 5.326 0 0 0-5.321 5.32 5.31 5.31 0 0 0 1.984 4.139 5.31 5.31 0 0 0-1.984 4.138v7.095h10.641v-7.095a5.31 5.31 0 0 0-1.984-4.138zm-.38 8.868h-5.912v-4.73a2.96 2.96 0 0 1 2.956-2.956 2.96 2.96 0 0 1 2.956 2.956v4.73zm-2.956-10.05a2.96 2.96 0 0 1-2.956-2.956 2.96 2.96 0 0 1 2.956-2.956 2.96 2.96 0 0 1 2.956 2.956 2.96 2.96 0 0 1-2.956 2.956z"/>
				<path d="M22.197 3.81A12.92 12.92 0 0 0 13.001 0C9.526 0 6.26 1.353 3.803 3.81c-5.07 5.07-5.07 13.322 0 18.393l.836.836 1.673-1.672-.837-.836c-4.149-4.15-4.149-10.9 0-15.05a10.576 10.576 0 0 1 7.526-3.116c2.842 0 5.514 1.107 7.524 3.116 4.15 4.15 4.15 10.9 0 15.05l-.836.836 1.672 1.672.836-.836c5.07-5.072 5.07-13.323 0-18.393z"/>
			</svg>
		<?php elseif( is_post_type_archive( 'contest' ) ): ?>
			<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 30.928 30.928" aria-labelledby="contest-icon-title contest-icon-desc">
				<title id="contest-icon-title">
					<?php esc_html( __( 'Content Archive Title Icon' ) ); ?>
				</title>
				<desc id="contest-icon-desc">
					<?php esc_html( __( 'Icon of a trophy' ) ); ?>
				</desc>
				<path d="M24.791 4.451c.02-.948-.016-1.547-.016-1.547l-9.264-.007h-.094l-9.265.007s-.035.599-.015 1.547H0v1.012c0 .231.039 5.68 3.402 8.665C4.805 15.373 6.555 15.999 8.618 16c.312 0 .633-.021.958-.049 1.172 1.605 2.526 2.729 4.049 3.289v4.445H9.154v2.784H7.677v1.561H23.251v-1.56h-1.478v-2.784h-4.471v-4.445c1.522-.56 2.877-1.684 4.049-3.289.327.028.648.048.96.048 2.062-.002 3.812-.627 5.215-1.873 3.363-2.985 3.402-8.434 3.402-8.665V4.451h-6.137zM4.752 12.619c-1.921-1.7-2.489-4.61-2.657-6.144h4.158c.176 1.911.59 4.292 1.545 6.385.175.384.359.748.547 1.104-1.433-.055-2.639-.502-3.593-1.345zm21.424 0c-.953.844-2.16 1.29-3.592 1.345.188-.355.372-.72.547-1.104.955-2.093 1.369-4.474 1.544-6.385h4.158c-.168 1.533-.735 4.443-2.657 6.144z"/>
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

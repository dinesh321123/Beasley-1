<?php

get_header();

the_post();

?><div <?php post_class(); ?>><?php
	if ( ee_is_first_page() ) :
		get_template_part( 'partials/show/header' );
		get_template_part( 'partials/podcast/header' );

		?><div class="content-wrap">
			<?php get_template_part( 'partials/podcast/actions' ); ?>
		</div><?php
	endif;

	?><div class="entry-content content-wrap">
		<?php get_template_part( 'partials/ads/sidebar-sticky' ); ?>
		<?php get_template_part( 'partials/podcast/episodes' ); ?>
	</div>
</div><?php

get_footer();

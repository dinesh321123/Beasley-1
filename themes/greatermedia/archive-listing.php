<?php
/**
 * Archive template file for directory listing
 */

get_header();

?><section class="directory-archive">
	<?php get_template_part( 'partials/directory-listing/archive-header' ); ?>
	<?php get_template_part( 'partials/directory-listing/archive-categories' ); ?>
</section><?php

get_footer();

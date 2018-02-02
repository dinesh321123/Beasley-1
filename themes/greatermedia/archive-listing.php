<?php
/**
 * Archive template file for directory listing
 */

get_header();

echo '<div class="container">';
	get_template_part( 'partials/directory-listing/archive-header' );
	get_template_part( 'partials/directory-listing/archive-categories' );
echo '</div>';

get_footer();

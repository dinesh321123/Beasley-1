<?php

echo '<div class="archive-title content-wrap">';
	get_template_part( 'partials/archive/icon', get_query_var( 'post_type' ) );
	the_archive_title( '<h1>', '</h1>' );
echo '</div>';

if (!empty(get_query_var( 'cat' ))) {
	the_archive_description('<div class="content-wrap" style="padding-bottom: 0">', '</div>');
}

<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header();

if ( ! get_query_var( 'view' ) ) :
	get_template_part( 'partials/article', get_post_format() );
else :
	get_template_part( 'content-gallery-slideshow' );
endif;

get_footer();
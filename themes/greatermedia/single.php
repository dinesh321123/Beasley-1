<?php
/**
 * Single Post template file
 *
 * @package Greater Media
 * @since   0.1.0
 */

get_header(); ?>

	<main class="main" role="main">

		<?php get_template_part( 'partials/article', get_post_format() ); ?>

	</main>

<?php get_footer();
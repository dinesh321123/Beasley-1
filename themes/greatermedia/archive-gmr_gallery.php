<?php
/**
 * Archive template file for Galleries
 *
 * @package Greater Media
 * @since   0.1.0
 *
 * @todo this template file still needs to be layed out according to the design
 */

get_header(); ?>

	<main class="main" role="main">

		<div class="container">

			<section class="gallery__archive">

				<?php get_template_part( 'partials/gallery-archive' ); ?>

			</section>

		</div>

	</main>

<?php get_footer(); ?>
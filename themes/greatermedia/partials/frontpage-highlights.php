<?php
/**
 * Partial for the Front Page Highlights
 * Music Theme - Community and Events
 * News/Sports Theme - Events and Contests
 *
 * @package Greater Media
 * @since   0.1.0
 */
?>

<section class="home__highlights">

	<div class="highlights__col">

		<?php if ( is_news_site() ) {

			get_template_part( 'partials/news/highlights');

		} else {

			
				get_template_part( 'partials/music/highlights' );
			

		} ?>

	</div>

</section>
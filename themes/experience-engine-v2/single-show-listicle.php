<?php

get_header();

the_post();
?>
<style>
	.placeholder-dfp {
		display: none;
	}
</style>
<?php
echo '<div class="', join( ' ', get_post_class() ), '">';
	echo '<div class="article-inner-container">';
		echo '<div class="content-wrap">';
			if ( ee_is_first_page() ) :
				get_template_part( 'partials/show/custom-header' );
			endif;

			echo '<div class="content-wrap">';
				ee_the_subtitle( 'Listicle' );
				get_template_part( 'partials/show/listiclecpt' );
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

get_footer();

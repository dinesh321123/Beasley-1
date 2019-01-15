<?php

$related_articles = ee_get_related_articles();
if ( empty( $related_articles ) ) :
	return;
endif;

echo '<div class="related-articles content-wrap">';
	ee_the_subtitle( 'You might also like' );

	echo '<div class="archive-tiles -list">';
		foreach ( $related_articles as $article ) :
			$GLOBALS['post'] = $article;
			setup_postdata( $article );

			get_template_part( 'partials/tile', $article->post_type );
		endforeach;
	echo '</div>';
echo '</div>';

wp_reset_postdata();

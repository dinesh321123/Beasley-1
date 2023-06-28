<?php
if ( ! class_exists( 'TrendingArticle' ) ) :
	return;
endif;

echo ee_render_trending_articles('embed_sidebar');
<?php
add_action('commentsrss2_head', 'recent_section_feeds_on_show');

function recent_section_feeds_on_show() {
	$feeds_query = \GreaterMedia\Shows\get_show_main_query( 16 );
		if ( $feeds_query->have_posts() ) :
			while ( $feeds_query->have_posts() ) {
				$feeds_query->the_post();
	?>
		<item>
			<title><?php echo get_the_title();	?></title>
			<link><?php echo get_post_permalink();?></link>
			<dc:creator><?php echo get_the_author(); ?> </dc:creator>
			<pubDate><?php echo get_the_date(); ?></pubDate>
			<?php // $category = get_the_category();
			foreach(get_the_category() as $category){
				echo '<category>'.$category->cat_name.'</category>';
			} ?>
			<media:featureImage><?php echo get_the_post_thumbnail(); ?></media:featureImage>
		</item>
	<?php
			}
		endif;
}

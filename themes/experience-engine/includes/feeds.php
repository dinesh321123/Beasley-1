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
				<premalink><?php echo esc_url( get_post_permalink() ); ?></premalink>
				<dc:creator><?php echo get_the_author(); ?> </dc:creator>
				<pubDate><?php echo get_the_date(); ?></pubDate>
				<?php // $category = get_the_category();
				foreach(get_the_category() as $category){
					echo '<category>' . esc_html( $category->cat_name ) . '</category>';
				} ?>
				<?php
				if ( has_post_thumbnail( $post->ID ) ) {
					$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'original' );
					if ( ! empty( $thumbnail[0] ) ) { ?>
						<media:featureImage url="<?php echo esc_attr( $thumbnail[0] ); ?>"  width="<?php echo esc_attr( $thumbnail[1] ); ?>"  height="<?php echo esc_attr( $thumbnail[2] ); ?>" > </media:featureImage> <?php
					}
				}
				?>
			</item>
			<?php
		}
	endif;
}

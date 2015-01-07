<?php while ( have_posts() ) : the_post(); ?>

	<?php
	global $post; 
	
	$partial_name = '';
	
	if ( 'tribe_events' == $post->post_type ) {
		$partial_name = 'tribe_events';
	}
	
	get_template_part( 'partials/entry', $partial_name ); 
	?>
	
<?php endwhile; ?>
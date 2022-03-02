<?php while ( have_posts() ) : the_post();

	$closure_type = get_post_meta( get_the_ID(), 'gmedia_closure_type', true );
	$closure_general_location = get_post_meta( get_the_ID(), 'gmedia_closure_general_location', true );
	?>

	<article class="closure cf">
		<div class="closure-attr--entity">
			<p><?php the_title(); ?></p>
		</div>
		<div class="closure-attr--entity_location">
			<p><?php echo esc_html( $closure_general_location ); ?></p>
		</div>
		<div class="closure-attr--type">
			<p><?php echo esc_html( $closure_type ); ?></p>
		</div>
	</article>
<?php endwhile; ?>
<?php $post_terms = get_the_terms( $post->ID, 'am_category' ); ?>
<?php if ( ! empty ( $post_terms ) ): ?>
<div>
	<ul class="post-categories">
		<?php 
		foreach ( $post_terms as $index => $post_term ) { 
			$category_url = get_category_link( $post_term->term_id );
			echo '<li><a href="', $category_url, '" rel="category tag">', $post_term->name, '</a></li>';
		} 
		?>
	</ul>	
</div>
<?php endif; ?>

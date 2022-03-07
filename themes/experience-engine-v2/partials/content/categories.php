<?php if ( has_category() ) : ?>
	<div>
		<?php
		$exclude_list	=	array( "must-haves-chr-ac", "stacker", "must-haves-rock", "must-haves-urban", "must-haves-sports", "must-haves-news-talk", "must-haves-country" );
		$cate_details	=	get_the_category( $post->ID );
		if(count($cate_details) > 0) {
			echo '<ul class="post-categories">';
			foreach ($cate_details as $category) {
				if( !in_array( $category->slug, $exclude_list) ) {
					echo "<li><a href = '" . get_category_link($category->term_id) . "' rel='category tag' style='padding-left: 5px; text-transform:capitalize;'>" . $category->cat_name . "</a></li>";
				}
			}
			echo "</ul>";
		}
		?>
		<?php // the_category(); ?>
	</div>
<?php endif; ?>

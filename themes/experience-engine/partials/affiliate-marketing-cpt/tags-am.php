<?php 
	$tag_list = $tags = get_the_term_list( $post->ID, 'am_tag', '<div class="post-tags post-tags-am"><div class="post-tag-label">Tags</div><div class="post-tag-items">', ',', '</div></div>' );
		echo $tag_list;
?>
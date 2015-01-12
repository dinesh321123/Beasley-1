<?php

namespace Greater_Media\Gallery_Post_Thumbnails; 

function post_has_gallery( $post = null )
{
	$post = get_post( $post );
	
	if ( ! $post ) {
		return; 
	}
	
	return (bool) preg_match( '#\[gallery#i', $post->post_content );  
}

<?php

namespace Greater_Media\Gallery_Post_Thumbnails;

class Thumbnail_Filter
{	
	protected $_filtering_meta = false; 
	 
	public function __construct()
	{	
		add_filter( 'get_post_metadata', array( $this, 'filter_post_meta' ), 10, 4 );
		add_action( 'save_post', array( $this, 'clear_cached_gallery_thumb' ) );
	}
	
	public function filter_post_meta( $data, $post_id, $key, $single )
	{
		if ( '_thumbnail_id' == $key && ! $this->_filtering_meta ) {
			$this->_filtering_meta = true;
		
			$thumbnail_id = get_post_meta( $post_id, $key, $single );
		
			if ( ! $thumbnail_id ) {
				// Try to get one from cache. 
				$thumbnail_id = wp_cache_get( $post_id, 'gm/post_gallery_thumb' );
				
				if ( -1 == $thumbnail_id ) {
					// "Blank" value represented this way so we don't keep 
					// refreshing the cache. Return false. 
					$thumbnail_id = false; 
				} elseif ( ! $thumbnail_id ) {
					// Okay, get a new one. 
					$thumbnail_id = $this->_get_first_gallery_image( $post_id );
										
					// If we've got nothing, store a "blank" value so we don't keep
					// refreshing the cache. 
					if ( ! $thumbnail_id ) {
						$thumbnail_id = -1;
					}  
					
					// Throw it in the cache. 
					wp_cache_set( $post_id, $thumbnail_id, 'gm/post_gallery_thumb' );
				} 
			}
			
			$data = $thumbnail_id; 
			$this->_filtering_meta = false; 
		}
				
		return $data; 
	}
	
	public function clear_cached_gallery_thumb( $post_id ) 
	{
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		
		wp_cache_delete( $post_id, 'gm/post_gallery_thumb' );
	}
	
	protected function _get_first_gallery_image( $post )
	{
		$post = get_post( $post );
		
		// Make sure there's a post. 
		if ( ! $post ) {
			return; 
		}		
		
		// Make sure there's a gallery. 
		if ( ! preg_match( '#\[gallery#i', $post->post_content ) ) {
			return; 
		}
		
		// Does the gallery have image IDs specified? 
		if ( preg_match( '#\[gallery[^\]]+ids\s*=\s*((\'|")?)(\d+)#', $post->post_content, $matches ) ) {
			return (int) $matches[3]; 
		}
		
		// Find the first image attached to this post.
		$children = get_children( array( 
			'post_parent' => $post->ID, 
			'post_status' => 'inherit', 
			'post_type' => 'attachment', 
			'post_mime_type' => 'image',
			'posts_per_page' => 1,
			'fields' => 'ids',
		) ); 
		if ( $children ) {
			return (int) $children[0]; 
		}
	}
}

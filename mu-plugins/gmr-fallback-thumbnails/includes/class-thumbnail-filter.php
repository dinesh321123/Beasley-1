<?php

namespace Greater_Media\Fallback_Thumbnails;

use TDS;

class Thumbnail_Filter {
	protected $_filtering_meta = false;

	public function __construct() {
		add_filter( 'get_post_metadata', array( $this, 'filter_post_meta' ), 10, 4 );
		add_action( 'save_post', array( $this, 'clear_cached_fallback_thumb' ) );
	}

	/**
	 * Filter the post meta to add a featured image for posts if they do not have one
	 *
	 * @param $data
	 * @param $post_id
	 * @param $key
	 * @param $single
	 *
	 * @return bool|int|mixed|void
	 */
	public function filter_post_meta( $data, $post_id, $key, $single ) {
		if ( '_thumbnail_id' == $key && ! $this->_filtering_meta ) {
			$this->_filtering_meta = true;

			$thumbnail_id = get_post_meta( $post_id, $key, $single );

			if ( ! $thumbnail_id ) {
				// Try to get one from cache. 
				$thumbnail_id = wp_cache_get( $post_id, 'gm/post_fallback_thumb' );

				if ( - 1 == $thumbnail_id ) {
					// "Blank" value represented this way so we don't keep 
					// refreshing the cache. Return false. 
					$thumbnail_id = false;
				} elseif ( ! $thumbnail_id ) {
					// Okay, get a new one.
					if ( 'podcast' == get_post_type( $post_id ) ) {
						$thumbnail_id = $this->_get_image_for_podcast( $post_id );
					} elseif ( 'episode' == get_post_type( $post_id ) ) {
						$thumbnail_id = $this->_get_image_for_podcast_episode( $post_id );
					} elseif ( 'gmr_album' == get_post_type( $post_id ) ) {
						$thumbnail_id = $this->_get_image_for_album( $post_id );
					} else {
						$thumbnail_id = $this->_get_image_for_gallery( $post_id );
					}

					// If we've got nothing, store a "blank" value so we don't keep
					// refreshing the cache. 
					if ( ! $thumbnail_id ) {
						$thumbnail_id = - 1;
					}

					// Throw it in the cache. 
					wp_cache_set( $post_id, $thumbnail_id, 'gm/post_fallback_thumb' );
				}
			}

			$data                  = $thumbnail_id;
			$this->_filtering_meta = false;
		}

		return $data;
	}

	public function clear_cached_fallback_thumb( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		wp_cache_delete( $post_id, 'gm/post_fallback_thumb' );
	}

	/**
	 * Get thumbnail for a podcast. This will use the logo for the show associated with the podcast.
	 *
	 * @param $post_id
	 *
	 * @return int|void
	 */
	protected function _get_image_for_podcast( $post_id ) {
		//  Find the associated show post
		$terms = wp_get_object_terms( $post_id, '_shows' );

		if ( ! $terms ) {
			return;
		}

		$show_post = TDS\get_related_post( $terms[0] );

		if ( ! $show_post ) {
			return;
		}

		return (int) get_post_meta( $show_post->ID, 'logo_image', true );
	}

	/**
	 * Get thumbnail for a podcast episode. This will pull the image from the parent podcast if the episode does not
	 * have a featured image
	 *
	 * @param $post
	 *
	 * @return int|void
	 */
	protected function _get_image_for_podcast_episode( $post ) {

		$post = get_post( $post );

		// Make sure there's a post.
		if ( ! $post ) {
			return;
		}

		$parent_post = $post->post_parent;

		if ( ! $parent_post ) {
			return;
		}

		return $this->_get_image_for_podcast( $parent_post );

	}

	/**
	 * Get thumbnail for an album. This will use the first image of the first gallery in the album.
	 *
	 * @param $post
	 *
	 * @return int|void
	 */
	protected function _get_image_for_album( $post ) {
		$post = get_post( $post );

		// Make sure there's a post.
		if ( ! $post ) {
			return;
		}

		// Get the first gallery in this album.
		$child_gallery_posts = get_posts( array(
			'post_parent'    => $post->ID,
			'post_type'      => 'gmr_gallery',
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => 1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		) );

		// Get the thumbnail for that gallery.
		if ( $child_gallery_posts ) {
			return $this->_get_image_for_gallery( $child_gallery_posts[0] );
		}
	}

	/**
	 * Get thumbnail for a gallery. This will use the first image in the gallery.
	 *
	 * @param $post
	 *
	 * @return int|void
	 */
	protected function _get_image_for_gallery( $post ) {
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
			'post_parent'    => $post->ID,
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		) );
		if ( $children ) {
			return (int) $children[0];
		}
	}
}

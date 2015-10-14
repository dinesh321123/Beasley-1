<?php

namespace GreaterMedia\Gigya\Migration;

class FacebookMember {

	public $facebook_id;
	public $use_avatar;
	public $links = array();
	public $likes = array();

	function parse( $node ) {
		$attr              = $node->attributes;
		$this->facebook_id = $attr->getNamedItem( 'FacebookID' )->nodeValue;

		if ( ! is_null( $attr->getNamedItem( 'UseFacebookProfileImageForCommentingAvatar' ) ) ) {
			$this->use_avatar  = $attr->getNamedItem( 'UseFacebookProfileImageForCommentingAvatar' )->nodeValue;
		}

		$this->parse_nodes( $node );
	}

	function parse_nodes( $node ) {
		$child_node = $node->firstChild;

		while ( ! is_null( $child_node ) ) {
			switch ( $child_node->nodeName ) {
				case 'FacebookUserLinks':
					$this->links[] = $this->parse_facebook_link( $child_node );
					break;

				case 'FacebookUserLikes':
					$this->likes[] = $this->parse_facebook_like( $child_node );
					break;
			}

			$child_node = $child_node->nextSibling;
		}
	}

	function parse_facebook_link( $node ) {
		$link = new FacebookLink( $node );
		$link->parse( $node );

		return $link;
	}

	function parse_facebook_like( $node ) {
		$like = new FacebookLike();
		$like->parse( $node );

		return $like;
	}

}

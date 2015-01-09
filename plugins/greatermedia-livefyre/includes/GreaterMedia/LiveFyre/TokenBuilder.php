<?php

namespace GreaterMedia\LiveFyre;

use Livefyre\Livefyre;

class TokenBuilder {

	public $options;
	public $expires = 31536000; // 1 year

	function __construct( $options ) {
		$this->options = $options;
	}

	function tokens_for( $post ) {
		$network_name = $this->get_option( 'network_name' );
		$network_key  = $this->get_option( 'network_key' );
		$site_id      = $this->get_option( 'site_id' );
		$site_key     = $this->get_option( 'site_key' );

		$title      = $post->post_title;
		$article_id = strval( $post->ID );
		$url        = get_permalink( $post->ID );

		$network    = Livefyre::getNetwork( $network_name, $network_key );
		$site       = $network->getSite( $site_id, $site_key );
		$collection = $site->buildCommentsCollection( $title, $article_id, $url );

		$tokens = array(
			'collection_meta' => $collection->buildCollectionMetaToken(),
			'checksum'        => $collection->buildChecksum(),
		);

		return $tokens;
	}

	function get_auth_token() {
		$network_name = $this->get_option( 'network_name' );
		$network_key  = $this->get_option( 'network_key' );
		$network      = Livefyre::getNetwork( $network_name, $network_key );

		return $network->buildUserAuthToken(
			md5( get_gigya_user_id() ),
			get_gigya_user_field( 'firstName' ) . ' ' . get_gigya_user_field( 'lastName' ),
			$this->expires
		);
	}

	function get_option( $name ) {
		if ( array_key_exists( $name, $this->options ) ) {
			return $this->options[ $name ];
		} else {
			return '';
		}
	}


}

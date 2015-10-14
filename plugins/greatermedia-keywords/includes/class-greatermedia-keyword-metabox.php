<?php
/**
 * Created by Eduard
 * Date: 20.11.2014 19:08
 */

class GreaterMedia_Keyword_MetaBox {

	private $post_types;

	public function __construct() {
		//$this->post_types = get_post_types( '', 'names' );
		add_action( 'post_submitbox_misc_actions', array( $this, 'keyword_post_submitbox_meta') );
	}

	public function keyword_post_submitbox_meta() {
		global $post;
		if( in_array( $post->post_type, GreaterMedia_Keyword_Admin::$supported_post_types ) ) {
			$keywords = array();
			$options = GreaterMedia_Keyword_Admin::get_keyword_options( GreaterMedia_Keyword_Admin::$plugin_slug . '_option_name' );
			if( is_array( $options ) && !empty( $options ) ) {
				foreach( $options as $keyword => $post_data) {
					if( $post_data['post_id'] == $post->ID ) {
						array_push( $keywords, $keyword );
					}
				}
				if( !empty( $keywords ) ) {
					$keywords = implode( ', ', $keywords );
					$url = admin_url( 'tools.php?page=' . GreaterMedia_Keyword_Admin::$plugin_slug );
					echo '<div class="misc-pub-section keyword_meta">';
					echo '<label for="keyword">Keywords: </label>';
					echo '<span id="keyword" class="keyword">' . $keywords . '</span> ';
					echo '<a href="' . $url . '" class="edit-visibility">';
					echo '<span aria-hidden="true">Edit</span>';
					echo '<span class="screen-reader-text">Edit</span></a>';
					echo '</div>';
				}
			}
		}
	}
}

$GreaterMedia_Keyword_MetaBox = new GreaterMedia_Keyword_MetaBox();

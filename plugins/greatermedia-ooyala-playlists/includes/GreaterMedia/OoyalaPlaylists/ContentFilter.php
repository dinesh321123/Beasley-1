<?php

namespace GreaterMedia\OoyalaPlaylists;

class ContentFilter
{
	public function __construct()
	{
		add_filter( 'content_save_pre', array( $this, 'filter_content' ), 0 );
	}

	public function filter_content($content)
	{
		$pattern = '#<div\s*id\s*=\s*\\\(\'|")playerContainer.*ooyala\.com\/v3\/.*?<\/script>.*?<\/script>#is';
		$pattern_with_encoded_brackets = str_replace( array( '<', '>' ), array( '&lt;', '&gt;' ), $pattern );

		$content = preg_replace_callback( array( $pattern, $pattern_with_encoded_brackets ),
			array( $this, 'replacement_callback' ), $content, -1, $match_count );
		return $content;
	}

	public function replacement_callback( $matches )
	{
		$unescaped_content = str_replace( '\\', '', $matches[0] );

		// Retrieve player id
		preg_match( '#https:\/\/player.ooyala.com\/v3\/([a-zA-Z0-9]*)#', $unescaped_content, $player_id_matches );
		$player_id = $player_id_matches[1];

		// Retrieve playlist id(s)
		preg_match( '#playlistsPlugin.*\[(([\w\",])+)\]\}#' , $unescaped_content, $playlist_id_matches );
		$playlist_ids = str_replace( '"', '', $playlist_id_matches[1] );

		// Retrieve video to start with if provided
		preg_match( '#OO\.Player\.create\(\'playerContainer\',\s?\'((?:[a-zA-Z0-9-_])+)\'#', $unescaped_content, $video_id_matches );
		$video_id = is_array($video_id_matches) ? $video_id_matches[1] : '';

		// Retrieve all other settings
		$attr_map = array(
			'autoplay' => 'autoplay',
			'loop' => 'loop',
			'height' => 'height',
			'width' => 'width',
			'useFirstVideoFromPlaylist' => 'use_first_video_from_playlist'
		);

		preg_match_all( '#(autoplay|loop|height|width|useFirstVideoFromPlaylist):\s?(?:\\\"|\\\')?([\w]*?)(?:\\\"|\\\')?(?:,|\}|\s)#', $matches[0], $sub_matches, PREG_SET_ORDER );

		$res = '[ooyala_playlist';
		$res .= ' player_id="' . $player_id . '"';
		$res .= ' playlist_ids="' . $playlist_ids . '"';
		$res .= ' video_id="' . $video_id . '"';
		foreach ( $sub_matches as $sub_match ) {
			if ( array_key_exists( $sub_match[1], $attr_map ) ) {
				$sub_match[1] = $attr_map[ $sub_match[1] ];
				if (!empty($sub_match[2])){
					$res .= ' ' . $sub_match[1] . '="' . $sub_match[2] . '"';
				}
			}
		}
		$res .= ']';

		return $res;
	}
}

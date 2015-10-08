<?php

namespace WordPress\Utils;

class InlineLibSynReplacer {

	static public $replacements = 0;

	public $media_url_pattern = '/var mediaURL\s*=\s*"([^"]+)";/';
	public $iframe_tag_pattern = '/<iframe[^>]+>/';
	public $iframe_src_pattern = "/src=['\"](http:\/\/[a-zA-Z0-9-]+\.libsyn\.com\/embed\/[^'\"]*?)['\"]/";
	public $container;
	public $file_names = array();

	function find_and_replace( $content ) {
		$iframes       = $this->find_iframes( $content );
		$libsyn_embeds = $this->find_embeds( $iframes );

		foreach ( $libsyn_embeds as $embed ) {
			$content = $this->replace_embed( $content, $embed );
		}

		return $content;
	}

	function find( $content ) {
		$iframes       = $this->find_iframes( $content );
		$libsyn_embeds = $this->find_embeds( $iframes );

		return $libsyn_embeds;
	}

	function replace_embed( $content, $embed ) {
		$replacement = $this->get_replacement( $embed['src'] );

		if ( $replacement !== false ) {
			$replacement_url = $replacement['url'];
			$replacement_text = "[audio mp3=\"{$replacement_url}\"][/audio]";
			$content = str_replace( $embed['tag'], $replacement_text, $content );

			self::$replacements++;
		}

		return $content;
	}

	function get_replacement( $url ) {
		$media_file = $this->download( $url );

		if ( $media_file !== false ) {
			$attachment                   = $this->sideload_replacement( $media_file );
			$replacement = array();
			$replacement['attachment_id'] = $attachment['ID'];
			$replacement['url']           = $attachment['file_meta']['url'];

			return $replacement;
		} else {
			return false;
		}
	}

	function sideload_replacement( $path ) {
		$entity = $this->container->entity_factory->build( 'attachment' );
		$attachment = array( 'file' => $path );

		return $entity->add( $attachment );
	}

	function download( $url ) {
		$iframe_content = $this->fetch( $url );
		$media_url      = $this->parse( $iframe_content );

		if ( $media_url !== false ) {
			//\WP_CLI::log( "Downloading $media_url" );
			$path = $this->get_cache_file_name( $media_url );

			if ( $this->container->opts['fake_media'] ) {
				return $path;
			}

			$media_file = $this->download_mp3( $media_url );
			file_put_contents( $path, $media_file );
			//\WP_CLI::success( "Downloaded to $path" );

			return $path;
		} else {
			return false;
		}
	}

	function download_mp3( $url ) {
		if ( $this->container->opts['fake_media'] ) {
			return fopen( $this->container->asset_locator->get_random_mp3(), 'r' );
		} else {
			return fopen( $url, 'r' );
		}
	}

	function get_cache_file_name( $url ) {
		if ( $this->container->opts['fake_media'] ) {
			return $this->path_for_file_name( 'sample_mp3_file.mp3' );
		}

		$name  = basename( $url );
		$index = 0;
		$path = $this->path_for_file_name( $name, $index );

		while ( file_exists( $path ) ) {
			$path = $this->path_for_file_name( $name, ++$index );
		}

		return $path;
	}

	function path_for_file_name( $name, $index = 0 ) {
		if ( $index !== 0 ) {
			$path_info = pathinfo( $name );
			$name      = $path_info['filename'] . '-' . $index . '.' . $path_info['extension'];
		}

		return $this->get_downloads_dir() . '/' . $name;
	}

	function get_downloads_dir() {
		return $this->container->config->get_downloads_dir();
	}

	function fetch( $url ) {
		if ( $this->container->opts['fake_media'] ) {
			return 'var mediaURL = "http://traffic.libsyn.com/foo.mp3";';
		} else {
			return file_get_contents( $url );
		}
	}

	function parse( $content ) {
		$result = preg_match( $this->media_url_pattern, $content, $matches );

		if ( $result === 1 ) {
			return $matches[1];
		} else {
			return false;
		}
	}

	/* helpers */
	function find_iframes( $content ) {
		return $this->find_all( $content, $this->iframe_tag_pattern );
	}

	function find_embeds( $iframes ) {
		$embeds = array();

		foreach ( $iframes as $iframe ) {
			$embed = $this->find_embed( $iframe );

			if ( $embed !== false ) {
				$embeds[] = $embed;
			}
		}

		return $embeds;
	}

	function find_embeds_inplace( $iframes ) {
		$embeds = array();

		foreach ( $iframes as $iframe ) {
			$embed = $this->find_embed( $iframe );

			if ( $embed !== false ) {
				$embed['file'] = $this->download( $embed['src'] );
				$embeds[] = $embed;
			}
		}

		return $embeds;
	}

	function find_embed( $iframe ) {
		$embed = $this->find_lib_syn_embed( $iframe );

		if ( $embed !== false ) {
			return array(
				'src' => $embed,
				'tag' => $iframe,
			);
		} else {
			return false;
		}
	}

	function find_lib_syn_embed( $content ) {
		$result = preg_match( $this->iframe_src_pattern, $content, $matches );

		if ( $result === 1 ) {
			return $matches[1];
		} else {
			return false;
		}
	}

	function find_all( $content, $pattern ) {
		$result = preg_match_all( $pattern, $content, $matches );

		if ( $result >= 1 ) {
			return $matches[0];
		} else {
			return array();
		}
	}


}

<?php

namespace WordPress\Utils;

class InlineImageReplacer {

	public $container;
	public $img_tag_pattern = '/<img[^>]+>/';
	public $img_src_pattern = "/src=['\"]([^'\"]*?)['\"]/";
	public $class_src_pattern = "/class=['\"]([^'\"]*?)['\"]/";
	public $bg_url_pattern = "/url\('([^']*)'\)/";

	function find_and_replace( $content, $parent = null ) {
		$content = $this->find_and_replace_img_tags( $content, $parent );
		$content = $this->find_and_replace_background_images( $content, $parent );

		return $content;
	}

	function find_and_replace_img_tags( $content, $parent = null ) {
		$image_tags = $this->find_image_tags( $content );
		$images     = $this->find_images( $image_tags );

		foreach ( $images as $image_item ) {
			$content = $this->replace_image( $image_item, $content );
		}

		return $content;
	}

	function find_and_replace_background_images( $content, $parent = null ) {
		$images = $this->find_background_images( $content );

		foreach ( $images as $image_item ) {
			$content = $this->replace_background_image( $image_item, $content );
		}

		return $content;
	}

	function find_background_images( $content ) {
		$result = preg_match_all( $this->bg_url_pattern, $content, $matches );

		if ( $result >= 1 ) {
			return $matches[1];
		} else {
			return array();
		}
	}

	function replace_background_image( $src, $content ) {
		$replacement = $this->get_replacement( $src );

		if ( $replacement !== false ) {
			$new_url       = $replacement['url'];
			$attachment_id = $replacement['attachment_id'];

			$content = str_replace( $src, $new_url, $content );;
		}

		return $content;
	}

	function replace_image( $image_item, $content ) {
		$tag         = $image_item['tag'];
		$src         = $image_item['src'];
		$replacement = $this->get_replacement( $src );

		if ( $replacement !== false ) {
			$new_url       = $replacement['url'];
			$attachment_id = $replacement['attachment_id'];
			$new_tag       = str_replace( $src, $new_url, $tag );

			if ( strpos( $new_tag, 'class' ) !== false ) {
				$new_class = 'class="$1' . ' wp-image-' . $attachment_id . '"';
				$new_tag   = preg_replace( $this->class_src_pattern, $new_class, $new_tag, 1 );
			} else {
				$new_tag = str_replace( '<img', '<img class="' . "wp-image-$attachment_id" . '" ', $new_tag );
			}

			$content   = str_replace( $tag, $new_tag, $content );
		}

		return $content;
	}

	function find_image_tags( $content ) {
		$result = preg_match_all( $this->img_tag_pattern, $content, $matches );

		if ( $result >= 1 ) {
			return $matches[0];
		} else {
			return array();
		}
	}

	function find_images( $image_tags ) {
		$images = array();

		foreach ( $image_tags as $image_tag ) {
			$image = $this->find_image( $image_tag );
			if ( ! empty( $image ) ) {
				$images[] = array(
					'tag' => $image_tag,
					'src' => $image,
				);
			}
		}

		return $images;
	}

	function find_image( $image_tag ) {
		$result = preg_match( $this->img_src_pattern, $image_tag, $matches );

		if ( $result === 1 ) {
			return $matches[1];
		} else {
			return false;
		}
	}

	function get_replacement( $image ) {
		$replacement   = array();
		$asset_locator = $this->container->asset_locator;

		$path       = parse_url( $image, PHP_URL_PATH );
		$path       = urldecode( $path );
		$found_path = $asset_locator->find( $path );

		if ( $found_path !== false ) {
			$attachment                   = $this->sideload_replacement( $path );
			$replacement['attachment_id'] = $attachment['ID'];
			$replacement['url']           = $attachment['file_meta']['url'];

			return $replacement;
		}

	}

	function sideload_replacement( $path ) {
		$entity = $this->container->entity_factory->build( 'attachment' );
		$attachment = array( 'file' => $path );

		return $entity->add( $attachment );
	}

}

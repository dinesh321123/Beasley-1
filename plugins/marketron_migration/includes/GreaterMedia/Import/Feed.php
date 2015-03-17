<?php

namespace GreaterMedia\Import;

class Feed extends BaseImporter {

	function get_tool_name() {
		return 'feed';
	}

	function import_source( $source ) {
		$tool      = $this->get_tool();
		$tool_name = $tool->get_name();
		$posts     = $this->get_entity( 'post' );
		$articles  = $this->articles_from_source( $source );
		$total     = count( $articles );
		$locator   = $this->container->asset_locator;
		$notify    = new \cli\progress\Bar( "Importing $total items from $tool_name", $total );
		$max_items = $this->get_site_option( 'limit' );
		$item_index = 1;

		foreach ( $articles as $article ) {
			$post = $this->post_from_article( $article );
			$posts->add( $post );
			$notify->tick();

			if ( $item_index++ > $max_items ) {
				break;
			}
		}

		$notify->finish();
	}

	function post_from_article( $article ) {
		$post           = array();
		$categories     = $this->categories_from_article( $article );
		$tags           = $this->tags_from_article( $article );
		$redirects      = $this->redirects_from_article( $article );
		$featured_image = $this->featured_image_from_article( $article );
		$content_parts  = $this->content_from_article( $article );
		$content        = $content_parts['body'];
		$post_format    = $content_parts['post_format'];
		$featured_audio = $this->featured_audio_from_article( $article );
		$authors        = $this->authors_from_article( $article );
		$post_title     = $this->title_from_article( $article );
		$post_excerpt   = $this->excerpt_from_article( $article );
		$created_on     = $this->import_string( $article['UTCStartDateTime'] );
		$modified_on    = $this->import_string( $article['LastModifiedUTCDateTime'] );

		if ( ! is_null( $featured_audio ) ) {
			$post_format = 'audio';
		}

		$post = array(
			'post_author'           => 0,
			'post_authors'          => $authors,
			'post_type'             => 'post',
			'post_title'            => $post_title,
			'post_name'             => sanitize_title( $post_title ),
			'post_excerpt'          => $post_excerpt,
			'post_content'          => $content,
			'post_content_filtered' => null,
			'post_status'           => 'publish',
			'comment_status'        => 'open',
			'ping_status'           => 'open',
			'to_ping'               => null,
			'pinged'                => null,
			'post_parent'           => 0,
			'menu_order'            => 0,
			'post_mime_type'        => null,
			'comment_count'         => 0,
			'created_on'            => $created_on,
			'modified_on'           => $modified_on,

			'tags'           => $tags,
			'categories'     => $categories,
			'post_format'    => $post_format,
		);

		if ( ! is_null( $featured_image ) ) {
			$post['featured_image'] = $featured_image;
		}

		if ( ! is_null( $featured_audio ) ) {
			$post['featured_audio'] = $featured_audio;
		}

		return $post;
	}

	function articles_from_source( $source ) {
		return $source->Articles->Article;
	}

	function content_from_article( $article ) {
		$content     = $article['ArticleText'];
		$post_format = 'standard';

		if ( isset( $content ) ) {
			$content = $this->import_string( $content );
		} else {
			$content = '';
		}

		if ( ! empty( $article['PrimaryMediaReference'] ) ) {
			$primary_media_ref = $this->import_string( $article['PrimaryMediaReference'] );

			if ( strpos($primary_media_ref, 'youtube.com' ) !== false ) {
				$content     = $primary_media_ref . '<br/>' . $content;
				$post_format = 'video';
			}
		}

		$content = preg_replace(
			'#<div.*data-youtube-id="(.*)">.*</div>#',
			'http://www.youtube.com/watch?v=${1}',
			$content, -1, $videos
		);

		if ( $post_format !== 'video' && $videos > 0 ) {
			$post_format = 'video';
		}

		return array(
			'body'        => $content,
			'post_format' => $post_format,
		);
	}

	function title_from_article( $article ) {
		$title = $article['Title'];

		if ( ! empty( $title ) ) {
			$title = $this->import_string( $title );
		} else {
			$title = $this->import_string( $article['Subtitle'] );
		}

		$title = htmlentities( $title );
		$title = ucwords( $title );

		return $title;
	}

	function excerpt_from_article( $article ) {
		$excerpt = $article['ExcerptText'];

		if ( ! empty( $excerpt ) ) {
			$excerpt = $this->import_string( $excerpt );
		} else {
			$excerpt = '';
		}

		return $excerpt;
	}

	function categories_from_article( $article ) {
		$categories     = $article->ArticleCategories->ArticleCategory;
		$category_names = array();

		if ( isset( $categories ) ) {
			foreach ( $categories as $category ) {
				if ( isset( $category['CategoryName'] ) ) {
					$category_names[] = $this->import_string( $category['CategoryName'] );
				}
			}
		}

		return array_unique( $category_names );
	}

	function tags_from_article( $article ) {
		$tags      = $article->Tags->Tag;
		$tag_names = array();

		if ( isset( $tags ) ) {
			foreach ( $tags as $tag ) {
				$tag_name = $tag['Tag'];
				if ( isset( $tag_name ) ) {
					$tag_names[] = $this->import_string( $tag_name );
				}
			}
		}

		return array_unique( $tag_names );
	}

	function redirects_from_article( $article ) {
		$site_domain        = $this->get_site_option( 'domain' );
		$slug_history_items = $article->SlugHistoryItems->SlugHistoryItem;
		$redirects          = array();

		if ( isset( $slug_history_items ) ) {
			foreach ( $slug_history_items as $slug_history_item ) {
				$slug = $slug_history_item['ArticleHistoricalSlug'];

				if ( isset( $slug ) ) {
					$redirects[] = "http://$site_domain/" . $this->import_string( $slug );
				}
			}
		}

		return array_unique( $redirects );
	}

	function featured_image_from_article( $article ) {
		$path = $article['FeaturedImageFilepath'];

		if ( ! empty( $path ) ) {
			return $this->import_string( $path );
		} else {
			return null;
		}
	}

	function featured_audio_from_article( $article ) {
		$path = $article['FeaturedAudioFilepath'];

		if ( ! empty( $path ) ) {
			return $this->import_string( $path );
		} else {
			return null;
		}
	}

	function authors_from_article( $article ) {
		$authors      = $article->Authors->Author;
		$author_names = array();

		if ( isset( $authors ) ) {
			foreach ( $authors as $author ) {
				$author_name = $author['Author'];
				if ( isset( $author_name ) ) {
					$author_names[] = $this->import_string( $author_name );
				}
			}
		}

		return array_unique( $author_names );
	}

	function to_post( $article ) {
		$post = array(
			'post_author'           => 0,
			'post_type'             => 'post',
			'post_title'            => htmlentities( $article['Title'] ),
			'post_name'             => $article['Slug'],
			'post_excerpt'          => $article['ExcerptText'],
			'post_content'          => $article['ArticleText'],
			'post_content_filtered' => null,
			'post_status'           => 'publish',
			'comment_status'        => 'open',
			'ping_status'           => 'open',
			'to_ping'               => null,
			'pinged'                => null,
			'post_date'             => $article['UTCStartDateTime'],
			'post_date_gmt'         => $article['UTCStartDateTime'],
			'post_modified'         => $article['LastModifiedUTCDateTime'],
			'post_modified_gmt'     => $article['LastModifiedUTCDateTime'],
			'post_parent'           => 0,
			'menu_order'            => 0,
			'post_mime_type'        => null,
			'comment_count'         => 0,
		);

		return $post;
	}

}

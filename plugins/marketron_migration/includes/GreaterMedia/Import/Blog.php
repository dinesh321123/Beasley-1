<?php

namespace GreaterMedia\Import;

class Blog extends BaseImporter {

	function get_tool_name() {
		return 'blog';
	}

	function import_source( $source ) {
		$blogs = $this->blogs_from_source( $source );

		foreach ( $blogs as $blog ) {
			$blog_id   = $this->import_string( $blog['BlogID'] );
			$blog_name = $this->import_string( $blog['BlogName'] );

			if ( $this->can_import_marketron_name( $blog_name, 'blog' ) ) {
				$this->import_blog( $blog, $blog_name, $blog_id );
			} else {
				\WP_CLI::log( '    Excluded Blog: ' . $blog_name );
			}
		}
	}

	function import_blog( $blog, $blog_name, $blog_id ) {
		$author  = $this->mapped_author_for_blog( $blog_name);
		$entries = $this->entries_from_blog( $blog );
		$total   = count( $entries );
		$msg     = "Importing $total entries from Blog($blog_name)";
		$msg     = str_pad( $msg, 60, ' ' );
		$notify  = new \WordPress\Utils\ProgressBar( $msg, $total );

		foreach ( $entries as $entry ) {
			$this->import_blog_entry( $entry, $blog_id, $blog_name, $author );
			$notify->tick();
		}

		$notify->finish();
	}

	function import_blog_entry( $blog_entry, $blog_id, $blog_name, $author ) {
		$post                 = $this->post_from_blog_entry( $blog_entry );
		$post['post_authors'] = array( $author );
		$post['categories']   = $this->mapped_categories_for_blog( $blog_name );

		if ( $this->has_audio( $blog_entry ) ) {
			$post['episode_name']    = $post['post_title'];

			$episode_podcast = $this->mapped_podcast_for_blog( $blog_name );

			// Either - mapping
			if ( ! empty( $episode_podcast ) ) {
				$post['episode_podcast'] = $episode_podcast;
				$post['episode_file']    = $post['featured_audio'];

				$entity = $this->get_entity( 'podcast_episode' );
			} else {
				//error_log( "Empty Podcast for: $blog_name" );
				$entity = $this->get_entity( 'blog' );
			}
		} else {
			$post['shows'] = array( $this->mapped_show_for_blog( $blog_name ) );
			$entity       = $this->get_entity( 'blog' );
		}

		$entity->add( $post );
	}

	function post_from_blog_entry( $blog_entry ) {
		$post_title = $this->import_string( $blog_entry['EntryTitle'] );
		$post_title = htmlentities( ucwords( $post_title ) );

		$post_status    = 'publish';
		$tags           = $this->tags_from_blog_entry( $blog_entry );
		$created_on     = $this->import_string( $blog_entry['DateCreated'] );
		$post_content   = $this->content_from_blog_entry( $blog_entry );
		$post_body      = $post_content['body'];
		$featured_image = $this->featured_image_from_blog_entry( $blog_entry );
		$featured_audio = $this->featured_audio_from_blog_entry( $blog_entry );
		$entry_url      = $this->import_string( $blog_entry['BlogEntryURL'] );

		if ( isset( $blog_entry['DateModified'] ) ) {
			$modified_on = $this->import_string( $blog_entry['DateModified'] );
		} else {
			$modified_on = $created_on;
		}

		$post = array(
			'post_title'   => $post_title,
			'post_status'  => $post_status,
			'post_content' => $post_body,
			'created_on'   => $created_on,
			'modified_on'  => $modified_on,
			'marketron_id' => $this->import_string( $blog_entry['BlogEntryID'] ),

			'tags' => $tags,
		);

		if ( ! is_null( $featured_image ) ) {
			$post['featured_image'] = $featured_image;
		}

		if ( ! is_null( $featured_audio ) ) {
			$post['featured_audio'] = $featured_audio;
		} else {
			$post['post_format'] = $post_content['post_format'];
		}

		if ( ! is_null( $entry_url ) ) {
			$post['redirects'] = array(
				array( 'url' => $entry_url ),
			);
		}

		$this->replace_post_format_in_title( $post );

		return $post;
	}

	function replace_post_format_in_title( &$post ) {
		$title = $post['post_title'];
		$post_format = null;

		if ( strpos( $title, '[AUDIO]' ) !== false ) {
			$title = str_replace( '[AUDIO]', '', $title );
			$post_format = 'audio';
		} else if ( strpos( $title, '[VIDEO]' ) !== false ) {
			$title = str_replace( '[VIDEO]', '', $title );
			$post_format = 'video';
		} else if ( strpos( $title, '[LINK]' ) !== false ) {
			$title = str_replace( '[LINK]', '', $title );
			$post_format = 'link';
		} else if ( strpos( $title, '[GALLERY]' ) !== false ) {
			$title = str_replace( '[GALLERY]', '', $title );
			$post_format = 'gallery';
		}

		$title = trim( $title );
		$title = ltrim( $title, '-' );
		$title = trim( $title );

		if ( empty( $post['post_format'] ) ) {
			$post['post_format'] = $post_format;
		} else if ( $post['post_format'] !== $post_format ) {
			$post['post_format'] = $post_format;
		}

		$post['post_title'] = $title;
	}

	function featured_image_from_blog_entry( $blog_entry ) {
		if ( isset( $blog_entry->BlogEntryImage ) ) {
			$image = $blog_entry->BlogEntryImage;

			if ( isset( $image['MainImageSrc'] ) ) {
				return $this->import_string( $image['MainImageSrc'] );
			} else {
				return null;
			}
		} else {
			return null;
		}
	}

	function featured_audio_from_blog_entry( $blog_entry ) {
		if ( isset( $blog_entry->BlogEntryAudio ) ) {
			$audio = $blog_entry->BlogEntryAudio;

			if ( ! empty( $audio['AudioSrc'] ) ) {
				return $this->import_string( $audio['AudioSrc'] );
			} else {
				return null;
			}
		} else {
			return null;
		}
	}

	function content_from_blog_entry( $blog_entry ) {
		$content     = $blog_entry->BlogEntryText;
		$post_format = 'standard';

		if ( isset( $content ) ) {
			$content = $this->import_string( $content );
		} else {
			$content = '';
		}

		if ( ! empty( $article['PrimaryMediaReference'] ) ) {
			$primary_media_ref = $this->import_string( $article['PrimaryMediaReference'] );

			if ( strpos($primary_media_ref, 'youtube.com' ) !== false ) {
				$content     = '[embed]' . $primary_media_ref . '[/embed]'. '<br/>' . $content;
				$post_format = 'video';
			} else if ( strpos( $primary_media_ref, 'youtu.be' ) !== false ) {
				$primary_media_ref = str_replace( 'youtu.be/', 'youtube.com/watch?v=', $primary_media_ref );
				$content     = '[embed]' . $primary_media_ref . '[/embed]' . '<br/>' . $content;
				$post_format = 'video';
			}

		} else if ( strpos( $content, 'data-youtube-id' ) !== false ){
			$content = preg_replace(
				'#<div.*data-youtube-id="(.*)">.*</div>#',
				'[embed]http://www.youtube.com/watch?v=${1}[/embed]',
				$content, -1, $videos
			);

			if ( $post_format !== 'video' && $videos > 0 ) {
				$post_format = 'video';
			}
		} else if ( strpos( $content, 'youtube.com/embed' ) !== false ) {
			$content = preg_replace(
				'#<iframe.*src="https?://www.youtube.com/embed/([^"]*)".*</iframe>#',
				'[embed]https://www.youtube.com/watch?v=${1}[/embed]',
				$content, -1, $videos
			);

			if ( $post_format !== 'video' && $videos > 0 ) {
				$post_format = 'video';
			}
		} else if ( strpos( $content, 'player.vimeo.com' ) !== false ) {
			$content = preg_replace(
				'#<iframe.*src="([^"]*)".*</iframe>#',
				'[embed]${1}[/embed]',
				$content, -1, $videos
			);

			if ( $post_format !== 'video' && $videos > 0 ) {
				$post_format = 'video';
			}
		}
		/* <iframe src="https://www.youtube.com/embed/TPTe-Z3hfNc" frameborder="0" width="560" height="315"></iframe> */


		return array(
			'body'        => $content,
			'post_format' => $post_format,
		);
	}

	function tags_from_blog_entry( $blog_entry ) {
		if ( isset( $blog_entry['Tags'] ) ) {
			$tags = $this->import_string( $blog_entry['Tags'] );
			$tags = explode( ' ', $tags );
		} else {
			$tags = array();
		}

		return $tags;
	}

	function has_audio( $blog_entry ) {
		if ( isset( $blog_entry->BlogEntryAudio ) ) {
			return ! empty( $blog_entry->BlogEntryAudio['AudioSrc'] );
		} else {
			return false;
		}
	}

	function blogs_from_source( $source ) {
		return $source->Blog;
	}

	function mapped_author_for_blog( $blog_name ) {
		$mapping = $this->get_mapping_for_blog( $blog_name );
		if ( ! is_null( $mapping ) ) {
			return $mapping->wordpress_author_name;
		} else {
			return null;
		}
	}

	function mapped_podcast_for_blog( $blog_name ) {
		$mapping = $this->get_mapping_for_blog( $blog_name );
		if ( ! is_null( $mapping ) ) {
			return $mapping->wordpress_podcast_name;
		} else {
			//\WP_CLI::warning( "No podcast mapping for blog: $blog_name" );
			return null;
		}
	}

	function mapped_categories_for_blog( $blog_name ) {
		$mapping = $this->get_mapping_for_blog( $blog_name );
		if ( ! is_null( $mapping ) ) {
			return array( $mapping->wordpress_category );
		} else {
			return array();
		}
	}

	function mapped_show_for_blog( $blog_name ) {
		$mapping = $this->get_mapping_for_blog( $blog_name );
		if ( ! is_null( $mapping ) ) {
			$show = $mapping->wordpress_show_name;

			if ( ! is_null( $show ) ) {
				return $show;
			} else {
				\WP_CLI::error( 'No show for blog: ' . $blog_name );
			}
		} else {
			\WP_CLI::error( 'No show for blog: ' . $blog_name );
			return null;
		}
	}

	function get_mapping_for_blog( $blog_name ) {
		$mapping = $this->get_mappings()->get_mapping_by_name( $blog_name, 'blog' );
		return $mapping;
	}

	function authors_from_blog( $blog ) {
		$author_elements = $blog->BlogAuthor;
		$authors         = array();

		foreach ( $author_elements as $author_element ) {
			$author_name = $this->import_string( $author_element['AuthorName'] );
			$authors[]   = $author_name;
		}

		return $authors;
	}

	function entries_from_blog( $blog ) {
		return $blog->BlogEntries->BlogEntry;
	}


}

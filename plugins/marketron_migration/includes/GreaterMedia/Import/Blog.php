<?php

namespace GreaterMedia\Import;

class Blog extends BaseImporter {

	function get_tool_name() {
		return 'blog';
	}

	function get_mappings() {
		return $this->container->mappings;
	}

	function can_import( $blog_id ) {
		return $this->get_mappings()->can_import( $blog_id );
	}

	function import_source( $source ) {
		$blogs = $this->blogs_from_source( $source );

		foreach ( $blogs as $blog ) {
			$blog_id   = $this->import_string( $blog['BlogID'] );
			$blog_name = $this->import_string( $blog['BlogName'] );

			if ( $this->can_import( $blog_id ) ) {
				$this->import_blog( $blog, $blog_name, $blog_id );
			}
		}
	}

	function import_blog( $blog, $blog_name, $blog_id ) {
		$author  = $this->mapped_author_for_blog( $blog_id );
		$entries = $this->entries_from_blog( $blog );
		$total   = count( $entries );
		$msg     = "Importing $total entries from Blog($blog_name)";
		$msg     = str_pad( $msg, 60, ' ' );
		$notify  = new \cli\progress\Bar( $msg, $total );

		foreach ( $entries as $entry ) {
			$this->import_blog_entry( $entry, $blog_id, $author );
			$notify->tick();
		}

		$notify->finish();
	}

	function import_blog_entry( $blog_entry, $blog_id, $author ) {
		$post                 = $this->post_from_blog_entry( $blog_entry );
		$post['post_authors'] = array( $author );
		$post['categories']   = $this->mapped_categories_for_blog( $blog_id );

		if ( $this->has_audio( $blog_entry ) ) {
			$post['episode_name']    = $post['post_title'];
			$post['episode_podcast'] = $this->mapped_podcast_for_blog( $blog_id );
			$post['episode_file']    = $post['featured_audio'];

			$entity = $this->get_entity( 'podcast_episode' );
		} else {
			$post['show'] = $this->mapped_show_for_blog( $blog_id );
			$entity       = $this->get_entity( 'blog' );
		}

		$entity->add( $post );
	}

	function post_from_blog_entry( $blog_entry ) {
		$post_title = $this->import_string( $blog_entry['EntryTitle'] );
		$post_title = ucwords( $post_title );

		$post_status    = 'publish';
		$tags           = $this->tags_from_blog_entry( $blog_entry );
		$created_on     = $this->import_string( $blog_entry['DateCreated'] );
		$post_content   = $this->content_from_blog_entry( $blog_entry );
		$post_content   = $post_content['body'];
		$featured_image = $this->featured_image_from_blog_entry( $blog_entry );
		$featured_audio = $this->featured_audio_from_blog_entry( $blog_entry );

		if ( isset( $blog_entry['DateModified'] ) ) {
			$modified_on = $this->import_string( $blog_entry['DateModified'] );
		} else {
			$modified_on = $created_on;
		}

		$post = array(
			'post_title' => $post_title,
			'post_status' => $post_status,
			'post_content' => $post_content,
			'created_on' => $created_on,
			'modified_on' => $modified_on,

			'tags' => $tags,
		);

		if ( ! is_null( $featured_image ) ) {
			$post['featured_image'] = $featured_image;
		}

		if ( ! is_null( $featured_audio ) ) {
			$post['featured_audio'] = $featured_audio;
		}

		return $post;
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

	function mapped_author_for_blog( $blog_id ) {
		$mapping = $this->get_mappings()->get_mapping( $blog_id );
		return $mapping->wordpress_author_name;
	}

	function mapped_podcast_for_blog( $blog_id ) {
		$mapping = $this->get_mappings()->get_mapping( $blog_id );
		return $mapping->wordpress_podcast_name;
	}

	function mapped_categories_for_blog( $blog_id ) {
		$mapping = $this->get_mappings()->get_mapping( $blog_id );
		return array( $mapping->wordpress_category );
	}

	function mapped_show_for_blog( $blog_id ) {
		$mapping = $this->get_mappings()->get_mapping( $blog_id );
		return $mapping->wordpress_show_name;
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

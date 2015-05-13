<?php

namespace GreaterMedia\Import;

class Feed extends BaseImporter {

	function get_tool_name() {
		return 'feed';
	}

	function import_source( $source ) {
		$tool             = $this->get_tool();
		$tool_name        = $tool->get_name();
		$posts            = $this->get_entity( 'blog' );
		$podcast_episodes = $this->get_entity( 'podcast_episode' );
		$articles         = $this->articles_from_source( $source );
		$total            = count( $articles );
		$locator          = $this->container->asset_locator;
		$notify           = new \WordPress\Utils\ProgressBar( "Importing $total items from $tool_name", $total );

		$podcast_count = 0;
		$blog_post_count = 0;
		$blog_with_shows = 0;
		$max = 3000;
		$min = 9500;
		$index = 0;

		foreach ( $articles as $article ) {
			if ( ! $this->container->mappings->can_import_marketron_name( (string) $article->Feeds->Feed['Feed'] ) ) {
				continue;
			}

			$post = $this->post_from_article( $article );

			if ( ! empty( $post['feed_names'] ) && count( $post['feed_names'] ) > 1 ) {
				foreach ( $post['feed_names'] as $feed_name ) {
					$new_post = $post;
					$new_categories = array( $feed_name );
					$new_post['shows'] = $this->show_from_categories( $new_categories );

					// KLUDGE: Duplication
					if ( ! empty( $new_post['featured_audio'] ) && ! empty( $new_post['shows'] ) && ! empty( $this->mapped_podcast_for_show( $new_post['shows'][0] ) ) ) {
						$new_post['episode_name']    = $new_post['post_title'];
						$new_post['episode_podcast'] = $this->mapped_podcast_for_show( $new_post['shows'][0] );
						$new_post['episode_file']    = $new_post['featured_audio'];
					//error_log( $new_post['episode_podcast'] . ' x ' . implode( ', ', $post['feed_names'] ) );

						$podcast_episodes->add( $new_post );
						$podcast_count++;
						//error_log( 'Found Show Podcast Episode: ' . $new_post['show'] );
					} else {
						$posts->add( $new_post );
						$blog_post_count++;
					}
				}

			} else {
				if ( ! empty( $post['featured_audio'] ) && ! empty( $post['shows'] ) && ! empty( $this->mapped_podcast_for_show( $post['shows'][0] ) ) ) {
					$post['episode_name']    = $post['post_title'];
					$post['episode_podcast'] = $this->mapped_podcast_for_show( $post['shows'][0] );
					$post['episode_file']    = $post['featured_audio'];

					$podcast_episodes->add( $post );
					$podcast_count++;
					//error_log( 'Found Show Podcast Episode: ' . $post['show'] );
				} else {
					$posts->add( $post );
					$blog_post_count++;
				}
			}


			$notify->tick();
		}

		//\WP_CLI::log( 'Total Lib Syn Replacements: ' . \WordPress\Utils\InlineLibSynReplacer::$replacements );

		$notify->finish();
	}

	function post_from_article( $article ) {
		$post           = array();
		$categories     = $this->categories_from_article( $article );
		$tags           = $this->tags_from_article( $article );
		$collections    = $this->collections_from_article( $article );

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
		$shows          = $this->show_from_categories( $categories );
		$feed_names     = $this->feed_names_from_article( $article );

		//\WP_CLI::log( "Importing Post: $post_title" );

		if ( ! is_null( $featured_audio ) ) {
			$post_format = 'audio';
		} else if ( ! empty( $content_parts['featured_audio'] ) ) {
			$featured_audio = $content_parts['featured_audio'];
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

			'tags'        => $tags,
			'categories'  => $categories,
			'collections' => $collections,
			'post_format' => $post_format,
			'redirects'   => $redirects,
			//'feed_names'  => $feed_names,
		);

		if ( ! is_null( $featured_image ) ) {
			$post['featured_image'] = $featured_image;
		}

		if ( ! is_null( $featured_audio ) ) {
			$post['featured_audio'] = $featured_audio;
		}

		if ( ! empty( $shows ) ) {
			$post['shows'] = $shows;
		} else {
			$post['shows'] = array( $this->show_from_tags( $tags ) );

			if ( empty( $post['shows'] ) ) {
				$post['shows'] = array( $this->show_from_title( $post['post_title'] ) );
			}
		}

		if ( ! empty( $post['shows'] ) ) {
			//\WP_CLI::log( 'Found Show: ' . $post['show'] );
		}

		return $post;
	}

	function show_from_title( $name ) {
		$mappings = $this->container->mappings;
		$authors  = $mappings->get_matched_authors( $name );

		if ( ! empty( $authors ) ) {
			$show = $mappings->get_show_from_author_names( $authors );
		} else {
			$show = null;
		}

		return null;
	}

	function show_from_tags( &$tags ) {
		$show = $this->show_from_categories( $tags );
		if ( empty( $show ) ) {
			$show = $this->container->mappings->get_show_from_author_names( $tags );
		}
		return $show;
	}



	function articles_from_source( $source ) {
		return $source->Articles->Article;
	}

	function content_from_article( $article ) {
		$content        = $article['ArticleText'];
		$post_format    = 'standard';
		$featured_audio = null;

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
			'[embed]http://www.youtube.com/watch?v=${1}[/embed]',
			$content, -1, $videos
		);

		if ( $post_format !== 'video' && $videos > 0 ) {
			$post_format = 'video';
		}

		$inline_image_replacer = $this->container->inline_libsyn_replacer;
		$libsyn_embeds = $inline_image_replacer->find( $content );

		if ( count( $libsyn_embeds ) > 0 ) {
			//foreach ( $libsyn_embeds as $embed ) {
				//$content = str_replace( $embed['tag'], '', $content );
			//}

			//$featured_audio = $libsyn_embeds[0]['file'];
			$featured_audio = null;
			$post_format = 'standard';
		}

		return array(
			'body'        => $content,
			'post_format' => $post_format,
			'featured_audio' => $featured_audio,
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

		$excerpt = wp_strip_all_tags( $excerpt );

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

		$feed_names = $this->feed_names_from_article( $article );
		$category_names = array_merge( $category_names, $feed_names );

		$mappings = $this->container->mappings;

		if ( ! empty( $feed_names ) ) {
			foreach ( $feed_names as $feed_name ) {
				$category_name = $mappings->get_category_from_marketron_name( $feed_name );
				if ( ! empty( $category_name ) ) {
					$category_names[] = $category_name;
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

	function collections_from_article( $article ) {
		$mappings = $this->container->mappings;

		if ( $mappings->show_is_collection ) {
			$feeds      = $this->feeds_from_article( $article );

			if ( ! empty( $feeds ) ) {
				$feed_name  = $this->import_string( $feeds[0]['Feed'] );
				//error_log( 'Collection: ' . $feed_name );
				return array( $feed_name );
			} else {
				return array();
			}
		} else {
			return array();
		}
	}

	function feeds_from_article( $article ) {
		return $article->Feeds->Feed;
	}

	function feed_names_from_article( $article ) {
		$feeds      = $this->feeds_from_article( $article );
		$feed_names = array();

		if ( ! empty( $feeds ) ) {
			foreach ( $feeds as $feed ) {
				$feed_name = $this->import_string( $feed['Feed'] );
				$feed_names[] = $feed_name;
			}
		}

		$feed_names = array_merge(
			$feed_names, $this->feed_category_names_from_article( $article )
		);

		//error_log( 'Found Feed Names: ' . print_r( $feed_names, true ) );
		return $feed_names;
	}

	function feed_names_from_article_only( $article ) {
		$feeds      = $this->feeds_from_article( $article );
		$feed_names = array();

		if ( ! empty( $feeds ) ) {
			foreach ( $feeds as $feed ) {
				$feed_name = $this->import_string( $feed['Feed'] );
				$feed_names[] = $feed_name;
			}
		}

		return array_unique( $feed_names );
	}

	function feed_categories_from_article( $article ) {
		if ( isset( $article->Feeds->Feed->FeedCategories->FeedCategory ) ) {
			return $article->Feeds->Feed->FeedCategories->FeedCategory;
		} else {
			return array();
		}
	}

	function feed_category_names_from_article( $article ) {
		$categories = $this->feed_categories_from_article( $article );
		$category_names = array();

		if ( ! empty( $categories) ) {
			foreach( $categories as $category ) {
				$category_names[] = $this->import_string( $category['Category'] );
			}
		}

		return $category_names;
	}

	function show_from_categories( &$categories ) {
		return $this->container->mappings->get_show_from_categories( $categories );
	}

	function redirects_from_article( $article ) {
		$site_domain        = $this->get_site_option( 'domain' );
		$slug_history_items = $article->SlugHistoryItems->SlugHistoryItem;
		$redirects          = array();

		if ( isset( $slug_history_items ) ) {
			foreach ( $slug_history_items as $slug_history_item ) {
				$slug = $slug_history_item['ArticleHistoricalSlug'];

				if ( isset( $slug ) ) {
					$redirects[] = array(
						'url' => "http://$site_domain/" . $this->import_string( $slug )
					);
				}
			}
		}

		return $redirects;
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

	function mapped_podcast_for_show( $show ) {
		$mappings = $this->container->mappings;
		return $mappings->get_podcast_for_show( $show );
	}

}

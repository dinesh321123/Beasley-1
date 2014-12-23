<?php

/**
 * This script is adopted from Darin Kotter's xml-import.php
 * Additional content types added and wrapped into plugin
 */

// Example command
// wp gmedia-migration import /vagrant/marketron/Feeds.xml --type=feed --site=wmmr --url=gmedia.dev

/**
 * Import content from XML files.
 *
 */
class GMedia_Migration extends WP_CLI_Command {

	/**
	 * Allowed mime types we check against.
	 *
	 * @var array
	 */
	public $allowed_mime_types = array(
		'application/xml',
		'text/xml',
		'application/xhtml+xml',
		'text/plain',
	);

	/**
	 * Only channels we want to import.
	 *
	 * @var array
	 */
	public $channels = array(
		'Best of Indy 2010',
		'Best of Indy 2011',
		'Bios',
		'50 Best Restaurants',
	);

	/**
	 * Photo sizes we try to import.
	 *
	 * @var array
	 */
	public $photo_sizes = array(
		'5'  => '__1600W',
		'4'  => '__900W',
		'19' => '__500x500',
		'17' => '__400W',
		'3'  => '__372W',
		'16' => '__300W',
		'6'  => '__240X240',
		'15' => '__200W',
		'13' => '__160W',
		'14' => '__100W',
		'2'  => '__100X100',
		'1'  => '__60X60',
		'18' => '__40X40',
	);

	/**
	 * Current site URL to use.
	 *
	 * @var string
	 */
	public $site_url;


	public $skip = 0;

	public $config_file = '';
	public $type = '';

	/**
	 * Reset the DB
	 * -e 'show databases;'
	 * @synopsis [--wipe-images]
	 */
	public function reset( $args = array(), $assoc_args = array() ) {
		global $wpdb;

		//$command = "mysql -u root -proot -h 127.0.0.1 -D gmedia < ";
		$command = "mysql -u root -proot -h gmedia.dev -D gmedia < ";

		if ( file_exists( '/srv/www/gmedia/reset-wordpress.sql' ) ) {
			$output = shell_exec( $command . '/srv/www/gmedia/reset-wordpress.sql' );
			if ( null === $output ) {
				WP_CLI::success( 'Database reset' );
			} else {
				WP_CLI::error( 'Error occured: ' . $output );
			}
		}


		$output = shell_exec( $command . '/srv/www/gmedia/reset-wordpress.sql' );
		if ( isset( $assoc_args['wipe-images'] ) ) {
			shell_exec( 'rm -rf /srv/www/gmedia/wp-content/uploads/*' );
		}
	}

	/**
	 * Handle the import of an xml file.
	 *
	 * @synopsis <file> --type=<content-type> --site=<site> [--force] [--skip] [--config_file]
	 */
	public function import( $args = array(), $assoc_args = array() ) {
		if ( isset( $assoc_args['type'] ) ) {
			$type = $assoc_args['type'];
			$this->type = $type;
		} else {
			WP_CLI::error( 'Please specify the content type.' );
		}

		if( isset( $assoc_args['skip'] ) ) {
			$this->skip = absint( $assoc_args['skip'] );
		}

		if( isset( $assoc_args['config_file'] ) ) {
			$this->config_file = $assoc_args['config_file'];
			if( !file_exists( $this->config_file ) ) {
				WP_CLI::error( "$this->config_file doesn't exist\n" );
			}
		} elseif( $type == 'blog' || $type == 'blogs' ) {
			WP_CLI::error( "Type is set to blog. Please provide config file!" );
		}

		if ( isset( $assoc_args['site'] ) ) {
			$this->site_url = $assoc_args['site'];
			/*switch ( $site ) {
				case 'wmmr':
					$this->site_url = 'wmmr';
					break;
				case 'wmgk':
					$this->site_url = 'wmgk';
					break;
				case 'indianapolis':
				case 'indy':
					$this->site_url = 'indianapolismonthly';
					break;
				case 'la':
				case 'los angeles':
					$this->site_url = 'lamag';
					break;
				case 'oc':
				case 'orange coast':
					$this->site_url = 'orangecoast';
					break;
				case 'wedding':
					$this->site_url = 'cincinnatiweddingmagazine';
					break;
			}*/
		} else {
			WP_CLI::error( 'Please specify the site.' );
		}

		if( isset( $assoc_args['config_file'] ) ) {
			$this->config_file = $assoc_args['config_file'];
			if( !file_exists( $this->config_file ) ) {
				WP_CLI::error( "$this->config_file doesn't exist\n" );
			}
		} elseif( $type == 'blog' || $type == 'blogs' ) {
			WP_CLI::error( "Type is set to blog. Please provide config file!" );
		}
		$file  = $args[0];
		$force = isset( $assoc_args['force'] );

		if ( false !== stripos( $file, '.xml' ) ) {
			$finfo          = finfo_open( FILEINFO_MIME_TYPE );
			$file_mime_type = finfo_file( $finfo, $file );

			if ( in_array( $file_mime_type, $this->allowed_mime_types ) ) {
				$this->load_xml( $file, $type, $force );
			} else {
				WP_CLI::error( 'Please import a valid file type.' );
			}

			finfo_close( $finfo );
		} else {
			WP_CLI::error( 'Please import a valid file type.' );
		}

		WP_CLI::success( "Imported $file" );
	}

	/**
	 * Process the xml file.
	 *
	 * @var string $file  Location of file.
	 * @var string $type  Content type to import.
	 * @var bool   $force Whether to force import or not.
	 * @return void
	 */
	private function load_xml( $file, $type, $force ) {
		$xml = simplexml_load_file( $file );

		if ( $xml && is_object( $xml ) ) {
			WP_CLI::log( "Starting import of $file" );

			switch ( $type ) {
				case 'feed':
				case 'feeds':
					$articles = $xml->Articles;
					$this->process_feeds( $articles, $force );
					break;
				case 'blog':
				case 'blogs':
					$blogs = $xml->Blog;
					$this->process_blogs( $blogs, $force );
					break;
				case 'channel':
				case 'channels':
				case 'factory_channel':
				case 'factory_channels':
					$channels = $xml;
					$this->process_channels( $channels, $force );
					break;
				case 'event':
				case 'events':
					$events = $xml;
					$this->process_events( $events, $force );
					break;
				case 'photoalbum':
				case 'photoalbums':
					$galleries = $xml;
					$this->process_photoalbums( $galleries, $force );
					break;
				case 'showcase':
				case 'showcases':
					$showcases = $xml;
					$this->process_showcases( $showcases, $force );
					break;
				case 'video':
				case 'videos':
					$videos = $xml;
					$this->process_videos( $videos, $force );
					break;
				case 'venue':
				case 'venues':
					$venues = $xml;
					$this->process_venues( $venues, $force );
					break;
				case 'concert':
				case 'concerts':
					$calendars = $xml;
					$this->process_concerts( $calendars, $force );
					break;
				case 'podcast':
				case 'podcasts':
					$podcasts = $xml;
					$this->process_podcasts( $podcasts, $force );
					break;
				case 'survey':
				case 'surveys':
					$surveys = $xml;
					$this->process_surveys( $surveys, $force );
					break;
				case 'contest':
				case 'contests':
					$contests = $xml;
					$this->process_contests( $contests, $force );
					break;
				case 'schedule':
				case 'schedules':
					$schedules = $xml;
					$this->process_schedules( $schedules, $force );
					break;
				default:
					WP_CLI::error( "Type is set!" );
					break;
			}
		} else {
			WP_CLI::error( 'XML file not valid.' );
		}

		return;
	}

	/**
	 * Import articles from the XML file.
	 *
	 * @var SimpleXMLElement $articles Articles from file.
	 * @var bool             $force    Whether to force import or not.
	 * @return void
	 */
	private function process_feeds( $articles, $force ) {
		global $wpdb;

		$taxonomy_mapping = array();
		if( $this->config_file != '' ) {
			$taxonomy_mapping = $this->parse_taxonomy_mapping();
		}

		$total  = count( $articles->Article );
		$notify = new \cli\progress\Bar( "Importing $total articles", $total );

		$count = 0;
		foreach ( $articles->Article as $article ) {
			$user_id = get_current_user_id();

			if ( isset( $article->Authors->Author ) ) {
				foreach ( $article->Authors->Author as $author ) {
					if ( isset( $author['Author'] ) && '' !== trim( (string) $author['Author'] ) ) {
						$author_name = (string) $author['Author'];
						$exists = $this->check_if_user_exists( $author_name, 'name' );
					} else if ( isset( $author['EmailAddress'] ) && '' !== trim( (string) $author['EmailAddress'] ) ) {
						$author_email = (string) $author['EmailAddress'];
						$exists = $this->check_if_user_exists( $author_email );
					} else {
						$exists = false;
					}
					if( isset( $author['ImageFilepath'] ) && $author['ImageFilepath'] != '' ) {
						if ( $exists ) {
							$user_id = $exists;
							if ( ! get_user_meta( $user_id, 'simple_local_avatar' ) ) {
								$image_id = $this->import_author_images( $author['ImageFilepath'] );
								if ( $image_id ) {
									$meta_value = array();
									$meta_value['media_id'] = $image_id;
									$url = wp_get_attachment_url( $image_id );
									$meta_value['full'] = $url;
									update_user_meta( $user_id, 'simple_local_avatar', $meta_value );
								}
							}
						} else {
							$user_id = $this->create_user( $author );
							if ( ! get_user_meta( $user_id, 'simple_local_avatar' ) ) {
								$image_id = $this->import_author_images( $author['ImageFilepath'] );
								if ( $image_id ) {
									$meta_value = array();
									$meta_value['media_id'] = $image_id;
									$url = wp_get_attachment_url( $image_id );
									$meta_value['full'] = $url;
									update_user_meta( $user_id, 'simple_local_avatar', $meta_value );
								}
							}
						}
					}
				}
			}

			$article_hash = trim( (string) $article['Title'] ) . (string) $article['UTCStartDateTime'];
			$article_hash = md5( $article_hash );

			// grab the existing post ID (if it exists).
			$wp_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'gmedia_import_id' AND meta_value = '" . $article_hash . "'" );

			// If we're not forcing import, skip existing posts.
			if ( ! $force && $wp_id ) {
				continue;
			}

			// counter to clear the cache
			$count++;
			if( $count == 100 ) {
				if( class_exists('MTM_Migration_Utils') ) {
					MTM_Migration_Utils::stop_the_insanity();
				}
				sleep(15);
				$count = 0;
			}
			$feed_article_title = strtolower( trim( (string) $article['Title'] ) );

			if( isset( $article['PrimaryMediaReference'] ) ) {
				$content =  trim( (string) $article['PrimaryMediaReference'] . '<br>' . (string) $article['ArticleText'] );
			} else {
				$content =  trim( (string) $article['ArticleText'] );
			}

			$post = array(
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'post_author'   => $user_id,
				'post_name'     => trim( (string) $article['Slug'] ),
				'post_title'    => ucwords( $feed_article_title ),
				'post_content'  => $content,
				'post_excerpt'  => trim( (string) $article['ExcerptText'] ),
				'post_date'     => (string) $article['UTCStartDateTime'],
				'post_date_gmt' => (string) $article['UTCStartDateTime'],
				'post_modified' => (string) $article['LastModifiedUTCDateTime'],
			);

			if ( $wp_id ) {
				$post['ID'] = $wp_id;
			}

			$wp_id = wp_insert_post( $post );

			update_post_meta( $wp_id, 'gmedia_import_id', $article_hash );

			if( isset($article['FeaturedAudioFilepath']) && $wp_id ) {
				$media_file_id = $this->import_music_files( $wp_id, $article['FeaturedAudioFilepath'] );
				$media_url  = wp_get_attachment_url( $media_file_id );
				$updated_post                 = array( 'ID' => $wp_id );
				$updated_post['post_content'] = $content . '<br/>' . '[audio mp3="' . $media_url . '"][/audio]' ;

				wp_update_post( $updated_post );

				update_post_meta( $wp_id, '_legacy_music_file', $media_url );
			}

			// Process Feed Taxonomy Term
			if ( isset( $article->Feeds->Feed->FeedCategories->FeedCategory ) ) {
				$feed_cats = array();
				foreach ( $article->Feeds->Feed->FeedCategories->FeedCategory as $feed_category ) {
					if( $feed_category['Category'] ) {
						$cat_info['name'] = $feed_category['Category'];
						$feed_id = $this->process_term( $cat_info , 'category', 'post' );

						if ( $feed_id ) {
							array_push( $feed_cats, $feed_id );
						}
					}
				}
				if( !empty( $feed_cats ) ) {
					wp_set_post_terms( $wp_id, $feed_cats, 'category', false );
				}
			}

			if ( isset( $article->Feeds->Feed['Feed'] ) ) {
				$marketron_term = trim( (string)  $article->Feeds->Feed['Feed'] );

				$new_term['name'] = $taxonomy_mapping[$marketron_term]['term'];
				$new_tax = $taxonomy_mapping[$marketron_term]['taxonomy'];

				$feed_id = $this->process_term( $new_term, $new_tax, 'post' );

				if ( $feed_id ) {
					wp_set_post_terms( $wp_id, array( $feed_id ), $new_tax, false );
				}
			}

			$old_url = $this->site_url;
			if ( isset( $article->Feeds->Feed->FeedSlugHistoryItems ) ) {
				foreach ( $article->Feeds->Feed->FeedSlugHistoryItems->FeedSlugHistoryItem as $SlugHistoryitem ) {
					$old_url = trailingslashit( $this->site_url )  . trim( (string) $SlugHistoryitem['FeedHistoricalSlug'] )
					           . '/' . trim( (string) $article['SlugDate'] ) . '/' . trim( (string) $article['Slug'] );
				}
				update_post_meta( $wp_id, '_legacy_FeedArticle_old_url', $old_url );
				CMM_Legacy_Redirects::add_redirect( $old_url, $wp_id );
			}

			// Process Tags
			if ( isset( $article->Tags->Tag ) ) {
				$tag_ids = array();
				foreach ( $article->Tags->Tag as $tag ) {
					$tag_details['name'] = $tag['Tag'];
					$tag_id = $this->process_term( $tag_details, 'post_tag', 'post');
					if( $tag_id ) {
						array_push( $tag_ids, $tag_id );
					}
				}
				if ( !empty( $tag_ids ) ) {
					wp_set_post_terms( $wp_id, $tag_ids, 'post_tag', false );
				}
			}

			// Post Meta
			if ( isset( $article['Subtitle'] ) ) {
				$subtitle = (string) $article['Subtitle'];
				update_post_meta( $wp_id, '_gmedia_subtitle', apply_filters( 'the_title', $subtitle ) );
			}

			if ( isset( $article['ArticleViews'] ) ) {
				$ArticleViews = (string) $article['ArticleViews'];
				update_post_meta( $wp_id, '_legacy_ArticleViews', $ArticleViews );
			}

			// Featured Image
			if ( isset( $article['FeaturedImageFilepath'] ) ) {
				$featured_image_attrs = array();
				$featured_image_path  = (string) $article['FeaturedImageFilepath'];

				if ( isset( $article['FeaturedImageCaption'] ) ) {
					$featured_image_attrs['post_excerpt'] = (string) $article['FeaturedImageCaption'];
				} else {
					$featured_image_attrs['post_excerpt'] = '';
				}

				if ( isset( $article['FeaturedImageAttribute'] ) ) {
					$featured_image_attrs['post_content'] = (string) $article['FeaturedImageAttribute'];
				} else {
					$featured_image_attrs['post_content'] = '';
				}

				$image = $this->import_featured_image( $featured_image_path, $wp_id, $featured_image_attrs );

				if ( ! $image ) {
					WP_CLI::log( "Error: Featured image not added!" );
				}
			} else {
				WP_CLI::log( "Error: No Featured Image Found!" );
			}

			/*// Comments
			if ( isset( $article->Comments ) ) {
				foreach ( $article->Comments->Comment as $comment ) {
					$comment_id = $this->add_comment( $comment, $wp_id, $force );

					if ( $comment_id ) {
						if ( isset( $comment['ParentCommentID'] ) ) {
							$parent_comment_id = (int) $comment['ParentCommentID'];
							$this->add_parent_comment( $comment_id, $parent_comment_id );
						}
					}
				}
			}*/
		}

		$notify->finish();
	}


	private function check_file( $file ) {
		$file_headers = @get_headers($file);

		if( $file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0] == 'HTTP/1.1 301 Moved Permanently' ) {
			$exists = false;
		}
		else {
			$exists = true;
		}

		return $exists;
	}

	private function import_music_files( $post_id, $filepath ) {

		$blog_id = get_current_blog_id();
		$upload_dir = get_site_url( $blog_id );

		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$id = '';
		$old_filename = '';

		$filename = str_replace( '\\', '/', $filepath );
		$filename = urldecode( $filename ); // for filenames with spaces
		$filename = str_replace( ' ', '%20', $filename );
		$filename = str_replace( '&amp;', '&', $filename );
		$filename = str_replace( '&mdash;', '—', $filename );

		$old_filename = $upload_dir . '/wp-content/uploads/' . $filename;

		if( !$this->check_file( $old_filename ) ) {
			if( strpos( $this->site_url,'wmgk' ) ) {
				$old_filename = 'http://media.wmgk.com/' . $filename;
			} else {
				$old_filename = trailingslashit( $this->site_url ) . $filename;
			}
		}

		$tmp = download_url( $old_filename );
		preg_match( '/[^\?]+\.(mp3|mp4|flv)/i', $filename, $matches );

		// make sure we have a match.  This won't be set for PDFs and .docs
		if ( $matches && isset( $matches[0] ) ) {
			$name = str_replace( '%20', ' ', basename( $matches[0] ) );
			$file_array['name'] = $name;
			$file_array['tmp_name'] = $tmp;

			// If error storing temporarily, unlink
			if ( is_wp_error( $tmp ) ) {
				@unlink( $file_array['tmp_name'] );
				$file_array['tmp_name'] = '';
			}

			// do the validation and storage stuff
			$id = media_handle_sideload( $file_array, 0 );

			// If error storing permanently, unlink
			if ( is_wp_error( $id ) ) {
				@unlink( $file_array['tmp_name'] );
				WP_CLI::log( "Error uploading music:". $id->get_error_message() );
				WP_CLI::log( "Filename: $old_filename" );
				$id = '';
			}

		} else {
			@unlink( $tmp );
			WP_CLI::log( "Error: ". $filename . " not added." );
		}

		return $id;

	}

	/**
	 * Check if a user exists based on their email.
	 *
	 * @var string $value User's information to check.
	 * @var string $type What type of information to check
	 * @return int|bool
	 */
	private function check_if_user_exists( $value, $type = 'email' ) {
		if ( 'email' === $type ) {
			if ( $user_id = email_exists( $value ) ) {
				return $user_id;
			} else {
				return false;
			}
		} elseif ( 'name' === $type ) {
			if ( $user_id = username_exists( sanitize_user( $value ) ) ) {
				return $user_id;
			} else {
				return false;
			}
		}

		return false;
	}

	private function import_author_images( $filepath ) {
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$id = '';
		$old_filename = '';

		$filename = str_replace( '\\', '/', $filepath );
		$filename = urldecode( $filename ); // for filenames with spaces
		$filename = str_replace( ' ', '%20', $filename );
		$filename = str_replace( '&amp;', '&', $filename );
		$filename = str_replace( '&mdash;', '—', $filename );

		if ( preg_match( '/^http/', $filename ) || preg_match( '/^www/', $filename ) ) {
			$old_filename = $filename;
		} else {
			$old_filename = trailingslashit( $this->site_url) . $filename;
		}

		$tmp = download_url( $old_filename );
		preg_match( '/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|Jpeg|JPEG|gif|GIF|png|PNG)/', $filename, $matches );

		// make sure we have a match.  This won't be set for PDFs and .docs
		if ( $matches && isset( $matches[0] ) ) {
			$name = str_replace( '%20', ' ', basename( $matches[0] ) );
			$file_array['name'] = $name;
			$file_array['tmp_name'] = $tmp;

			// If error storing temporarily, unlink
			if ( is_wp_error( $tmp ) ) {
				@unlink( $file_array['tmp_name'] );
				$file_array['tmp_name'] = '';
			}

			// do the validation and storage stuff
			$id = media_handle_sideload( $file_array, 0 );

			// If error storing permanently, unlink
			if ( is_wp_error( $id ) ) {
				@unlink( $file_array['tmp_name'] );
				WP_CLI::log( "Error: ". $id->get_error_message() );
				WP_CLI::log( "Filename: $old_filename" );
				$id = '';
			}
		} else {
			@unlink( $tmp );
			WP_CLI::log( "Error: ". $filename . " not added." );
		}

		return $id;
	}

	/**
	 * Create a new user.
	 *
	 * @var object $author Current author object.
	 * @var bool|string $email Current email.
	 * @return int
	 */
	private function create_user( $author, $email = false ) {
		if ( $email ) {
			$userdata = array(
				'user_login' => sanitize_user( $author ),
				'user_pass'  => wp_generate_password(),
				'user_email' => $author,
			);
		} else {
			$slug = '';
			$email = '';
			$description = '';
			$urls = array();
			$user_url = '';

			if ( isset( $author->AuthorURLs->AuthorURL ) ) {
				foreach( $author->AuthorURLs->AuthorURL as $url ) {
					$type = (string) $url['URLType'];

					if ( 'Other' === $type ) {
						$user_url = (string) $url['URL'];
					}

					$urls[$type] = (string) $url['URL'];
				}
			}

			if ( isset( $author['Slug'] ) && '' !== trim( (string) $author['Slug'] ) ) {
				$slug = (string) $author['Slug'];
			}
			if ( isset( $author['EmailAddress'] ) && '' !== trim( (string) $author['EmailAddress'] ) ) {
				$email = (string) $author['EmailAddress'];
			}
			if ( isset( $author['description'] ) && '' !== trim( (string) $author['description'] ) ) {
				$description = (string) $author['description'];
			}

			$userdata = array(
				'user_login'    => sanitize_user( (string) $author['Author'] ),
				'user_pass'     => wp_generate_password(),
				'user_nicename' => $slug,
				'nickname'      => $author['Author'],
				'display_name'  => $author['Author'],
				//'user_email'    => $email,
				'description'   => $description,
				'user_url'      => $user_url
			);

			$image = (string) $author['ImageFilepath'];
		}

		$user_id = wp_insert_user( $userdata );

		if ( ! is_wp_error( $user_id ) ) {
			if ( isset( $urls['Twitter'] ) ) {
				update_user_meta( $user_id, 'twitter', esc_url_raw( $urls['Twitter'] ) );
			}

			if ( isset( $urls['Facebook'] ) ) {
				update_user_meta( $user_id, 'facebook', esc_url_raw( $urls['Facebook'] ) );
			}
			return $user_id;
		} else {
			return 1;
		}
	}

	/**
	 * Download all images found in post_content and update those image paths.
	 *
	 * @param string $content Post content.
	 * @param int $post_id ID of post to update.
	 * @return string
	 */
	private function import_media( $content, $post_id = 0 ) {
		preg_match_all( '#<img(.*?)src="(.*?)"(.*?)>#', $content, $matches, PREG_SET_ORDER );

		if ( is_array( $matches ) ) {
			foreach ( $matches as $match ) {
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );

				$old_filename = '';
				$filename = $match[2];
				$img = $match[0];
				$filename = urldecode( $filename ); // for filenames with spaces
				$filename = str_replace( ' ', '%20', $filename );
				$filename = str_replace( '&amp;', '&', $filename );
				$filename = str_replace( '&mdash;', '—', $filename );

				if ( preg_match( '/^http/', $filename ) || preg_match( '/^www/', $filename ) ) {
					$old_filename = $filename;
				} else {
					$old_filename = trailingslashit( $this->site_url.com) . $filename;
				}

				$tmp = download_url( $old_filename );
				preg_match( '/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|Jpeg|JPEG|gif|GIF|png|PNG)/', $filename, $matches );

				if ( isset( $matches[0] ) ) {
					$name = str_replace( '%20', ' ', basename( $matches[0] ) );
					$file_array['name'] = $name;
				} else {
					$file_array['name'] = $tmp;
				}
				$file_array['tmp_name'] = $tmp;

				// If error storing temporarily, unlink
				if ( is_wp_error( $tmp ) ) {
					@unlink( $file_array['tmp_name'] );
					$file_array['tmp_name'] = '';
				}

				// do the validation and storage stuff
				$id = media_handle_sideload( $file_array, $post_id );

				// If error storing permanently, unlink
				if ( is_wp_error( $id ) ) {
					@unlink( $file_array['tmp_name'] );
					WP_CLI::log( "Error: ". $id->get_error_message() );
					WP_CLI::log( "Filename: $old_filename" );
				} else {
					$src = wp_get_attachment_url( $id );

					if ( $src ) {
						$content = str_replace( $filename, $src, $content );
					} else {
						WP_CLI::log( "Error: $old_filename not changed in post content." );
					}

					@unlink( $file_array['tmp_name'] );
				}
			}
		}

		return $content;
	}

	/**
	 * Create new terms.
	 *
	 * @var object $term Current term object.
	 * @var string $taxonomy Taxonomy to use.
	 * @return int|bool
	 */
	private function process_term( $term, $taxonomy, $post_type ) {
		$args = array();
		$term_name = '';
		$slug = '';
		$desc = '';
		$parent = '';

		$term_name = (string) $term['name'];
		$slug = isset( $term['slug'] ) ? (string) $term['slug'] : '';
		$desc = isset( $term['desc'] ) ? (string) $term['desc'] : '';

		$term_name = sanitize_term_field( 'name', $term_name, 0, $taxonomy, 'db' );

		if ( ! taxonomy_exists( $taxonomy ) ) {
			register_taxonomy( $taxonomy , array( $post_type ) );
			WP_CLI::warning( "Registering temporary taxonomy - $taxonomy" , $taxonomy);
		}

		if ( $term = term_exists( $term_name, $taxonomy ) ) {
			return (int) $term['term_id'];
		}

		if ( $parent ) {
			$parent_term_name = sanitize_term_field( 'name', $parent, 0, $taxonomy, 'db' );

			if ( $parent_term = term_exists( $parent_term_name, $taxonomy ) ) {
				$parent = (int) $parent_term['term_id'];
			} else {
				$parent_term = wp_insert_term( $parent, $taxonomy );

				if ( is_wp_error( $parent_term ) ) {
					WP_CLI::log( "Error: Term $parent not imported." );
					$parent = 0;
				} else {
					$parent = (int) $parent_term['term_id'];
				}
			}

			$args['parent'] = $parent;
		}

		if ( $slug ) {
			$args['slug'] = $slug;
		}
		if ( $desc ) {
			$args['description'] = $desc;
		}

		$term = wp_insert_term( $term_name, $taxonomy, $args );

		if ( is_wp_error( $term ) ) {
			WP_CLI::log( "Error: Term $term_name not imported." );
			WP_CLI::log( "Error Message: " . $term->get_error_message() );
			return false;
		}

		return (int) $term['term_id'];
	}

	/**
	 * Download featured image.
	 *
	 * @param string $filepath Path to image.
	 * @param int $post_id Post to associate with image.
	 * @param array $attrs Image attributes
	 * @return int
	 */
	private function import_featured_image( $filepath, $post_id = 0, $attrs ) {
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$featured_image = '';
		$old_filename = '';

		$filename = str_replace( '\\', '/', $filepath );
		$filename = urldecode( $filename ); // for filenames with spaces
		$filename = str_replace( ' ', '%20', $filename );
		$filename = str_replace( '&amp;', '&', $filename );
		$filename = str_replace( '&mdash;', '—', $filename );

		if ( preg_match( '/^http/', $filename ) || preg_match( '/^www/', $filename ) ) {
			$old_filename = $filename;
		} else {
			$old_filename = trailingslashit( $this->site_url ) . $filename;
		}

		$tmp = download_url( $old_filename );
		preg_match( '/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|Jpeg|JPEG|gif|GIF|png|PNG)/', $filename, $matches );

		// make sure we have a match.  This won't be set for PDFs and .docs
		if ( $matches && isset( $matches[0] ) ) {
			$name = str_replace( '%20', ' ', basename( $matches[0] ) );
			$file_array['name'] = $name;
			$file_array['tmp_name'] = $tmp;

			// If error storing temporarily, unlink
			if ( is_wp_error( $tmp ) ) {
				@unlink( $file_array['tmp_name'] );
				$file_array['tmp_name'] = '';
			}

			// do the validation and storage stuff
			$id = media_handle_sideload( $file_array, $post_id, null, $attrs );

			// If error storing permanently, unlink
			if ( is_wp_error( $id ) ) {
				@unlink( $file_array['tmp_name'] );
				WP_CLI::log( "Error: ". $id->get_error_message() );
				WP_CLI::log( "Filename: $old_filename" );
			} else {
				$featured_image = set_post_thumbnail( $post_id, $id );
				@unlink( $file_array['tmp_name'] );
			}
		} else {
			@unlink( $tmp );
			WP_CLI::log( "Error: ". $filename . " not added." );
		}

		return $featured_image;
	}

	/**
	 * Add comment to an article.
	 *
	 * @param object $comment Comment information.
	 * @param int $post_id ID of post to update.
	 * @param bool $force Whether to force import of comments already imported.
	 * @return int
	 */
	private function add_comment( $comment, $post_id, $force ) {
		global $wpdb;

		$comment_id = $wpdb->get_var( $sql = "SELECT comment_id from {$wpdb->commentmeta} WHERE meta_key = '_gmedia_old_comment_id' AND meta_value = '". (int) $comment['CommentID'] ."'" );

		if ( ! $force && $comment_id ) {
			return $comment_id;
		}

		$comment_data = array( 'comment_post_ID' => $post_id );

		if ( isset( $comment['Status'] ) ) {
			$old_comment_id = ( 'Approved' === (string) $comment['CommentID'] ) ? 1 : 0;
		}

		if ( isset( $comment['AuthorName'] ) ) {
			$comment_data['comment_author'] = (string) $comment['AuthorName'];
		}

		if ( isset( $comment['EmailAddress'] ) ) {
			$comment_data['comment_author_email'] = (string) $comment['EmailAddress'];
		}

		if ( isset( $comment['AuthorURL'] ) ) {
			$comment_data['comment_author_url'] = (string) $comment['AuthorURL'];
		}

		if ( isset( $comment['IPAddress'] ) ) {
			$comment_data['comment_author_IP'] = (string) $comment['IPAddress'];
		}

		if ( isset( $comment['UTCDateCreated'] ) ) {
			$comment_data['comment_date'] = (string) $comment['UTCDateCreated'];
			$comment_data['comment_date_gmt'] = (string) $comment['UTCDateCreated'];
		}

		if ( isset( $comment->CommentText ) ) {
			$comment_data['comment_content'] = (string) $comment->CommentText;
		}

		$comment_id = wp_insert_comment( $comment_data );

		if ( isset( $comment['CommentID'] ) ) {
			add_comment_meta( $comment_id, '_gmedia_old_comment_id', (int) $comment['CommentID'] );
		}

		return $comment_id;
	}

	/**
	 * Add correct parent/child hierarchy to comments.
	 *
	 * @param int $comment_id ID of current comment to update.
	 * @param int $parent_comment_id ID of old parent comment.
	 * @return void
	 */
	private function add_parent_comment( $comment_id, $parent_comment_id ) {
		global $wpdb;

		$comment_parent_id = $wpdb->get_var( $sql = "SELECT comment_id from {$wpdb->commentmeta} WHERE meta_key = '_gmedia_old_comment_id' AND meta_value = '". $parent_comment_id ."'" );

		if ( $parent_comment_id ) {
			$updated = wp_update_comment( array( 'comment_ID' => $comment_id, 'comment_parent' => $comment_parent_id ) );

			if ( ! $updated ) {
				WP_CLI::log( "Error: comment not updated with parent." );
			}
		} else {
			WP_CLI::log( "Error: No parent comment ID found." );
		}
	}

	/**
	 * Import blog articles from the XML file.
	 *
	 * @var array $blogs Articles from certain blogs from file.
	 * @var bool  $force Whether to force import or not.
	 * @return void
	 */
	private function process_blogs( $blogs, $force ) {
		global $wpdb;

		$taxonomy_map = array();

		$taxonomy_map = $this->parse_taxonomy_mapping();

		$total = count( $blogs );

		$notify = new \cli\progress\Bar( "Importing $total blogs", $total );
		$skipper = 0;

		foreach ( $blogs as $single_blog ) {

			$blog      = (string) $single_blog['BlogName'];
			$blog_desc = (string) $single_blog['BlogDescription'];

			//echo $taxonomy_map[ $blog ];

			foreach( $blogs->BlogAuthor as $author ) {
				if ( isset( $author['AuthorEmailAddress'] ) ) {
					$author_email = (string) $author['AuthorEmailAddress'];
					$exists = $this->check_if_user_exists( $author_email );

					if ( $exists ) {
						$user_id = $exists;
					} else {
						$user_id = $this->create_user( $author_email, true );
					}
				} else {
					$user_id = get_current_user_id();
				}
			}

			$count = 0;
			foreach ( $single_blog->BlogEntries->BlogEntry as $entry ) {

				if( $skipper != $this->skip ) {
					$skipper++;
					$notify->tick();
					continue;
				}

				$entry_hash = trim( (string) $entry['EntryTitle'] ) . (string) $entry['BlogEntryID'] . (string) $entry['EntryPostedUTCDatetime'];
				$entry_hash = md5( $entry_hash );

				// grab the existing post ID (if it exists).
				$wp_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'gmedia_import_id' AND meta_value = '" . $entry_hash . "'" );

				// If we're not forcing import, skip existing posts.
				if ( ! $force && $wp_id ) {
					$notify->tick();
					WP_CLI::log( "Already exists $wp_id" );
					continue;
				}

				// counter to clear the cache
				$count++;
				if( $count == 100 ) {
					if( class_exists('MTM_Migration_Utils') ) {
						MTM_Migration_Utils::stop_the_insanity();
					}
					sleep(15);
					$count = 0;
				}

				$blog_entry_title = strtolower( trim( (string) $entry['EntryTitle'] ) );
				$post = array(
					'post_type'     => 'post',
					'post_status'   => 'publish',
					'post_title'    => ucwords( $blog_entry_title ),
					'post_content'  => trim( (string) $entry->BlogEntryText ),
					'post_date'     => (string) $entry['EntryPostedUTCDatetime'],
					'post_date_gmt' => (string) $entry['EntryPostedUTCDatetime'],
				);

				if ( 'Draft' === $entry['StatusDescription'] ) {
					$post['post_status'] = 'draft';
				}

				if ( $wp_id ) {
					$post['ID'] = $wp_id;
				}

				$wp_id = wp_insert_post( $post );

				update_post_meta( $wp_id, 'gmedia_import_id', $entry_hash );

				if( isset( $single_blog->BlogEntries->BlogEntry->BlogEntryAudio ) ) {
					foreach ( $single_blog->BlogEntries->BlogEntry->BlogEntryAudio as $single_audio ) {
						if( isset( $single_audio['AudioSrc'] ) ) {
							$media_file_id = $this->import_music_files( $wp_id, $single_audio['AudioSrc'] );
							$media_url  = wp_get_attachment_url( $media_file_id );

							$updated_post                 = array( 'ID' => $wp_id );
							$content = $this->import_media( trim( (string) $entry->BlogEntryText ), $wp_id );
							$updated_post['post_content'] =  $content . '<br/>' . '[audio mp3="' . $media_url . '"][/audio]' ;

							wp_update_post( $updated_post );

							update_post_meta( $wp_id, '_legacy_blog_music_file', $media_url );
						}
					}

				} else {
					// Download images found in post_content and update post_content with new images.
					$updated_post                 = array( 'ID' => $wp_id );
					$content = $this->import_media( trim( (string) $entry->BlogEntryText ), $wp_id );
					$updated_post['post_content'] = $content;
					wp_update_post( $updated_post );
				}

				// Process Blog Taxonomy Term
				if ( isset( $blog ) ) {
					$blog_info['name'] = trim( $taxonomy_map[ $blog ][ 'term' ] );
					$blog_info['desc'] = trim( $blog_desc );

					$blog_id = $this->process_term( $blog_info, $taxonomy_map[ $blog ][ 'taxonomy' ], 'post' );

					if ( $blog_id ) {
						wp_set_post_terms( $wp_id, array( $blog_id ), $taxonomy_map[ $blog ][ 'taxonomy' ], false );
					}
				}

				// Process Tags
				/*if ( isset( $entry['Tags'] ) ) {
					$tags = trim( (string) $entry['Tags'] );
					$tags = explode( ',', $tags );
					if( count( $tags ) == 1 ) {
						$tags = explode( ' ', trim( (string) $entry['Tags'] ) );
					}

					foreach ( $tags as $tag ) {
						if ( $tag != "" ) {
							$tag_term['name'] = $tag;
							$tag_id = $this->process_term( $tag_term, 'post_tag', 'post' );

							if ( $tag_id ) {
								wp_set_post_terms( $wp_id, array( $tag_id ), 'post_tag', true );
							}
						}
					}
				}*/

				// TO-DO
				// Images
				if ( isset( $entry->BlogEntryImage ) ) {
					foreach ( $entry->BlogEntryImage as $image ) {
						$width = (string) $image['MainImageWidth'];
						if( $width >= 300 ) {
							// import images here
							$featured_image_attrs = array();
							$featured_image_path  = '/Pics/' . (string) $image['MainImageSrc'];
							$this->import_featured_image( $featured_image_path, $wp_id, $featured_image_attrs );
						}
					}
				}

				/*if ( isset( $entry->Comments ) ) {
					foreach ( $entry->Comments->Comment as $comment ) {
						$comment_id = $this->add_comment( $comment, $wp_id, $force );

						if ( $comment_id ) {
							if ( isset( $comment['ParentCommentID'] ) ) {
								$parent_comment_id = (int) $comment['ParentCommentID'];
								$this->add_parent_comment( $comment_id, $parent_comment_id );
							}
						}
					}
				}*/

				// add redirect
				if ( isset( $entry->BlogEntryURL ) ) {
					//add redirect
					CMM_Legacy_Redirects::add_redirect( (string) $entry->BlogEntryURL, $wp_id );
				}

			}
			//$notify->tick();
		}
		//$notify->finish();
	}

	/**
	 * Import channel articles from the XML file.
	 *
	 * @var SimpleXMLElement $channels Articles from file.
	 * @var bool             $force    Whether to force import or not.
	 * @return void
	 */
	private function process_channels( $channels, $force ) {
		global $wpdb;

		$total  = count( $channels->Channel );
		$notify = new \cli\progress\Bar( "Importing $total channels!", $total );

		$count = 0;
		$skipper = 0;
		$taxonomy = 'category';
		if( $this->type == 'factory_channel' || $this->type == 'factory_channels' ) {
			$taxonomy = 'collection';
		}
		foreach ( $channels->Channel as $channel ) {

			$channel_title = (string) $channel['ChannelTitle'];
			$channel_desc  = (string) $channel['ChannelDescription'];
			$blog_info['name']     = trim( $channel_title );
			$blog_info['desc']     = trim( $channel_desc );
			$blog_id       = $this->process_term( $blog_info, $taxonomy, 'post' );

			foreach ( $channel->Story as $story ) {

				if( $this->skip != $skipper ) {
					$skipper++;
					continue;
				}

				$story_hash = trim( (string) $story['Headline'] ) . (string) $story['StoryDate'] . (string) $story['StoryID'];
				$story_hash = md5( $story_hash );

				// grab the existing post ID (if it exists).
				$wp_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'gmedia_import_id' AND meta_value = '" . $story_hash . "'" );

				// If we're not forcing import, skip existing posts.
				if ( ! $force && $wp_id ) {
					WP_CLI::log( 'Already exists ' . trim( (string) $story['Headline'] ) );
					continue;
				}

				// counter to clear the cache
				$count++;
				if( $count == 100 ) {
					if( class_exists('MTM_Migration_Utils') ) {
						MTM_Migration_Utils::stop_the_insanity();
					}
					sleep(15);
					$count = 0;
				}
				$story_title = strtolower( trim( (string) $story['Headline'] ) );

				$post = array(
					'post_type'    => 'post',
					'post_status'  => 'publish',
					'post_title'   => ucwords( $story_title ),
					'post_content' => trim( (string) $story->StoryText ),
					'post_date'    => (string) $story['StoryDate']
				);

				if ( $wp_id ) {
					$post['ID'] = $wp_id;
				}

				$wp_id = wp_insert_post( $post );

				// Download images found in post_content and update post_content with new images.
				$updated_post                 = array( 'ID' => $wp_id );
				$updated_post['post_content'] = $this->import_media( trim( (string) $story->StoryText ), $wp_id );
				wp_update_post( $updated_post );

				update_post_meta( $wp_id, 'gmedia_import_id', $story_hash );

				// Process Blog Taxonomy Term
				if ( $blog_id ) {
					wp_set_post_terms( $wp_id, array( $blog_id ), $taxonomy, false );
				}

				// Post Meta
				if ( isset( $story['Subheadline'] ) ) {
					$subtitle = (string) $story['Subheadline'];
					update_post_meta( $wp_id, '_gmedia_subtitle', apply_filters( 'the_title', $subtitle ) );
				}

				// Featured Image
				if ( isset( $story['HeadlineImageFilename'] ) ) {
					$featured_image_attrs = array();
					$featured_image_path  = '/Pics/' . (string) $story['HeadlineImageFilename'];

					$image = $this->import_featured_image( $featured_image_path, $wp_id, $featured_image_attrs );

					if ( ! $image ) {
						WP_CLI::log( "Error: Featured image not added!" );
					}
				} else {
					WP_CLI::log( "Error: No Featured Image Found!" );
				}

				// Comments
				/*if ( isset( $story->Comments ) ) {
					foreach ( $story->Comments->Comment as $comment ) {
						$comment_id = $this->add_comment( $comment, $wp_id, $force );

						if ( $comment_id ) {
							if ( isset( $comment['ParentCommentID'] ) ) {
								$parent_comment_id = (int) $comment['ParentCommentID'];
								$this->add_parent_comment( $comment_id, $parent_comment_id );
							}
						}
					}
				}*/
			}
			WP_CLI::log( 'Imported channel ' . (string) $channel['ChannelTitle'] );
			//$notify->tick();
		}
		$notify->finish();
	}

	/**
	 * Import events from the XML file.
	 *
	 * @var array $events Events from file.
	 * @var bool  $force  Whether to force import or not.
	 * @return void
	 */
	private function process_events( $events, $force ) {
		global $wpdb;

		$total  = count( $events->EventCalendar );
		$notify = new \cli\progress\Bar( "Importing $total event calendars", $total );

		$count = 0;
		foreach ( $events->EventCalendar as $calendar ) {
			$event_cat['name'] = (string) $calendar['EventCalendarName'];
			$event_cat['desc'] = (string) $calendar['EventCalendarDescription'];

			$total    = count( $calendar->Event );
			$progress = new \cli\progress\Bar( "Importing $total events", $total );

			foreach ( $calendar->Event as $event ) {
				$event_hash = trim( (string) $event['EventName'] ) . (string) $event['DateCreated'];
				$event_hash = md5( $event_hash );

				// grab the existing post ID (if it exists).
				$wp_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'gmedia_import_id' AND meta_value = '" . $event_hash . "'" );

				// If we're not forcing import, skip existing posts.
				if ( ! $force && $wp_id ) {
					$progress->tick();
					continue;
				}

				// counter to clear the cache
				$count++;
				if( $count == 100 ) {
					if( class_exists('MTM_Migration_Utils') ) {
						MTM_Migration_Utils::stop_the_insanity();
						sleep( 15 );
					}
					$count = 0;
				}

				$tribe_event = array(
					'post_type'     => 'tribe_events',
					'post_status'   => 'publish',
					'post_title'    => wp_strip_all_tags(trim( (string) $event['EventName'] )),
					'post_content'  => trim( (string) $event['EventDescription'] ),
					'post_date'     => (string) $event['DateCreated'],
					'post_modified' => (string) $event['DateModified'],
				);

				if ( $wp_id ) {
					$tribe_event['ID'] = $wp_id;
				}

				$wp_id = wp_insert_post( $tribe_event );

				// Download images found in post_content and update post_content with new images.
				$updated_post                 = array( 'ID' => $wp_id );
				$updated_post['post_content'] = $this->import_media( trim( (string) $event['EventDescription'] ), $wp_id );
				wp_update_post( $updated_post );

				update_post_meta( $wp_id, 'gmedia_import_id', $event_hash );

				// Process Event Categories taxonomy
				$cat_id = $this->process_term( $event_cat, 'tribe_events_cat', 'tribe_events');

				if ( $cat_id ) {
					wp_set_post_terms( $wp_id, array( $cat_id ), 'tribe_events_cat', false );
				}


				// Featured Image
				if ( isset( $calendar['EventImageFilePath'] ) ) {
					$featured_image_attrs = array();
					$featured_image_path = (string) $calendar['EventImageFilePath'];

					$image = $this->import_featured_image( $featured_image_path, $wp_id, $featured_image_attrs );

					if ( ! $image ) {
						WP_CLI::warning( "Featured image not added!" );
					}
				} else {
					WP_CLI::log( "No Featured Image Found!" );
				}
				// Post Meta
				if ( isset( $event['EventDate'] ) ) {
					$start_date = date( 'Y-m-d', strtotime( (string) $event['EventDate'] ) );

					if ( isset( $event['EventTime'] ) ) {
						$event_time = (string) $event['EventTime'];

						if ( strpos( $event_time, '-' ) !== false ) {
							$times = explode( '-', $event_time );
						} else {
							$times = explode( '�', $event_time );
						}

						if ( isset( $times[0] ) ) {
							$start_time = trim( $times[0] );
						} else {
							$start_time = '';
						}

						if ( isset( $times[1] ) ) {
							$end_time = trim( $times[1] );
						} else {
							$end_time = '';
						}

						$start_time = date( 'G:i:s', strtotime( $start_time ) );
						$end_time   = date( 'G:i:s', strtotime( $end_time ) );

						$start_datetime = $start_date . ' ' . $start_time;
						update_post_meta( $wp_id, '_EventStartDate', $start_datetime );
					}
				}
				if ( isset( $event['EventEndDate'] ) ) {
					$end_date     = date( 'Y-m-d', strtotime( (string) $event['EventEndDate'] ) );
					$end_datetime = $end_date . ' ' . $end_time;
					update_post_meta( $wp_id, '_EventEndDate', $end_datetime );
				} else {
					$end_date     = $start_date;
					$end_datetime = $end_date . ' ' . $end_time;
					update_post_meta( $wp_id, '_EventEndDate', $end_datetime );
				}

				if( isset($event['DateToRelease']) ) {
					update_post_meta( $wp_id, '_legacy_DateToRelease', (string) $event['DateToRelease'] );
				}

				// Save Venue (which is a separate CPT)
				if ( isset( $event['EventLocation'] ) ) {
					$venue = trim( (string) $event['EventLocation'] );

					$existing_venue = get_page_by_title( $venue, 'OBJECT', 'tribe_venue' );
					if ( !$existing_venue  ) {
						$venue_args = array(
							'post_type'   => 'tribe_venue',
							'post_status' => 'publish',
							'post_title'  => $venue,
						);

						$venue_id = wp_insert_post( $venue_args );
					} else {
						$venue_id = $existing_venue->ID;
					}

					if ( $venue_id ) {
						update_post_meta( $wp_id, '_EventVenueID', $venue_id );
					}
				}

				$progress->tick();
			}

			$progress->finish();
			$notify->tick();
		}

		$notify->finish();
	}

	/**
	 * Import galleries from the XML file.
	 *
	 * @var SimpleXMLElement $galleries Galleries from file.
	 * @var bool $force Whether to force import or not.
	 * @return void
	 */
	private function process_photoalbums( $galleries, $force ) {
		global $wpdb;

		$total = count( $galleries->Album );
		$notify = new \cli\progress\Bar( "Importing $total albums", $total );

		$count = 0;
		foreach ( $galleries->Album as $album ) {
			$gallery_hash = trim( (string) $album['AlbumName'] ) . (string) $album['UTCDateCreated'];
			$gallery_hash = md5( $gallery_hash );

			// grab the existing post ID (if it exists).
			$wp_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'gmedia_import_id' AND meta_value = '".$gallery_hash ."'" );

			// If we're not forcing import, skip existing posts.
			if ( ! $force && $wp_id ) {
				$notify->tick();
				continue;
			}

			// counter to clear the cache
			$count++;
			if( $count == 100 ) {
				if( class_exists('MTM_Migration_Utils') ) {
					MTM_Migration_Utils::stop_the_insanity();
				}
				sleep(15);
				$count = 0;
			}


			$gallery_args = array(
				'post_type'     => 'gmr_gallery',
				'post_status'   => 'publish',
				'post_title'    => trim( (string) $album['AlbumName'] ),
				'post_content'  => trim( (string) $album['Description'] ),
				'post_date'     => (string) $album['UTCDateCreated'],
				'post_date_gmt' => (string) $album['UTCDateCreated'],
				'post_modified' => (string) $album['UTCDateModified'],
			);

			if ( $wp_id ) {
				$gallery_args['ID'] = $wp_id;
			}

			$wp_id = wp_insert_post( $gallery_args );

			set_post_format($wp_id, 'gallery' ); //sets the given post to the 'gallery' format

			update_post_meta( $wp_id, 'gmedia_import_id', $gallery_hash );

			// Process Gallery Category Terms
			if ( isset( $album['Categories'] ) ) {
				$gallery_cats = explode( ',', (string) $album['Categories'] );
				$cat_ids = array();
				foreach ( $gallery_cats as $gallery_cat ) {
					if ( '' !== trim( $gallery_cat ) ) {
						$term_title = $gallery_cat;
						$album_term['name'] = $term_title;
						$cat_id = $this->process_term( $album_term, 'category', 'gmr_gallery');
						if( $cat_id ) {
							array_push( $cat_ids, $cat_id );
						}
					}
				}
				if ( !empty( $cat_ids ) ) {
					wp_set_post_terms( $wp_id, $cat_ids, 'category', false );
				}
			}

			// Post Meta
			if ( isset( $album['AlbumType'] ) ) {
				$album_type = (string) $album['AlbumType'];
				update_post_meta( $wp_id, '_gmedia_album_type', sanitize_text_field( $album_type ) );
			}

			// Import Gallery Images
			if ( isset( $album->Photo ) ) {
				$image_ids = array();

				$filepath = (string) $album->Photo['Filename'];
				$filepath = str_replace( '\\', '/', $filepath );
				preg_match( '/(\d+)\/-sizeID-/', $filepath, $matches );
				if ( isset( $matches[1] ) ) {
					$gallery_id = $matches[1];
					update_post_meta( $wp_id, '_gmedia_orig_gallery_id', sanitize_text_field( $matches[1] ) );
				} else {
					$gallery_id = 0;
					WP_CLI::log( "Error: can't get gallery id from ". $filepath );
				}

				// Find businesses that have galleries and set the associated gallery to draft
				$business_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = '_gmedia_album_id' AND meta_value = '". $gallery_id ."'" );
				if ( $business_id ) {
					$draft_gallery = array( 'ID' => $wp_id, 'post_status' => 'draft' );
					wp_update_post( $draft_gallery );
				} else {

					foreach ( $album->Photo as $photo ) {
						$photo_info = array(
							'caption'     => $photo['PhotoCaption'],
							'attribution' => $photo['Attribution'],
							'path'        => $photo['Filename']
						);
						$image_ids[] = $this->import_gallery_images( $photo_info, $wp_id );
					}

					if ( ! empty( $image_ids ) ) {
						update_post_meta( $wp_id, '_gmedia_gallery_images', $image_ids );
						$image_ids = implode( ',', $image_ids );
						$gallery = '[gallery ids="'. $image_ids .'"]';

						$updated_post['post_content'] = trim( (string) $album['Description'] ) . $gallery;
						$updated_post['ID'] = $wp_id;

						wp_update_post( $updated_post );
					}
				}
			}

			//$notify->tick();
		}

		$notify->finish();
	}

	/**
	 * Download gallery image and associate with post.
	 *
	 * @param array $image Image information.
	 * @param int $post_id ID of post to update.
	 * @return string
	 */
	private function import_gallery_images( $image, $post_id = 0 ) {
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$image_id = '';
		$attrs = array();
		$filename = str_replace( '\\', '/', $image['path'] );
		$filename = urldecode( $filename ); // for filenames with spaces
		$filename = str_replace( ' ', '%20', $filename );
		$filename = str_replace( '&amp;', '&', $filename );
		$filename = str_replace( '&mdash;', '—', $filename );

		if ( preg_match( '/^http/', $filename ) || preg_match( '/^www/', $filename ) ) {
			$old_filename = $filename;
		} else {
			$old_filename = trailingslashit( $this->site_url) . $filename;
		}

		if ( strpos( $old_filename, '-sizeID-' ) !== false ) {
			$tmp = '';

			foreach ( $this->photo_sizes as $size_id => $size_name ) {
				$replaced_filename = str_replace( '-sizeID-', $size_id, $old_filename );
				$replaced_filename = str_replace( '-photosize-', $size_name, $replaced_filename );
				$replaced_filename = str_replace( ' ', '%20', $replaced_filename );

				$tmp = download_url( $replaced_filename );

				if ( ! is_wp_error( $tmp ) ) {
					break;
				}
			}
		} else {
			$replaced_filename = str_replace( ' ', '%20', $old_filename );
			$tmp = download_url( $replaced_filename );
		}

		if ( $tmp ) {
			preg_match( '/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|Jpeg|JPEG|gif|GIF|png|PNG)/', $filename, $matches );
			if ( is_array( $matches ) && isset( $matches[0] ) ) {
				$name = str_replace( '-photosize-', '', basename( $matches[0] ) );
				$name = str_replace( '%20', ' ', basename( $matches[0] ) );
				$file_array['name'] = $name;
			} else {
				$file_array['name'] = $tmp;
			}
			$file_array['tmp_name'] = $tmp;

			// If error storing temporarily, unlink
			if ( is_wp_error( $tmp ) ) {
				@unlink( $file_array['tmp_name'] );
				$file_array['tmp_name'] = '';
			}

			if ( isset( $image['caption'] ) && '' !== trim( $image['caption'] ) ) {
				$attrs['post_excerpt'] = sanitize_text_field( $image['caption'] );
			}

			if ( isset( $image['attribution'] ) && '' !== trim( $image['attribution'] ) ) {
				$attrs['post_content'] = sanitize_text_field( $image['attribution'] );
			}

			// do the validation and storage stuff
			$image_id = media_handle_sideload( $file_array, $post_id, null, $attrs );

			// If error storing permanently, unlink
			if ( is_wp_error( $image_id ) ) {
				@unlink( $file_array['tmp_name'] );
				WP_CLI::log( "Error: ". $image_id->get_error_message() );
				WP_CLI::log( "Filename: $old_filename" );

				return -1;
			}

			@unlink( $file_array['tmp_name'] );
		}

		return $image_id;
	}

	/**
	 * Import showcases from the XML file.
	 *
	 * @var SimpleXMLElement $showcases Showcases from file.
	 * @var bool $force Whether to force import or not.
	 * @return void
	 */
	private function process_showcases( $showcases, $force ) {
		global $wpdb;

		$total = count( $showcases->Showcase );
		$notify = new \cli\progress\Bar( "Importing $total showcases", $total );

		$count = 0;
		foreach ( $showcases->Showcase as $showcase ) {
			$showcase_hash = trim( (string) $showcase['ShowcaseName'] ) . (string) $showcase['DateCreated'];
			$showcase_hash = md5( $showcase_hash );

			// grab the existing post ID (if it exists).
			$wp_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'gmedia_import_id' AND meta_value = '".$showcase_hash ."'" );

			// If we're not forcing import, skip existing posts.
			if ( ! $force && $wp_id ) {
				//$notify->tick();
				continue;
			}

			// counter to clear the cache
			$count++;
			if( $count == 10 ) {
				if( class_exists('MTM_Migration_Utils') ) {
					MTM_Migration_Utils::stop_the_insanity();

				}
				sleep(15);
				$count = 0;
			}

			$showcase_post = array(
				'post_type'     => 'gmr_album',
				'post_status'   => 'publish',
				'post_title'    => trim( (string) $showcase['ShowcaseName'] ),
				'post_content'  => trim( (string) $showcase['ShowcaseDescription'] ),
				'post_date'     => (string) $showcase['DateCreated'],
				'post_modified' => (string) $showcase['DateModified']
			);


			if ( $wp_id ) {
				$showcase_post['ID'] = $wp_id;
			}

			$wp_id = wp_insert_post( $showcase_post );

			update_post_meta( $wp_id, 'gmedia_import_id', $showcase_hash );

			$questions = array();
			if ( isset( $showcase['Short01Label'] ) ) {
				$questions[] = sanitize_text_field( (string) $showcase['Short01Label'] );
			}
			if ( isset( $showcase['Short02Label'] ) ) {
				$questions[] = sanitize_text_field( (string) $showcase['Short02Label'] );
			}
			if ( isset( $showcase['Short03Label'] ) ) {
				$questions[] = sanitize_text_field( (string) $showcase['Short03Label'] );
			}
			if ( isset( $showcase['Medium01Label'] ) ) {
				$questions[] = sanitize_text_field( (string) $showcase['Medium01Label'] );
			}
			if ( isset( $showcase['Long01Label'] ) ) {
				$questions[] = sanitize_text_field( (string) $showcase['Long01Label'] );
			}

			update_post_meta( $wp_id, '_legacy_labels', $questions );

			$gallery_ids = array();
			$menu_order = 1;
			foreach ( $showcase as $entry ) {
				$gallery_ids[] = $this->process_showcase_entry( $entry, $wp_id, $force, $menu_order );
				$menu_order++;
			}

			if ( ! empty( $gallery_ids ) ) {
				update_post_meta( $wp_id, '_gmedia_related_galleries', $gallery_ids );
			}
		}
	}

	/**
	 * Import galleries associated with a showcase.
	 *
	 * @var array $entry Showcase entry.
	 * @var int $parent_id ID of contest post gallery is associated with.
	 * @var bool $force Whether to force import or not.
	 * @return void
	 */
	private function process_showcase_entry( $entry, $parent_id, $force, $menu_order ) {
		global $wpdb;

		$entry_hash = trim( (string) $entry['ShowcaseEntryName'] ) . (string) $entry['DateCreated'];
		$entry_hash = md5( $entry_hash );

		// grab the existing post ID (if it exists).
		$wp_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'gmedia_import_id' AND meta_value = '".$entry_hash ."'" );

		// If we're not forcing import, skip existing posts.
		if ( ! $force && $wp_id ) {
			return $wp_id;
		}

		$showcase_entry_post = array(
			'post_type'     => 'gmr_gallery',
			'post_status'   => 'publish',
			'post_parent'   => $parent_id,
			'post_title'    => trim( (string) $entry['ShowcaseEntryName'] ),
			'post_date'     => (string) $entry['DateCreated'],
			'post_date_gmt'     => (string) $entry['DateCreated'],
			'post_modified' => (string) $entry['DateModified'],
			'menu_order'    => $menu_order
		);

		if ( ! $entry['IsApproved'] ) {
			$showcase_entry_post['post_status'] = 'draft';
		}

		if ( $wp_id ) {
			$showcase_entry_post['ID'] = $wp_id;
		}

		$wp_id = wp_insert_post( $showcase_entry_post );
		WP_CLI::log( 'Added showcase entry' );
		set_post_format( $wp_id, 'gallery' );
		update_post_meta( $wp_id, 'gmedia_import_id', $entry_hash );
		update_post_meta( $wp_id, '_gmedia_related_content', $parent_id );

		// Post Meta
		if ( isset( $entry['SubmitterName'] ) ) {
			$submitter = (string) $entry['SubmitterName'];
			update_post_meta( $wp_id, '_gmedia_submitter', sanitize_text_field( $submitter ) );
		}
		if ( isset( $entry['SubmitterCityOrNeighborhood'] ) ) {
			$submitter_city = (string) $entry['SubmitterCityOrNeighborhood'];
			update_post_meta( $wp_id, '_gmedia_submitter_city', sanitize_text_field( $submitter_city ) );
		}
		if ( isset( $entry['ShowcaseEntryRatingCount'] ) ) {
			$rating_count = (string) $entry['ShowcaseEntryRatingCount'];
			update_post_meta( $wp_id, '_gmedia_rating_count', sanitize_text_field( $rating_count ) );
		}
		if ( isset( $entry['ShowcaseEntryRatingTally'] ) ) {
			$tally = (string) $entry['ShowcaseEntryRatingTally'];
			update_post_meta( $wp_id, '_gmedia_tally', sanitize_text_field( $tally ) );
		}

		$answers = array();
		if ( isset( $entry['ShortText01'] ) ) {
			$answers[] = sanitize_text_field( (string) $entry['ShortText01'] );
		}
		if ( isset( $entry['ShortText02'] ) ) {
			$answers[] = sanitize_text_field( (string) $entry['ShortText02'] );
		}
		if ( isset( $entry['ShortText03'] ) ) {
			$answers[] = sanitize_text_field( (string) $entry['ShortText03'] );
		}
		if ( isset( $entry['MediumText01'] ) ) {
			$answers[] = sanitize_text_field( (string) $entry['MediumText01'] );
		}
		if ( isset( $entry['Long01Label'] ) ) {
			$answers[] = sanitize_text_field( (string) $entry['Long01Label'] );
		}
		update_post_meta( $wp_id, '_gmedia_entry_answers', $answers );

		// Process Photos
		$image_ids = array();
		foreach ( $entry->ShowcasePhoto as $photo ) {
			$photo_info = array(
				'path' => $photo['PhotoURL']
			);
			$image_ids[] = $this->import_gallery_images( $photo_info, $wp_id );
		}

		if ( ! empty( $image_ids ) ) {
			update_post_meta( $wp_id, '_gmedia_gallery_images', $image_ids );
			$image_ids = implode( ',', $image_ids );
			$gallery = '[gallery ids="'. $image_ids .'"]';
			$updated_post = array( 'ID' => $wp_id, 'post_content' => $gallery );
			wp_update_post( $updated_post );
		}

		// add redirect
		if ( isset( $entry->ShowcaseEntryURL ) ) {
			CMM_Legacy_Redirects::add_redirect( (string) $entry->ShowcaseEntryURL, $wp_id );
		}

		return $wp_id;
	}

	/**
	 * Import video channel articles from the XML file.
	 *
	 * @var SimpleXMLElement $videos Articles from file.
	 * @var bool $force Whether to force import or not.
	 * @return void
	 */
	private function process_videos( $videos, $force ) {
		global $wpdb;

		$total = count( $videos->VideoChannel );
		$notify = new \cli\progress\Bar( "Importing $total video channels", $total );

		$count = 0;
		foreach ( $videos->VideoChannel as $channel ) {
			$channel_title = (string) $channel['VideoChannelName'];
			$channel_desc = (string) $channel['VideoChannelDescription'];
			$blog_info['name'] = trim( $channel_title );
			$blog_info['desc'] = trim( $channel_desc );
			$blog_id = $this->process_term( $blog_info, 'category' , 'post');

			foreach ( $channel->VideoPost as $post ) {
				$post_hash = trim( (string) $post['PostTitle'] ) . (string) $post['DateCreated'];
				$post_hash = md5( $post_hash );

				// grab the existing post ID (if it exists).
				$wp_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'gmedia_import_id' AND meta_value = '".$post_hash ."'" );

				// If we're not forcing import, skip existing posts.
				if ( ! $force && $wp_id ) {
					$notify->tick();
					continue;
				}

				// counter to clear the cache
				$count++;
				if( $count == 100 ) {
					if( class_exists('MTM_Migration_Utils') ) {
						MTM_Migration_Utils::stop_the_insanity();

					}
					sleep(15);
					$count = 0;
				}

				if ( isset( $post['PostedBy'] ) ) {
					$author_email = (string) $post['PostedBy'];
					$exists = $this->check_if_user_exists( $author_email );

					if ( $exists ) {
						$user_id = $exists;
					} else {
						$user_id = $this->create_user( $author_email, true );
					}
				} else {
					$user_id = get_current_user_id();
				}

				$post_content = trim( (string) $post['PostText'] );
				if( isset( $post['EmbededTag'] ) ) {
					$post_content .= html_entity_decode( (string) $post['EmbededTag'] );
				}

				$video_post = array(
					'post_type'     => 'post',
					'post_status'   => 'publish',
					'post_author'   => $user_id,
					'post_title'    => trim( (string) $post['PostTitle'] ),
					'post_content'  => $post_content,
					'post_date'     => (string) $post['DateCreated'],
					'post_modified' => (string) $post['DateModified']
				);

				if ( ! $post['IsApproved'] ) {
					$video_post['post_status'] = 'draft';
				}

				if ( $wp_id ) {
					$video_post['ID'] = $wp_id;
				}

				$wp_id = wp_insert_post( $video_post );

				// Download images found in post_content and update post_content with new images.
				$updated_post = array( 'ID' => $wp_id );
				$updated_post['post_content'] = $this->import_media( $post_content, $wp_id );

				wp_update_post( $updated_post );

				set_post_format( $wp_id, 'video' );
				update_post_meta( $wp_id, 'gmedia_import_id', $post_hash );

				// Process Blog Taxonomy Term
				if ( $blog_id ) {
					wp_set_post_terms( $wp_id, array( $blog_id ), 'category', false );
				}

				// Post Meta
				if ( isset( $post['YouTubeTitle'] ) ) {
					$youtube_title = (string) $post['YouTubeTitle'];
					update_post_meta( $wp_id, '_gmedia_youtube_title', apply_filters( 'the_title', $youtube_title ) );
				}
				if ( isset( $post['EmbededTag'] ) ) {
					$embed_tag = (string) $post['EmbededTag'];
					update_post_meta( $wp_id, '_gmedia_embed_tag', $embed_tag );
				}
				if ( isset( $post['Duration'] ) ) {
					$duration = (string) $post['Duration'];
					update_post_meta( $wp_id, '_gmedia_duration', sanitize_text_field( $duration ) );
				}

				// Featured Image
				if ( isset( $post['ImageURL'] ) ) {
					$featured_image_attrs = array();
					$featured_image_path = (string) $post['ImageURL'];

					$image = $this->import_featured_image( $featured_image_path, $wp_id, $featured_image_attrs );

					if ( ! $image ) {
						WP_CLI::log( "Error: Featured image not added!" );
					}
				} else {
					WP_CLI::log( "Error: No Featured Image Found!" );
				}
			}

			$notify->tick();
		}

		$notify->finish();
	}

	/**
	 * Import Venues as separate CPT
	 *
	 *
	 * @param $venues
	 * @param $force
	 */
	private function process_venues( $venues, $force ) {
		global $wpdb;

		$total  = count( $venues->Venue );
		$progress = new \cli\progress\Bar( "Importing $total venues", $total );

		$count = 0;
		foreach ( $venues->Venue as $venue ) {
			$venue_hash = trim( (string) $venue['VenueName'] ) . (string) $venue['DateCreated'];
			$venue_hash = md5( $venue_hash );

			// grab the existing post ID (if it exists).
			$wp_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'emmis_import_id' AND meta_value = '" . $venue_hash . "'" );

			// If we're not forcing import, skip existing posts.
			if ( ! $force && $wp_id ) {
				$progress->tick();
				continue;
			}

			// counter to clear the cache
			$count++;
			if( $count == 100 ) {
				if( class_exists('MTM_Migration_Utils') ) {
					MTM_Migration_Utils::stop_the_insanity();

				}
				sleep(15);
				$count = 0;
			}

			$tribe_venue = array(
				'post_type'     => 'tribe_venue',
				'post_status'   => 'publish',
				'post_title'    => trim( (string) $venue['VenueName'] ),
				'post_content'  => trim( (string) $venue['Directions'] ),
				'post_date'     => (string) $venue['DateCreated'],
				'post_modified' => (string) $venue['DateModified'],
			);

			if ( $wp_id ) {
				$tribe_venue['ID'] = $wp_id;
			}

			$wp_id = wp_insert_post( $tribe_venue );


			update_post_meta( $wp_id, 'gmedia_import_id', $venue_hash );


			// Post Meta
			if ( isset( $venue['PhoneNumber'] ) ) {
				update_post_meta( $wp_id, '_VenuePhone', trim( (string) $venue['PhoneNumber'] ) );
			}

			if ( isset( $venue['WebsiteURL'] ) ) {
				update_post_meta( $wp_id, '_VenueURL', trim( (string) $venue['WebsiteURL'] ) );
			}

			if ( isset( $venue['Address1'] ) ) {
				update_post_meta( $wp_id, '_VenueAddress', trim( (string) $venue['Address1'] ) );
			}

			if ( isset( $venue['City'] ) ) {
				update_post_meta( $wp_id, '_VenueCity', trim( (string) $venue['City'] ) );
			}

			if ( isset( $venue['State'] ) ) {
				update_post_meta( $wp_id, '_VenueStateProvince', trim( (string) $venue['State'] ) );
			}

			if ( isset( $venue['ZipCode'] ) ) {
				update_post_meta( $wp_id, '_VenueZip', trim( (string) $venue['ZipCode'] ) );
			}

			if ( isset( $venue['Country'] ) ) {
				update_post_meta( $wp_id, '_VenueCountry', (string) $venue['Country'] );
			} else {
				// default country is US
				update_post_meta( $wp_id, '_VenueCountry', 'United States' );
			}

			if ( isset( $venue['CrossStreet'] ) ) {
				update_post_meta( $wp_id, '_legacy_CrossStreet', trim( (string) $venue['CrossStreet'] ) );
			}

			if ( isset( $venue['VenueID'] ) ) {
				update_post_meta( $wp_id, '_legacy_VenueID', trim( (string) $venue['VenueID'] ) );
			}

			if ( isset( $venue['ParkingInformation'] ) ) {
				update_post_meta( $wp_id, '_legacy_ParkingInformation', trim( (string) $venue['ParkingInformation'] ) );
			}

			$progress->tick();
		}

		$progress->finish();
	}

	/**
	 * Import concerts from EventManager.XML
	 *
	 * @param $concerts
	 * @param $force
	 */
	private function process_concerts( $calendars, $force ) {
		global $wpdb;

		$total  = count( $calendars->Calendar );
		$notify = new \cli\progress\Bar( "Importing $total concert calendars", $total );

		$count = 0;
		foreach ( $calendars->Calendar as $calendar ) {
			$event_cat['name'] = (string) $calendar['CalendarName'];
			$event_cat['desc'] = (string) $calendar['CalendarDescription'];

			$total    = count( $calendar->Events->Event );
			$progress = new \cli\progress\Bar( "Importing $total concert events", $total );

			//$this->check_and_add_cpt('tribe_events');
			foreach ( $calendar->Events->Event as $event ) {

				$event_hash = trim( (string) $event['ConcertName'] ) . (string) $event['DateCreated'];
				$event_hash = md5( $event_hash );

				// grab the existing post ID (if it exists).
				$wp_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'gmedia_import_id' AND meta_value = '" . $event_hash . "'" );

				// If we're not forcing import, skip existing posts.
				if ( ! $force && $wp_id ) {
					$progress->tick();
					continue;
				}

				// counter to clear the cache
				$count++;
				if( $count == 100 ) {
					if( class_exists('MTM_Migration_Utils') ) {
						MTM_Migration_Utils::stop_the_insanity();

					}
					sleep(15);
					$count = 0;
				}

				$tribe_event = array(
					'post_type'     => 'tribe_events',
					'post_status'   => 'publish',
					'post_title'    => trim( (string) $event['ConcertName'] ),
					'post_content'  => trim( (string) $event['ConcertDescription'] ),
					'post_date'     => (string) $event['ConcertDate'],
					'post_modified' => (string) $event['DateModified'],
				);

				if ( $wp_id ) {
					$tribe_event['ID'] = $wp_id;
				}

				$wp_id = wp_insert_post( $tribe_event );

				// Download images found in post_content and update post_content with new images.
				if( trim( (string) $event['ConcertDescription'] ) != "" ) {
					$updated_post                 = array( 'ID' => $wp_id );
					$updated_post['post_content'] = $this->import_media( trim( (string) $event['ConcertDescription'] ), $wp_id );
					wp_update_post( $updated_post );
				}

				update_post_meta( $wp_id, 'gmedia_import_id', $event_hash );

				// Process Event Categories taxonomy
				$cat_id = $this->process_term( $event_cat, 'tribe_events_cat', 'tribe_events');

				if ( $cat_id ) {
					wp_set_post_terms( $wp_id, array( $cat_id ), 'tribe_events_cat', true );
				}

				// Featured Image
				if ( isset( $event['ConcertImageURL'] ) ) {
					$featured_image_attrs = array();
					$featured_image_path = urlencode( (string) $event['ConcertImageURL'] );

					$image = $this->import_featured_image( $featured_image_path, $wp_id, $featured_image_attrs );

					if ( ! $image ) {
						WP_CLI::warning( "Featured image not added!" );
					}
				}

				// Save concert metas

				//update_post_meta( $wp_id, '_EventAllDay', 'yes' );

				$start_date     = isset($event['ConcertDate']) ? (string) $event['ConcertDate']  : (string) $event['DateToRelease'];
				$end_date     = isset($event['ConcertDate']) ? (string) $event['ConcertDate']  : (string) $event['DateToExpire'] ;

				$start_date = explode( 'T', $start_date );
				$end_date = explode( 'T', $end_date );

				$start_date = date( "Y-m-d H:i:s", strtotime( $start_date[0] . ' ' . $start_date[1] ) );
				$end_date = date( "Y-m-d H:i:s", strtotime( $end_date[0] . ' ' . $end_date[1] ) );

				update_post_meta( $wp_id, '_EventStartDate', $start_date );
				update_post_meta( $wp_id, '_EventEndDate', $end_date );


				if( isset($event['TicketPrice']) ) {
					update_post_meta( $wp_id, '_EventCost', (string) $event['TicketPrice'] );
				}

				// meta info not used, added for legacy
				if( isset($event['AgeRestriction']) ) {
					update_post_meta( $wp_id, '_legacy_AgeRestriction', (string) $event['AgeRestriction'] );
				}

				if( isset($event['ConcertBlurb']) ) {
					update_post_meta( $wp_id, '_legacy_ConcertBlurb', (string) $event['ConcertBlurb'] );
				}

				if( isset($event['ConcertDate']) ) {
					update_post_meta( $wp_id, '_legacy_ConcertDate', (string) $event['ConcertDate'] );
				}

				if( isset($event['TicketPurchaseURL']) ) {
					update_post_meta( $wp_id, '_legacy_TicketPurchaseURL', (string) $event['TicketPurchaseURL'] );
				}

				if( isset($event['ConcertID']) ) {
					update_post_meta( $wp_id, '_legacy_ConcertID', (string) $event['ConcertID'] );
				}

				if( isset($event['Capacity']) ) {
					update_post_meta( $wp_id, '_legacy_Capacity', (string) $event['Capacity'] );
				}


				// Save Venue (which is a separate CPT)
				$concert_venue = $event->Venue;
				if ( isset( $concert_venue['VenueID'] ) ) {
					$venue_id = trim( (string) $concert_venue['VenueID'] );

					//var_dump($venue_id);

					$args = array(
						'post_type'        => 'tribe_venue',
						'meta_query' => array(
							array(
								'key' => '_legacy_VenueID',
								'value' => $venue_id,
							)
						)
					);

					$venue = get_posts( $args );

					if ( ! $venue ) {

						$venue_args = array(
							'post_type'   => 'tribe_venue',
							'post_status' => 'publish',
							'post_title'  => trim( (string) $concert_venue['VenueName'] ),
							'post_content' => trim( (string) $concert_venue['Directions'] ),
						);

						$venue_id = wp_insert_post( $venue_args );

						update_post_meta( $venue_id, '_VenueCountry', (string) $concert_venue['United States'] );

						if( isset( $concert_venue['PhoneNumber'] ) ) {
							update_post_meta( $venue_id, '_VenuePhone', (string) $concert_venue['PhoneNumber'] );
						}

						if( isset( $concert_venue['WebsiteURL'] ) ) {
							update_post_meta( $venue_id, '_VenueURL', (string) $concert_venue['WebsiteURL'] );
						}

						if( isset( $concert_venue['Address1'] ) ) {
							update_post_meta( $venue_id, '_VenueAddress', (string) $concert_venue['Address1'] );
						}

						if( isset( $concert_venue['City'] ) ) {
							update_post_meta( $venue_id, '_VenueCity', (string) $concert_venue['City'] );
						}

						if( isset( $concert_venue['State'] ) ) {
							update_post_meta( $venue_id, '_VenueStateProvince', (string) $concert_venue['State'] );
						}

						if( isset( $concert_venue['ZipCode'] ) ) {
							update_post_meta( $venue_id, '_VenueZip', (string) $concert_venue['ZipCode'] );
						}

						if( isset( $concert_venue['CrossStreet'] ) ) {
							update_post_meta( $venue_id, '_legacy_CrossStreet', (string) $concert_venue['CrossStreet'] );
						}

						if( isset( $concert_venue['ParkingInformation'] ) ) {
							update_post_meta( $venue_id, '_legacy_ParkingInformation', (string) $concert_venue['ParkingInformation'] );
						}

						if( isset( $concert_venue['Capacity'] ) ) {
							update_post_meta( $venue_id, '_legacy_Capacity', (string) $concert_venue['Capacity'] );
						}

						if( isset( $concert_venue['VenueID'] ) ) {
							update_post_meta( $venue_id, '_legacy_VenueID', (string) $concert_venue['VenueID'] );
						}

					} else {
						$venue_id = $venue[0]->ID;
					}

					if ( $venue_id ) {
						update_post_meta( $wp_id, '_EventVenueID', $venue_id );
					}
				}


				// Add redirects
				if( class_exists('CMM_Legacy_Redirects') ) {
					$redirectid_id = CMM_Legacy_Redirects::add_redirect( trailingslashit( $this->site_url ) . 'music/concerts/Details.aspx?ConcertID=' . $event['ConcertID'] , $wp_id );
				} else {
					WP_CLI::warning('Class for adding redircet is mssing!');
				}

				$progress->tick();
			}
			$progress->finish();
			$notify->tick();
		}
		$notify->finish();
	}

	/*
	 * Import an authors avatar
	 *
	 * @param string $filepath File path of image.
	 * @return string
	 */

	private function process_podcasts( $podcasts, $force ) {
		global $wpdb;

		$total  = count( $podcasts->Channel );
		$notify = new \cli\progress\Bar( "Importing $total podcast channels", $total );

		$count = 0;
		foreach ( $podcasts->Channel as $single_channel ) {

			$channel_hash = trim( (string) $single_channel['ChannelTitle'] ) . (string) $single_channel['UTCDateCreated'];
			$channel_hash = md5( $channel_hash );

				// grab the existing post ID (if it exists).
				$wp_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'gmedia_import_id' AND meta_value = '" . $channel_hash . "'" );

				// If we're not forcing import, skip existing posts.
				if ( ! $force && $wp_id ) {
					//$progress->tick();
					continue;
				}

				// counter to clear the cache
				$count++;
				if( $count == 10 ) {
					if( class_exists('MTM_Migration_Utils') ) {
						MTM_Migration_Utils::stop_the_insanity();
					}
					$count = 0;
				}

				$podcast = array(
					'post_type'     => 'podcast',
					'post_status'   => 'publish',
					'post_title'    => trim( (string) $single_channel['ChannelTitle'] ),
					'post_content'  => trim( (string) $single_channel['ChannelDescription'] ),
					'post_date'     => (string) $single_channel['UTCDateCreated'],
					'post_modified' => (string) $single_channel['UTCDateModified'],
				);

				if ( $wp_id ) {
					$podcast['ID'] = $wp_id;
				}

				if( isset($single_channel['IsActive']) && (string) $single_channel['IsActive'] != 1 ) {
					$podcast['post_status'] = 'draft';
				}

				$wp_id = wp_insert_post( $podcast );

				if( isset( $single_channel['ChannelDescription'] )) {
					// Download images found in post_content and update post_content with new images.
					$updated_post                 = array( 'ID' => $wp_id );
					$updated_post['post_content'] = $this->import_media( trim( (string) $single_channel['ChannelDescription'] ), $wp_id );
					wp_update_post( $updated_post );
				}


				update_post_meta( $wp_id, 'gmedia_import_id', $channel_hash );


				if( isset($single_channel['Podcast_Image'])  ) {
					// import images here
					if( (string) $single_channel['Podcast_Image'] != "") {
						$featured_image_attrs = array();
						$featured_image_path  = '/' . (string) $single_channel['Podcast_Image'];
						$this->import_featured_image( $featured_image_path, $wp_id, $featured_image_attrs );
					}
				}

				// Save metas
				if( isset($single_channel['WebmasterEmailAddress']) ) {
					update_post_meta( $wp_id, '_legacy_WebmasterEmailAddress', (string) $single_channel['WebmasterEmailAddress'] );
				}

				if( isset($single_channel['iTunes_Author']) ) {
					update_post_meta( $wp_id, '_legacy_iTunes_Author', (string) $single_channel['iTunes_Author'] );
				}

				if( isset($single_channel['iTunes_Image']) ) {
					update_post_meta( $wp_id, '_legacy_iTunes_Image', (string) $single_channel['iTunes_Image'] );
				}

				if( isset($single_channel['iTunes_Explicit']) ) {
					update_post_meta( $wp_id, '_legacy_iTunes_Explicit', (string) $single_channel['iTunes_Explicit'] );
				}

				if( isset($single_channel['iTunes_Keywords']) ) {
					update_post_meta( $wp_id, '_legacy_iTunes_Keywords', (string) $single_channel['iTunes_Keywords'] );
				}

				if( isset($single_channel['iTunes_NewFeedURL']) ) {
					update_post_meta( $wp_id, '_legacy_iTunes_NewFeedURL', (string) $single_channel['iTunes_NewFeedURL'] );
				}

				if( isset($single_channel['iTunes_Subtitle']) ) {
					update_post_meta( $wp_id, '_legacy_iTunes_Subtitle', (string) $single_channel['iTunes_Subtitle'] );
				}

				if( isset($single_channel['iTunesCategory']) ) {
					update_post_meta( $wp_id, '_legacy_iTunesCategory', (string) $single_channel['iTunesCategory'] );
				}

				if( isset($single_channel['DisplayPosition']) ) {
					update_post_meta( $wp_id, '_legacy_DisplayPosition', (string) $single_channel['DisplayPosition'] );
				}

				//gmp_audio_file_meta_key
				foreach ( $podcasts->Channel->Item as $podcast_item ) {
					$episode = array(
						'post_type'     => 'episode',
						'post_status'   => 'publish',
						'post_title'    => trim( (string) $podcast_item['ItemTitle'] ),
						'post_content'  => trim( (string) $podcast_item['ItemDescription'] ),
						'post_date'     => (string) $single_channel['UTCDateCreated'],
						'post_modified' => (string) $single_channel['UTCDateLastModified'],
						'post_parent'   => $wp_id
					);

					$episode_id = wp_insert_post( $episode );

					if( isset( $podcast_item['MediaFilename'] ) && $episode_id ) {
						$audio_path = str_ireplace( '\Media\\', '', (string) $podcast_item['MediaFilename'] );
						$media_file_id = $this->import_music_files( $wp_id, $audio_path );
						$url = wp_get_attachment_url( $media_file_id );
						$shortcode = '[audio mp3="' . $url . '"]';
						$episode = array(
							'post_content'  => $shortcode . '<br/>' . trim( (string) $podcast_item['ItemDescription'] ),
							'ID'            => $episode_id
						);
						wp_update_post( $episode );
					}
				}


			//$notify->tick();
			}

		//$notify->finish();
	}

	private function process_surveys( $surveys, $force ) {
		global $wpdb;

		$map_formbuilder = array(
			'Buttons'       =>  'radio',
			'Checkboxes'    =>  'checkboxes',
			'Text Box'      =>  'text',
			'Text Area'     =>  'text',
			'Calendar'      =>  'date',
			'Dropdown'      =>  'dropdown',
			'Label'         =>  'text'
		);

		$total  = count( $surveys->Survey );
		$notify = new \cli\progress\Bar( "Importing $total surveys", $total );

		$count = 0;
		foreach ( $surveys->Survey as $survey ) {
			$survey_id = (string) $survey['SurveyID'];

			$total  = count( $survey->Responses->Response );

			// grab the existing post ID (if it exists).
			$wp_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'gmedia_import_id' AND meta_value = '" . $survey_id . "'" );

			// If we're not forcing import, skip existing posts.
			if ( ! $force && $wp_id ) {
				$notify->tick();
				continue;
			}

			// counter to clear the cache
			$count++;
			if( $count == 10 ) {
				if( class_exists('MTM_Migration_Utils') ) {
					MTM_Migration_Utils::stop_the_insanity();
				}
				sleep(15);
				$count = 0;
			}

			$survey_args = array(
				'post_type'     => 'survey',
				'post_status'   => 'publish',
				'post_title'    => trim( (string) $survey['SurveyTitle'] ),
				'post_content'  => trim( (string) $survey['SurveyDescription'] ),
				'post_date'     => (string) $survey['UTCDateCreated'],
				'post_modified' => (string) $survey['UTCDateModified'],
			);


			if ( $wp_id ) {
				$survey_args['ID'] = $wp_id;
			}

			$wp_id = wp_insert_post( $survey_args );

			// Download images found in post_content and update post_content with new images.
			$updated_post = array( 'ID' => $wp_id );
			$updated_post['post_content'] = $this->import_media( trim( (string) $survey['SurveyDescription'] ), $wp_id );
			wp_update_post( $updated_post );

			update_post_meta( $wp_id, 'gmedia_import_id', $survey_id );
			update_post_meta( $wp_id, '_legacy_SurveyID', $survey_id );

			if( isset( $survey['ContestID'] ) )  {
				update_post_meta( $wp_id, '_legacy_Parent_ContestID', (int) $survey['ContestID'] );
			}

			// register hidden post type for qestions
			//$this->check_and_add_cpt('question');
			$form = array();
			foreach( $survey->Questions->Question as $question ) {

				$question_form = array();
				/*$question_args = array(
					'post_status'           => 'publish',
					'post_type'             => 'question',
					'post_parent'           => $wp_id,
					'menu_order'            => $menu_order,
					'post_title'            => isset( $question['FieldLabel']) ? (string) $question['FieldLabel'] : (string) $question['QuestionText'],
				);

				$question_id = wp_insert_post( $question_args );*/

				if( isset( $question['FieldLabel'] ) ) {
					$question_form['label'] = sanitize_text_field( (string) $question['FieldLabel'] );
					if( isset($question['QuestionText']) ) {
						//update_post_meta( $question_id, '_legacy_QuestionText', esc_html( (string) $question['QuestionText'] ) );
						$question_form['field_options']['description'] = esc_html( (string) $question['QuestionText'] );
					}
				} elseif( isset($question['QuestionText']) ) {
					$question_form['label'] = esc_html( (string) $question['QuestionText'] );
				}

				if( isset($question['InputStyle']) ) {
					//update_post_meta( $question_id, '_legacy_InputStyle', (string) $question['InputStyle'] );
					$question_form['field_type'] = $map_formbuilder[(string) $question['InputStyle']];
				}

				//update_post_meta( $question_id, 'parent_survey_old_id', $survey_id );
				if( isset($question['isRequired']) ) {
					//update_post_meta( $question_id, '_legacy_isRequired', (string) $question['isRequired'] );
					$question_form['required'] = (string) $question['isRequired'];
				}

				if( isset($question['SubQuestionID']) ) {
					$question_form['cid'] = absint( (string) $question['SubQuestionID'] );
				}

				foreach( $question->Option as $option ) {
					$question_form['field_options']['options'][] = array(
						'label'     =>  sanitize_text_field( (string) $option['Value'] ),
						'checked'   =>  sanitize_text_field( (string) $option['isCheckedByDefault'] )
					);
				}

				array_push( $form, $question_form );

			} /** finished importeding survey questions */

			$form_encoded = json_encode( $form );
			update_post_meta( $wp_id, 'survey_embedded_form', $form_encoded );

			if( isset( $survey->Responses->Response ) ) {

				foreach ( $survey->Responses->Response as $response ) {

					$response_values = array();

					foreach ( $response->Answer as $answer ) {

						// get parent question id
						$survey_form = get_post_meta( $wp_id, 'survey_embedded_form', true );
						$survey_form = json_decode( $survey_form );
						//$legacy_questionid = get_post_meta( $question_id, '_legacy_QuestionID_' . $question_id , true );

						//$survey_form['cid'] == (string) $answer['SubQuestionID']

						foreach ( $survey_form as $single_response ) {
							if ( $single_response->cid == (string) $answer['SubQuestionID'] ) {
								$response_values[ 'form_field_' . $single_response->cid ] = (string) $answer['AnswerValue'];
							}
						}
					}

					$response_args = array(
						'post_status' => 'publish',
						'post_type'   => 'survey_response',
						'post_parent' => $wp_id,
						'post_title'  => (string) $response['EmailAddress'],
					);

					$user_survey_id = (string) $response['UserSurveyID'];

					$response_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'gmedia_import_id' AND meta_value = '" . $user_survey_id . "'" );

					if ( ! $force && $response_id ) {
						continue;
					}

					$response_id = wp_insert_post( $response_args );

					if ( $response_id ) {

						if ( isset( $response['UTCCompletionDate'] ) ) {
							update_post_meta( $response_id, '_legacy_UTCCompletionDate', (string) $response['UTCCompletionDate'] );
						}

						if ( ! empty( $response_values ) ) {
							update_post_meta( $response_id, 'entry_reference', json_encode( $response_values ) );
						}

						update_post_meta( $response_id, 'gmedia_import_id', $user_survey_id );

						update_post_meta( $response_id, '_legacy_survey_MemberID', (string) $response['MemberID'] );
						update_post_meta( $response_id, '_legacy_survey_stand_alone', (string) $response['IsResponseFromStandAloneSurvey'] );
					}
				}
			}
			$notify->tick();
		}

		$notify->finish();
	}

	/**
	 * Import contest from XML
	 *
	 * @param $contests
	 * @param $force
	 */
	private function process_contests( $contests, $force ) {
		global $wpdb;

		$total  = count( $contests->Contest );
		$notify = new \cli\progress\Bar( "Importing $total contests", $total );

		$count = 0;
		foreach ( $contests->Contest as $contest ) {
			$contest_hash = trim( (string) $contest['Title'] ) . (string) $contest['DateCreated'];
			$contest_hash = md5( $contest_hash );

			$total  = count( $contest->Entries->Entry );
			$progress = new \cli\progress\Bar( "Importing $total Entries", $total );

			// grab the existing post ID (if it exists).
			$wp_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'gmedia_import_id' AND meta_value = '" . $contest_hash . "'" );

			// If we're not forcing import, skip existing posts.
			if ( ! $force && $wp_id ) {
				//$notify->tick();
				continue;
			}

			// counter to clear the cache
			$count++;
			if( $count == 100 ) {
				if( class_exists('MTM_Migration_Utils') ) {
					MTM_Migration_Utils::stop_the_insanity();
				}
				// sleep for 10 seconds
				sleep(15);
				$count = 0;
			}

			$contest_title = trim( (string) $contest['ContestName'] );
			preg_match( '/(\[\s*(\S+)\s*\])?(.*)/', $contest_title, $contest_title_matches );

			if( !empty( $contest_title_matches ) ) {
				$contest_title = $contest_title_matches[3];
			}

			$contest_args = array(
				'post_type'     => 'contest',
				'post_status'   => 'publish',
				'post_title'    => $contest_title,
				'post_content'  => trim( (string) $contest->ContestText ),
				'post_date'     => (string) $contest['DateCreated'],
			);

			if ( $wp_id ) {
				$contest_args['ID'] = $wp_id;
			}

			$wp_id = wp_insert_post( $contest_args );

			// Download images found in post_content and update post_content with new images.
			//$updated_post = array( 'ID' => $wp_id );
			/*$updated_post['post_content'] = $this->import_media( trim( (string) $contest->ContestText ), $wp_id );
			*/
			//wp_update_post( $updated_post );

			update_post_meta( $wp_id, 'gmedia_import_id', $contest_hash );

			// process metas
			if( isset($contest['StartDate']) ) {
				$start_date = strtotime( (string) $contest['StartDate'] );
				update_post_meta($wp_id, 'contest-start', $start_date);
			}

			if( isset($contest['EndDate']) ) {
				$end_date = strtotime( (string) $contest['EndDate'] );
				update_post_meta($wp_id, 'contest-end', $end_date);
			}

			if( isset($contest['EntryRestriction']) ) {
				update_post_meta($wp_id, '_legacy_EntryRestriction', (string) $contest['EntryRestriction'] );
			}

			if( isset($contest['GiveawayMedium']) ) {
				update_post_meta($wp_id, '_legacy_GiveawayMedium', (string) $contest['GiveawayMedium']);
			}

			if( isset($contest->ConfirmationText['ConfirmationHeader']) ) {
				update_post_meta( $wp_id, 'form-thankyou', (string) $contest->ConfirmationText['ConfirmationHeader'] . ' ' . (string) $contest->ConfirmationText );
			}

			if( isset($contest->ConfirmationText['ContestText']) ) {
				update_post_meta( $wp_id, 'prizes-desc', (string) $contest->ConfirmationText['ContestHeader'] . '</br>' . (string) $contest->ConfirmationText['ContestText'] );
			}

			if( isset( $contest->PromotionalVehicle['Type'] ) ) {
				update_post_meta($wp_id, '_legacy_PromotionalVehicle', (string) $contest->PromotionalVehicle['Type']);
			}

			// process terms

			if( !empty( $contest_title_matches ) && $contest_title_matches[1] ) {
				$contest_term['name'] = $contest_title_matches[2];
				$contest_term['desc'] = "";

				$contest_cat_id = $this->process_term( $contest_term, 'contest_type', 'contest' );

				if ( $contest_cat_id ) {
					wp_set_post_terms( $wp_id, array( $contest_cat_id ), 'contest_type', false );
				}
			}

			if( isset($contest['ContestCategory'])  ) {
				//form_login_restriction
				if( (string) $contest['ContestCategory'] == 'Non Club Contest' ) {
					update_post_meta( $wp_id, 'form_login_restriction', '' );
				} else {
					update_post_meta( $wp_id, 'form_login_restriction', 'logged-in' );
				}
				update_post_meta( $wp_id, '_legacy_ContestCategory', (string) $contest['ContestCategory'] );
			}

			if( isset( $contest['ContestID'] ) ) {
				update_post_meta( $wp_id, '_legacy_ContestID', (int) $contest['ContestID'] );
			}

			if( isset( $contest['SurveyID'] ) ) {
				$orig_survey_id = (int) $contest['SurveyID'];
				$form_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = '_legacy_SurveyID' AND meta_value = '" . $orig_survey_id . "'" );
				if( $form_id ) {
					$form = get_post_meta( $form_id, 'survey_embedded_form', true );
					update_post_meta( $form_id, 'gmedia_must_delete', 'true' );
					update_post_meta( $wp_id, 'embedded_form', $form );
				}
			}

			if( isset( $contest->Entries->Entry ) ) {

				foreach ( $contest->Entries->Entry as $entry ) {

					$entry_name = (string) $entry['ContestEntryID'];
					if( isset( $entry['MemberID'] ) ) {
						$entry_name = (string) $entry['MemberID'];
					}

					$entry_args = array(
						'post_status' => 'publish',
						'post_type'   => 'contest_entry',
						'post_parent' => $wp_id,
						'post_title'  => $entry_name,
						'post_date'   => (string) $entry['UTCEntryDate'],
					);

					$entry_id = wp_insert_post( $entry_args );

					if ( $entry_id ) {

						$response_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = '_legacy_survey_MemberID' AND meta_value = '" . $entry_name . "'" );

						$submitted_values = array();

						if( $response_id ) {
							update_post_meta( $response_id, 'gmedia_must_delete', 'true' );
							$submitted_values = get_post_meta( $response_id, 'entry_reference', true );
						}

						/*$contest_entry = GreaterMediaContestEntryEmbeddedForm::create_for_data(
							$wp_id,
							$entry_name,
							$entry_name,
							GreaterMediaContestEntry::ENTRY_SOURCE_EMBEDDED_FORM,
							$submitted_values
						);*/


						update_post_meta( $entry_id, 'entrant_name', $entry_name );
						update_post_meta( $entry_id, 'entrant_reference', $entry_name );
						update_post_meta( $entry_id, 'entry_source', GreaterMediaContestEntry::ENTRY_SOURCE_EMBEDDED_FORM );
						update_post_meta( $entry_id, 'entry_reference', $submitted_values );

						//do_action( 'greatermedia_contest_entry_save', $contest_entry );

						if ( isset( $entry['isWinner'] ) ) {
							update_post_meta( $entry_id, '_legacy_isWinner', (string) $entry['isWinner'] );
						}

						if ( isset( $entry['ContestEntryID'] ) ) {
							update_post_meta( $entry_id, '_legacy_ContestEntryID', (string) $entry['ContestEntryID'] );
						}

					}

				}
			}

			//$progress->finish();
			//$notify->tick();
		}

		//$notify->finish();
		//$this->clean_up();
	}

	private function process_schedules( $schedules, $force ) {
		global $wpdb;

		$total  = count( $schedules->OnAirNowItem );
		$notify = new \cli\progress\Bar( "Importing $total on air items", $total );

		$count = 0;

		foreach ( $schedules->OnAirNowItem as $scheduled_item ) {
			$scheduled_item_hash = trim( (string) $scheduled_item['TitleText'] ) . (string) $scheduled_item['DateModified'];
			$scheduled_item_hash = md5( $scheduled_item_hash );

			// grab the existing post ID (if it exists).
			$wp_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'gmedia_import_id' AND meta_value = '" . $scheduled_item_hash . "'" );

			// If we're not forcing import, skip existing posts.
			if ( ! $force && $wp_id ) {
				$notify->tick();
				continue;
			}

			// counter to clear the cache
			$count++;
			if( $count == 100 ) {
				if( class_exists('MTM_Migration_Utils') ) {
					MTM_Migration_Utils::stop_the_insanity();
				}
				sleep(15);
				$count = 0;
			}

			$show_title = trim( (string) $scheduled_item['TitleText'] );

			if( $show_title == '' ) {
				$show_title = 'No Title';
			}

			$show_title = str_ireplace( 'now on air: ', '', $show_title );

			$scheduled_item_args = array(
				'post_type'     => 'show',
				'post_status'   => 'publish',
				'post_title'    => $show_title,
				'post_date'     => (string) $scheduled_item['DateModified'],
			);

			if ( $wp_id ) {
				$scheduled_item_args['ID'] = $wp_id;
			}

			$wp_id = wp_insert_post( $scheduled_item_args );

			update_post_meta( $wp_id, 'gmedia_import_id', $scheduled_item_hash );

			// process metas
			if( isset($scheduled_item['LinkURL']) ) {
				update_post_meta( $wp_id, '_legacy_LinkURL', (string) $scheduled_item['LinkURL'] );
				CMM_Legacy_Redirects::add_redirect( (string) $scheduled_item['LinkURL'], $wp_id );
			}

			foreach ( $scheduled_item->Schedules->Schedule as $schedule ) {
				if( post_type_exists( 'show-episode' ) ) {
					$post_date_gmt = 0;
					$interval = 0;
					$weekdays = array( strtolower( (string) $schedule['WeekdayName'] ) );

					if( $weekdays[0] == "weekdays" ) {
						$weekdays = array(
							'monday',
							'tuesday',
							'wednesday',
							'thursday',
							'Friday',
						);
					}

					foreach ( $weekdays as $weekday ) {
						if( isset($schedule['StartTime'] ) ) {
							$start_time = (string) $schedule['StartTime'];
							if( $start_time >= 1000 ) {
								$start_time = substr( $start_time, 0, 2) . ':' . substr( $start_time, 2, 2);
							} elseif ( $start_time == 0 ) {
								$start_time = "00:00";
							} else {
								$start_time = substr( $start_time, 0, 1) . ':' . substr( $start_time, 1, 2);
							}
							$next_weekday = strtotime( "next $weekday" );
							$post_date_gmt = strtotime( date( 'Y-m-d', $next_weekday) . ' ' . $start_time );
						}
						if( isset($schedule['EndTime'] ) ) {
							$end_time = (string) $schedule['EndTime'];
							if( $end_time >= 1000 ) {
								$end_time = substr( $end_time, 0, 2) . ':' . substr( $end_time, 2, 2);
							} elseif ( $end_time == 0 ) {
								$end_time = "00:00";
							} else {
								$end_time = substr( $end_time, 0, 1) . ':' . substr( $end_time, 1, 2);
							}
						}
						$interval = strtotime( $end_time ) - strtotime( $start_time );

						$args = array(
							'post_title'    => $show_title,
							'post_type'     => ShowsCPT::EPISODE_CPT,
							'post_status'   => 'future',
							'post_date'     => date( DATE_ISO8601, $post_date_gmt ),
							'post_date_gmt' => date( DATE_ISO8601, $post_date_gmt ),
							'post_parent'   => $wp_id,
							'ping_status'   => 1,
							'menu_order'    => $interval,
						);

						$episode_id = wp_insert_post( $args );
					}

				}
				/**if( isset($schedule['StartTime'] ) ) {
					$start_time = (string) $schedule['StartTime'];
					if( $start_time >= 1000 ) {
						$start_time = substr( $start_time, 0, 2) . ':' . substr( $start_time, 2, 2);
					} elseif ( $start_time == 0 ) {
						$start_time = "00:00";
					} else {
						$start_time = substr( $start_time, 0, 1) . ':' . substr( $start_time, 1, 2);
					}
					update_post_meta( $wp_id, 'start_time_' . (string) $schedule['WeekdayName'], $start_time );
				}
				if( isset($schedule['EndTime'] ) ) {
					$end_time = (string) $schedule['EndTime'];
					if( $end_time >= 1000 ) {
						$end_time = substr( $end_time, 0, 2) . ':' . substr( $end_time, 2, 2);
					} elseif ( $end_time == 0 ) {
						$end_time = "00:00";
					} else {
						$end_time = substr( $end_time, 0, 1) . ':' . substr( $end_time, 1, 2);
					}
					update_post_meta( $wp_id, 'end_time_' . (string) $schedule['WeekdayName'], $end_time );
				}
				if( isset($schedule['WeekdayName'] ) ) {
					//update_post_meta( $wp_id, 'weekday', (string) $schedule['WeekdayName'] );
				}*/
			}

			$notify->tick();
		}

		$notify->finish();
	}

	/**
	 * Return ID of term.
	 *
	 * @var string $term Current term name.
	 * @var string $taxonomy Taxonomy to use.
	 * @return int|bool
	 */
	private function get_parent_term( $term, $taxonomy ) {
		$term_name = sanitize_term_field( 'name', $term, 0, $taxonomy, 'db' );

		if ( $term = term_exists( $term_name, $taxonomy ) ) {
			return (int) $term['term_id'];
		}

		$term = wp_insert_term( $term_name, $taxonomy );

		if ( is_wp_error( $term ) ) {
			WP_CLI::log( "Error: Term $term_name not imported." );
			return false;
		}

		return (int) $term['term_id'];
	}

	private function parse_taxonomy_mapping() {

		$config_file_handle = fopen( $this->config_file, "r");
		while ( ( $data = fgetcsv( $config_file_handle, 1000, ",")) !== FALSE) {
			$config_file[] = $data;
		}
		fclose($config_file_handle);

		foreach ( $config_file as $config_string ) {
			$marketron_taxonomy = html_entity_decode( $config_string[2], ENT_QUOTES | ENT_HTML5 );
			$new_term = $config_string[4];
			$taxonomy_map[ $marketron_taxonomy ]['term'] = $new_term;
			$taxonomy_map[ $marketron_taxonomy ]['taxonomy'] = $config_string[3];
		}

		return $taxonomy_map;
	}

}

WP_CLI::add_command( 'gmedia-migration', 'GMedia_Migration' );


class Post_Term extends \WP_CLI_Command{
	/**
	 * Add a term to a post
	 *
	 * @synopsis <id> <taxonomy> [--slug=<slug>] [--term-id=<term-id>] [--replace]
	 */
	public function add( $args, $assoc_args ){
		if ( empty($assoc_args['slug']) && empty($assoc_args['term-id']) ) {
			WP_CLI::error("You must specify the term slug or the term_id");
		}
		if ( ! empty($assoc_args['term-id']) ) {
			$term = \absint( $assoc_args['term-id'] );
		} else {
			$term = $assoc_args['slug'];
		}
		list( $post_id, $taxonomy ) = $args;
		if ( !isset($post_id) ){
			WP_CLI::error("You must specify the post ID");
		}
		if ( !isset($taxonomy) ){
			WP_CLI::error("You must specify the term taxonomy");
		}
		// if replace is set, then append is false
		$append = !! empty( $assoc_args['replace'] );
		$set_term = \wp_set_object_terms( $post_id, $term, $taxonomy, $append );
		if ( \is_wp_error($success) ) {
			WP_CLI::warning( $success->get_error_mesage() );
		} else {
			WP_CLI::success("$taxonomy $term successfully added to post ID $post_id");
		}
	}
	/**
	 * Remove one term from the post associations
	 *
	 * @synopsis <id> <taxonomy> [--slug=<slug>] [--term-id=<term-id>]
	 */
	public function remove( $args, $assoc_args ){
		if ( empty($assoc_args['slug']) && empty($assoc_args['term-id']) ) {
			WP_CLI::error("You must specify the term slug or the term_id");
			return;
		}
		if ( ! empty($assoc_args['term-id']) ) {
			$term  = \absint( $assoc_args['term-id'] );
			$field = 'term_id';
		} else {
			$term  = $assoc_args['slug'];
			$field = 'slug';
		}
		list( $post_id, $taxonomy ) = $args;
		if ( !isset($post_id) ){
			WP_CLI::error("You must specify the post ID");
			return;
		}
		if ( !isset($taxonomy) ){
			WP_CLI::error("You must specify the term taxonomy");
			return;
		}
		$existing_terms = \wp_get_object_terms( $post_id, $taxonomy );
		if ( \is_wp_error($existing_terms) ) {
			WP_CLI::warning( $existing_terms->get_error_mesage() );
			return;
		}
		$terms_count = count( $existing_terms );
		$new_terms = array();
		foreach ( $existing_terms as $e_term ) {
			if ( $term != $e_term->$field ) {
				$new_terms[] = (int)$e_term->term_id;
			}
		}
		switch ( $terms_count - count( $new_terms ) ){
			case 1:
				// exactly one term less
				$update = \wp_set_object_terms( $post_id, $new_terms, $taxonomy, false );
				if ( \is_wp_error($update) ) {
					WP_CLI::error( $update->get_error_message() );
				} elseif ( is_string($update) ) {
					WP_CLI::error("$taxonomy $update could not be added to the post");
				} else {
					WP_CLI::success("$taxonomy $term removed from post $post_id");
				}
				return;
				break;
			case 0:
				// no terms less
				WP_CLI::warning("$taxonomy $term was not associated to post $post_id");
				return;
				break;
			default:
				// weird stuff
				WP_CLI::warning("Something weird happened... or didn't");
				break;
		}
	}
	/**
	 * Unlink the object from the taxonomy
	 *
	 * @synopsis <id> <taxonomy>
	 */
	public function delete( $args, $assoc_args ){
		list( $post_id, $taxonomy ) = $args;
		$post_id = absint( $post_id );
		$post = \get_post($post_id);
		WP_CLI::confirm( "Sure you want to remove all $taxonomy terms from \"{$post->post_title}\" [ID: $post_id]?");
		\wp_delete_object_term_relationships( absint($post_id), $taxonomy );
		WP_CLI::success("All $taxonomy terms where removed from post $post_id");
	}
	/**
	 * Get terms associated to a post
	 *
	 * @synopsis <id> <taxonomy> [--format=<format>]
	 */
	public function get( $args, $assoc_args ){
		list( $post_id, $taxonomy ) = $args;
		$terms = \wp_get_object_terms( absint($post_id), $taxonomy );
		if ( \is_wp_error( $terms ) ) {
			WP_CLI::warning( $terms->get_error_message() );
		} else {
			WP_CLI::print_value( $terms, $assoc_args );
		}
	}

	public function clean_up() {
		global $wpdb;
		$wp_id = $wpdb->get_var( $sql = "SELECT post_id from {$wpdb->postmeta} WHERE meta_key = 'gmedia_must_delete' AND meta_value = 'true'" );
		WP_CLI::log( $wp_id );
	}
}
WP_CLI::add_command( 'post-term', 'Post_Term' );

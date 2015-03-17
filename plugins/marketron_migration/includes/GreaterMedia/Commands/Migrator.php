<?php

namespace GreaterMedia\Commands;

use GreaterMedia\MigrationConfig;
use GreaterMedia\Utils\Downloader;
use Marketron\MappingCollection;
use Marketron\Tools\Factory as ToolFactory;
use GreaterMedia\Import\Factory as ImporterFactory;
use Marketron\XMLExtractor;
use WordPress\Tables\Users;
use WordPress\Tables\Factory as TableFactory;
use WordPress\Entities\Factory as EntityFactory;
use WordPress\Utils\MediaSideLoader;

class Migrator {

	public $default_opts = array(
		'config_file'              => 'wmgk.json',
		'marketron_export'    => 'wmgk.zip',
		'tool'                => 'feed',
		'fresh'               => false,
		'migration_cache_dir' => 'migration_cache',
		'mapping_file'        => 'wmgk_mapping.csv',
	);

	public $default_tools = array(
		'feed', 'blog', 'venue', 'event_calendar',
		'photo_album_v2', 'showcase'
	);

	public $tool_factory;
	public $importer_factory;
	public $downloader;
	public $opts;
	public $entity_factory;
	public $table_factory;

	public $config;
	public $mappings;
	public $fresh;
	public $initialized = false;

	function _test_downloader( $args, $opts ) {
		$downloader = new Downloader( 'migration_cache/downloads' );
		$tmp_file = $downloader->download( 'https://www.google.co.in/images/srpr/logo10w.png' );
		\WP_CLI::log( 'First tmp file = ' . $tmp_file );

		$tmp_file = $downloader->download( 'https://www.google.co.in/images/srpr/logo10w.png' );
		\WP_CLI::log( 'Second tmp file = ' . $tmp_file );
	}

	function test_media_downloader( $args, $opts ) {
		$downloader = new Downloader(
			'migration_cache/downloads',
			'migration_cache/media'
		);
		$tmp_file = $downloader->download( 'http://media.wmgk.com/' . urlencode('Blogs/1001280/Watch The Mummers Strut (More Than A Tradition)mastered.mp3'  ) );
		\WP_CLI::log( 'First tmp file = ' . $tmp_file );

		$tmp_file = $downloader->download( 'http://media.wmgk.com/' . urlencode('Blogs/1001280/Watch The Mummers Strut (More Than A Tradition)mastered.mp3'  ) );
		\WP_CLI::log( 'Second tmp file = ' . $tmp_file );

	}

	function test_mapping( $args, $opts ) {
		$this->mapping_collection = new MappingCollection();
		$this->mapping_collection->load( 'wmgk_mapping.csv' );
	}

	function build_actions_json( $args, $opts ) {
		$user_ids = $opts['user_ids'];
		$output   = $opts['output'];

		$user_ids     = file( $user_ids );
		$records = array();

		foreach ( $user_ids as $user_id ) {
			$user_id = trim( $user_id );
			$actions_count = rand( 5, 50 );

			for ( $i = 0; $i < $actions_count; $i++ ) {
				$record = array(
					'UID' => $user_id,
					'data' => array(
						'actions' => array(
							array(
								'actionType' => 'action:contest',
								'actionID' => strval( rand( 50000, 100000 ) ),
								'actionData' => array(
									array(
										'name' => 'rc' . rand( 1, 10 ),
										'value_t' => 'lorem ispum dolor sit amet ' . rand( 1000, 100000 ),
									),
									array(
										'name' => 'timestamp',
										'value_i' => strtotime( 'now' ),
									),
								),
							),
						),
					)
				);

				$records[] = $record;
			}
		}

		$json = json_encode( $records, JSON_PRETTY_PRINT );
		file_put_contents( $output, $json );
		$count = count( $records );

		\WP_CLI::success( "Actions( $count ) JSON generated successfully." );
	}

	function migrate( $args, $opts ) {
		$opts          = wp_parse_args( $opts, $this->default_opts );
		$opts['fresh'] = filter_var( $opts['fresh'], FILTER_VALIDATE_BOOLEAN );

		$migration_cache_dir   = $opts['migration_cache_dir'];
		$marketron_export      = realpath( $opts['marketron_export'] );
		$marketron_export_dest = $migration_cache_dir . '/marketron_export';
		$fresh                 = $opts['fresh'];
		$config_file           = $opts['config_file'];
		$tool                  = $opts['tool'];

		$config_loader = new MigrationConfig();
		$this->opts    = $opts;
		$this->config  = $config_loader->load( $config_file );

		$mapping_file = $opts['mapping_file'];
		$this->mapping_collection = new MappingCollection();
		$this->mapping_collection->load( $mapping_file );

		$this->create_migration_cache_dir( $migration_cache_dir, $fresh );
		$this->extract( $marketron_export, $marketron_export_dest, $fresh );
		$this->format( $marketron_export_dest, $fresh );

		if ( $tool === 'all' ) {
			$tools_to_load = $this->default_tools;
		} else {
			$tools_to_load = array( $tool );
		}

		$this->downloader = new Downloader(
			$migration_cache_dir . '/downloads',
			$migration_cache_dir . '/media'
		);

		$this->tool_factory     = new ToolFactory( $this );
		$this->importer_factory = new ImporterFactory( $this );

		$this->load_tools( $tools_to_load );
		$this->import_tools( $tools_to_load );
	}

	private function load_tools( $tools_to_load ) {
		foreach ( $tools_to_load as $tool_name ) {
			$tool = $this->tool_factory->build( $tool_name );
			$tool->load();
		}
	}

	private function import_tools( $tools_to_import ) {
		foreach ( $tools_to_import as $tool_name ) {
			$tool = $this->importer_factory->build( $tool_name );
			$tool->import();
		}
	}

	private function create_migration_cache_dir( $migration_cache_dir, $fresh = false ) {
		$migration_cache_dir = $migration_cache_dir;
		$has_cache_dir       = is_dir( $migration_cache_dir );

		if ( ! $has_cache_dir ) {
			\WP_CLI::log( 'Creating cache dir ...' );
			system( "mkdir -p \"{$migration_cache_dir}\"" );
			system( "mkdir -p \"{$migration_cache_dir}\"/marketron_export" );
			system( "mkdir -p \"{$migration_cache_dir}\"/downloads" );
		}
	}

	private function extract( $marketron_export, $dest, $fresh = false ) {
		$update_flag = $fresh ? '-u' : '-f';
		system( "unzip $update_flag -d \"$dest\" \"$marketron_export\" " );
	}

	private function format( $dir, $fresh = false ) {
		$pattern = "$dir/*.{xml,XML}";
		$files   = glob( $pattern, GLOB_BRACE );
		$files   = preg_grep( '/._formatted.xml$/', $files, PREG_GREP_INVERT );

		foreach ( $files as $file ) {
			$outfile = preg_replace( '/.(XML|xml)$/', '_formatted.xml', $file );
			if ( ! file_exists( $outfile ) || $fresh ) {
				\WP_CLI::log( 'Cleaning up: ' . basename( $file ) );
				system( "xmllint --huge --format --output $outfile $file" );
			}
		}
	}

	function initialize( $args, $opts ) {
		if ( ! $this->initialized ) {
			$this->load_params( $args, $opts );

			$this->config = new MigrationConfig( $this->site_dir );
			$this->config->container = $this;
			$this->config->load();

			$this->side_loader = new MediaSideLoader();
			$this->side_loader->container = $this;

			$this->asset_locator = new \WordPress\Utils\AssetLocator();
			$this->asset_locator->container = $this;

			$this->error_reporter = new \GreaterMedia\Utils\ErrorReporter();
			$this->error_reporter->container = $this;

			$this->table_factory = new TableFactory();
			$this->table_factory->container = $this;

			$this->entity_factory = new EntityFactory();
			$this->entity_factory->container = $this;

			$this->backup_manager = new \WordPress\Tables\BackupManager();
			$this->backup_manager->container = $this;

			$this->mappings = new MappingCollection();
			$this->mappings->container = $this;

			$this->xml_extractor = new XMLExtractor();
			$this->xml_extractor->container = $this;

			$this->tool_factory     = new ToolFactory();
			$this->tool_factory->container = $this;

			$this->importer_factory = new ImporterFactory();
			$this->importer_factory->container = $this;

			$this->initialized = true;
		}
	}

	/* fast migration */
	function fast_migrate( $args, $opts ) {
		$this->initialize( $args, $opts );

		if ( $this->fresh ) {
			// if backup does not exist create it first
			if ( ! file_exists( $this->get_backup_file() ) ) {
				$this->backup( $args, $opts );
			} else {
				$this->restore( $args, $opts );
				$this->side_loader->restore();
			}
		}

		$this->mappings->load();
		$this->xml_extractor->extract();

		$tool = 'all';
		$migration_cache_dir = 'migration_cache';
		$this->opts['migration_cache_dir'] = 'migration_cache';

		if ( $tool === 'all' ) {
			$tools_to_load = $this->default_tools;
		} else {
			$tools_to_load = array( $tool );
		}

		$this->downloader = new Downloader(
			$migration_cache_dir . '/downloads',
			$migration_cache_dir . '/media'
		);

		$this->mappings->import();

		$this->load_tools( $tools_to_load );
		$this->import_tools( $tools_to_load );

		//$this->test_users();
		//$this->test_users_api();

		//$this->test_terms();
		//$this->test_shadow_taxonomy();

		//$this->test_posts();
		//$this->test_attachments();

		//$this->test_feed_import();
		//$this->test_tags();
		//$this->test_author();
		//$this->test_entity_attachment();
		//$this->side_loader->sync();
		//
		//$this->test_legacy_redirect();
		//$this->test_post_format();
		$this->table_factory->export();
		$this->table_factory->import();
		$this->update_term_counts();

		$this->side_loader->sync();
		$this->error_reporter->save_report();
	}

	function test_post_format() {
		$posts = $this->entity_factory->get_entity( 'post' );

		$post = array(
			'post_author' => 1,
			'post_title' => 'lorem 1',
			'post_content' => 'http://lorem.com',
			'post_format' => 'link',
			'tags' => array( 'one', 'two', 'three' ),
			'categories' => array( 'cat one', 'cat two', 'cat three' ),
			'featured_image' => '\\Blogs\\1001038\\JDB Podcast- general-184.jpg'
		);

		$posts->add( $post );
		$this->table_factory->export();
		$this->table_factory->import();
	}

	function test_legacy_redirect() {
		$legacy_redirects = $this->entity_factory->get_entity( 'legacy_redirect' );
		$legacy_redirects->container = $this;

		$redirect = array( 'url' => 'http://wmgk1.greatermedia.dev/lorem/ipsum/dolor', 'post_id' => 51 );
		$legacy_redirects->add( $redirect );

		$this->table_factory->export();
		$this->table_factory->import();
	}

	function test_entity_attachment() {
		$attachments = new \WordPress\Entities\Attachment();
		$attachments->container = $this;

		$attachment = array(
			'file' => '\\Blogs\\1001038\\JDB Podcast- general-184.jpg',
		);

		$attachment = $attachments->add( $attachment );

		$attachment = array(
			'file' => '/Blogs/1001039/Colin_hay_bounce_2.mp3',
		);

		$attachment = $attachments->add( $attachment );
		//var_dump( $attachment );

		$this->table_factory->export();
		$this->table_factory->import();
	}

	function test_asset_locator() {
		$asset_locator = new \WordPress\Utils\AssetLocator();
		$asset_locator->container = $this;

		$result = $asset_locator->find( 'foo.jpeg' );
		$result = $asset_locator->find( '\\Pics\\Feeds\\Articles\\2013129\\151567\\13-051-247.jpg' );
		$result = $asset_locator->find( '\\Blogs\\1001280\\stuart-front.jpg"' );
		$result = $asset_locator->find( '\\Blogs\\1001280\\sundance-001.jpg' );
		$result = $asset_locator->find( '\\Blogs\\1001038\\JDB Podcast- general-184.jpg' );
		$result = $asset_locator->find( '/Blogs/1001039/Colin_hay_bounce_2.mp3' );
	}

	function test_author() {
		$authors = new \WordPress\Entities\Author();
		$authors->container = $this;

		$author = array( 'display_name' => 'John Doe' );
		$result = $authors->add( $author );

		$author = array( 'display_name' => 'Jane Doe' );
		$result = $authors->add( $author );
		//var_dump( $result );
		//
		$this->table_factory->export();
		$this->table_factory->import();
	}

	function test_tags() {
		$tags = new \WordPress\Entities\Tag();
		$tags->container = $this;
		$tag_id = $tags->add( 'a one' );
		$tag_id = $tags->add( 'a one' );
		$tag_id = $tags->add( 'a one' );
		$tag_id = $tags->add( 'a one' );
		//error_log( "a one tag id = " . $tag_id );

		$tag_id = $tags->add( 'a two', 1 );
		$tag_id = $tags->add( 'a three', 2 );
		$tag_id = $tags->add( 'a three', 2 );
		//error_log( "a one tag id = " . $tag_id );

		//$categories = new \WordPress\Entities\Categories();
		//$categories->container = $this;
		//$category_id = $categories->add( 'a one', 2 );

		//$categories = new \WordPress\Entities\Categories();
		//$categories->container = $this;
		//$category_id = $categories->add( 'a one', 2 );
		//error_log( "a one category id = " . $category_id );

		//$tag_id = $tags->add( 'a one' );
		//error_log( "a one tag id = " . $tag_id );

		//$tags->add( 'b one' );
		//error_log( "b one tag id = " . $tag_id );

		$this->table_factory->export();
	}

	function test_feed_import() {
		$posts = $this->table_factory->build( 'posts' );
		$posts->export();
		$posts->import();
	}

	function test_sideload( $args, $opts ) {
		$this->load_params( $args, $opts );

		$this->config = new MigrationConfig( $this->site_dir );
		$this->config->container = $this;
		$this->config->load();

		$side_loader = new MediaSideLoader();
		$side_loader->container = $this;

		# works
		//error_log( $side_loader->get_sync_source_dir() );
		//error_log( $side_loader->get_sync_target_dir() );
		//error_log( $side_loader->get_upload_dir_for( strtotime( 'now' ) ) );
		//error_log( $side_loader->get_upload_dir_for( new \DateTime() ) );
		//error_log( $side_loader->get_upload_dir_for( gmdate( 'Y-m-d H:i:s' ) ) );
		//error_log( $side_loader->get_upload_dir_for( strtotime( '-1 year' ) ) );
		//error_log( $side_loader->get_backup_dir() );

		//$side_loader->backup();
		//$side_loader->restore();
		//$side_loader->sync();

		//$result = $side_loader->sideload( 'wmgk/backups/uploads/2015/03/3103817499_8fc3f945ef_z.jpg' );
		$result = $side_loader->sideload( 'wmgk/backups/uploads/2015/02/wll-11-10.mp3' );
		var_dump( $result );

	}

	function test_attachments() {
		//http://wmgk1.greatermedia.dev/wp-content/uploads/sites/5/2010/11/ledzep160.jpg
		$posts = $this->table_factory->build( 'posts' );
		$total = 1;

		foreach ( range( 1, $total ) as $i ) {
			$post = array(
				'post_author'   => 0,
				'post_date'     => gmdate( 'Y-m-d H:i:s' ),
				'post_date_gmt' => gmdate( 'Y-m-d H:i:s' ),
				'post_title' => "photo title $i",
				'post_name' => "photo name $i",
				'post_content' => null,
				'post_content_filtered' => null,
				'post_excerpt' => null,
				'post_status' => 'inherit',
				'comment_status' => 'open',
				'ping_status' => 'open',
				'to_ping' => null,
				'pinged'  => null,
				'post_modified'     => gmdate( 'Y-m-d H:i:s' ),
				'post_modified_gmt' => gmdate( 'Y-m-d H:i:s' ),
				'post_parent' => 0,
				'menu_order' => 0,
				'post_type' => 'attachment',
				'post_mime_type' => 'image/jpeg',
				'comment_count' => 0,
				'guid' => 'http://wmgk1.greatermedia.dev/wp-content/uploads/sites/5/2015/03/3103817499_8fc3f945ef_z.jpg',
				'postmeta' => array(
					'_wp_attached_file' => '2015/03/3103817499_8fc3f945ef_z.jpg',
					'_wp_attachment_metadata' => serialize(array(
						'width' => 640,
						'height' => 480,
						'file' => '2015/03/3103817499_8fc3f945ef_z.jpg',
						'sizes' => array(),
						'image_meta' => array()
					))
				)
			);

			$posts->add( $post );
		}

		$posts->export();
		$posts->import();

		$postmeta = $this->table_factory->build( 'postmeta' );
		$postmeta->export();
		$postmeta->import();
	}

	function test_posts() {
		$posts = $this->table_factory->build( 'posts' );
		$total = 100;

		foreach ( range( 1, $total ) as $i ) {
			$post = array(
				'post_author'   => 0,
				'post_date'     => gmdate( 'Y-m-d H:i:s' ),
				'post_date_gmt' => gmdate( 'Y-m-d H:i:s' ),
				'post_title' => "a test post $i",
				'post_name' => sanitize_title( 'a test post $i' ),
				'post_content' => '<p>This is a <strong>test</strong></p>',
				'post_content_filtered' => null,
				'post_excerpt' => "post excerpt $i",
				'post_status' => 'publish',
				'comment_status' => 'open',
				'ping_status' => 'open',
				'to_ping' => null,
				'pinged'  => null,
				'post_modified'     => gmdate( 'Y-m-d H:i:s' ),
				'post_modified_gmt' => gmdate( 'Y-m-d H:i:s' ),
				'post_parent' => 0,
				'menu_order' => 0,
				'post_type' => 'post',
				'post_mime_type' => null,
				'comment_count' => 0,
			);

			$posts->add( $post );
		}

		$posts->export();
		$posts->import();
	}

	function test_terms() {
		$terms = $this->table_factory->build( 'terms' );
		$term_relationships = $this->table_factory->build( 'term_relationships' );
		$total = 1;
		$post_id = 195418;

		foreach ( range( 1, $total ) as $i ) {
			$term = array(
				'name' => 'term ' . $i,
				'slug' => sanitize_title( 'term ' . $i ),
				'term_taxonomy' => array(
					'taxonomy' => 'category',
				)
			);

			$term = $terms->add( $term );

			$term_relationships->add(
				array(
					'term_taxonomy_id' => $term['term_taxonomy']['term_taxonomy_id'],
					'object_id' => $post_id,
					'term_order' => 0,
				)
			);
		}

		$terms->export();
		$terms->import();

		$term_taxonomy = $this->table_factory->build( 'term_taxonomy' );
		$term_taxonomy->export();
		$term_taxonomy->import();

		$term_relationships->export();
		$term_relationships->import();
	}

	function test_shadow_taxonomy() {
		$terms = $this->table_factory->build( 'terms' );
		$term_relationships = $this->table_factory->build( 'term_relationships' );
		$total = 10000;
		$post_id = 195418;

		foreach ( range( 1, $total ) as $i ) {
			$term = array(
				'name' => 'justashow ' . $i,
				'slug' => sanitize_title( 'justashow ' . $i ),
				'term_taxonomy' => array(
					'taxonomy' => '_shows',
				)
			);

			$term = $terms->add( $term );

			$term_relationships->add(
				array(
					'term_taxonomy_id' => $term['term_taxonomy']['term_taxonomy_id'],
					'object_id' => $post_id,
					'term_order' => 0,
				)
			);
		}

		$terms->export();
		$terms->import();

		$term_taxonomy = $this->table_factory->build( 'term_taxonomy' );
		$term_taxonomy->export();
		$term_taxonomy->import();

		$term_relationships->export();
		$term_relationships->import();
	}

	function test_users_api() {
		$id = wp_create_user( 'dms', 'foobar', 'dms@foo.com' );
		\WP_CLI::log( 'Created user: ' . $id );
	}

	function test_users() {
		$users = $this->table_factory->build( 'users' );
		$users->container = $this;

		$total_users = 10000;
		$password    = wp_hash_password( 'foobar' );
		$notify      = new \cli\progress\Bar( "Created $total_users Test Users", $total_users );

		foreach ( range( 1, $total_users ) as $i ) {
			$users->add(
				array(
					'user_login'          => 'me' . $i,
					'user_nicename'       => 'me' . $i,
					'user_pass'           => $password,
					'user_email'          => "me$i@foo.com",
					'user_url'            => '',
					'user_registered'     => gmdate( 'Y-m-d H:i:s' ),
					'user_activation_key' => '',
					'user_status'         => 0,
					'display_name'        => 'Foo User' . $i,

					'usermeta'                 => array(
						'nickname'             => 'me' . $i,
						'first_name'           => 'me first',
						'last_name'            => 'me last',
						'description'          => '',
						'rich_editing'         => 'true',
						'comment_shortcuts'    => 'false',
						'use_ssl'              => 0,
						'show_admin_bar_front' => true,
						'wp_5_capabilities'    => serialize( array( 'author' => true ) ),
						'wp_5_user_level'      => 0,
					)
				)
			);

			$notify->tick();
		}

		$notify->finish();

		$users->export();
		$users->import();

		$usermeta = $this->table_factory->build( 'usermeta' );
		$user = $users->get_row_with_field( 'user_login', 'me1' );

		$usermeta->add( array(
			'user_id' => $user['ID'],
			'meta_key' => 'wp_5_show_tt_id',
			'meta_value' => '128731',
		));
		$usermeta->add( array(
			'user_id' => $user['ID'],
			'meta_key' => 'wp_5_show_tt_id_128731',
			'meta_value' => 1,
		));

		$usermeta->export();
		//var_dump( $usermeta->rows );
		$usermeta->import();
	}

	function restore( $args, $opts ) {
		$this->initialize( $args, $opts );
		$this->backup_manager->restore();
		/*
		$this->table_factory->build_all();
		$this->table_factory->count();

		$backup = $this->get_backup_file();

		if ( file_exists( $backup ) ) {
			\WP_CLI::log( "Restoring Backup: $backup" );
			system( "wp db import \"$backup\"" );
		} else {
			\WP_CLI::error( "Backup not found: $backup" );
		}

		\WP_CLI::log( '' );
		 */
	}

	function backup( $args, $opts ) {
		$this->initialize( $args, $opts );
		$this->backup_manager->backup();
		/*
		$backup = $this->get_backup_file();

		if ( file_exists( $backup ) ) {
			\WP_CLI::confirm( 'Backup file exists, Are you sure you want to overwrite it?' );
			\WP_CLI::log( "Overwriting Database Backup: $backup" );
		} else {
			\WP_CLI::log( "Backing up Database: $backup" );
		}

		system( "wp db export \"$backup\"" );
		\WP_CLI::log( '' );
		*/
	}

	private function load_params( $args, $opts ) {
		$this->args = $args;
		$this->opts = $opts;

		if ( ! array_key_exists( 'site_dir', $opts ) ) {
			\WP_CLI::error( '--site_dir option must be specified' );
		}

		$this->site_dir = $opts['site_dir'];
		$this->fresh    = array_key_exists( 'fresh', $opts ) && filter_var( $opts['fresh'], FILTER_VALIDATE_BOOLEAN );
	}

	private function get_backup_file() {
		return $this->site_dir . '/backups/database.sql';
	}

	private function update_term_counts() {
		global $wpdb;
		$query = <<<SQL
UPDATE {$wpdb->prefix}term_taxonomy
SET count = (
	SELECT COUNT(*) FROM {$wpdb->prefix}term_relationships rel
    LEFT JOIN {$wpdb->prefix}posts po ON (po.ID = rel.object_id)
    WHERE
        rel.term_taxonomy_id = {$wpdb->prefix}term_taxonomy.term_taxonomy_id
        AND
		{$wpdb->prefix}term_taxonomy.taxonomy NOT IN ('link_category')
        AND
        po.post_status IN ('publish', 'future')
);
SQL;
		$wpdb->query( $query );
	}

}

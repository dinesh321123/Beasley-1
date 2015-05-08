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

	//public $default_tools = array(
		//'feed', 'blog', 'venue', 'event_calendar', 'channel',
		//'video_channel', 'event_manager',
		//'photo_album_v2', 'showcase', 'podcast', 'survey',
		//'contest',
	//);

	public $default_tools = array(
		'feed'
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

	public function format_data( $args, $opts ) {
		$this->initialize( $args, $opts );

		$dir     = $this->config->get_marketron_files_dir() . '/data';
		$fresh   = $this->opts['fresh'];
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

	function initialize( $args, $opts, $update = true ) {
		if ( ! $this->initialized ) {
			$this->load_params( $args, $opts );

			$this->config = new MigrationConfig( $this->site_dir );
			$this->config->container = $this;
			$this->config->load();

			$this->config_loader = new \GreaterMedia\ConfigLoader();
			$this->config_loader->container = $this;

			//if ( $update ) {
				//$this->config_loader->load();
			//}

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

			$this->inline_image_replacer = new \WordPress\Utils\InlineImageReplacer();
			$this->inline_image_replacer->container = $this;

			$this->initialized = true;
		}
	}

	/* fast migration */
	function fast_migrate( $args, $opts ) {
		$this->initialize( $args, $opts, true );

		if ( $this->fresh ) {
			// if backup does not exist create it first
			if ( ! file_exists( $this->backup_manager->get_backup_file() ) ) {
				$this->backup( $args, $opts );
			} else {
				$this->restore( $args, $opts );
				$this->side_loader->restore();
			}
		}

		$this->mappings->load();
		$this->xml_extractor->extract();
		$this->mappings->import();

		$tools_to_load = $this->get_tools_to_load();
		$this->load_tools( $tools_to_load );

		if ( $this->opts['export_to_gigya'] ) {
			$this->entity_factory->build( 'gigya_user' )->export();
		}

		$this->table_factory->export();
		$this->error_reporter->save_report();
		$this->side_loader->sync();
	}

	function configure_api_keys( $args, $opts ) {
		$this->initialize( $args, $opts );
		$this->config_loader->load();
		$this->config_loader->load_live_streams();
	}

	function repair( $args, $opts ) {
		$opts['repair'] = true;
		$opts['tools_to_load'] = 'feed';
		$this->initialize( $args, $opts, false );

		//$repairer = new \GreaterMedia\Import\Repair\EmbeddedFormRepairer();
		//$repairer->container = $this;
		//$repairer->update_cids( $this->opts['site_dir'] . '/output/cids.json' );

		$repairer = new \GreaterMedia\Import\Repair\FeaturedImageRepairer();
		$repairer->container = $this;
		$repairer->repair();
	}

	function restore( $args, $opts ) {
		$this->initialize( $args, $opts, false );
		$this->backup_manager->restore();
	}

	function backup( $args, $opts ) {
		$this->initialize( $args, $opts, false );
		$this->backup_manager->backup();
	}

	private function load_params( $args, $opts ) {
		$this->args = $args;
		$this->opts = $opts;

		if ( ! array_key_exists( 'site_dir', $this->opts ) ) {
			\WP_CLI::error( '--site_dir option must be specified' );
		}

		if ( ! array_key_exists( 'tools_to_load', $this->opts ) ) {
			$this->opts['tools_to_load'] = $this->default_tools;
		} else if ( $opts['tools_to_load'] !== 'all' ) {
			$this->opts['tools_to_load'] = explode( ',', $this->opts['tools_to_load'] );
		}

		$this->site_dir = $opts['site_dir'];

		$this->load_boolean_opt( 'fake_media', false );
		$this->load_boolean_opt( 'fresh', false );
		$this->load_boolean_opt( 'export_to_gigya', true );
		$this->load_boolean_opt( 'repair', false );

		$this->fresh = $this->opts['fresh'];
	}

	function load_boolean_opt( $name, $default ) {
		if ( ! array_key_exists( $name, $this->opts ) ) {
			$this->opts[ $name ] = $default;
		} else {
			$this->opts[ $name ] = filter_var( $this->opts[ $name ], FILTER_VALIDATE_BOOLEAN );
		}
	}

	function get_tools_to_load() {
		$tools_to_load = $this->opts['tools_to_load'];

		if ( $tools_to_load === 'all' ) {
			$tools_to_load = $this->tool_factory->get_tool_names();
		} else if ( $tools_to_load[0] === 'none' ) {
			$tools_to_load = array();
		}

		return $tools_to_load;
	}

	function update_term_counts( $args, $opts ) {
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

		\WP_CLI::success( 'Taxonomy Term counts updated successfully' );
	}

	function review_featured_images( $args, $opts ) {
		if ( ! array_key_exists( 'log_file', $opts ) ) {
			\WP_CLI::error( '--log_file option must be specified' );
		}

		if ( ! array_key_exists( 'min_width', $opts ) ) {
			$min_width = 300;
		} else {
			$min_width = intval( $opts['min_width'] );
		}

		$log_file = $opts['log_file'];
		$reviewer = new \WordPress\Utils\FeaturedImageReviewer();
		$reviewer->review( $log_file, $min_width );
	}

	function print_image_regenerator( $args, $opts ) {
		$this->load_params( $args, $opts );

		if ( ! array_key_exists( 'procs', $opts ) ) {
			$procs = 20;
		} else {
			$procs = intval( $opts[ 'procs' ] );
		}

		$attachments_log = $this->opts['site_dir'] . '/output/attachments.log';
		$cmd  = 'cat ' . escapeshellarg( $attachments_log );
		$cmd .= ' | ';
		$cmd .= " xargs -I ID -P $procs";
		$cmd .=	' wp media regenerate ID --yes ';
		$cmd .= ' --url=' . ltrim( get_site_url(), 'http://' );

		\WP_CLI::log( $cmd );
	}

	function export_actions( $args, $opts ) {
		$this->initialize( $args, $opts, false );

		$gigya_user = $this->entity_factory->get_entity( 'gigya_user' );
		$gigya_user->export_actions();
	}

	function prepare( $args, $opts ) {
		$this->load_params( $args, $opts );

		$this->config = new MigrationConfig( $this->site_dir );
		$this->config->container = $this;

		$this->config_loader = new \GreaterMedia\ConfigLoader();
		$this->config_loader->container = $this;

		$this->config_loader->prepare();
	}

	function merge_duplicate_taxonomy( $args, $opts ) {
		if ( ! empty( $opts['taxonomy'] ) ) {
			$taxonomy = $opts['taxonomy'];
		} else {
			$taxonomy = 'post_tag';
		}

		$merger      = new \WordPress\Utils\DuplicateTaxonomyMerger();
		$merge_count = $merger->merge( $taxonomy );

		if ( $merge_count > 0 ) {
			\WP_CLI::success( "Merged $merge_count Terms for Taxonomy: $taxonomy" );
		} else {
			\WP_CLI::warning( "No Duplicate Terms found for Taxonomy: $taxonomy" );
		}
	}

	function repair_optin_groups( $args, $opts ) {
		$optin_groups_config = $opts['optin_groups'];
		$errors_file         = $opts['errors_file'];

		$optin_groups = new \GreaterMedia\Import\Repair\OptinGroups();
		$optin_groups->load( $optin_groups_config );

		$repairer         = new \GreaterMedia\Import\Repair\OptinGroupsRepairer();
		$repairer->groups = $optin_groups;
		$total = $repairer->repair( $errors_file );

		\WP_CLI::success( "Successfully Repaired $total Optin Groups" );
	}

	function repair_video_embeds( $args, $opts ) {
		$refresher = new \GreaterMedia\Import\Repair\VideoRefresher();
		$refresher->refresh();
	}

	/* profile verification */
	function verify_gigya_import( $args, $opts ) {
		$marketron_accounts_file = $opts['marketron_accounts'];
		$gigya_accounts_file     = $opts['gigya_accounts'];
		$error_file              = $opts['errors'];

		$csv_loader     = new \GreaterMedia\Profile\GigyaCSVLoader();
		$gigya_accounts = $csv_loader->load( $gigya_accounts_file );
		//var_dump( $gigya_accounts );
		//return;

		\WP_CLI::log( 'Loaded ' . count( $gigya_accounts ) . ' Gigya Accounts' );

		$json_loader        = new \GreaterMedia\Profile\ImportJSONLoader();
		$marketron_accounts = $json_loader->load( $marketron_accounts_file );

		\WP_CLI::log( 'Loaded ' . count( $marketron_accounts ) . ' Marketron Accounts' );

		$verifier = new \GreaterMedia\Profile\ImportVerifier(
			$marketron_accounts, $gigya_accounts
		);

		$success = $verifier->verify();

		if ( $success ) {
			\WP_CLI::success( 'Gigya Profile Import Verified!!!' );
		} else {
			$errors = $verifier->errors;
			$data   = implode( "\n", $errors );

			file_put_contents( $error_file, $data );
			\WP_CLI::error( 'Verification Failed with ' . count( $errors ) . ' errors.' );
		}
		//print_r( count( $marketron_accounts ) );
	}
}


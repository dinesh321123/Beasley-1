<?php

namespace GreaterMedia\Gigya\Commands;

use GreaterMedia\Gigya\FakeProfiles\FakeGigyaUser;
use GreaterMedia\Gigya\GigyaRequest;
use GreaterMedia\Gigya\Schema\AccountSchema;
use GreaterMedia\Gigya\Schema\ActionsSchema;
use Faker\Factory;

/**
 * Set of commands to seed fake users into Gigya.
 */
class GigyaCommand extends \WP_CLI_Command {

	/**
	 * Creates fake Gigya Users.
	 *
	 * ## OPTIONS
	 *
	 * <count>
	 * : Number of fake users to create
	 *
	 * <file>
	 * : Path to file to write generated users json
	 *
	 * ## EXAMPLES
	 *
	 * wp gigya seed_users 10
	 *
	 */
	public function seed_users( $args, $opts ) {
		$arg_count = count( $args );
		if ( $arg_count < 2 ) {
			\WP_CLI::error( 'Invalid arguments' );
			return;
		}

		$count = intval( $args[0] );
		$file  = $args[1];

		if ( ! ( is_int( $count ) && $count > 0 ) ) {
			\WP_CLI::error( "Count must be a number > 0 - was $count" );
			return;
		}

		if ( is_null( $file ) ) {
			$file = 'users.json';
		}

		$errors_file = $file . '.errors.log';

		file_put_contents( $file, "[\n" );
		file_put_contents( $errors_file, '' );

		\WP_CLI::log( "Creating $count fake users in Gigya ..." );

		$faker = Factory::create( 'en_US' );

		for ( $i = 0; $i < $count; $i++ ) {
			$user = new FakeGigyaUser();
			$user->seed( $faker );

			try {
				$user->save();
				$progress   = ( $i + 1 ) / $count * 100;
				$percent    = sprintf( '%.2f%%', $progress );
				$user_id    = $user->get( 'UID' );
				$user_email = str_pad( $user->get( 'email' ), 35, ' ', STR_PAD_BOTH );

				\WP_CLI::success(
					"Created User( {$user_email} ):\t {$user_id} - {$percent}"
				);

				if ( $count > 1 && $i < $count - 1 ) {
					$user_line  = $user->to_json() . ",\n";
				} else {
					$user_line = $user->to_json() . "\n";
				}

				file_put_contents( $file, $user_line, FILE_APPEND );
			} catch ( \Exception $e ) {
				\WP_CLI::warning( $e->getMessage() );
				$error_message = $user->to_json() . "\n";
				$error_message .= $user->response->getErrorMessage() . "\n";
				$error_message .= $user->response->getResponseText() . "\n\n";
				file_put_contents( $errors_file, $error_message, FILE_APPEND );
			}
		}

		file_put_contents( $file, ']', FILE_APPEND );
		\WP_CLI::success( "Creating $count fake users in Gigya ... DONE" );
	}

	public function set_account_schema( $args, $opts ) {
		$schema  = new AccountSchema();
		$request = new GigyaRequest( null, null, 'accounts.setSchema' );

		try {
			$schema->update( $request );
			\WP_CLI::success( 'Updated Gigya Account Schema' );
		} catch ( \Exception $err ) {
			\WP_CLI::error( $err->getMessage() );
		}
	}

	public function get_account_schema( $args, $opts ) {
		$schema  = new AccountSchema();
		$request = new GigyaRequest( null, null, 'accounts.getSchema' );

		try {
			$schema_text = $schema->fetch( $request );
			\WP_CLI::log( $schema_text );
		} catch ( \Exception $err ) {
			\WP_CLI::error( $err->getMessage() );
		}
	}

	public function set_actions_schema( $args, $opts ) {
		$schema  = new ActionsSchema();
		$request = new GigyaRequest( null, null, 'ds.setSchema' );

		try {
			$schema->update( $request );
			\WP_CLI::success( 'Updated Gigya Data Store Schema' );
		} catch ( \Exception $err ) {
			\WP_CLI::error( $err->getMessage() );
		}
	}

	public function get_actions_schema( $args, $opts ) {
		$schema  = new ActionsSchema();
		$request = new GigyaRequest( null, null, 'ds.getSchema' );

		try {
			$schema_text = $schema->fetch( $request );
			\WP_CLI::log( $schema_text );
		} catch ( \Exception $err ) {
			\WP_CLI::error( $err->getMessage() );
		}
	}

	function formatBytes( $size, $precision = 2 ) {
		$base = log($size) / log(1024);
		$suffixes = array('', 'k', 'M', 'G', 'T');

		return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
	}

	function new_string_of_size( $size, $input = 'A' ) {
		return "{$size}k - " . str_repeat( $input, $size * 1024 );
	}

	public function add_contest_entry( $args, $opts ) {
		$user_id              = $args[0];
		$size                 = intval( $args[1] );
		//$text_file          = $args[1];
		//$text_file_contents = file_get_contents( $text_file );
		$text_file_contents   = $this->new_string_of_size( $size );

		$action = array(
			'actionType'   => 'record:contest',
			'actionTypeID' => rand( 1, 10000 ),
			'actionData'   => array(
				'c1_t'       => $text_file_contents,
				//'c1_t' => 'foo',
			)
		);

		$actions = array(
			'actions' => array(
				$action
			)
		);

		$data    = json_encode( $actions );
		$request = new GigyaRequest( null, null, 'accounts.setAccountInfo' );
		$request->setParam( 'UID', $user_id );
		$request->setParam( 'data', $data );
		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
			\WP_CLI::success( 'Added Contest Entry' );
			\WP_CLI::log( $response->getResponseText() );
		} else {
			\WP_CLI::log( $response->getResponseText() );
		}
	}

	public function add_multi_contest_entries( $args, $opts ) {
		$user_id   = $args[0];
		$unit_size = intval( $args[1] );
		$total     = intval( $args[2] );
		$actions   = array();

		for ( $i = 0; $i < $total; $i++ ) {
			$value = "{$unit_size}-{$total} " . $this->new_string_of_size( $unit_size );
			$action = array(
				'actionType'   => 'record:contest',
				'actionTypeID' => rand( 1, 10000 ),
				'actionData'   => array(
					'c1_t'     => $value,
				)
			);

			$actions[] = $action;
		}

		$data = array( 'actions' => $actions );
		$data = json_encode( $data );

		$request = new GigyaRequest( null, null, 'accounts.setAccountInfo' );
		$request->setParam( 'UID', $user_id );
		$request->setParam( 'data', $data );
		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
			\WP_CLI::success( 'Added Contest Entry' );
			\WP_CLI::log( $response->getResponseText() );
		} else {
			\WP_CLI::log( $response->getResponseText() );
		}
	}

	public function get_account_info( $args, $opts ) {
		$user_id = $args[0];
		$request = new GigyaRequest( null, null, 'accounts.getAccountInfo' );
		$request->setParam( 'UID', $user_id );
		$response = $request->send();

		if ( $response->getErrorCode() === 0 ) {
			\WP_CLI::log( $response->getResponseText() );
		} else {
			\WP_CLI::error( 'Failed to get account info' );
			\WP_CLI::log( $response->getResponseText() );
		}
	}

	public function reset_users( $args, $opts ) {
	}

	public function query( $args, $opts ) {
		$query = $args[0];

		if ( strstr( $query, 'from accounts' ) !== false ) {
			$endpoint = 'accounts.search';
		} else {
			$endpoint = 'ds.search';
		}

		$request = new GigyaRequest( null, null, $endpoint );
		$request->setParam( 'query', $query );
		$response      = $request->send();
		$response_text = $response->getResponseText();

		if ( $response->getErrorCode() === 0 ) {
			\WP_CLI::success( 'Query Executed Successfully' );
			\WP_CLI::log( $response_text );
		} else {
			\WP_CLI::success( 'Query Failed' );
			\WP_CLI::log( $response_text );
		}
	}

}

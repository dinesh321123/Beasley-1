<?php

namespace GreaterMedia\Gigya\Sync;

use GreaterMedia\Gigya\GigyaRequest;

class QueryPaginator {

	public $store_type;
	public $page_size;

	function __construct( $store_type, $page_size ) {
		$this->store_type = $store_type;
		$this->page_size  = $page_size;
	}

	function fetch( $query, $cursor = 0 ) {
		$request     = $this->request_for( $this->store_type );
		$limit_query = $this->to_limit_query( $query, $cursor, $this->page_size );
		$limit_query = $this->to_light_query( $limit_query );

		$request->setParam( 'query', $limit_query );

		$json            = $this->send( $request, $limit_query );
		$total_results   = $json['totalCount'];
		$results_in_page = $json['objectsCount'];
		$results         = $json['results'];

		if ( $total_results > 0 ) {
			$has_next = $cursor + $results_in_page < $total_results;
			$progress = ceil( ( $cursor + $results_in_page ) / $total_results * 100 );
		} else {
			$progress = 100;
			$has_next = false;
		}

		$result = array(
			'total_results'   => $total_results,
			'results_in_page' => $results_in_page,
			'results'         => $results,
			'has_next'        => $has_next,
			'cursor'          => $cursor + $results_in_page,
			'progress'        => $progress,
		);

		return $result;
	}

	function fetch_with_cursor( $query, $cursor = false ) {
		$request     = $this->request_for( $this->store_type );
		$limit_query = $this->to_cursor_limit_query( $query, $this->page_size );
		$limit_query = $this->to_light_query( $limit_query );

		if ( $cursor === false ) {
			$request->setParam( 'openCursor', true );
			$request->setParam( 'query', $limit_query );
		} else {
			$request->setParam( 'cursorId', $cursor );
		}

		$json     = $this->send( $request, $limit_query );
		$has_next = array_key_exists( 'nextCursorId', $json );

		$result = array(
			'total_results'   => $json['totalCount'],
			'results_in_page' => $json['objectsCount'],
			'results'         => $json['results'],
			'has_next'        => $has_next,
		);

		if ( $has_next ) {
			$result['cursor'] = $json['nextCursorId'];
		} else {
			$result['cursor'] = false;
		}

		return $result;
	}

	function to_cursor_limit_query( $query, $page_size ) {
		return "{$query} limit {$page_size}";
	}

	function to_limit_query( $query, $start, $page_size ) {
		return "{$query} order by UID start {$start} limit {$page_size}";
	}

	function to_light_query( $query ) {
		return str_replace( 'select *', 'select UID', $query );
	}

	function send( $request, $query ) {
		$response      = $request->send();
		$response_text = $response->getResponseText();


		if ( $response->getErrorCode() === 0 ) {
			$json = json_decode( $response_text, true );

			if ( is_array( $json ) ) {
				return $json;
			} else {
				throw new \Exception(
					"QueryPaginator: Failed to decode response json - {$response_text}"
				);
			}
		} else {
			$error_message = $this->error_message_for( $response );
			throw new \Exception(
				"QueryPaginator: Query Failed - {$query} - " . $error_message
			);
		}
	}

	function error_message_for( $response ) {
		$response_text = $response->getResponseText();
		$json          = json_decode( $response_text, true );

		if ( json_last_error() === JSON_ERROR_NONE ) {
			if ( array_key_exists( 'errorDetails', $json ) ) {
				return $json['errorDetails'];
			} else {
				return $response->getErrorMessage();
			}
		} else {
			return 'Gigya API returned invalid JSON - ' . $response_text;
		}
	}

	function request_for( $store_type ) {
		$endpoint = $this->endpoint_for( $store_type );
		$request  = new GigyaRequest( null, null, $endpoint );
		$params   = $this->params_for( $store_type );

		foreach ( $params as $param_name => $param_value ) {
			$request->setParam( $param_name, $param_value );
		}

		return $request;
	}

	function endpoint_for( $store_type ) {
		if ( $store_type === 'profile' ) {
			return 'accounts.search';
		} else if ( $store_type === 'data_store' ) {
			return 'ds.search';
		} else {
			throw new \Exception(
				"QueryPaginator: Unknown store_type - {$store_type}"
			);
		}
	}

	function params_for( $store_type ) {
		$params = array();

		if ( $store_type === 'profile' ) {
			//
		} else if ( $store_type === 'data_store' ) {
			$params['type'] = 'actions';
		}

		return $params;
	}

}

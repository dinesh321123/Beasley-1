<?php

namespace GreaterMedia\Gigya\Sync;

use GreaterMedia\Gigya\GigyaRequest;

class QueryPaginatorTest extends \WP_UnitTestCase {

	public $paginator;

	function setUp() {
		parent::setUp();

		$this->paginator = new QueryPaginator( 'profile', 10 );
		$this->init_gigya_keys();
	}

	function tearDown() {
		parent::tearDown();
	}

	function init_gigya_keys() {
		$settings = array(
			'gigya_api_key' => '3_e_T7jWO0Vjsd9y0WJcjnsN6KaFUBv6r3VxMKqbitvw-qKfmaUWysQKa1fra5MTb6',
			'gigya_secret_key' => 'trS0ufXWUXZ0JBcpr/6umiRfgUiwT7YhJMQSDpUz/p8=',
		);

		$settings = json_encode( $settings );
		update_option( 'member_query_settings', $settings, true );
	}

	function test_it_has_a_store_type() {
		$this->assertEquals( 'profile', $this->paginator->store_type );
	}

	function test_it_has_a_page_size() {
		$this->assertEquals( 10, $this->paginator->page_size );
	}

	function test_it_has_valid_params_for_data_store_query() {
		$actual = $this->paginator->params_for( 'data_store' );
		$this->assertEquals( 'actions', $actual['type'] );
	}

	function test_it_has_valid_params_for_profile_query() {
		$actual = $this->paginator->params_for( 'profile' );
		$this->assertEquals( array(), $actual );
	}

	function test_it_has_valid_endpoint_for_profile_query() {
		$actual = $this->paginator->endpoint_for( 'profile' );
		$this->assertEquals( 'accounts.search', $actual );
	}

	function test_it_has_valid_endpoint_for_data_store_query() {
		$actual = $this->paginator->endpoint_for( 'data_store' );
		$this->assertEquals( 'ds.search', $actual );
	}

	function test_it_throws_exception_for_unknown_store_type() {
		$this->setExpectedException( 'Exception' );
		$this->paginator->endpoint_for( 'unknown' );
	}

	function test_it_can_build_request_for_profile_queries() {
		$actual = $this->paginator->request_for( 'profile' );
		$this->assertInstanceOf( 'GreaterMedia\Gigya\GigyaRequest', $actual );
	}

	function test_it_can_build_request_for_data_store_queries() {
		$actual = $this->paginator->request_for( 'data_store' );
		$this->assertInstanceOf( 'GreaterMedia\Gigya\GigyaRequest', $actual );
	}

	function test_it_can_execute_valid_gigya_request() {
		$query = 'select UID from accounts limit 1';
		$request = new GigyaRequest( null, null, 'accounts.search' );
		$request->setParam( 'query', $query );
		$actual = $this->paginator->send( $request, $query );

		$this->assertEquals( 1, $actual['objectsCount'] );
	}

	function test_it_can_execute_invalid_gigya_request() {
		$query = 'select UID from foo limit 1';
		$request = new GigyaRequest( null, null, 'accounts.search' );
		$request->setParam( 'query', $query );

		$this->setExpectedException( 'Exception' );
		$actual = $this->paginator->send( $request, $query );
	}

	function test_it_can_convert_query_to_cursor_limit_query() {
		$query = 'select UID from accounts';
		$actual = $this->paginator->to_cursor_limit_query( $query, 100 );

		$expected = 'select UID from accounts limit 100';
		$this->assertEquals( $expected, $actual );
	}

	function test_it_can_convert_query_to_limit_query() {
		$query = 'select UID from accounts';
		$actual = $this->paginator->to_limit_query( $query, 10, 100 );

		$expected = 'select UID from accounts order by UID start 10 limit 100';
		$this->assertEquals( $expected, $actual );
	}

	function test_it_has_a_next_cursor_after_first_page_of_cursor_query() {
		$query = 'select UID from accounts';
		$actual = $this->paginator->fetch_with_cursor( $query );

		$this->assertNotEmpty( $actual['cursor'] );
		$this->assertQueryStats( $actual );
		$this->assertEquals( 10, $actual['results_in_page'] );
	}

	function assertQueryStats( $actual ) {
		$this->assertInternalType( 'int', $actual['total_results'] );
		$this->assertInternalType( 'int', $actual['results_in_page'] );
		$this->assertGreaterThan( 0, count( $actual['results'] ) );
	}

	function test_it_can_paginate_over_cursor_query_till_completion() {
		// expecting 43 results
		$query = 'select UID from accounts where profile.age = 93';
		$paginator = new QueryPaginator( 'profile', 20 );

		$actual = $paginator->fetch_with_cursor( $query );
		$cursor = $actual['cursor'];
		$this->assertTrue( $actual['has_next'] );
		$this->assertEquals( 20, $actual['results_in_page'] );

		$actual = $paginator->fetch_with_cursor( $query, $cursor );
		$cursor = $actual['cursor'];
		$this->assertTrue( $actual['has_next'] );
		$this->assertEquals( 20, $actual['results_in_page'] );

		$actual = $paginator->fetch_with_cursor( $query, $cursor );
		$cursor = $actual['cursor'];
		$this->assertTrue( $actual['has_next'] );
		$this->assertEquals( 3, $actual['results_in_page'] );

		$actual = $paginator->fetch_with_cursor( $query, $cursor );
		$cursor = $actual['cursor'];
		$this->assertFalse( $actual['has_next'] );
		$this->assertFalse( $cursor );
	}

	function test_it_can_fetch_cursor_query_with_results_less_than_page_size() {
		$query     = 'select UID from accounts where profile.age = 93';
		$paginator = new QueryPaginator( 'profile', 10000 );

		$actual = $paginator->fetch_with_cursor( $query );
		$cursor = $actual['cursor'];
		$this->assertEquals( 43, $actual['results_in_page'] );

		$actual = $paginator->fetch_with_cursor( $query, $cursor );
		$cursor = $actual['cursor'];
		$this->assertFalse( $cursor );
	}

	function test_it_can_fetch_first_page_of_query() {
		$query = 'select UID from accounts';
		$actual = $this->paginator->fetch( $query );

		$this->assertEquals( 10, $actual['results_in_page'] );
		$this->assertQueryStats( $actual );
		$this->assertTrue( $actual['has_next'] );
	}

	function test_it_fetch_query_with_results_less_than_page_size() {
		$query     = 'select UID from accounts where profile.age = 93';
		$paginator = new QueryPaginator( 'profile', 10000 );

		$actual = $paginator->fetch( $query );
		$this->assertEquals( 43, $actual['results_in_page'] );
		$this->assertFalse( $actual['has_next'] );
	}

	function test_it_fetch_query_with_no_results() {
		$query     = 'select UID from accounts where profile.age = 5';
		$paginator = new QueryPaginator( 'profile', 10000 );

		$actual = $paginator->fetch( $query );
		$this->assertEquals( 0, $actual['results_in_page'] );
		$this->assertEquals( 0, $actual['total_results'] );
		$this->assertEquals( 100, $actual['progress'] );
		$this->assertFalse( $actual['has_next'] );
	}

	function test_it_can_fetch_pages_of_a_query_one_at_a_time_till_completion() {
		// expecting 43 results
		$query = 'select UID from accounts where profile.age = 93';
		$paginator = new QueryPaginator( 'profile', 20 );

		$actual = $paginator->fetch( $query );
		$cursor = $actual['cursor'];
		$this->assertEquals( 20, $actual['results_in_page'] );

		$actual = $paginator->fetch( $query, $cursor );
		$cursor = $actual['cursor'];
		$this->assertEquals( 20, $actual['results_in_page'] );

		$actual = $paginator->fetch( $query, $cursor );
		$cursor = $actual['cursor'];
		$this->assertEquals( 3, $actual['results_in_page'] );
		$this->assertFalse( $actual['has_next'] );
	}

	function test_it_can_compute_progress_of_pagination() {
		$query = 'select UID from accounts where profile.age = 93';
		$paginator = new QueryPaginator( 'profile', 10 );

		$actual = $paginator->fetch( $query );
		$cursor = $actual['cursor'];
		$this->assertEquals( 24, $actual['progress'] );

		$actual = $paginator->fetch( $query, $cursor );
		$cursor = $actual['cursor'];
		$this->assertEquals( 47, $actual['progress'] );

		$actual = $paginator->fetch( $query, $cursor );
		$cursor = $actual['cursor'];
		$this->assertEquals( 70, $actual['progress'] );

		$actual = $paginator->fetch( $query, $cursor );
		$cursor = $actual['cursor'];
		$this->assertEquals( 94, $actual['progress'] );

		$actual = $paginator->fetch( $query, $cursor );
		$cursor = $actual['cursor'];
		$this->assertEquals( 100, $actual['progress'] );
	}

}

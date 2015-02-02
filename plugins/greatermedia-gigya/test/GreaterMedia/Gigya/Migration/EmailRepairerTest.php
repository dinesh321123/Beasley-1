<?php

namespace GreaterMedia\Gigya\Migration;

class EmailRepairerTest extends \PHPUnit_Framework_TestCase {

	public $repairer;

	function setUp() {
		parent::setUp();
		$this->repairer = new EmailRepairer();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_it_can_strip_leading_chars() {
		$actual = $this->repairer->strip_leading( '..foo', '\.\.' );
		$this->assertEquals( 'foo', $actual );
	}

	function test_it_can_strip_trailing_chars() {
		$actual = $this->repairer->strip_trailing( 'foo..', '\.\.' );
		$this->assertEquals( 'foo', $actual );
	}

	function test_it_can_replace_all_occurances_of_chars() {
		$actual = $this->repairer->replace_with( 'abcFOOxyzFOO', 'FOO', 'BAR' );
		$this->assertEquals( 'abcBARxyzBAR', $actual );
	}

	function test_it_can_replace_com1() {
		$actual = $this->repairer->replace_com1( 'foo.c om' );
		$this->assertEquals( 'foo.com', $actual );
	}

	function test_it_can_replace_com2() {
		$actual = $this->repairer->replace_com2( 'foo.c,om' );
		$this->assertEquals( 'foo.com', $actual );
	}

	function test_it_can_replace_bracket() {
		$actual = $this->repairer->replace_bracket( 'f[oo.com' );
		$this->assertEquals( 'foo.com', $actual );
	}

	function test_it_can_replace_double_at() {
		$actual = $this->repairer->replace_double_at( 'foo@bar@com' );
		$this->assertEquals( 'foo@bar.com', $actual );
	}

	function test_it_can_replace_dot_at() {
		$actual = $this->repairer->replace_dot_at( 'a.b.c.@foo.com' );
		$this->assertEquals( 'a.b.c@foo.com', $actual );
	}

	function test_it_can_replace_double_dot() {
		$actual = $this->repairer->replace_double_dot( 'a.foo.com' );
		$this->assertEquals( 'a@foo.com', $actual );
	}

	function test_it_can_patch_at_msn() {
		$actual = $this->repairer->patch_at_msn( 'foo@msn' );
		$this->assertEquals( 'foo@msn.com', $actual );
	}

	function test_it_can_choose_first_or() {
		$actual = $this->repairer->choose_first_or( 'foo@bar.com or bar@foo.com' );
		$this->assertEquals( 'foo@bar.com', $actual );
	}

	function test_it_can_choose_first_and() {
		$actual = $this->repairer->choose_first_and( 'foo@bar.com and bar@foo.com' );
		$this->assertEquals( 'foo@bar.com', $actual );
	}

	function test_it_can_replace_comma() {
		$actual = $this->repairer->replace_comma( 'foo,bar.com' );
		$this->assertEquals( 'foobar.com', $actual );
	}

	function test_it_can_replace_space() {
		$actual = $this->repairer->replace_space( 'foo bar bar foo . com' );
		$this->assertEquals( 'foobarbarfoo.com', $actual );
	}

	function test_it_knows_if_email_is_not_valid() {
		$actual = $this->repairer->is_valid( 'foo' );
		$this->assertFalse( $actual );
	}

	function test_it_knows_if_email_is_valid() {
		$actual = $this->repairer->is_valid( 'me@foo.com' );
		$this->assertTrue( $actual );
	}

	function test_it_can_repair_emails() {
		$table = array(
			'foo@bar.com or bar@foo.com'  => 'foo@bar.com',
			'foo@bar.com and bar@foo.com' => 'foo@bar.com',
			'.me@foo.com'                 => 'me@foo.com',
			'..me@foo.com'                => 'me@foo.com',
			'me@foo.com.'                 => 'me@foo.com',
			'me@foo.com..'                => 'me@foo.com',
			'me@foo,com'                  => 'me@foo.com',
			'me@foo.c,om'                 => 'me@foo.com',
			'me@foo@com'                  => 'me@foo.com',
			'me@msn'                      => 'me@msn.com',
			'me,you@foo.com'              => 'meyou@foo.com',
			'me.foo.com'                  => 'me@foo.com',
			'me you bar@foo.com'          => 'meyoubar@foo.com',
			'me'                          => false,
		);

		foreach ( $table as $email => $expected ) {
			$actual = $this->repairer->repair( $email );
			$this->assertEquals( $expected, $actual );
		}
	}

}

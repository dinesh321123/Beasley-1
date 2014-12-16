<?php

namespace GreaterMedia\LiveFyre;

class PluginTest extends \WP_UnitTestCase {

	function test_it_can_be_created() {
		$plugin = new Plugin();
		$this->assertInstanceOf( 'GreaterMedia\LiveFyre\Plugin', $plugin );
	}

}

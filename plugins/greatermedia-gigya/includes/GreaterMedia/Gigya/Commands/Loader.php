<?php

namespace GreaterMedia\Gigya\Commands;

class Loader {

	function load() {
		\WP_CLI::add_command(
			'gigya', 'GreaterMedia\Gigya\Commands\GigyaCommand'
		);
	}

}

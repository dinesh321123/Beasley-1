<?php

namespace GreaterMedia\Gigya\Commands;

class Loader {

	function load() {
		\WP_CLI::add_command(
			'gigya', 'GreaterMedia\Gigya\Commands\GigyaCommand'
		);

		\WP_CLI::add_command(
			'marketron', 'GreaterMedia\Gigya\Commands\MarketronCommand'
		);

		\WP_CLI::add_command(
			'emma', 'GreaterMedia\MyEmma\Commands\EmmaCommand'
		);
	}

}

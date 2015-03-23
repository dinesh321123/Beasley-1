<?php

namespace WordPress\Utils;

class ProgressBar extends \cli\progress\Bar {

	public $message_size = 75;

	function __construct( $message, $count ) {
		$message = str_pad( $message, $this->message_size, ' ', STR_PAD_RIGHT );
		parent::__construct( $message, $count );
	}

}

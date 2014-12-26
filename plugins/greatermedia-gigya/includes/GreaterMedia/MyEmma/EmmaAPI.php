<?php

namespace GreaterMedia\MyEmma;

require_once __DIR__ . '/../../MyEmma/Emma.php';

class EmmaAPI extends \Emma {

	function __construct( $account_id = null, $public_key = null, $private_key = null ) {
		// TODO: will come from options
		parent::__construct(
			'1745171', 'bb784afb4e46477af27a', '89f30ef0b55cdd12b41c'
		);
		//$this->_debug = true;
	}

}

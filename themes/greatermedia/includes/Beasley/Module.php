<?php

namespace Beasley;

abstract class Module {

	/**
	 * Converts method name into callable and returns it to use as a callback for
	 * an action, filter or another function that needs callable callback.
	 *
	 * @access public
	 * @param string $method
	 * @return array
	 */
	public function __invoke( $method ) {
		return array( $this, $method );
	}

	/**
	 * Registers current module.
	 *
	 * @abstract
	 * @access public
	 */
	public abstract function register();

}

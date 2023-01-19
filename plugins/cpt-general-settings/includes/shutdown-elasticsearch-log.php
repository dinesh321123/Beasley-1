<?php

/**
 * Class LogShutdownTest logs that shutdown was called. This is a temporary test which will eventually be removed.
 * THis can be removed without breaking any site functionality.
 */
class LogShutdownTest
{
	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init()
	{
		add_action( 'shutdown', array(__CLASS__, 'log_shutdown'), 999 );
	}

	public static function log_shutdown()
	{
		error_log( '90210 - shutdown reached with 999 priority action' );
	}

}

LogShutdownTest::init();

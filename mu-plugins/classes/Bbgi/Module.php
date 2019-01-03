<?php

namespace Bbgi;

abstract class Module {

	private static $_modules = array();

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

	/**
	 * Registers modules.
	 *
	 * @static
	 * @access public
	 */
	public static function register_modules() {
		self::$_modules = array(
			'site'              => new \Bbgi\Site(),
			'seo'               => new \Bbgi\Seo(),
			'settings'          => new \Bbgi\Settings(),
			'shortcodes'        => new \Bbgi\Shortcodes(),
			'video'             => new \Bbgi\Media\Video(),
			'image-attributes'  => new \Bbgi\Image\Attributes(),
			'thumbnail-column'  => new \Bbgi\Image\ThumbnailColumn(),
			'experience-engine' => new \Bbgi\Integration\ExperienceEngine(),
			'google'            => new \Bbgi\Integration\Google(),
			'firebase'          => new \Bbgi\Integration\Firebase(),
			'dfp'               => new \Bbgi\Integration\Dfp(),
		);

		if ( current_theme_supports( 'secondstreet' ) ) {
			self::$_modules['secondstreet'] = new \Bbgi\Integration\SecondStreet();
		}

		foreach ( self::$_modules as $module ) {
			$module->register();
		}
	}

	/**
	 * Returns a module.
	 *
	 * @static
	 * @access public
	 * @param string $name
	 * @return \Bbgi\Module
	 */
	public static function get( $name ) {
		return ! empty( self::$_modules[ $name ] )
			? self::$_modules[ $name ]
			: null;
	}

}

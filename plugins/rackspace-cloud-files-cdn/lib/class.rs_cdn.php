<?php

/**
 * CDN class
 */
class RS_CDN {


	public $oc_connection;
	public $oc_container;
	public $cdn_url;
	public $api_settings;
	public $uploads;


	/**
	 *  Create new Openstack Object
	 */
	function __construct($custom_settings = null, $oc_version = null) {
		// Get settings, if they exist
		(object) $custom_settings = (!is_null($custom_settings)) ? $custom_settings : get_option( RS_CDN_OPTIONS );

		// Set settings
		$settings = new stdClass();
		$settings->username = (isset($custom_settings->username)) ? $custom_settings->username : 'Username';
		$settings->apiKey = (isset($custom_settings->apiKey)) ? $custom_settings->apiKey : 'API Key';
		$settings->use_ssl = (isset($custom_settings->use_ssl)) ? $custom_settings->use_ssl : false;
		$settings->container = (isset($custom_settings->container)) ? $custom_settings->container : 'default';
		$settings->cdn_url = (isset($custom_settings->cdn_url)) ? $custom_settings->cdn_url : null;
		$settings->files_to_ignore = (isset($custom_settings->files_to_ignore)) ? $custom_settings->files_to_ignore : null;
		$settings->remove_local_files = (isset($custom_settings->remove_local_files)) ? $custom_settings->remove_local_files : false;
		$settings->custom_cname = (isset($custom_settings->custom_cname)) ? $custom_settings->custom_cname : null;
		$settings->region = (isset($custom_settings->region)) ? $custom_settings->region : 'ORD';
		$settings->url = (isset($custom_settings->url)) ? $custom_settings->url : 'https://identity.api.rackspacecloud.com/v2.0/';

		// Set API settings
		$this->api_settings = (object) $settings;
	}


	/**
	 * Openstack Connection Object
	 *
	 * @access private
	 * @return \OpenCloud\ObjectStore\Service
	 */
	private function connection_object() {
		// If connection object is already set, return it
		if ( isset( $this->oc_connection ) ) {
			// Return existing connection object
			return $this->oc_connection;
		}

		// Get settings
		$api_settings = $this->api_settings;

		// Create connection object
		$connection = new \OpenCloud\Rackspace(
			$api_settings->url, array(
			'username' => $api_settings->username,
			'apiKey' => $api_settings->apiKey
			)
		);

		// Try to create connection object
		try {
			$cdn = $connection->ObjectStore( 'cloudFiles', $api_settings->region, 'publicURL' );
			$this->oc_connection = $cdn;
			return $this->oc_connection;
		} catch ( Exception $exc ) {
			$this->oc_connection = null;
			return null;
		}
	}

	/**
	 * Retrieve Openstack CDN Container Object
	 *
	 * @return \OpenCloud\ObjectStore\Container
	 */
	public function container_object() {
		// If container object is already set, return it
		if ( isset( $this->oc_container ) ) {
			// Return existing container
			return $this->oc_container;
		}

		// Get settings
		$api_settings = $this->api_settings;

		// Check if connection object is valid
		if ( is_null( $this->connection_object() ) ) {
			return null;
		}

		// Setup container
		try {
			// Try to set container
			$this->oc_container = $this->connection_object()->Container( $api_settings->container );

			// Return container
			return $this->oc_container;
		} catch ( Exception $exc ) {
			$this->oc_container = null;
			return null;
		}
	}

	/**
	 * Uploads given file attachment to CDN
	 */
	public function upload_file( $file_path, $file_name = null ) {
		// Get ready to upload file to CDN
		$container = $this->container_object();
		if ( ! $container ) {
			return false;
		}
		
		// Create params array to upload object
		$params = array();
		$content_type = get_content_type( $file_path );
		if ( $content_type !== false ) {
			$params['content_type'] = $content_type;
		}

		// Create file object and upload it
		$file = $container->DataObject();
		$file->name = ! empty( $file_name ) ? $file_name : basename( $file_path );;
		if ( $file->Create( $params, $file_path ) ) {
			return true;
		}

		return false;
	}

	public function download_file( $file_path, $file_name ) {
		// Get ready to upload file to CDN
		$container = $this->container_object();
		if ( ! $container ) {
			return false;
		}

		// Create file object and download it
		$file = $container->DataObject( ! empty( $file_name ) ? $file_name : basename( $file_path ) );
		if ( $file->SaveToFilename( $file_path ) ) {
			return true;
		}

		return false;
	}

	/**
	*  Get list of CDN objects
	*/
	public function get_cdn_objects( $force_cache = false ) {
		// temporary deactivated
		return array();

		static $cdn_objects_cache = null;

		if ( ! is_null( $cdn_objects_cache ) && ! $force_cache ) {
			return $cdn_objects_cache;
		}

		$cdn_objects_cache = array();

		// Ensure CDN instance exists
		if ( check_cdn() !== false ) {
			// Path to cache file
			$cache_file_path = RS_CDN_PATH . 'object_cache.dat';

			// Check if caching is enabled
			if ( $force_cache === true || ! is_writable( RS_CDN_PATH ) || ! is_writable( $cache_file_path ) ) {
				// Update object cache
				try {
					$container = $this->container_object();
					if ( ! $container ) {
						return array();
					}
					
					$objects = $container->objectList();
				} catch ( Exception $exc ) {
					return array();
				}

				// Setup objects
				$cdn_objects_cache = array();
				foreach ( $objects as $object ) {
					$cdn_objects_cache[] = array( 'fn' => $object['name'], 'fs' => $object['bytes'] );
				}

				// Write files to cache file
				if ( is_writable( RS_CDN_PATH ) && ( is_writable( $cache_file_path ) || ! file_exists( $cache_file_path ) ) ) {
					// Write to cache file
					file_put_contents( $cache_file_path, serialize( $cdn_objects_cache ) );
				}
			} else {
				// Return caching
				$cdn_objects_cache = unserialize( file_get_contents( $cache_file_path ), true );
			}
		}

		return $cdn_objects_cache;
	}

	/**
	 * Force CDN object cache
	 */
	public function force_object_cache() {
		$this->get_cdn_objects( true );
	}


	/**
	* Removes given file attachment(s) from CDN
	*/
	public function delete_files( $files ) {
		// Get container object
		$container = $this->container_object();
		if ( ! $container ) {
			return false;
		}

		// Delete object(s)
		if (count($files) > 0) {
			foreach ($files as $cur_file) {
				if (trim($cur_file) == '') {
					continue;
				}
				try {
					$file = $container->DataObject();
					$file->name = $cur_file;
					try {
						@$file->Delete();
					} catch (Exception $exc) {
						// Do nothing
					}
				} catch (Exception $exc) {
					// Do nothing
				}
			}

            // Force CDN cache because we removed files
            $this->force_object_cache();
		}
		return true;
	}
}
<?php

/**
 * Ensure CDN instance exists
 */
function check_cdn() {
	global $rackspace_cdn;

	// Verify class has been loaded
	if ( ! class_exists( 'RS_CDN' ) ) {
		require_once RS_CDN_PATH . "lib/class.rs_cdn.php";
	}

	// Check if CDN exists
	try {
		if ( ! $rackspace_cdn || ! is_a( $rackspace_cdn, 'RS_CDN' ) ) {
			$rackspace_cdn = new RS_CDN();
		}
	} catch ( Exception $exc ) {
		return false;
	}

	// Check if connection OR container objects are null, if so, return false
	if ( is_null( $rackspace_cdn->connection_object() ) || is_null( $rackspace_cdn->container_object() ) ) {
		return false;
	}

	// Session created successfully
	return true;
}


/**
 * Schedules attachment synchronization.
 * 
 * @param array $meta_data The attachment metadata.
 * @param int $post_id The attachment id.
 */
function rackspace_on_attachment_metadata_update( $meta_data, $post_id ) {
	if ( check_cdn() === false ) {
		return $meta_data;
	}

	global $rackspace_cdn;

	// get upload dir and attachment file
	$upload_dir = wp_upload_dir();

	$filename = ! empty( $meta_data['file'] ) ? $meta_data['file'] : get_post_meta( $post_id, '_wp_attached_file', true );
	$filepath = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $filename;

	// sync image
	if ( empty( $meta_data[ RS_META_SYNCED ] ) ) {
		try {
			if ( is_readable( $filepath ) ) {
				// upload file
				$rackspace_cdn->upload_file( $filepath, $filename );
				// update metadata
				$meta_data[ RS_META_SYNCED ] = true;

				// delete file when successfully uploaded, if set
				if ( isset( $rackspace_cdn->api_settings->remove_local_files ) && $rackspace_cdn->api_settings->remove_local_files == true ) {
					@unlink( $filepath );
				}
			}
		} catch ( Exception $e ) {}
	}

	// sync image sizes
	if ( ! empty( $meta_data['sizes'] ) ) {
		$root_dir = dirname( $filepath ) . DIRECTORY_SEPARATOR;
		$base_dir = dirname( $meta_data['file'] ) . DIRECTORY_SEPARATOR;
		foreach ( $meta_data['sizes'] as $size => $meta ) {
			if ( empty( $meta_data['sizes'][ $size ][ RS_META_SYNCED ] ) ) {
				try {
					$cur_file = $root_dir . $meta['file'];
					if ( ! is_readable( $cur_file ) ) {
						continue;
					}

					// upload file
					$rackspace_cdn->upload_file( $cur_file, $base_dir . $meta['file'] );
					// update metadata
					$meta_data['sizes'][ $size ][ RS_META_SYNCED ] = true;

					// delete file when successfully uploaded, if set
					if ( isset( $rackspace_cdn->api_settings->remove_local_files ) && $rackspace_cdn->api_settings->remove_local_files == true ) {
						@unlink( $cur_file );
					}
				} catch ( Exception $e ) {}
			}
		}
	}
	
	return $meta_data;
}
add_filter( 'wp_update_attachment_metadata', 'rackspace_on_attachment_metadata_update', 10, 2 );


/**
 * Updates attachment URL to use CDN version of a file if possible.
 * 
 * @global RS_CDN $rackspace_cdn
 * @param string $url Initial attachment URL.
 * @param int $attachment_id The attachment id.
 * @return string The attachment URL.
 */
function rackspace_update_attachment_url( $url, $attachment_id ) {
	if ( check_cdn() === false ) {
		return $url;
	}

	$metadata = wp_get_attachment_metadata( $attachment_id );
	if ( empty( $metadata[ RS_META_SYNCED ] ) ) {
		return $url;
	}

	global $rackspace_cdn;

	$file = get_post_meta( $attachment_id, '_wp_attached_file', true );
	if ( isset( $rackspace_cdn->api_settings->custom_cname ) && trim( $rackspace_cdn->api_settings->custom_cname ) != '' ) {
		$cdn_url = $rackspace_cdn->api_settings->custom_cname;
	} else {
		$cdn_url = isset( $rackspace_cdn->api_settings->use_ssl ) ? get_cdn_url( 'ssl' ) : get_cdn_url();
	}

	return trailingslashit( $cdn_url ) . $file;
}
add_filter( 'wp_get_attachment_url', 'rackspace_update_attachment_url', 1, 2 );


/**
 * Updates attachment image attributes to use rackspace URL if available.
 *
 * @global RS_CDN $rackspace_cdn
 * @param array $attr The attachment image attributes.
 * @param WP_Post $attachment The attachment object.
 * @param string $size The attachment image size.
 * @return array Attachment attributes.
 */
function rackspace_update_attachment_image_attr( $attr, $attachment, $size ) {
	if ( check_cdn() === false ) {
		return $attr;
	}

	$metadata = wp_get_attachment_metadata( $attachment->ID );
	if ( empty( $metadata['sizes'][ $size ][ RS_META_SYNCED ] ) ) {
		return $attr;
	}

	global $rackspace_cdn;

	if ( isset( $rackspace_cdn->api_settings->custom_cname ) && trim( $rackspace_cdn->api_settings->custom_cname ) != '' ) {
		$cdn_url = $rackspace_cdn->api_settings->custom_cname;
	} else {
		$cdn_url = isset( $rackspace_cdn->api_settings->use_ssl ) ? get_cdn_url( 'ssl' ) : get_cdn_url();
	}

	$attr['src'] = trailingslashit( $cdn_url ) . $metadata['sizes'][ $size ]['file'];
	
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'rackspace_update_attachment_image_attr', 1, 3 );


/**
 * Removes CDN file when attachment is deleted.
 *
 * @global RS_CDN $rackspace_cdn
 * @param int $attachment_id Attachment id.
 */
function rackspace_delete_attachment( $attachment_id ) {
	if ( check_cdn() === false ) {
		return;
	}

	global $rackspace_cdn;

	$files = array();
	$metadata = wp_get_attachment_metadata( $attachment_id );
	$base_dir = trailingslashit( dirname( $metadata['file'] ) );

	if ( ! empty( $metadata[ RS_META_SYNCED ] ) ) {
		if ( ! isset( $metadata['file'] ) ) {
			$files[] = get_post_meta( $attachment_id, '_wp_attached_file', true );
		} else {
			$files[] = $metadata['file'];
		}
	}

	if ( ! empty( $metadata['sizes'] ) ) {
		foreach ( $metadata['sizes'] as $meta ) {
			if ( ! empty( $meta[ RS_META_SYNCED ] ) ) {
				$files[] = $base_dir . $meta['file'];
			}
		}
	}

	if ( ! empty( $files ) ) {
		$rackspace_cdn->delete_files( $files );
	}
}
add_action( 'delete_attachment', 'rackspace_delete_attachment' );


/**
 * Upload main image and thumbnails to CDN.
 * Remove the local copy if user specified in settings.
 */
function upload_images($meta_id, $post_id, $meta_key='', $meta_value='') {
    // Check attachment metadata
    if ($meta_key == '_wp_attachment_metadata') {
    	// Ensure CDN instance exists
		if (check_cdn() === false) {
			return false;
			die();
		}

		global $rackspace_cdn;

		// Get upload dir
		$upload_dir = wp_upload_dir();

		// Get files to upload
		$files_to_upload = get_files_to_sync();

		// Add original file to array
		$files_to_upload['upload'][] = array('fn' => $meta_value['file']);

		// Upload files
		foreach ($files_to_upload['upload'] as $cur_file) {
			// Set file name
			$cur_file_data = $cur_file;
			$cur_file = $upload_dir['basedir'].'/'.$cur_file_data['fn'];
			$file_name = $cur_file_data['fn'];

			// Upload file to CDN, add to file check
			try {
				$rackspace_cdn->upload_file($cur_file, $file_name);
			} catch (Exception $exc) {
				return false;
				die();
			}

			// Delete file when successfully uploaded, if set
			if (isset($rackspace_cdn->api_settings->remove_local_files) && $rackspace_cdn->api_settings->remove_local_files == true) {
				@unlink($cur_file);
			}
		}

        // Update CDN image cache
        $rackspace_cdn->get_cdn_objects(true);

		return true;
    }

	// Check attached file meta
    if ($meta_key == '_wp_attached_file') {
    	// Ensure CDN instance exists
		if (check_cdn() === false) {
			return false;
			die();
		}

		global $rackspace_cdn;

		// Get upload dir
		$upload_dir = wp_upload_dir();

		$cur_file = $upload_dir['basedir'].'/'.$meta_value;
		$file_name = $meta_value;
		$content_type = get_content_type($cur_file);

		// Upload file to CDN, add to file check
		try {
			$rackspace_cdn->upload_file($cur_file, $file_name);
		} catch (Exception $exc) {
			return false;
			die();
		}

		// Delete file when successfully uploaded, if set
		if (isset($rackspace_cdn->api_settings->remove_local_files) && $rackspace_cdn->api_settings->remove_local_files == true) {
			
			if (stripos($content_type, 'image') === false) {
			    @unlink($cur_file);
		    }
		}

        // Update CDN image cache
        $rackspace_cdn->get_cdn_objects(true);

		return true;
    }
}
//add_action('added_post_meta', 'upload_images', 10, 4);
//add_action('updated_post_meta', 'upload_images', 10, 4);


/**
 * Delete file from CDN
 */
function remove_cdn_files( $post_id ){
	global $wpdb, $rackspace_cdn;

	// Ensure CDN instance exists
	if (check_cdn() === false) {
		return false;
	}
	
	// Get attachment metadata so we can delete all attachments associated with this image
	$query = $wpdb->prepare( "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND (meta_key = '_wp_attachment_metadata' OR meta_key = '_wp_attached_file')", $post_id );
	$attachment_metadata = $wpdb->get_results( $query );
	if ( count( $attachment_metadata ) > 0 ) {
		// Check if meta value or attached file
        foreach ($attachment_metadata as $cur_attachment_metadata) {
			// Unserialize image data
			$all_image_sizes = unserialize($cur_attachment_metadata->meta_value);

            // Check if unserialize was successful, if so, this is an array/object
			if ($all_image_sizes !== false) {
				// Add main file to delete request
				if (trim($all_image_sizes['file']) != '') {
					$files_to_delete[] = $all_image_sizes['file'];
				}

				// Get attachment folder name
				$attach_folder = pathinfo($all_image_sizes['file']);
				$attach_folder = ($attach_folder['dirname'] != '') ? trim($attach_folder['dirname'], '/').'/' : '';

				// Add each thumb to array
				if (isset($all_image_sizes['sizes']) && count($all_image_sizes['sizes']) > 0) {
					foreach ($all_image_sizes['sizes'] as $cur_img_size) {
						// Add attachment to delete queue
						if (trim($cur_img_size['file']) != '') {
						    // Set current file name
						    $cur_file = trim($attach_folder.basename($cur_img_size['file']));

							// Don't add if already in array
							if (!in_array($cur_file, $files_to_delete)) {
							    $files_to_delete[] = $cur_file;
							 }
						}
					}
				}
			} else {
				// Add file to delete
				$files_to_delete[] = $cur_attachment_metadata->meta_value;
			}
		}
	}

	// Send batch delete
	$rackspace_cdn->delete_files( $files_to_delete );
}
//add_action( 'delete_attachment', 'remove_cdn_files');


/**
 * Verify file does not exist so we don't overwrite it.
 * If the file exists, increment the file name.
 */
function verify_filename($filename, $filename_raw = null) {
	global $wpdb;

	// Ensure CDN instance exists
	if (check_cdn() === false) {
		return $filename;
	}

	// Get file info
	$info = pathinfo($filename);
	$ext  = empty($info['extension']) ? '' : '.' . $info['extension'];

	// Get attachment metadata so we can delete all attachments associated with this image
	$query = $wpdb->prepare( "SELECT guid FROM {$wpdb->posts} WHERE guid LIKE %s", "%" . preg_replace( '/[0-9]*$/', '', $info['filename'] ) . "%" . $info['extension'] );
	$existing_files = $wpdb->get_results( $query );

	// Check if file exists
	if (count($existing_files) > 0) {
		// File list
		foreach ($existing_files as $cur_file) {
			$my_files[] = basename($cur_file->guid);
		}

		// Loop through files
		$i=1;
		foreach ($my_files as $cur_file) {
			$file_parts = pathinfo($cur_file);

			if ($file_parts['basename'] == basename($filename)) {
				$filename = $file_parts['filename'].'.'.$file_parts['extension'];
				while (in_array($filename, $my_files)) {
					$filename = $file_parts['filename'].$i++.'.'.$file_parts['extension'];
				}
			}
		}
	}

	return basename($filename);
}
add_filter('sanitize_file_name', 'verify_filename', 10, 2);


/**
 * Get a list of the files that need uploaded
 */
function get_files($params) {
	// Ensure CDN instance exists
	if (check_cdn() === false) {
		return array('response' => 'fail', 'message' => 'Error instantiating CDN session.');
	}

	$arr_files_to_sync = get_files_to_sync();
	$arr_files_to_sync = array_merge($arr_files_to_sync['upload'], $arr_files_to_sync['download']);
	echo json_encode($arr_files_to_sync);
	die();
}
add_action('wp_ajax_get_files', 'get_files');


/**
*  Get list of files to sync
*/
function get_files_to_sync() {
	// temporary deactivated
	return array( 'upload' => array(), 'download' => array() );

	// Array to store files needing upload/download
	$objects_to_upload = array();
	$objects_to_download = array();

	// Ensure CDN instance exists
	if (check_cdn() === false) {
		return array('response' => 'fail', 'message' => 'Error instantiating CDN session.', 'upload' => $objects_to_upload, 'download' => $objects_to_download);
	}

	global $rackspace_cdn;

	// Get CDN objects
	$local_objects = get_local_files();
	$remote_objects = $rackspace_cdn->get_cdn_objects(true);

	// If CDN objects is null, we need to return an error because we couldn't fetch them
	if (is_null($remote_objects)) {
		return array('response' => 'error', 'message' => 'Unable to retrieve files.');
	}

	// Check local files needing uploaded
	foreach ($local_objects as $cur_local_object) {
		if (!in_array($cur_local_object, $remote_objects) && $cur_local_object['fs'] > 0) {
			$objects_to_upload[] = $cur_local_object;
		}
	}

	// Check remote files needing DOWNloaded
	foreach ($remote_objects as $cur_remote_object) {
		if (!in_array($cur_remote_object, $local_objects) && $cur_remote_object['fs'] > 0) {
			$cdn_url = (isset($rackspace_cdn->api_settings->use_ssl)) ? get_cdn_url('ssl') : get_cdn_url();
			$cur_remote_object['fn'] = $cdn_url.'/'.$cur_remote_object['fn'];
			$objects_to_download[] = $cur_remote_object;
		}
	}

	// Return array of files that need synchronized
	return array('upload' => $objects_to_upload, 'download' => $objects_to_download);
}


/**
 * Get a list of the files that need uploaded
 */
function get_files_to_remove($params) {
	echo json_encode(get_local_files());
	die();
}
add_action('wp_ajax_get_files_to_remove', 'get_files_to_remove');


/**
 * Sync existing local file to CDN
 */
function sync_existing_file() {
	// Ensure CDN instance exists
	if (check_cdn() === false) {
		echo json_encode(array('response' => 'fail', 'message' => 'Error instantiating CDN session.'));
		die();
	}

	global $rackspace_cdn;

    // Get CDN object(s)
    // $cdn_objects = $rackspace_cdn->get_cdn_objects();

	// Upload file - Get file to upload
	$upload_dir = wp_upload_dir();
	$file_to_sync = $_REQUEST['file_path'];

	// Check if file needs uploaded or downloaded
	if (stripos($file_to_sync, 'http') === false) {
		// Upload file, prepend local file path
		$file_to_sync = $upload_dir['basedir'].'/'.$file_to_sync;

		// Check if file exists, fail if not
		if (!file_exists($file_to_sync)) {
			echo json_encode(array('response' => 'error', 'message' => 'Upload for "'.basename($file_to_sync).'" failed (SEF-001).'));
			die();
		}

		// Get upload dir
		$upload_dir = wp_upload_dir();

		// Try to upload file
		try {
			// Try to upload file
			$rackspace_cdn->upload_file($file_to_sync, str_replace($upload_dir['basedir'].'/', '', $file_to_sync));
		} catch (Exception $exc) {
			// Let the browser know upload failed
			echo json_encode(array('response' => 'error', 'message' => 'Upload for "'.basename($file_to_sync).'" failed. Exception: '.$exc.' (SEF-002).'));
			die();
		}

		// Verify file was successfully uploaded
		if (isset($rackspace_cdn->api_settings->remove_local_files) && $rackspace_cdn->api_settings->remove_local_files == true) {
			if (verify_exists($file_to_sync) == true) {
				@unlink($file_to_sync);
			}
		}

		// Force CDN object cache
		$rackspace_cdn->force_object_cache();
	} else {
		// Download file - Get CDN URL
		$cdn_url = (isset($rackspace_cdn->api_settings->use_ssl)) ? get_cdn_url('ssl') : get_cdn_url();

		// Write file to disk
		$file_info = pathinfo($file_to_sync);
		$remote_file_name = rawurlencode($file_info['filename']);
		file_put_contents(str_replace($cdn_url, $upload_dir['basedir'], $file_to_sync), file_get_contents(str_replace($file_info['filename'], $remote_file_name, $file_to_sync)));
	}

    // If this is the last file, force update CDN cache
    if ($_REQUEST['current_file_num'] == $_REQUEST['rs_cdn_num_files_to_sync']) {
        $rackspace_cdn->force_object_cache();
    }

	// Let the browser know upload was successful
	echo json_encode(array('response' => 'success', 'file_path' => $file_to_sync));
	die();
}
add_action('wp_ajax_sync_existing_file', 'sync_existing_file');
add_action('wp_ajax_upload_existing_file', 'sync_existing_file');


/**
 * Remove existing local file, verify it's on the CDN first
 */
function remove_existing_file() {
	// Ensure CDN instance exists
	if (check_cdn() === false) {
		echo json_encode(array('response' => 'error', 'message' => 'Error instantiating CDN session.'));
		die();
	}

	global $rackspace_cdn;

	// Upload file - Get file to upload
	$upload_dir = wp_upload_dir();
	$file_to_sync = $_REQUEST['file_path'];

	// Get CDN URL
	$cdn_url = (isset($rackspace_cdn->api_settings->use_ssl)) ? get_cdn_url('ssl') : get_cdn_url();

	// Get remote file URL
	$file_info = pathinfo($file_to_sync);
	$remote_file_name = str_replace($file_info['filename'], rawurlencode($file_info['filename']), $file_to_sync);

	// If file is not on the CDN, upload it
	if (verify_exists($cdn_url.'/'.$remote_file_name)) {
		// Headers are good, delete local file
		try {
			// Remove file
			unlink($upload_dir['basedir'].'/'.$file_to_sync);
		} catch (Exception $exc) {
			// Let the browser know file removal failed
			echo json_encode(array('response' => 'error', 'message' => $exc));
			die();
		}
	} else {
		// Try to upload file
		try {
			// Try to upload file
			$rackspace_cdn->upload_file($upload_dir['basedir'].'/'.$file_to_sync, $file_to_sync);

			// Successful upload, delete the file
			unlink($upload_dir['basedir'].'/'.$file_to_sync);
		} catch (Exception $exc) {
			// Let the browser know upload failed
			echo json_encode(array('response' => 'error', 'message' => 'Upload for "'.basename($file_to_sync).'" failed (REF-002).'));
			die();
		}
	}

    // If this is the last file, force update CDN cache
    if ($_REQUEST['current_file_num'] == $_REQUEST['rs_cdn_num_files_to_sync']) {
        $rackspace_cdn->force_object_cache();
    }

	// Let the browser know upload was successful
	echo json_encode(array('response' => 'success', 'file_path' => $upload_dir['basedir'].'/'.$file_to_sync));
	die();
}
add_action('wp_ajax_remove_existing_file', 'remove_existing_file');


/**
 *	Set CDN path for image
  */
function set_cdn_path($attachment) {
    // Ensure CDN instance exists
	if (check_cdn() === false) {
		return $attachment;
	}

	global $rackspace_cdn;

	// Uploads folder data
	$upload_data = wp_upload_dir();

    // If HTTPS is on, replace http with https URL
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        $local_uploads_url = str_replace('http://', 'https://', $upload_data['baseurl']).'/';
    } else {
        $local_uploads_url = $upload_data['baseurl'];
    }

	// Get public CDN URL
	try {
		if (isset($rackspace_cdn->api_settings->custom_cname) && trim($rackspace_cdn->api_settings->custom_cname) != '') {
			 $cdn_url = $rackspace_cdn->api_settings->custom_cname;
		} else {
			$cdn_url = (isset($rackspace_cdn->api_settings->use_ssl)) ? get_cdn_url('ssl') : get_cdn_url();
		}
	} catch (Exception $e) {
		return $attachment;
	}

    // Get base URL defined for uploads
    $base_uploads_url = str_replace(array('http://','https://'), '(http|https)://', $upload_data['baseurl']);
    $base_uploads_url = str_replace('/', '\/', $base_uploads_url);

    // Loop through attachments and rewrite with CDN URL if they are on the CDN
    preg_match_all('/'.$base_uploads_url.'\/.*?\.[a-z]{3}+/i', $attachment, $attachments);

    // Get attachments and check if on CDN
    if (count($attachments) > 0) {
        foreach ($attachments as $cur_attachments) {
            // Check if array
            foreach ($cur_attachments as $cur_attachment) {
                if ($cur_attachment != 'http' && $cur_attachment != 'https') {
                    // Verify attachment exists
                    $new_attachment = trim(str_replace($local_uploads_url, '', $cur_attachment), '/');

                    // If we are good to go, return the attachment
                    if (verify_exists( $new_attachment )) {
	                    // NEW ATTACHMENT IS THE FULL URL, so GET RID OF THE BASE!!!!
	                    $new_attachment = str_replace( $upload_data['baseurl'], '', $new_attachment );
	                    if ( strpos( $new_attachment, '/' ) === 0 ) {
							$new_attachment = substr( $new_attachment, 1 );
	                    }

	                    $attachment = str_replace($cur_attachment, trailingslashit( $cdn_url ) . $new_attachment, $attachment);
                    }
                }
            }
        }
    }
    
    // Return attachment
    return $attachment;
}
//add_filter('the_content', 'set_cdn_path');
//add_filter('richedit_pre', 'set_cdn_path');


/**
 *	Get local files
 */
function get_local_files() {
	global $rackspace_cdn;
	
	// Get uploads directory
	$upload_dir = wp_upload_dir();
	$dir = $upload_dir['basedir'];
	$local_files = array();

	// If uploads directory is not found, tell the user to create it
	if (!is_dir($dir)) {
		return array('response' => 'error', 'message' => 'Directory "'.$dir.'" not found. Please create it.');
	}

	// Setup directory iterator
	$files = new RecursiveIteratorIterator(
	    new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
	);

	// Loop through files and find out if they need uploaded
	$i = 0;
	foreach ($files as $fileinfo) {
		$the_file = $fileinfo->getRealPath();
		$file_path = pathinfo($the_file);
	    if (!is_dir($the_file)) {
	    	if (isset($rackspace_cdn->api_settings->files_to_ignore)) {
	    		// File extensions ignored
	    		$ignore_files = explode(",", $rackspace_cdn->api_settings->files_to_ignore);
		    	if (!in_array($file_path['extension'], $ignore_files)) {
		    		$cur_local_file = $fileinfo->getRealPath();
		    		$local_files[$i++] = array('fn' => trim(str_replace($upload_dir['basedir'], '', $cur_local_file), '/'), 'fs' => filesize($cur_local_file));
		    	}
	    	} else {
		    	// No file extensions ignored
		    	$cur_local_file = $fileinfo->getRealPath();
		    	$local_files[$i++] = array('fn' => trim(str_replace($upload_dir['basedir'], '', $cur_local_file), '/'), 'fs' => filesize($cur_local_file));
	    	}
	    }
	}
	return $local_files;
}


/**
 *	Verify file exists
 */
function verify_exists( $file_path = null ) {
	// Ensure CDN instance exists
	if (check_cdn() === false || is_null($file_path)) {
		return false;
	}

	global $rackspace_cdn;

	// Get CDN URL
	if (isset($rackspace_cdn->api_settings->custom_cname) && trim($rackspace_cdn->api_settings->custom_cname) != '') {
		$cdn_url = $rackspace_cdn->api_settings->custom_cname;
	} else {
		$cdn_url = (isset($rackspace_cdn->api_settings->use_ssl)) ? get_cdn_url('ssl') : get_cdn_url();
	}

	// Define variables needed
	$upload_dir = wp_upload_dir();
	
	// Get local file path
	$file_url = str_replace(array($cdn_url.'/', $upload_dir['basedir'].'/', $upload_dir['baseurl'].'/'), '', $file_path);

	// Return true/false if file exists on CDN or not
	return find_file_name( $file_url );
}


/**
 * Multidimensional array search
 */
function find_file_name( $file_name ) {
    // Ensure CDN instance exists
	if (check_cdn() === false) {
		return false;
	}

	global $rackspace_cdn;

	// Get CDN objects
	$cdn_objects = $rackspace_cdn->get_cdn_objects();

	// Loop through and see if we can find the file name
	foreach ($cdn_objects as $cur_cdn_object) {
		if ($cur_cdn_object['fn'] === $file_name) {
			return true;
		} 
	}

	// Return false by default
	return false;
}


/**
 * Get CDN URL
 */
function get_cdn_url($type = 'http') {
	// Ensure CDN instance exists
	if (check_cdn() === false) {
		$wp_url = wp_upload_dir();
		return $wp_url['baseurl'];
	}

	global $rackspace_cdn;

	// Get correct CDN URL
	$type = strtolower($type);
	if ($type == 'ssl' || $type == 'https') {
		// Return HTTPS URI
		return $rackspace_cdn->container_object()->SSLURI();
	} else {
		// Return HTTP URI
		return $rackspace_cdn->container_object()->CDNURI();
	}
}


/**
 * Get content/mime type of file
 */
function get_content_type($file) {
    // Get file info
    $ext = pathinfo($file);
    $ext = $ext['extension'];

    // Get available content types
    $cdn_content_types = get_available_content_types();

    // Return mime type for extension 
    if (isset($cdn_content_types[$ext])) { 
        return $cdn_content_types[$ext]; 
    } else { 
        return false; 
    } 
}


/**
 * Get available content types
 */
function get_available_content_types() {
    return array( 
        "ez" => "application/andrew-inset", 
        "hqx" => "application/mac-binhex40", 
        "cpt" => "application/mac-compactpro", 
        "doc" => "application/msword", 
        "bin" => "application/octet-stream", 
        "dms" => "application/octet-stream", 
        "lha" => "application/octet-stream", 
        "lzh" => "application/octet-stream", 
        "exe" => "application/octet-stream", 
        "class" => "application/octet-stream", 
        "so" => "application/octet-stream", 
        "dll" => "application/octet-stream", 
        "oda" => "application/oda", 
        "pdf" => "application/pdf", 
        "ai" => "application/postscript", 
        "eps" => "application/postscript", 
        "ps" => "application/postscript", 
        "smi" => "application/smil", 
        "smil" => "application/smil", 
        "wbxml" => "application/vnd.wap.wbxml", 
        "wmlc" => "application/vnd.wap.wmlc", 
        "wmlsc" => "application/vnd.wap.wmlscriptc", 
        "bcpio" => "application/x-bcpio", 
        "vcd" => "application/x-cdlink", 
        "pgn" => "application/x-chess-pgn", 
        "cpio" => "application/x-cpio", 
        "csh" => "application/x-csh", 
        "dcr" => "application/x-director", 
        "dir" => "application/x-director", 
        "dxr" => "application/x-director", 
        "dvi" => "application/x-dvi", 
        "spl" => "application/x-futuresplash", 
        "gtar" => "application/x-gtar", 
        "hdf" => "application/x-hdf", 
        "js" => "application/x-javascript", 
        "skp" => "application/x-koan", 
        "skd" => "application/x-koan", 
        "skt" => "application/x-koan", 
        "skm" => "application/x-koan", 
        "latex" => "application/x-latex", 
        "nc" => "application/x-netcdf", 
        "cdf" => "application/x-netcdf", 
        "sh" => "application/x-sh", 
        "shar" => "application/x-shar", 
        "swf" => "application/x-shockwave-flash", 
        "sit" => "application/x-stuffit", 
        "sv4cpio" => "application/x-sv4cpio", 
        "sv4crc" => "application/x-sv4crc", 
        "tar" => "application/x-tar", 
        "tcl" => "application/x-tcl", 
        "tex" => "application/x-tex", 
        "texinfo" => "application/x-texinfo", 
        "texi" => "application/x-texinfo", 
        "t" => "application/x-troff", 
        "tr" => "application/x-troff", 
        "roff" => "application/x-troff", 
        "man" => "application/x-troff-man", 
        "me" => "application/x-troff-me", 
        "ms" => "application/x-troff-ms", 
        "ustar" => "application/x-ustar", 
        "src" => "application/x-wais-source", 
        "xhtml" => "application/xhtml+xml", 
        "xht" => "application/xhtml+xml", 
        "zip" => "application/zip", 
        "au" => "audio/basic", 
        "snd" => "audio/basic", 
        "mid" => "audio/midi", 
        "midi" => "audio/midi", 
        "kar" => "audio/midi", 
        "mpga" => "audio/mpeg", 
        "mp2" => "audio/mpeg", 
        "mp3" => "audio/mpeg", 
        "aif" => "audio/x-aiff", 
        "aiff" => "audio/x-aiff", 
        "aifc" => "audio/x-aiff", 
        "m3u" => "audio/x-mpegurl", 
        "ram" => "audio/x-pn-realaudio", 
        "rm" => "audio/x-pn-realaudio", 
        "rpm" => "audio/x-pn-realaudio-plugin", 
        "ra" => "audio/x-realaudio", 
        "wav" => "audio/x-wav", 
        "pdb" => "chemical/x-pdb", 
        "xyz" => "chemical/x-xyz", 
        "bmp" => "image/bmp", 
        "gif" => "image/gif", 
        "ief" => "image/ief", 
        "jpeg" => "image/jpeg", 
        "jpg" => "image/jpeg", 
        "jpe" => "image/jpeg", 
        "png" => "image/png", 
        "tiff" => "image/tiff", 
        "tif" => "image/tif", 
        "djvu" => "image/vnd.djvu", 
        "djv" => "image/vnd.djvu", 
        "wbmp" => "image/vnd.wap.wbmp", 
        "ras" => "image/x-cmu-raster", 
        "pnm" => "image/x-portable-anymap", 
        "pbm" => "image/x-portable-bitmap", 
        "pgm" => "image/x-portable-graymap", 
        "ppm" => "image/x-portable-pixmap", 
        "rgb" => "image/x-rgb", 
        "xbm" => "image/x-xbitmap", 
        "xpm" => "image/x-xpixmap", 
        "xwd" => "image/x-windowdump", 
        "igs" => "model/iges", 
        "iges" => "model/iges", 
        "msh" => "model/mesh", 
        "mesh" => "model/mesh", 
        "silo" => "model/mesh", 
        "wrl" => "model/vrml", 
        "vrml" => "model/vrml", 
        "css" => "text/css", 
        "html" => "text/html", 
        "htm" => "text/html", 
        "asc" => "text/plain", 
        "txt" => "text/plain", 
        "rtx" => "text/richtext", 
        "rtf" => "text/rtf", 
        "sgml" => "text/sgml", 
        "sgm" => "text/sgml", 
        "tsv" => "text/tab-seperated-values", 
        "wml" => "text/vnd.wap.wml", 
        "wmls" => "text/vnd.wap.wmlscript", 
        "etx" => "text/x-setext", 
        "xml" => "text/xml", 
        "xsl" => "text/xml", 
        "mpeg" => "video/mpeg", 
        "mpg" => "video/mpeg", 
        "mpe" => "video/mpeg", 
        "qt" => "video/quicktime", 
        "mov" => "video/quicktime", 
        "mxu" => "video/vnd.mpegurl", 
        "avi" => "video/x-msvideo", 
        "movie" => "video/x-sgi-movie", 
        "ice" => "x-conference-xcooltalk" 
    );
}


/**
 *	Add download link for all file(s)
 */
/*
function show_download_link($actions, $post) {
	// Relative path/name of the file
	$the_file = str_replace(WP_CONTENT_URL, '.', $post->guid);

	// adding the Action to the Quick Edit row
	$actions['Download'] = '<a href="'.WP_CONTENT_URL.'/download.php?file='.$the_file.'">Download</a>';

	return $actions;    
}
add_filter('media_row_actions', 'show_download_link', 10, 2);
*/
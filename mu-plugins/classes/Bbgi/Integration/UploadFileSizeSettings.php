<?php
/**
 * MOdule responsible for registering network settings for UploadFileSizeSettings
 *
 * @pacakge Bbgi
 */

namespace Bbgi\Integration;

class UploadFileSizeSettings extends \Bbgi\Module {
	private static $_fields = array(
		'video_maxupload_filesize'  => 'Video',
		'audio_maxupload_filesize'     => 'Audio',
		'image_maxupload_filesize' => 'Image',
	);
	private static $_station_fields = array(
		'video_maxupload_filesize_station_setting'  => 'video',
		'audio_maxupload_filesize_station_setting'     => 'audio',
		'image_maxupload_filesize_station_setting' => 'image',
	);

	/**
	 * Registers module.
	 *
	 * @access public
	 */
	public function register() {
		// add_filter( 'bbgiconfig', $this( 'populate_settings' ) );

		add_action( 'wpmu_options', $this( 'show_network_settings' ) );
		add_action( 'update_wpmu_options', $this( 'save_network_settings' ) );

		add_action( 'post-upload-ui', $this('show_custom_media_message') );
		add_action( 'bbgi_register_settings', $this( 'register_settings' ), 10, 2 );
		add_filter( 'wp_handle_upload_prefilter', $this('max_upload_size' ) );
	}

	function max_upload_size( $file ) {
		$size = $file['size'];
		$size = $size / 1024;
		$type = $file['type'];

		foreach (self::$_station_fields as $id => $label) :
			if ( strpos( $type, $label ) !== false ) {
				$max_upload_size	=	esc_attr( get_option( $id ) );
				if( ! $max_upload_size ) {
					$max_upload_size_network	=	esc_attr( get_site_option( $label.'_maxupload_filesize' ) );
					$max_upload_size			= isset( $max_upload_size_network ) && $max_upload_size_network != "" ? $max_upload_size_network : wp_max_upload_size() ;
				}

				$limit = $max_upload_size;
				$limit_output = $max_upload_size. ' KB';
				if ( $size > $limit ) {
					$file['error'] = $label . ' files must be smaller than ' . $limit_output;
				}
			}
		endforeach;
		return $file;
	}

	public function register_settings( $group, $page ) {
		$section_id = 'beasley_upload_filesize';

		add_settings_section( $section_id, 'Upload File size Station Settings', '__return_false', $page );
		foreach (self::$_station_fields as $id => $label) :
			add_settings_field($id, $label.' max upload file size', array($this, 'bbgi_number_field'), $page, $section_id, array('name' => $id, 'type' => 'number','label' => $label) );
			register_setting( $group, $id, 'sanitize_text_field' );
		endforeach;
	}

	public function bbgi_number_field( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'type'    => 'text',
			'name'    => '',
			'default' => '',
			'class'   => 'regular-text',
			'desc'    => '',
		) );
		$value			= get_option( $args['name'], $args['default'] );
		$max_upload_size= esc_attr( get_site_option( $args['label'].'_maxupload_filesize' ) );

		if( ! $max_upload_size ) {
			$max_upload_size = wp_max_upload_size();
		}

		printf(
			'<label><input type="%s" name="%s" class="%s" value="%s" style="width: 100px" max="%s"> KB</label>',
			esc_attr( $args['type'] ),
			esc_attr( $args['name'] ),
			esc_attr( $args['class'] ),
			esc_attr( $value ),
			$max_upload_size
		);

		printf(
			'<p class="description"> Allocated %s size is <strong> %s KB </strong> from network settings</p>',
			esc_attr( $args['label'] ),
			$max_upload_size
		);
	}

	public function show_custom_media_message() {
		?><style>.max-upload-size { display: none; }</style><?php
		foreach ( self::$_station_fields as $id => $label ) :
			$max_upload_size	=	esc_attr( get_option( $id ) );
			if( ! $max_upload_size ) {
				$max_upload_size_network	=	esc_attr( get_site_option( $label.'_maxupload_filesize' ) );
				$max_upload_size			= isset( $max_upload_size_network ) && $max_upload_size_network != "" ? $max_upload_size_network : wp_max_upload_size() ;
			}

			echo '<p>Maximum upload '.esc_html( $label ) . ' size: ' . $max_upload_size . ' KB.</p>';
		endforeach;
	}
	public function populate_settings( $settings ) {
		$settings['upload-filesize-settings'] =  array(
			'audio_maxupload_filesize'	=> get_site_option( 'audio_maxupload_filesize' ),
			'image_maxupload_filesize'	=> get_site_option( 'image_maxupload_filesize' ),
			'video_maxupload_filesize'	=> get_site_option( 'video_maxupload_filesize' ),
		);

		return $settings;
	}

	/**
	 * Saves network settings.
	 *
	 * @access public
	 * @action update_wpmu_options
	 */
	public function save_network_settings() {
		foreach ( self::$_fields as $id => $label ) {
			$value = filter_input( INPUT_POST, $id );
			$value = sanitize_text_field( $value );
			update_site_option( $id, $value );
		}
	}

	/**
	 * Shows upload file size network settings
	 */
	public function show_network_settings() {
		?><h2>Upload File size Settings</h2>
		<table id="menu" class="form-table">
		<?php foreach ( self::$_fields as $id => $label ) : ?>
			<tr>
				<th scope="row"><?php echo esc_html( $label ).' Max upload file size'; ?></th>
				<td>
					<label>
						<input type="number" class="regular-text" name="<?php echo esc_attr( $id ); ?>" min="1" style="width: 100px" value="<?php echo esc_attr( get_site_option( $id ) ); ?>"> KB
					</label>
				</td>
			</tr>
		<?php endforeach; ?>
		</table><?php
	}
}

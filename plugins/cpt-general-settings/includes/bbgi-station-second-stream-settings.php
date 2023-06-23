<?php
/**
 * BbgiStationSettings Class
 *
 * This class is responsible for rendering the "Second Stream Settings" options page in the WordPress admin area.
 * It allows the site administrator to manage settings related to the Second Stream functionality,
 * such as enabling/disabling the feature, and setting the available days and time range when the Second Stream is active.
 *
 * @package BbgiStationSettings
 * @author  WordPress PHP Developer
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
class BbgiStationSecondStreamSettings {

	const bbgi_option_group = 'bbgi_site_options';
	/**
	 * Contains the slug of the settings page once it's registered
	 *
	 * @access protected
	 * @var string
	 */
	protected $_bbgi_settings_page_hook;

	/**
	 * Constructor method.
	 * Initializes the class and hooks the needed actions.
	 */
	function __construct()
	{
		add_action( 'init', array( __CLASS__, 'bbgi_settings_page_init' ), 0 );
		add_action( 'admin_menu', array( $this, 'add_bbgi_settings_page' ), 1 );
		add_action( 'admin_init', array( $this, 'bbgi_register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	/**
	 * Adds the 'manage_bbgi_station_settings' capability to the administrator role.
	 */
	public static function bbgi_settings_page_init() {
		// Register custom capability for Draft Kings On/Off Setting and Max mega menu
		$roles = [ 'administrator' ];

		foreach ( $roles as $role ) {
			$role_obj = get_role($role);

			if (is_a($role_obj, \WP_Role::class)) {
				$role_obj->add_cap( 'manage_bbgi_station_settings', false );
			}
		}
	}

	/**
	 * Adds the "Second Stream Settings" page under the "Settings" menu.
	 */
	public function add_bbgi_settings_page() {
		$this->_bbgi_settings_page_hook = add_options_page( 'Second Stream Settings', 'Second Stream', 'manage_bbgi_station_settings', 'second-stream-settings', array( $this, 'render_bbgi_settings_page') );
	}

	/**
	 * Renders the "Second Stream Settings" options page.
	 */
	public function render_bbgi_settings_page() {
		echo '<form action="options.php" id="station-setting-form" method="post" style="max-width:750px;">';
		settings_fields( self::bbgi_option_group );
		do_settings_sections( $this->_bbgi_settings_page_hook );
		submit_button( 'Submit' );
		echo '</form>';
	}

	/**
	 * Registers the settings fields.
	 */
	public function bbgi_register_settings() {
		$ad_second_stream_enabled_args = array(
			'name'     => 'ad_second_stream_enabled',
			'selected' => get_option( 'ad_second_stream_enabled', 'on' ),
		);

		add_settings_section( 'ee_bbgi_site_settings', 'Second Stream Settings', '__return_false', $this->_bbgi_settings_page_hook );
		add_settings_field('ad_second_stream_enabled', 'Enable Second Stream', array($this, 'render_ad_second_stream_enabled'), $this->_bbgi_settings_page_hook, 'ee_bbgi_site_settings', $ad_second_stream_enabled_args);
		add_settings_field( 'ss_enabled_days', '', array($this, 'render_ss_days_enabled'), $this->_bbgi_settings_page_hook, 'ee_bbgi_site_settings', ['name'=>'ss_enabled_days']);

		register_setting(self::bbgi_option_group, 'ad_second_stream_enabled', 'sanitize_text_field');
		register_setting(self::bbgi_option_group, 'ss_enabled_days', 'sanitize_text_field');

	}

	/**
	 * Renders the "Enable Second Stream" field.
	 *
	 * @param array $args Field arguments.
	 */
	public function render_ad_second_stream_enabled( $args ) {
		?><select onchange="changeStream(jQuery)" name="<?php echo esc_attr( $args['name'] ); ?>">
		<option value="on"
			<?php selected( $args['selected'], 'on' ); ?>
		>On</option>
		<option value="off"
			<?php selected( $args['selected'], 'off' ); ?>
		>Off</option>

		</select><?php
	}

	/**
	 * Renders the "Days and Time range" field for the Second Stream.
	 *
	 * This function is responsible for rendering the interactive field where the site administrator
	 * can define on which days and during which time range the Second Stream should be active.
	 * It creates a table with checkboxes for each day of the week and two corresponding input fields for
	 * the start and end time of the active time range.
	 *
	 * This function uses a hidden input field to store the enabled days and their time ranges as a JSON object.
	 * A JavaScript/jQuery script is used to update the hidden field's value when any changes are made to
	 * the visible HTML elements (checkboxes and time inputs).
	 *
	 * On form submission, the JSON object in the hidden field is saved as one option in the WordPress database.
	 *
	 * @param array $args Field arguments.
	 */
	public 	function render_ss_days_enabled( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'type'    => 'hidden',
			'name'    => '',
			'default' => '',
			'class'   => 'regular-text',
			'desc'    => '',
		) );
		$style = '';
		$value = get_option( $args['name'], $args['default'] );
		$OnOption = get_option('ad_second_stream_enabled');
		$daysData = (array)json_decode($value);
		if($OnOption == 'off'){
			$style = 'display: none;';
		}

		// Render the hidden input field that contains the JSON object which represents the enabled days and their time ranges.
		printf(
			'<input type="%s" name="%s" class="%s" value="%s">',
			esc_attr( $args['type'] ),
			esc_attr( $args['name'] ),
			esc_attr( $args['class'] ),
			esc_attr( $value )
		);

		// Render the description for the field if one was provided.
		if ( ! empty( $args['desc'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args['desc'] ) );
		}

		//an array of days which we want to show in the table
		$days = ['monday'=> ['name'=>'monday','desc'=>'Monday','start_time'=>'','end_time'=>''],
			'tuesday'=> ['name'=>'tuesday','desc'=>'Tuesday','start_time'=>'','end_time'=>''],
				'wednesday'=> ['name'=>'wednesday','desc'=>'Wednesday','start_time'=>'','end_time'=>''],
				'thursday'=> ['name'=>'thursday','desc'=>'Thursday','start_time'=>'','end_time'=>''],
				'friday'=> ['name'=>'friday','desc'=>'Friday','start_time'=>'','end_time'=>''],
				'saturday'=> ['name'=>'saturday','desc'=>'Saturday','start_time'=>'','end_time'=>''],
				'sunday'=> ['name'=>'sunday','desc'=>'Sunday','start_time'=>'','end_time'=>''],];

		// output the days table
		echo '<table  cellspacing="0" align="center" class="ss_days_class" style="'. $style. '">';
		echo '<tr><td>Days</td><td>Start Time</td><td>End Time</td></tr>';
		foreach ($days as $daysargs){

			/*
			 * parses the arguments for the days table and sets the default values.
			 *  these arguments include:
			 *   		type: the type of input field to be rendered
			 *   		name: the name of the input field
			 * 			default: the default value of the input field
			 * 			class: the class of the input field
			 * 			desc: the description of the input field
			 */
			$daysargs = wp_parse_args( $daysargs, array(
				'type'    => 'checkbox',
				'name'    => '',
				'default' => false,
				'class'   => 'regular-text',
				'desc'    => '',
			) );

			// initializes the value, start time, and end time of the day
			$value = '';
			$startTime= '';
			$endTime= '';

			// if the day is enabled, set the value to checked and set the start and end time
			if(array_key_exists($daysargs['name'],$daysData)){
				$value = 'checked';
				$dayArray = (array)$daysData[$daysargs['name']];
				$startTime = $dayArray['startTime'];
				$endTime = $dayArray['endTime'];
			}


			// $checked is used to determine if the checkbox should be checked or not. $value will contain the
			// value 'checked' if the day is enabled and an empty string if the day is disabled.
			$checked = $value ? 'checked' : '';

			echo '<tr class="ss_tr_'.$daysargs['name'].'">';
			echo '<td>';
			if ( ! empty( $daysargs['desc'] ) ) {
				printf( '<span class="description">%s</span>', esc_html( $daysargs['desc'] ) );
			}

			// renders the checkbox for the day utilizing the $daysargs array defined above
			printf(
				'<input type="%s" name="%s" style="margin-left: 8px;" onclick="checkFluency(jQuery)" class="%s" %s></td>',
				esc_attr( $daysargs['type'] ),
				esc_attr( $daysargs['name'] ),
				esc_attr( $daysargs['class'] ),
				esc_attr( $checked )
			);

			// renders the start and end time inputs for the day
			echo '<td><input type="text" style="max-width: 50%;" name="starttime" value="'.$startTime.'"  onchange="checkFluency(jQuery)" ></td><td><input type="text" style="max-width: 50%;" name="endtime" value="'.$endTime.'" onchange="checkFluency(jQuery)" ></td></td>';
			echo '</tr>';
		}
		echo '</table>';

	}
	/**
	 * Enqueues scripts and styles for the admin page
	 *
	 * @global string $typenow The current type.
	 * @global string $pagenow The current page.
	 */
	public static function enqueue_scripts() {
		wp_enqueue_script( 'station_setting_script', GENERAL_SETTINGS_CPT_URL . "assets/js/station-setting.js", array('jquery'), GENERAL_SETTINGS_CPT_VERSION, true );
	}
}

// Initialize the class
new BbgiStationSecondStreamSettings();

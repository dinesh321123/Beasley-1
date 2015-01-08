<?php


/**
 * Class GMP_Settings
 */
class GMP_Settings {

	const option_group = 'GMP_settings';

	/**
	 * Contains the slug of the settings page once it's registered
	 *
	 * @var
	 */
	protected $_settings_page_hook;

	/**
	 * Instance of this class, if it has been created.
	 *
	 * @var GMP_Settings
	 */
	protected static $_instance = null;

	/**
	 * Get the instance of this class, or set it up if it has not been setup yet.
	 *
	 * @var GMP_Settings
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new static();
			self::$_instance->_init();
		}

		return self::$_instance;
	}

	public function __construct() {
		// I do nothing
	}

	/**
	 * Sets up actions and filters.
	 */
	protected function _init() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add the settings page
	 */
	public function add_settings_page() {
		$this->_settings_page_hook = add_submenu_page( 'edit.php?post_type=podcast', 'Podcast Settings', 'Settings', 'manage_options', 'gmp-settings', array( $this, 'render_settings_page' ) );
	}

	/**
	 * Render the settings page
	 */
	public function render_settings_page() {
		?>
		<form action="options.php" method="post" class="gmp-settings-form" style="max-width: 550px;">
			<?php
			settings_errors();
			settings_fields( self::option_group );
			do_settings_sections( $this->_settings_page_hook );

			/**
			 * Allows adding additional settings sections here.
			 *
			 * Useful for the sections that are only enabled if theme support is enabled, we can conditionally add settings for each child theme.
			 */
			do_action( 'gmp-settings-additional-settings' );

			submit_button( 'Submit' );
			?>
		</form>
	<?php
	}

	/**
	 * Register the settings page
	 */
	public function register_settings() {
		// Settings Section
		add_settings_section( 'gmp_settings', 'Greater Media Podcast Settings', array( $this, 'render_gmp_settings_section' ), $this->_settings_page_hook );

		/**
		 * Allows us to register extra settings that are not necessarily always present on all child sites.
		 */
		do_action( 'gmp-settings-register-settings', self::option_group );
	}

	/**
	 * Render inputs for the settings page
	 */
	public function render_gmp_settings_section() {

		?>

		<p>
			FUTURE: In order to properly submit a Podcast to iTunes, additional fields will need to be added to the Feed via XML. This area will serve as an options page for the editors at Greater Media to be able to choose the appropriate rating, iTunes Subtitle, author, owner info, podcast image, and more. Ref: <a href="https://www.apple.com/itunes/podcasts/specs.html">https://www.apple.com/itunes/podcasts/specs.html</a>.

		</p>

	<?php
	}

	/**
	 * Enqueue scripts
	 */
	public static function enqueue_scripts() {

		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		//wp_enqueue_style( 'gmp-admin-styles', GMLIVEPLAYER_URL . "assets/css/greater_media_live_player_admin{$postfix}.css", array(), GMLIVEPLAYER_VERSION );

	}

}

//GMP_Settings::instance();

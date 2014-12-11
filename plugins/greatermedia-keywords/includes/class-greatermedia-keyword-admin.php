<?php
/**
 * Created by Eduard
 * Date: 20.11.2014 0:10
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class GreaterMedia_Keyword_Admin {

	public static $plugin_slug = 'gmedia_keywords' ;
	public static $supported_post_types = array(
		'post',
		'attachment',
		'podcast',
		'episode',
		'contest',
		'personality',
		'show',
		'albums',
		'tribe_events',
		'survey',
		'question',
		'response',
		'page',
		'livefyre-media-wall'
	);

	private $postfix;

	private $_page_slug;

	public function __construct() {
		$this->postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

		$this::$supported_post_types = apply_filters( 'keywords_supported_cpts', $this::$supported_post_types );

		// Load admin style sheet and js.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'save_settings' ) );

		add_action( 'wp_ajax_delete_keyword', array( $this, 'delete_keyword' ) );
	}

	public function enqueue_admin_styles(){
		wp_enqueue_style( 'select2');
		wp_enqueue_style(
			$this::$plugin_slug . '-admin-style'
			, GMKEYWORDS_URL . "assets/css/greatermedia_keywords{$this->postfix}.css"
			, array()
			, GMKEYWORDS_VERSION
			, 'all'
		);
	}

	public function enqueue_admin_scripts( $page_slug ) {

		if ( $this->_page_slug == $page_slug ) {
			wp_enqueue_script( 'select2');
			wp_enqueue_script( 'jquery-effects-core');
			wp_enqueue_script( 'jquery-effects-slide');

			wp_enqueue_script(
				$this::$plugin_slug . '-admin-script'
				, GMKEYWORDS_URL . "assets/js/greatermedia_keywords{$this->postfix}.js"
				, array( 'jquery' )
				, GMKEYWORDS_VERSION
			);

			wp_localize_script(
				$this::$plugin_slug . '-admin-script'
				, 'ajax_data'
				, array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'delete_key_nonce' => wp_create_nonce( 'perform-keyword-delete-nonce' )
				)
			);
		}
	}


	/**
	 * Register the administration menu
	 *
	 */
	public function add_plugin_admin_menu() {

		/**
		 * Add a settings page for this plugin to a main level menu under the Dashboard menu link
		 */
		$this->_page_slug = add_menu_page(
			'On-Air Keywords',
			'Keywords',
			'manage_options',
			$this::$plugin_slug,
			array( $this, 'display_plugin_admin_page' ),
			'dashicons-lightbulb',
			3
		);

	}

	/**
	 * Render the settings page for this plugin.
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add or update a WordPress option.
	 * The option will _not_ auto-load.
	 * Source: https://eamann.com/tech/wordpress-options-auto-loading/
	 *
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return bool
	 */
	public static function add_or_update( $name, $value ) {
		$success = add_option( $name, $value, '', 'no' );

		if ( ! $success ) {
			$success = update_option( $name, $value );
		}

		return $success;
	}

	public function save_settings() {

		$nonce = '';

		if( isset( $_POST['save_new_keyword'] ) ) {
			$nonce = $_POST['save_new_keyword'];
		}

		if( !wp_verify_nonce( $nonce, 'save_new_keyword' ) ) {
			return false;
		}

		$pairs = get_option( $this::$plugin_slug . '_option_name' );
		$pairs = self::array_map_r( 'sanitize_text_field', $pairs );

		if ( isset( $_POST["save_keyword_settings"] ) && current_user_can('manage_options') ) {

			$linked_content = isset( $_POST['linked_content'] ) ? sanitize_text_field( $_POST['linked_content'] ) : '';
			$keyword = isset( $_POST['keyword'] )? sanitize_text_field( $_POST['keyword'] ) : '';
			$keyword_key = strtolower( $keyword );

			$linked_content = explode( ',', $linked_content );

			if( $keyword == '' ) {
				echo '<div id="message" class="error"><p>Keyword can\'t be empty!</p></div>';
				return false;
			}

			if( is_array( $pairs ) && array_key_exists( $keyword_key, $pairs ) ) {
				echo '<div id="message" class="error"><p>Keyword ' . esc_html( $keyword ) . ' already used!</p></div>';
				return false;
			}

			$pairs[$keyword_key] = array(
				'keyword'       =>  $keyword,
				'post_id'       =>  intval( $linked_content[0] ),
				'post_title'    =>  sanitize_text_field( $linked_content[1] )
			);

			if( self::add_or_update( $this::$plugin_slug . '_option_name', $pairs ) ) {
				echo '<div id="message" class="updated"><p>Keywords saved</p></div>';
				set_transient( $this::$plugin_slug . '_option_name', $pairs, WEEK_IN_SECONDS * 4 );
			}
		}

		return true;
	}


	/**
	 * Remove keyword from options and cache
	 */
	public function delete_keyword() {

		$success = false;
		// get nonce from ajax post
		$nonce = $_POST['delete_key_nonce'];

		// verify nonce, with predefined
		if ( ! wp_verify_nonce( $nonce, 'perform-keyword-delete-nonce' ) ) {
			wp_die ( ':P' );
		}

		if( isset( $_POST['post_id'] ) ) {
			$key_post_id = intval( $_POST['post_id'] );
			$pairs = get_option( $this::$plugin_slug . '_option_name' );
			$pairs = self::array_map_r( 'sanitize_text_field', $pairs );
			foreach ( $pairs as $key => $linked_content ) {
				if( $linked_content['post_id'] == $key_post_id ) {
					unset( $pairs[$key] );
					$success = self::add_or_update( $this::$plugin_slug . '_option_name', $pairs );
					set_transient( $this::$plugin_slug . '_option_name', $pairs, WEEK_IN_SECONDS * 4 );
					break;
				}
			}
		}

		die( $success );
	}

	/**
	 * Helper function to sanitize multidimensional array
	 * http://php.net/manual/en/function.array-map.php#78904
	 *
	 * @param $func
	 * @param $arr
	 *
	 * @return array
	 */
	public static function array_map_r( $func, $arr ) {
		$newArr = array();
		if( is_array( $arr) ) {
			foreach( $arr as $key => $value )
			{
				$newArr[ $key ] = ( is_array( $value ) ? self::array_map_r( $func, $value ) : ( is_array($func) ? call_user_func_array($func, $value) : $func( $value ) ) );
			}

			return $newArr;
		} else {
			$arr = is_array($func) ? call_user_func_array($func, $arr) : $func( $arr );
			return $arr;
		}
	}

	/**
	 * Tries to get keywords from transient cache
	 * Otherwise queries options table
	 *
	 * @param $name
	 *
	 * @return mixed|void
	 */
	public static function get_keyword_options( $name ) {
		$name = sanitize_text_field( $name );

		$options = get_transient( $name );
		if( !$options ) {
			$options = get_option( $name );
		}

		if ( empty( $options ) ) {
			$options = array();
		}

		return $options;
	}

	public static function get_post_for_keywords() {
		$posts  =   array();

		$args   =   array(
			'post_type'         =>  self::$supported_post_types,
			'posts_per_page'    =>  500,
			'post_status'       =>  'publish',
		);

		$query = new WP_Query( $args );

		foreach( $query->posts as $post ) {
			$posts[] = $post;
		}
		$page = 1;
		if( $query->max_num_pages > 1 ) {
			while( $page < $query->max_num_pages ) {
				$args['offset'] = $page * 500;
				$query = new WP_Query( $args );
				foreach( $query->posts as $post ) {
					$posts[] = $post;
				}
				$page++;
			}
		}

		return $posts;
	}
}


new GreaterMedia_Keyword_Admin();
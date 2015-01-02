<?php

namespace GreaterMedia\Gigya;

/**
 * The main plugin class for greatermedia-gigya.
 *
 * @package GreaterMedia\Gigya
 */
class Plugin {

	/**
	 * Path to the main plugin file. Used with WordPress helpers like
	 * plugins_url.
	 *
	 * @var string
	 */
	public $plugin_file = null;

	/**
	 * Stores the plugin_file and initializes any dependencies.
	 *
	 * @access public
	 * @param string $plugin_file The path to the main plugin file.
	 */
	public function __construct( $plugin_file ) {
		$this->plugin_file = $plugin_file;
	}

	/**
	 * Sets up the initialization hooks for the plugin.
	 *
	 * @access public
	 * @return void
	 */
	public function enable() {
		add_action( 'init', array( $this, 'initialize' ) );
		add_action( 'admin_init', array( $this, 'initialize_admin' ) );
		add_action( 'admin_menu', array( $this, 'initialize_admin_menu' ) );

		register_activation_hook(
			$this->plugin_file,
			array( $this, 'migrate' )
		);
	}

	public function migrate() {
		$migrator = new Sync\TempSchemaMigrator();
		$migrator->migrate();

		flush_rewrite_rules();
	}

	/**
	 * Initializes the CPT for the plugin.
	 *
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->member_query_post_type = new MemberQueryPostType();
		$this->member_query_post_type->register();

		/* Lazy register ajax handlers only if this is an ajax request */
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$this->register_ajax_handlers();
		}

		/* Lazy load the async tasks */
		if ( defined( 'DOING_ASYNC' ) && DOING_ASYNC ) {
			$this->register_task_handlers();
		}

		$profile_page = new ProfilePage();
		$profile_page->register();

		$contest_entry_dispatcher = new Action\ContestEntryDispatcher();
		$contest_entry_dispatcher->register();

		wp_register_script(
			'wp_ajax_api',
			$this->postfix( plugins_url( 'js/wp_ajax_api.js', $this->plugin_file ), '.js' ),
			array( 'jquery' ),
			GMR_GIGYA_VERSION,
			true
		);

		if ( ! $profile_page->is_user_on_profile_page() && ! is_admin() ) {
			$this->enqueue_script(
				'gigya_session',
				'js/gigya_session.js',
				array( 'jquery', 'cookies-js', 'wp_ajax_api' )
			);

			$session_data = array(
				'data'                        => array(
					'ajax_url'                => admin_url( 'admin-ajax.php' ),
					'save_gigya_action_nonce' => wp_create_nonce( 'save_gigya_action' ),
					'has_participated_nonce'  => wp_create_nonce( 'has_participated' )
				)
			);

			wp_localize_script(
				'gigya_session', 'gigya_session_data', $session_data
			);
		}
	}

	public function initialize_admin() {
		add_action( 'add_meta_boxes_member_query', array( $this, 'initialize_member_query_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'did_save_post' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'show_flash' ) );
	}

	public function initialize_admin_menu() {
		$settings_page = new SettingsPage();
		$settings_page->register();
	}

	/**
	 * Registers the ajax handlers for this plugin.
	 *
	 * @access public
	 * @return void
	 */
	public function register_ajax_handlers() {
		$handlers   = array();

		//$handlers[] = new Ajax\GigyaLoginAjaxHandler();
		//$handlers[] = new Ajax\GigyaLogoutAjaxHandler();
		$handlers[] = new Ajax\PreviewResultsAjaxHandler();
		$handlers[] = new Ajax\RegisterAjaxHandler();
		$handlers[] = new Ajax\ListEntryTypesAjaxHandler();
		$handlers[] = new Ajax\ListEntryFieldsAjaxHandler();
		$handlers[] = new Ajax\ChangeGigyaSettingsAjaxHandler();
		$handlers[] = new Ajax\HasParticipatedAjaxHandler();
		$handlers[] = new Ajax\MemberQueryStatusAjaxHandler();
		$handlers[] = new Ajax\EmmaMemberOptoutAjaxHandler();
		$handlers[] = new Ajax\ChangeMemberQuerySegmentAjaxHandler();

		if ( is_gigya_user_logged_in() ) {
			$handlers[] = new Ajax\SaveGigyaActionAjaxHandler();
		}

		foreach ( $handlers as $handler ) {
			$handler->register();
		}
	}

	public function register_task_handlers() {
		$launcher = new Sync\Launcher();
		$launcher->register();

		$actionPublisher = new Action\Publisher();
		$actionPublisher->register();
	}

	/**
	 * Registers the member query post_type metaboxes.
	 *
	 * @access public
	 * @param WP_Post $post The current post object
	 * @return void
	 */
	public function initialize_member_query_meta_boxes( $post ) {
		$member_query = new MemberQuery( $post->ID );
		$this->member_query_post_type->register_meta_boxes( $member_query );

		$this->initialize_member_query_scripts( $member_query );
		$this->initialize_member_query_styles( $member_query );

		add_action( 'admin_notices', array( $this, 'render_preloader' ) );
	}

	function render_preloader() {
		include GMR_GIGYA_PATH . '/templates/preloader.php';
	}

	function initialize_member_query_scripts( $member_query ) {
		wp_dequeue_script( 'autosave' );

		wp_enqueue_script( 'underscore' );
		wp_enqueue_script( 'backbone' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		//$this->enqueue_script( 'select2', 'js/vendor/select2.js' );

		$this->enqueue_script( 'backbone.collectionView', 'js/vendor/backbone.collectionView.js', 'backbone' );
		$this->enqueue_script(
			'query_builder', 'js/query_builder.js', array( 'backbone', 'underscore', 'wp_ajax_api' )
		);

		wp_localize_script(
			'query_builder', 'member_query_data', $member_query->properties
		);

		$sentinel = new Sync\Sentinel( $member_query->post_id, array( 'mode' => 'export' ) );

		if ( $sentinel->get_status_code() === 'running' && $sentinel->has_expired() ) {
			$sentinel->set_status_code( 'completed' );
			$sentinel->add_error( 'Error: The query timed out' );
		}

		$status_meta = $sentinel->get_status_meta();

		$meta = array(
			'ajax_url'                          => admin_url( 'admin-ajax.php' ),
			'preview_member_query_nonce'        => wp_create_nonce( 'preview_member_query' ),
			'list_entry_types_nonce'            => wp_create_nonce( 'list_entry_types' ),
			'list_entry_fields_nonce'           => wp_create_nonce( 'list_entry_fields' ),
			'member_query_status_nonce'         => wp_create_nonce( 'member_query_status' ),
			'change_member_query_segment_nonce' => wp_create_nonce( 'change_member_query_segment' ),
			'status_meta'                       => $sentinel->get_status_meta()
		);

		wp_localize_script(
			'query_builder', 'member_query_meta', $meta
		);
	}

	function initialize_member_query_styles( $member_query ) {
		$this->enqueue_style( 'query_builder', 'css/query_builder.css', 'jquery-ui' );
		//$this->enqueue_style( 'select2', 'css/vendor/select2.css' );
	}

	/**
	 * If post was saved then calls member query helper
	 * functions, else ignores the save.
	 *
	 * @access public
	 * @param int $post_id The id of the parent post CPT of this MemberQuery
	 * @param WP_Post $post The post object that was saved.
	 * @return void
	 */
	public function did_save_post( $post_id, $post = null ) {
		if ( ! is_null( $post ) ) {
			$post_type   = $post->post_type;
			$post_status = $post->post_status;

			if ( $post_status === 'publish' && $post_type === 'member_query' ) {
				return $this->publish_member_query( $post_id, $post );
			}
		}
	}

	/**
	 * Saves the MemberQuery JSON in postmeta and then publishes the
	 * segment to MailChimp.
	 *
	 * If constraints not present in POST assumed to be a quick-edit and
	 * does not update the member query.
	 *
	 * @access public
	 * @param int $post_id The id of the parent post CPT of this MemberQuery
	 * @param WP_Post $post The post object that was saved.
	 * @return void
	 */
	public function publish_member_query( $post_id, $post = null ) {
		if ( ! array_key_exists( 'constraints', $_POST ) ) {
			return;
		}

		$this->member_query_post_type->verify_meta_box_nonces();

		try {
			$member_query = new MemberQuery( $post_id );
			$member_query->build_and_save();

			if ( $this->can_export_member_query() ) {
				$this->export_member_query( $post_id );
			}
		} catch ( \Exception $e ) {
			$this->set_flash( $e->getMessage() );
		}
	}

	public function export_member_query( $member_query_id ) {
		$launcher = new Sync\Launcher();
		$launcher->launch( $member_query_id, 'export' );
	}

	public function can_export_member_query() {
		if ( array_key_exists( 'export_member_query', $_POST ) ) {
			$export_member_query = $_POST['export_member_query'];
			return intval( $export_member_query ) === 1;
		} else {
			return false;
		}
	}

	/* helpers */
	public function show_flash() {
		$flash = $this->get_flash();
		if ( $flash !== false ) {
			include GMR_GIGYA_PATH . '/templates/flash.php';
			$this->clear_flash();
		}
	}

	public function get_flash_key() {
		return get_current_user_id() . '_member_query_flash';
	}

	public function set_flash( $message ) {
		set_transient( $this->get_flash_key(), $message, 30 );
	}

	public function get_flash() {
		return get_transient( $this->get_flash_key() );
	}

	public function clear_flash() {
		delete_transient( $this->get_flash_key() );
	}

	public function enqueue_script( $id, $path, $dependency = null ) {
		$dependencies = $this->get_dependencies( $dependency );
		$path         = $this->postfix( $path, '.js' );

		wp_enqueue_script(
			$id,
			plugins_url( $path, $this->plugin_file ),
			$dependencies,
			GMR_GIGYA_VERSION
		);
	}

	public function enqueue_style( $id, $path, $dependency = null ) {
		$dependencies = $this->get_dependencies( $dependency );
		$path         = $this->postfix( $path, '.css' );

		wp_enqueue_style(
			$id,
			plugins_url( $path, $this->plugin_file ),
			$dependencies,
			GMR_GIGYA_VERSION
		);
	}

	/**
	 * Helper to allow for a single dependency.
	 */
	public function get_dependencies( $dependency ) {
		if ( ! is_null( $dependency ) ) {
			if ( is_array( $dependency ) ) {
				$dependencies = $dependency;
			} else {
				$dependencies = array( $dependency );
			}
		} else {
			$dependencies = array();
		}

		return $dependencies;
	}

	/**
	 * Adds a .min postfix to a path depending on script debug mode.
	 */
	public function postfix( $path, $extension ) {
		if ( $this->get_script_debug() ) {
			return $path;
		} else {
			return str_replace( $extension, ".min{$extension}", $path );
		}
	}

	function get_script_debug() {
		return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
	}
}

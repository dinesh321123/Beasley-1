<?php

namespace GreaterMedia\Gigya;

/**
 * MetaBox is the base class that concrete meta boxes are derived from.
 *
 * A MetaBox has the following responsibilities,
 *
 * 1. Registering itself with WordPress.
 * 2. Rendering it's markup from a template.
 * 3. Optionally enqueuing it's own additional scripts & styles.
 * 4. Nonce verification. All 'Save' actions must verify the nonce of
 * the meta box before proceeding.
 *
 * This base class provides abstract methods that must be implemented by
 * the sub class. This allows the sub class to declare it's meta box
 * properties, without doing registration/rendering itself.
 *
 * A meta box's markup is provided by templates in the directory,
 *
 * { plugin }/templates/metaboxes
 *
 * Templates can use $this->data to render dynamic content related to
 * that meta box.
 *
 * @package GreaterMedia\Gigya
 */
class MetaBox {

	/**
	 * The data object for this meta box. This is used inside templates
	 * as $this->data->foo.
	 *
	 * @access public
	 * @var mixed
	 */
	public $data;

	/**
	 * Properties of the meta box. These correspond to the getters like
	 * get_id, get_title.
	 *
	 * You can either set the params directly or subclass and override
	 * the getters.
	 *
	 * If provided must contain the keys,
	 *
	 * 1. id
	 * 2. title
	 * 3. context
	 * 4. priority
	 * 5. template
	 *
	 * @access public
	 * @var array
	 */
	public $params = array(
		'id'        => 'meta_box',
		'title'     => 'Meta Box',
		'post_type' => 'member_query',
		'context'   => 'normal',
		'priority'  => 'default',
		'template'  => null,
	);

	/**
	 * Stores the member query for this meta box.
	 *
	 * @access public
	 * @param mixed $data
	 */
	public function __construct( $data ) {
		$this->data = $data;
	}

	/**
	 * Registers the Meta Box with WordPress.
	 *
	 * @access public
	 * @return void
	 */
	public function register() {
		add_meta_box(
			$this->get_id(),
			$this->get_title(),
			$this->get_callback(),
			$this->get_post_type(),
			$this->get_context(),
			$this->get_priority()
		);
	}

	/**
	 * Abstract method that should echo the markup for a metabox.
	 *
	 * By default it renders the template for this meta box.
	 *
	 * All meta boxes are rendered with a nonce field corresponding to
	 * it's id. All 'Save' based actions must verify this nonce before
	 * proceeding with the request.
	 *
	 * @return void
	 */
	public function render() {
		wp_nonce_field(
			$this->get_id(),
			$this->get_nonce_field(),
			false
		);

		$template_to_render = $this->get_template();
		if ( ! is_null( $template_to_render ) ) {
			include( $this->get_template_path( $template_to_render ) );
		}
	}

	/**
	 * Verifies that a nonce corresponding to the current meta box
	 * exists in the POST and is valid.
	 *
	 * This function will halt current script execution if the nonce
	 * verification fails.
	 *
	 * In PHPUnit mode script halting is skipped, the function returns false
	 * instead.
	 *
	 * @access public
	 * @return boolean
	 */
	public function verify_nonce() {
		$nonce_action = $this->get_id();
		$nonce_field  = $nonce_action . '_nonce';

		if ( array_key_exists( $nonce_field, $_POST ) ) {
			$nonce_value = sanitize_text_field( $_POST[ $nonce_field ] );
		} else {
			$nonce_value = '';
		}

		if ( wp_verify_nonce( $nonce_value, $nonce_action ) === false ) {
			if ( ! defined( 'PHPUNIT_RUNNER' ) ) {
				wp_die(
					__( 'You do not have sufficient permissions to access this page.', 'gmr_gigya' )
				);
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	/***************************************************/
	/* Helpers                                         */
	/***************************************************/

	/**
	 * Returns the name of the template used to render this meta box.
	 *
	 * If absent, a .php extension is assumed.
	 *
	 * @access public
	 * @return string
	 */
	public function get_template() {
		return $this->params['template'];
	}

	/**
	 * Unique id of the MetaBox.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->params['id'];
	}

	/**
	 * Returns the name of the nonce that is rendererd into the MetaBox.
	 *
	 * Default is {id}_nonce
	 *
	 * @return string
	 */
	public function get_nonce_field() {
		return $this->get_id() . '_nonce';
	}

	/**
	 * The non-localized title of the MetaBox. Localization is performed
	 * at the time of registration.
	 */
	public function get_title() {
		return $this->params['title'];
	}

	/**
	 * The callback that renders the meta box.
	 *
	 * Default is to call render.
	 */
	public function get_callback() {
		return array( $this, 'render' );
	}

	/**
	 * The post_type name that this meta box belongs to.
	 *
	 * By default meta boxes belong to the `member_query` custom post type.
	 */
	public function get_post_type() {
		if ( array_key_exists( 'post_type', $this->params ) ) {
			return $this->params['post_type'];
		} else {
			return 'member_query';
		}
	}

	/**
	 * The context of the meta box. Default context is 'normal'.
	 */
	public function get_context() {
		return $this->params['context'];
	}

	/**
	 * The priority of the meta box. Default priority is 'default'.
	 *
	 * @access public
	 * @return string
	 */
	public function get_priority() {
		return $this->params['priority'];
	}

	/**
	 * Optional callback args to pass to the meta box at time of render.
	 *
	 * @access public
	 * @return array
	 */
	public function get_callback_args() {
		return array();
	}

	/**
	 * Returns the full path to the template file specified.
	 *
	 * @param $path The template file name, without extension.
	 * @return string Expanded path to template file.
	 */
	public function get_template_path( $path ) {
		return GMR_GIGYA_PATH . "/templates/metaboxes/{$path}.php";
	}

}

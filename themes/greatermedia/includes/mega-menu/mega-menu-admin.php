<?php

/**
 * Admin UI and CRUD operation for setting the format of the navigation menus
 *
 * To work, this requires the `wp_nav_menu_item_custom_fields` hook, which is not part of core (yet).
 * It will fail gracefully without it. Because native hooks do not exist, we are using an MU plugin
 * to replace the nav edit walker. See the 'mega-menu-support' MU plugin for implementation details.
 *
 * @See ticket: https://core.trac.wordpress.org/ticket/18584
 * @See patch: https://core.trac.wordpress.org/attachment/ticket/14414/nav_menu_custom_fields.patch
 *
 *
 * Class GreaterMediaMegaMenuAdmin
 */
class GreaterMediaMegaMenuAdmin {

	/**
	 * This is a whitelisted array of nav menu formatting options.
	 *
	 * @var array
	 */
	public static $options = array(
		''      => 'Standard',
		'fw' => 'Full-width, four column',
	);

	/**
	 * The meta key under which we'll store the formatting option. It's attached to the nav menu item
	 * @var string
	 */
	public static $meta_key = 'gmr_menu_format2';

	/**
	 * WP Hooks
	 */
	public static function init() {
		add_action( 'wp_nav_menu_item_custom_fields', array(
			__CLASS__,
			'nav_menu_fields'
		), null, 4 );
		add_action( 'wp_update_nav_menu_item', array(
			__CLASS__,
			'save_nav_menu_fields'
		), null, 3 );
	}

	/**
	 * Add a formatting select list to top level nav menu items in the edit screen
	 *
	 * @param $item_id integer
	 * @param $item    WP_Post
	 * @param $depth   integer - 0 is top-level
	 * @param $args    array Arguments passed to the walker
	 */
	public static function nav_menu_fields( $item_id, $item, $depth, $args ) {

		// Only apply this drop down to the root-level menus.
		if ( $depth > 0 ) {
			return;
		}

		$format = self::get_nav_menu_format( $item_id );

		?>
		<p class="description description-wide">
			<label>Format:<br>
				<select name="nav-menu-format[<?php echo $item_id; ?>]" id="nav-menu-format-<?php echo intval( $item_id ); ?>">
					<?php
					foreach ( self::$options as $value => $text ) {
						?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $format ); ?>>
							<?php echo esc_html( $text ); ?>
						</option>
					<?php
					}
					?>
				</select>
			</label>
		</p>
	<?php
	}

	/**
	 * Save the nav menu format.
	 *
	 * @param $menu_id         integer
	 * @param $menu_item_db_id integer
	 * @param $menu_item_data  array
	 */
	public static function save_nav_menu_fields( $menu_id, $menu_item_db_id, $menu_item_data ) {

		// Permissions check. I think this is done before the hook this is called on ever fires, but just in case
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( __( 'Cheatin&#8217; uh?' ), 403 );
		}

		if ( isset( $_POST['nav-menu-format'] ) ) {
			foreach ( $_POST['nav-menu-format'] as $menu_item_id => $nav_menu_format ) {

				if ( array_key_exists( $nav_menu_format, self::$options ) && ! empty( $nav_menu_format ) ) {
					update_post_meta( $menu_item_id, self::$meta_key, $nav_menu_format ); // $nav_menu_format checked against whitelist
				} else {
					delete_post_meta( $menu_item_id, self::$meta_key );
				}

			}
		}

	}

	/**
	 * Helper function to get nav menu format
	 *
	 * Will return false if the current format is not whitelisted (or empty )
	 * (bool) false === Standard format
	 * Otherwise returns a string, the key, of the format option.
	 * @see self::$options
	 *
	 * @param $nav_item_id integer
	 *
	 * @return bool|string
	 */
	public static function get_nav_menu_format( $nav_item_id ) {

		$format = get_post_meta( $nav_item_id, self::$meta_key, true );

		// check against whitelist
		if ( array_key_exists( $format, self::$options ) && ! empty( $format ) ) {
			return $format;
		} else {
			return false;
		}

	}


}

add_action( 'after_setup_theme', array( 'GreaterMediaMegaMenuAdmin', 'init' ) );
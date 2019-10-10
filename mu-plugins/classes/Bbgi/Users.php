<?php
/**
 * Module responsible for managing users sitewide
 *
 * @package Bbgi
 */

namespace Bbgi;

class Users extends \Bbgi\Module {
	const USER_DISABLED_META = 'bbgi_is_user_disabled';
	const FILTER_NAME_FIELD = 'bbgi_user_status';

	/**
	 * Register actions and hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_filter( 'authenticate', [ $this, 'filter_authenticate' ], 21, 3 ); // after wp_authenticate_username_password runs.
		add_action( 'show_user_profile', [ $this, 'render_fields' ] );
		add_action( 'edit_user_profile', [ $this, 'render_fields' ] );
		add_action( 'personal_options_update', [ $this, 'save_fields' ] );
		add_action( 'edit_user_profile_update', [ $this,'save_fields'] );
		add_action( 'restrict_manage_users', [ $this, 'filter_users_dropdown' ], 99 );
		add_action( 'restrict_manage_network_users', [ $this, 'filter_users_dropdown' ], 99 );
		add_action( 'pre_get_users', [ $this, 'filter_dropdown' ] );
	}

	/**
	 * Determine whether the user can authenticate or not.
	 *
	 * @param \WP_User|null|\WP_Error $user WordPress user object.
	 * @param string                  $username Username.
	 * @param string                  $password Password supplied by the user.
	 *
	 * @return mixed
	 */
	public function filter_authenticate( $user, $username, $password ) {
		if ( is_a( $user, \WP_User::class ) && $this->is_user_disabled( $user->ID ) ) {
			return new \WP_Error(
				'bbgi_disabled_user',
				esc_html__( 'This user account has been disabled by an administrator', 'beasley' )
			);
		}

		return $user;
	}

	/**
	 * Returns whether the user is disabled or not.
	 *
	 * @param integer $user_id User id.
	 * @return boolean
	 */
	public function is_user_disabled( $user_id ) {
		return (bool) get_user_meta( $user_id, self::USER_DISABLED_META, true );
	}

	/**
	 * Render our custmo fields.
	 *
	 * @param \WP_User $user The user object.
	 *
	 * @return void
	 */
	public function render_fields( \WP_User $user ) {
		if ( ! current_user_can( 'manage_network_users' ) || $user->ID === get_current_user_id() ) {
			return;
		}
		?>
		<h3><?php esc_html_e( 'Beasley Security', 'beasley' ); ?></h3>
		<?php wp_nonce_field( 'beasley_security', 'bbgi_user_settings_nonce' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row">
						<?php esc_html_e( 'Disable user', 'beasley' ); ?>
				</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span><?php esc_html_e( 'Disable user', 'beasley' ); ?></span>
						</legend>
						<label for="<?php echo esc_attr( self::USER_DISABLED_META ); ?>">
							<input  type="checkbox"
									id="<?php echo esc_attr( self::USER_DISABLED_META ); ?>"
									name="<?php echo esc_attr( self::USER_DISABLED_META ); ?>"
									value="1"
									<?php checked( $this->is_user_disabled( $user->ID ) )?>
							/>
							<?php esc_html_e( 'Disabling an user will prevent it from logging in on the site', 'beasley' ); ?>
						</label>

					</fieldset>

				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Saves our custom fields.
	 *
	 * @param integer $user_id The user id.
	 *
	 * @return void
	 */
	public function save_fields( $user_id ) {
		if ( ! wp_verify_nonce( $_POST['bbgi_user_settings_nonce'], 'beasley_security' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_network_users' ) ) {
			return;
		}

		$is_user_disabled = filter_input( INPUT_POST, self::USER_DISABLED_META, FILTER_SANITIZE_NUMBER_INT );

		update_user_meta( $user_id, self::USER_DISABLED_META, $is_user_disabled );
	}

	/**
	 * Filter users dropdown
	 *
	 * @return void
	 */
	public function filter_users_dropdown( $which ) {
		if ( $which !== 'top' ) {
			return;
		}

		?>
		<select name="<?php echo esc_attr( self::FILTER_NAME_FIELD ); ?>"
				id="<?php echo esc_attr( self::FILTER_NAME_FIELD ); ?>"
				class="postform" style="float:none;margin-left:5px;">
			<option value=""><?php echo esc_html__( 'All Statuses', 'beasley' ); ?></option>
			<option value="enabled" <?php selected( filter_input( INPUT_GET, self::FILTER_NAME_FIELD, FILTER_SANITIZE_STRING ), 'enabled' ); ?>><?php echo esc_html__( 'Enabled', 'beasley' ); ?></option>
			<option value="disabled" <?php selected( filter_input( INPUT_GET, self::FILTER_NAME_FIELD, FILTER_SANITIZE_STRING ), 'disabled' ); ?>><?php echo esc_html__( 'Disabled', 'beasley' ); ?></option>
		</select>

		<?php
		submit_button (__( 'Filter' ), null, $which, false );
	}

	/**
	 * Filter out users based on filter_users_dropdown
	 *
	 * @param \WP_Query $query The query object
	 *
	 * @return void
	 */
	public function filter_dropdown( $query ) {
		global $pagenow;

		$user_status = filter_input( INPUT_GET, self::FILTER_NAME_FIELD, FILTER_SANITIZE_STRING );

		if ( ! is_admin() || 'users.php' !== $pagenow || ! $user_status ) {
			return;
		}

		if ( $user_status === 'disabled' ) {
			$query->set(
				'meta_query',
				[
					[
						'key'   => self::USER_DISABLED_META,
						'value' => '1',
					],
				]
			);
		} elseif( $user_status === 'enabled' ) {
			$query->set(
				'meta_query',
				[
					'relation' => 'OR',
					[
						'key'   => self::USER_DISABLED_META,
						'value' => '0',
					],
					[
						'key' => self::USER_DISABLED_META,
						'compare' => 'NOT EXISTS',
					]
				]
			);
		}
	}
}

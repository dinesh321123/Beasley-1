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
	const USER_LAST_LOGIN_META = 'bbgi_user_last_login_meta';
	const INACTIVITY_THRESHOLD = 60;

	/**
	 * Register actions and hooks.
	 *
	 * @return void
	 */
	public function register() {
		//add_filter( 'authenticate', [ $this, 'filter_authenticate' ], 21, 3 ); // after wp_authenticate_username_password runs.
		add_action( 'wp_login', [ $this, 'on_login' ], 10, 2 );
		add_action( 'user_register', [ $this, 'on_register' ], 10, 2 );
		add_action( 'show_user_profile', [ $this, 'render_fields' ] );
		add_action( 'edit_user_profile', [ $this, 'render_fields' ] );
		add_action( 'personal_options_update', [ $this, 'save_fields' ] );
		add_action( 'edit_user_profile_update', [ $this,'save_fields'] );
		add_action( 'restrict_manage_users', [ $this, 'filter_users_dropdown' ], 99 );
		add_action( 'restrict_manage_network_users', [ $this, 'filter_users_dropdown' ], 99 );
		add_action( 'pre_get_users', [ $this, 'filter_dropdown' ] );

		if ( current_user_can( 'manage_network_users' ) ) {
			add_filter( 'manage_site-users-network_columns', [ $this, 'add_last_login_column' ] );
			add_filter( 'manage_users_columns', [ $this, 'add_last_login_column' ] );
			add_filter( 'wpmu_users_columns', [ $this, 'add_last_login_column' ], 1 );
			add_filter( 'manage_users_custom_column', [ $this, 'manage_users_custom_column' ], 10, 3 );
			add_filter( 'manage_users_sortable_columns', [ $this, 'add_sortable_column' ] );
			add_filter( 'manage_users-network_sortable_columns', [ $this, 'add_sortable_column' ] );
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			\WP_CLI::add_command( 'bbgi users set_last_login', [ $this, 'cli_set_last_login' ] );
			\WP_CLI::add_command( 'bbgi users get_disabled_users', [ $this, 'cli_get_disabled_users' ] );
		}
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
		if ( is_a( $user, \WP_User::class ) && ( $this->is_user_disabled( $user->ID ) || $this->maybe_disable_user( $user->ID ) ) ) {
			return new \WP_Error(
				'bbgi_disabled_user',
				esc_html__( 'This user account has been disabled by an administrator', 'beasley' )
			);
		}

		return $user;
	}

	/**
	 * Runs right after a user logs in
	 *
	 * @param string   $user_login The user login
	 * @param \WP_User $user The user object.
	 *
	 * @return void
	 */
	public function on_login( $user_login, \WP_User $user ) {
		$this->set_last_login( $user->ID );
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
	 * Maybe disable a given user.
	 *
	 * @param integer $user_id User id.
	 *
	 * @return Boolean
	 */
	public function maybe_disable_user( $user_id ) {
		// check if the user didn't login in the past 60 days.
		$last_login = $this->get_last_login( $user_id );

		//  if the user does not have last login data so we can't disable it.
		if ( ! $last_login ) {
			return false;
		}

		$today      = time();
		$diff       = date_diff( date_create( date( 'Y-m-d', $today ) ), date_create( date( 'Y-m-d', $last_login ) ) );

		if ( $diff->days >= self::INACTIVITY_THRESHOLD ) {
			update_user_meta( $user_id, self::USER_DISABLED_META, true );
			return true;
		}

		return false;
	}

	/**
	 * Returns the user last login.
	 *
	 * @param int $user_id
	 *
	 * @return int
	 */
	public function get_last_login( $user_id ) {
		return (int) get_user_meta( $user_id, self::USER_LAST_LOGIN_META, true );
	}

	/**
	 * Set the user last login
	 *
	 * @param integer $user_id The user's id.
	 *
	 * @return void
	 */
	public function set_last_login( $user_id ) {
		update_user_meta( $user_id, self::USER_LAST_LOGIN_META, time() );
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
							<?php esc_html_e( 'Disabling an user will prevent it from logging in on the site.', 'beasley' ); ?>
						</label>
						<br />
						<p class="description" id="<?php echo esc_attr( self::USER_DISABLED_META ); ?>">
							<?php
								echo esc_html(
									sprintf(
										'Re-enabling an user will grant another %s days to log in.',
										self::INACTIVITY_THRESHOLD
									),
									'bbgi'
								);
							?>
						</p>
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

		$was_user_disabled = $this->is_user_disabled( $user_id );
		$is_user_disabled  = filter_input( INPUT_POST, self::USER_DISABLED_META, FILTER_SANITIZE_NUMBER_INT );

		if ( $was_user_disabled && ! $is_user_disabled ) {
			// reset last login to today.
			$this->set_last_login( $user_id );
		}

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

		if ( isset( $query->query_vars['orderby'] ) && self::USER_LAST_LOGIN_META === $query->query_vars['orderby'] ) {
			$query->set( 'meta_key', self::USER_LAST_LOGIN_META );
			$query->set( 'orderby', 'meta_value_num' );
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

	/**
	 * Adds our custom columns to the users table.
	 *
	 * @param array $columns The default columns
	 *
	 * @return array
	 */
	public function add_last_login_column( $columns ) {
		$columns[ self::USER_LAST_LOGIN_META ] = esc_html__( 'Last Login', 'beasley' );

		return $columns;
	}

	/**
	 * Adds our custom columns to the users table.
	 *
	 * @param array $columns The default columns
	 *
	 * @return array
	 */
	public function add_sortable_column( $columns ) {
		$columns[ self::USER_LAST_LOGIN_META ] = self::USER_LAST_LOGIN_META;

		return $columns;
	}

	/**
	 * Adds the last login column.
	 *
	 * @param string $value Value of the custom columm.
	 * @param string $column_name Name of the custom column.
	 * @param int    $user_id The user's id.
	 *
	 * @return string
	 */
	public function manage_users_custom_column( $value, $column_name, $user_id ) {
		if ( self::USER_LAST_LOGIN_META === $column_name ) {
			$last_login = $this->get_last_login( $user_id );
			$value      = esc_html__( 'Never', 'beasley' );

			if ( $last_login ) {
				$value = date_i18n( 'Y/m/d H:i:s', $last_login );
			}
		}

		return $value;
	}

	/**
	 * CLI command to set last login
	 *
	 * [--dry-run]
	 * : Run the command in dry-run mode.
	 */
	public function cli_set_last_login( $args, $assoc_args ) {
		$dry_run = isset( $assoc_args['dry-run'] ) ? true : false;

		$users_ids = get_users(
			[
				'blog_id' => 0,
				'fields'  => 'ID',
			]
		);

		$progress = \WP_CLI\Utils\make_progress_bar( 'Updating users with last login', count( $users_ids ) );
		$updated = 0;
		foreach ( $users_ids as $user_id ) {
			if ( ! $this->get_last_login( $user_id ) ) {
				$updated++;
				if ( ! $dry_run ) {
					$this->set_last_login( $user_id );
				}
			}

			$progress->tick();
		}

		$progress->finish();

		\WP_CLI::success( sprintf( '%d users were updated', $updated ) );
	}

	/**
	 * CLI command to return disabled users
	 */
	public function cli_get_disabled_users( $args, $assoc_args ) {
		$users = get_users(
			[
				'blog_id' => 0,
				'meta_query' => [
					[
						'key'   => self::USER_DISABLED_META,
						'value' => '1',
					],
				]
			]
		);

		$data = [];

		/**
		 * @var \WP_User $user
		 */
		foreach ( $users as $user ) {
			$data[] = [
				$user->ID,
				$user->user_firstname . ' ' . $user->user_lastname,
				$user->user_login,
				$user->user_email,
			];
		}

		$table = new \cli\Table();
		$table->setHeaders( [ 'User ID', 'User Name', 'User Login', 'User Email' ] );
		$table->setRows( $data );
		$table->display();
	}
}

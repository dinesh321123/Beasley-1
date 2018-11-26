<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaContestsMetaboxes {

	public function __construct() {
		add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	/**
	 * Displays contest settings.
	 *
	 * @action post_submitbox_misc_actions
	 */
	public function post_submitbox_misc_actions() {
		global $typenow;
		if ( GMR_CONTEST_CPT != $typenow ) {
			return;
		}

		$post         = get_post();
		$contest_type = get_post_meta( $post->ID, 'contest_type', true );
		switch ( $contest_type ) {
			case 'onair':
				$contest_type_label = 'On Air';
				break;
			case 'both':
				$contest_type_label = 'On Air & Online';
				break;
			case 'online':
			default:
				$contest_type_label = 'Online';
				$contest_type       = 'online';
				break;
		}


		?>
		<div id="contest-type" class="misc-pub-section misc-pub-gmr-contest mis-pub-radio">
		Contest Type:
		<span class="post-pub-section-value radio-value"><?php echo esc_html( $contest_type_label ); ?></span>
		<a href="#" class="edit-radio hide-if-no-js" style="display: inline;"><span aria-hidden="true">Edit</span></a>

		<div class="radio-select hide-if-js">
			<label><input type="radio" name="contest_type" value="online"<?php checked( $contest_type, 'online' ); ?>>
				Online</label><br>
			<label><input type="radio" name="contest_type" value="onair"<?php checked( $contest_type, 'onair' ); ?>> On
				Air</label><br>
			<label><input type="radio" name="contest_type" value="both"<?php checked( $contest_type, 'both' ); ?>> On
				Air &amp; Online</label><br>

			<p>
				<a href="#" class="save-radio hide-if-no-js button"><?php esc_html_e( 'OK' ) ?></a>
				<a href="#" class="cancel-radio hide-if-no-js button-cancel"><?php esc_html_e( 'Cancel' ) ?></a>
			</p>
		</div>
		</div><?php
	}

	/**
	 * Enqueue JavaScript & CSS
	 * Implements admin_enqueue_scripts action
	 */
	public function admin_enqueue_scripts() {
		global $post;

		// Make sure this is the post editor
		$current_screen = get_current_screen();
		if ( 'post' !== $current_screen->base ) {
			return;
		}

		// Make sure there's a post
		if ( ! isset( $GLOBALS['post'] ) || ! ( $GLOBALS['post'] instanceof WP_Post ) ) {
			return;
		}

		if ( $post && GMR_CONTEST_CPT === $post->post_type ) {
			$base_path = trailingslashit( GREATER_MEDIA_CONTESTS_URL );
			$postfix   = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

			wp_enqueue_style( 'datetimepicker' );
			wp_enqueue_style( 'font-awesome' );

			wp_enqueue_script( 'ie8-node-enum' );
			wp_enqueue_script( 'underscore-mixin-deepextend' );
			wp_enqueue_script( 'backbone-deep-model' );
			wp_enqueue_script( 'datetimepicker' );

			wp_enqueue_script( 'rivets' );

			wp_enqueue_script( 'greatermedia-contests-admin', "{$base_path}js/contests-admin{$postfix}.js", array( 'jquery' ), false, true );
		};
	}

	/**
	 * Render an HTML5 date input meta field
	 *
	 * @param array $args
	 */
	public function render_date_field( array $args ) {
		static $render_server_time = true;

		$name = $args['name'];
		$date = ! empty( $args['value'] ) && is_numeric( $args['value'] ) ? $args['value'] : null;
		if ( ! empty( $date ) ) {
			$date += get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
		}

		$args['value'] = '';

		$args['type'] = 'date';
		$args['name'] = $name . '[date]';
		if ( $date ) {
			$args['value'] = date( 'Y-m-d', $date );
		}
		self::render_input( $args );

		$args['type'] = 'time';
		$args['name'] = $name . '[time]';
		$args['id']   = $args['id'] . '_time';
		if ( $date ) {
			$args['value'] = date( 'H:i', $date );
		}
		self::render_input( $args );

		if ( $render_server_time ) {
			$format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
			echo ' <small>(server time is ' . date( $format, current_time( 'timestamp' ) ) . ')</small>';
			$render_server_time = false;
		}

	}

	public function render_input( array $args ) {

		if ( ! isset( $args['type'] ) || empty( $args['type'] ) ) {
			$args['type'] = 'text';
		}

		if ( isset( $args['size'] ) ) {
			$size_attr = 'size="' . absint( $args['size'] ) . '"';
		} else {
			$size_attr = '';
		}

		echo '<input type="' . esc_attr( $args['type'] ) . '" id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $args['name'] ) . '" value="' . esc_attr( $args['value'] ) . '" ' . $size_attr . '>';

	}

	/**
	 * Register meta boxes on the Contest editor
	 * Implements add_meta_boxes action
	 */
	public function add_meta_boxes() {

		add_meta_box(
			'contest-settings-win',
			'What You Win',
			array( $this, 'contest_win_metabox' ),
			GMR_CONTEST_CPT,
			'normal',
			'high'
		);

		add_meta_box(
			'contest-settings-enter',
			'How to Enter',
			array( $this, 'contest_enter_metabox' ),
			GMR_CONTEST_CPT,
			'normal',
			'high'
		);

		add_meta_box(
			'contest-settings-rules',
			'Official Contest Rules',
			array( $this, 'contest_rules_metabox' ),
			GMR_CONTEST_CPT,
			'normal',
			'high'
		);

		add_meta_box(
			'contest-settings-restrictions',
			'Restrictions',
			array( $this, 'contest_restrictions_metabox' ),
			GMR_CONTEST_CPT,
			'normal',
			'high'
		);

		add_meta_box( 'gallery', 'Gallery', array( $this, 'gallery_meta_box' ), GMR_CONTEST_CPT, 'side' );
	}

	public function contest_win_metabox( WP_Post $post ) {
		$post_id = $post->ID;

		wp_nonce_field( 'contest_meta_boxes', '__contest_nonce' );

		wp_editor( get_post_meta( $post_id, 'prizes-desc', true ), 'greatermedia_contest_prizes' );
	}

	public function contest_enter_metabox( WP_Post $post ) {
		$post_id = $post->ID;

		wp_editor( get_post_meta( $post_id, 'how-to-enter-desc', true ), 'greatermedia_contest_enter' );
	}

	public function contest_rules_metabox( WP_Post $post ) {
		$post_id = $post->ID;

		wp_editor( get_post_meta( $post_id, 'rules-desc', true ), 'greatermedia_contest_rules' );
	}

	public function contest_restrictions_metabox( WP_Post $post ) {
		$post_id = $post->ID;

		$this->_restrictions_settings( $post );
	}

	private function _restrictions_settings( WP_Post $post ) {
		$post_status     = get_post_status_object( $post->post_status );
		$datetime_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

		$started = get_post_meta( $post->ID, 'contest-start', true );
		$ended   = get_post_meta( $post->ID, 'contest-end', true );

		$is_secret = get_post_meta( $post->ID, 'secret', true );
		$is_secret = filter_var( $is_secret, FILTER_VALIDATE_BOOLEAN );

		$offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;

		?>
		<table class="form-table">
		<tr>
			<th scope="row"><label for="greatermedia_contest_start">Start date</label></th>
			<td>
				<?php if ( ! $post_status->public ) : ?>
					<?php $this->render_date_field( array(
						'post_id' => $post->ID,
						'id'      => 'greatermedia_contest_start',
						'name'    => 'greatermedia_contest_start',
						'value'   => get_post_meta( $post->ID, 'contest-start', true )
					) ); ?>
				<?php else : ?>
					<b>
						<?php if ( ! empty( $started ) ) : ?>
							<?php echo date( $datetime_format, $started + $offset ); ?>
						<?php else : ?>
							&#8212;
						<?php endif; ?>
					</b>

					<?php if ( ! empty( $started ) ) : ?>
						<small style="margin-left:2em;">
							(server time is <?php echo date( $datetime_format, current_time( 'timestamp' ) ); ?>)
						</small>
					<?php endif; ?>
				<?php endif; ?>
			</td>
		</tr>

		<tr>
			<th scope="row"><label for="greatermedia_contest_end">End date</label></th>
			<td>
				<?php if ( ! $post_status->public ) : ?>
					<?php $this->render_date_field( array(
						'post_id' => $post->ID,
						'id'      => 'greatermedia_contest_end',
						'name'    => 'greatermedia_contest_end',
						'value'   => get_post_meta( $post->ID, 'contest-end', true ),
					) ); ?>
				<?php else : ?>
					<b>
						<?php if ( ! empty( $ended ) ) : ?>
							<?php echo date( $datetime_format, $ended + $offset ); ?>
						<?php else : ?>
							&#8212;
						<?php endif; ?>
					</b>

					<?php if ( empty( $started ) && ! empty( $ended ) ) : ?>
						<small style="margin-left:2em;">
							(server time is <?php echo date( $datetime_format, current_time( 'timestamp' ) ); ?>)
						</small>
					<?php endif; ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="content_is_secret">Make this contest secret</label></th>
			<td>
				<input type="hidden" name="content_is_secret"
				       value="0"/>
				<input type="checkbox" value="1" <?php checked( $is_secret ); ?> id="content_is_secret"
				       name="content_is_secret"/>
			</td>
		</tr>
		</table><?php
	}

	public function gallery_meta_box( WP_Post $post ) {

		$images = get_children( array(
			'numberposts'    => 500, // do we need more?
			'post_parent'    => $post->ID,
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_status'    => 'inherit',
		) );

		?><input type="text" class="widefat" readonly disabled
		         value="<?php echo esc_attr( '[gallery ids="' . implode( ',', array_keys( $images ) ) . '"]' ); ?>">
		<span class="description">
			To create a standalone gallery of the entries, copy the content of this field, create a new Gallery post, then paste it in the content for that Gallery.
		</span><?php

	}

	/**
	 * Save meta fields on post save
	 *
	 * @param int $post_id
	 */
	public function save_post( $post_id ) {

		$post = get_post( $post_id );

		// Verify that the form nonce is valid.
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, '__contest_nonce' ), 'contest_meta_boxes' ) ) {
			return;
		}

		// If this is an autosave, the editor has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Make sure the post type is correct
		if ( GMR_CONTEST_CPT !== $post->post_type ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Update the contest rules meta fields
		update_post_meta( $post_id, 'prizes-desc', wp_kses_post( $_POST['greatermedia_contest_prizes'] ) );
		update_post_meta( $post_id, 'how-to-enter-desc', wp_kses_post( $_POST['greatermedia_contest_enter'] ) );
		update_post_meta( $post_id, 'rules-desc', wp_kses_post( $_POST['greatermedia_contest_rules'] ) );
		update_post_meta( $post_id, 'contest_type', filter_input( INPUT_POST, 'contest_type' ) );

		$offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
		$dates  = array(
			'contest-start' => 'greatermedia_contest_start',
			'contest-end'   => 'greatermedia_contest_end',
		);

		if ( isset( $_POST['content_is_secret'] ) ) {
			$secret = filter_input( INPUT_POST, 'content_is_secret', FILTER_VALIDATE_BOOLEAN );
			update_post_meta( $post_id, 'secret', $secret );
			add_action( 'wpseo_saved_postdata', array( $this, 'update_noindex' ), 999 );

		}

		foreach ( $dates as $meta => $param ) {
			if ( isset( $_POST[ $param ]['date'] ) ) {
				$value = 0;
				if ( ! empty( $_POST[ $param ]['date'] ) ) {
					$value = $_POST[ $param ]['date'];
					if ( ! empty( $_POST[ $param ]['time'] ) ) {
						$value .= ' ' . $_POST[ $param ]['time'];
					}

					$value = strtotime( $value ) - $offset; // convert to timestamp and UTC
				}

				update_post_meta( $post_id, $meta, $value );
			}
		}
	}

	public function update_noindex( $post_id ) {
		global $post_id;

		$is_secret = get_post_meta( $post_id, 'secret', true );
		$is_secret = filter_var( $is_secret, FILTER_VALIDATE_BOOLEAN );

		update_post_meta( $post_id, '_yoast_wpseo_meta-robots-noindex', $is_secret );
	}

}

$GreaterMediaContestsMetaboxes = new GreaterMediaContestsMetaboxes();

<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

class GreaterMediaTimedContent extends VisualShortcode {

	function __construct() {

		parent::__construct(
			'time-restricted',
			'GreaterMediaTimedContentAdmin',
			'dashicons-clock',
			null,
			__( 'Timed Content', 'greatermedia-timed-content' )
		);

		add_action( 'current_screen', array( $this, 'current_screen' ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 20, 0 );
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
		add_action( 'greatermedia_expire_post', array( $this, 'greatermedia_expire_post' ) );

	}

	public function current_screen() {
		if ( current_user_can( 'manage_options' ) ) {
			$current_screen = get_current_screen();
			if ( 'options-general' !== $current_screen->id ) {
				$gmt_offset = intval( get_option( 'gmt_offset', 0 ) );
				if ( 0 === $gmt_offset && class_exists( 'GreaterMediaAdminNotifier' ) ) {
					GreaterMediaAdminNotifier::message( sprintf( "WordPress thinks this site's timezone is GMT (Greenwich, England). Please <a href=\"%s\">change the time zone</a> for correct date calculations.", esc_url( admin_url( 'options-general.php' ) ) ) );
				}
			}
		}
	}


	/**
	 * Set up the textdomain, even thought we don't really use it
	 */
	public function plugins_loaded() {
		load_plugin_textdomain( 'greatermedia-timed-content', false, GREATER_MEDIA_TIMED_CONTENT_PATH );
	}

	/**
	 * Render an expiration time field in the post submitbox
	 */
	public function post_submitbox_misc_actions() {

		global $post;

		if ( ! post_type_supports( $post->post_type, 'timed-content' ) ) {
			return;
		}

		$expiration_timestamp = get_post_meta( $post->ID, 'post_expiration', true );

		if ( false !== $expiration_timestamp && '' !== $expiration_timestamp && ! empty( $expiration_timestamp ) ) {
			$rendered_expiration_timestamp = date_i18n( __( 'M j, Y @ G:i' ), $expiration_timestamp );
		} else {
			$rendered_expiration_timestamp = __( 'Never', 'greatermedia-timed-content' );
		}

		include trailingslashit( GREATER_MEDIA_TIMED_CONTENT_PATH ) . 'tpl/post-submitbox-misc-actions.tpl.php';

	}

	/**
	 * Enqueue JavaScript and CSS resources for admin functionality as needed
	 */
	public function admin_enqueue_scripts() {

		global $post;

		if ( $post && post_type_supports( $post->post_type, 'timed-content' ) ) {

			// Enqueue CSS
			wp_enqueue_style( 'greatermedia-tc', trailingslashit( GREATER_MEDIA_TIMED_CONTENT_URL ) . 'css/greatermedia-timed-content.css' );
			wp_enqueue_style( 'datetimepicker');
			wp_enqueue_script( 'datetimepicker');

			wp_enqueue_script( 'date-format' );
			wp_enqueue_script( 'date-toisostring' );
			wp_enqueue_script( 'greatermedia-tc-admin-js', trailingslashit( GREATER_MEDIA_TIMED_CONTENT_URL ) . 'js/greatermedia-timed-content-admin.js', array(
				'jquery',
				'date-format'
			), false, true );

			$expiration_timestamp = get_post_meta( $post->ID, 'post_expiration', true );

			// Settings & translation strings used by the JavaScript code
			$settings = array(
				'templates'          => array(
					'expiration_time' => self::touch_exptime( 1, $expiration_timestamp ),
					'tinymce'         => file_get_contents( trailingslashit( GREATER_MEDIA_TIMED_CONTENT_PATH ) . 'tpl/greatermedia-tinymce-view-template.js' ),
				),
				'rendered_templates' => array(),
				'strings'            => array(
					'never'           => __( 'Never', 'greatermedia-timed-content' ),
					'Timed Content'   => __( 'Timed Content', 'greatermedia-timed-content' ),
					'Show content on' => __( 'Show content on', 'greatermedia-timed-content' ),
					'Hide content on' => __( 'Hide content on', 'greatermedia-timed-content' ),
					'Content'         => __( 'Content', 'greatermedia-timed-content' ),
				),
				'formats'            => array(
					'date'          => __( 'M j, Y G:i' ),
					'mce_view_date' => __( 'F j, Y g:i a' ),
				),
			);

			wp_localize_script( 'greatermedia-tc-admin-js', 'GreaterMediaTimedContent', $settings );

		}
	}

	/**
	 * Enqueue JavaScript and CSS for public-facing functionality
	 */
	public function wp_enqueue_scripts() {

		// Public-facing page
		wp_enqueue_script( 'greatermedia-tc', trailingslashit( GREATER_MEDIA_TIMED_CONTENT_URL ) . 'js/greatermedia-timed-content.js', array(
				'jquery',
				'underscore'
			), false, true );

	}

	/**
	 * On admin UI post save, update the expiration date postmeta
	 *
	 * @param int $post_id Post ID
	 */
	public function save_post( $post_id ) {

		$post = get_post( $post_id );

		if ( post_type_supports( $post->post_type, 'timed-content' ) ) {

			$exp_mm = isset( $_POST['hidden_exp_mm'] ) ? intval( $_POST['hidden_exp_mm'] ) : '0';
			$exp_jj = isset( $_POST['hidden_exp_jj'] ) ? intval( $_POST['hidden_exp_jj'] ) : '0';
			$exp_aa = isset( $_POST['hidden_exp_aa'] ) ? intval( $_POST['hidden_exp_aa'] ) : '0';
			$exp_hh = isset( $_POST['hidden_exp_hh'] ) ? intval( $_POST['hidden_exp_hh'] ) : '0';
			$exp_mn = isset( $_POST['hidden_exp_mn'] ) ? str_pad( intval( $_POST['hidden_exp_mn'] ), 2, '0', STR_PAD_LEFT ) : '00';

			$exp_str = "{$exp_mm} {$exp_jj} {$exp_aa} {$exp_hh} {$exp_mn}";
			if ( '0 0 0 0 00' !== trim( $exp_str ) ) {
				$exp_date      = DateTime::createFromFormat( 'n j Y G i', $exp_str );
				$exp_timestamp = $exp_date->getTimestamp();
			} else {
				$exp_timestamp = '';
			}

			$local_to_gmt_time_offset = get_option( 'gmt_offset' ) * - 1 * 3600;
			$exp_timestamp_gmt        = $exp_timestamp + $local_to_gmt_time_offset;
			delete_post_meta( $post_id, 'post_expiration' );
			if ( '' !== $exp_timestamp ) {
				add_post_meta( $post_id, 'post_expiration', $exp_timestamp );
			}

			// If the expiration date is in the future, set a cron to expire the post
			if ( $exp_timestamp_gmt > gmdate( 'U' ) ) {
				wp_clear_scheduled_hook( 'greatermedia_expire_post', array( $post_id ) ); // clear anything else in the system
				wp_schedule_single_event( $exp_timestamp_gmt, 'greatermedia_expire_post', array( $post_id ) );
			}

		} else {

			// Clean up any post expiration data that might already exist, in case the post support changed
			delete_post_meta( $post_id, 'post_expiration' );
			wp_clear_scheduled_hook( 'greatermedia_expire_post', array( $post_id ) );

		}

	}

	/**
	 * Print out HTML form date elements for editing post or comment publish date.
	 *
	 * @param int|bool $edit        Accepts 1|true for editing the date, 0|false for adding the date.
	 * @param int      $exptime_gmt Current expiration time
	 * @param int      $tab_index   Starting tab index
	 * @param int      $multi       Optional. Whether the additional fields and buttons should be added.
	 *                              Default 0|false.
	 *
	 * @return string HTML
	 * @see  touch_time() in wp-admin/includes/template.php
	 * @todo use a template instead of string concatenation for building HTML
	 */
	function touch_exptime( $edit = 1, $exptime_gmt = 0, $tab_index = 0, $multi = 0 ) {

		global $wp_locale;

		$html = '';

		$tab_index_attribute = '';
		if ( (int) $tab_index > 0 ) {
			$tab_index_attribute = " tabindex=\"$tab_index\"";
		}

		$time_adj = current_time( 'timestamp' );

		$jj = ! empty( $exptime_gmt ) ? gmdate( 'd', $exptime_gmt ) : '';
		$mm = ! empty( $exptime_gmt ) ? gmdate( 'm', $exptime_gmt ) : '';
		$aa = ! empty( $exptime_gmt ) ? gmdate( 'Y', $exptime_gmt ) : '';
		$hh = ! empty( $exptime_gmt ) ? gmdate( 'H', $exptime_gmt ) : '';
		$mn = ! empty( $exptime_gmt ) ? gmdate( 'i', $exptime_gmt ) : '';
		$ss = ! empty( $exptime_gmt ) ? gmdate( 's', $exptime_gmt ) : '';

		$cur_jj = gmdate( 'd', $time_adj );
		$cur_mm = gmdate( 'm', $time_adj );
		$cur_aa = gmdate( 'Y', $time_adj );
		$cur_hh = gmdate( 'H', $time_adj );
		$cur_mn = gmdate( 'i', $time_adj );

		$month = '<label for="exp_mm" class="screen-reader-text">' . __( 'Month' ) . '</label><select ' . ( $multi ? '' : 'id="exp_mm" ' ) . 'name="exp_mm"' . $tab_index_attribute . ">\n";
		for ( $i = 1; $i < 13; $i = $i + 1 ) {
			$monthnum = zeroise( $i, 2 );
			$month .= "\t\t\t" . '<option value="' . $monthnum . '" ' . selected( $monthnum, $mm, false ) . '>';
			/* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
			$month .= sprintf( __( '%1$s-%2$s' ), $monthnum, $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) ) . "</option>\n";
		}
		$month .= '</select>';

		$day    = '<label for="exp_jj" class="screen-reader-text">' . __( 'Day' ) . '</label><input type="text" ' . ( $multi ? '' : 'id="exp_jj" ' ) . 'name="exp_jj" value="' . $jj . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';
		$year   = '<label for="exp_aa" class="screen-reader-text">' . __( 'Year' ) . '</label><input type="text" ' . ( $multi ? '' : 'id="exp_aa" ' ) . 'name="exp_aa" value="' . $aa . '" size="4" maxlength="4"' . $tab_index_attribute . ' autocomplete="off" />';
		$hour   = '<label for="exp_hh" class="screen-reader-text">' . __( 'Hour' ) . '</label><input type="text" ' . ( $multi ? '' : 'id="exp_hh" ' ) . 'name="exp_hh" value="' . $hh . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';
		$minute = '<label for="exp_mn" class="screen-reader-text">' . __( 'Minute' ) . '</label><input type="text" ' . ( $multi ? '' : 'id="exp_mn" ' ) . 'name="exp_mn" value="' . $mn . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';

		$html .= '<div class="timestamp-wrap">';
		/* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
		$html .= sprintf( __( '%1$s %2$s, %3$s @ %4$s : %5$s' ), $month, $day, $year, $hour, $minute );

		$html .= '</div><input type="hidden" id="exp_ss" name="exp_ss" value="' . $ss . '" />';

		if ( $multi ) {
			return;
		}

		$html .= "\n\n";
		$map = array(
			'mm' => array( $mm, $cur_mm ),
			'jj' => array( $jj, $cur_jj ),
			'aa' => array( $aa, $cur_aa ),
			'hh' => array( $hh, $cur_hh ),
			'mn' => array( $mn, $cur_mn ),
		);
		foreach ( $map as $timeunit => $value ) {
			list( $unit, $curr ) = $value;

			$html .= '<input type="hidden" id="hidden_exp_' . $timeunit . '" name="hidden_exp_' . $timeunit . '" value="' . $unit . '" />' . "\n";
			$cur_timeunit = 'cur_' . $timeunit;
			$html .= '<input type="hidden" id="exp_' . $cur_timeunit . '" name="exp_' . $cur_timeunit . '" value="' . $curr . '" />' . "\n";
		}


		$html .= '<p>';
		$html .= '<a href="#edit_timestamp" class="save-timestamp hide-if-no-js button">' . __( 'OK' ) . '</a>';
		$html .= '<a href="#edit_timestamp" class="remove-timestamp hide-if-no-js button">' . __( 'Remove' ) . '</a>';
		$html .= '<a href="#edit_timestamp" class="cancel-timestamp hide-if-no-js button-cancel">' . __( 'Cancel' ) . '</a>';
		$html .= '</p>';

		return $html;

	}

	/**
	 * Expire a post to the "draft" status at the appointed time
	 *
	 * @param int $post_id
	 */
	public function greatermedia_expire_post( $post_id ) {

		$post              = get_post( $post_id );
		$post->post_status = 'draft';
		wp_update_post( $post );

	}

	/**
	 * Process the time-restricted shortcode
	 *
	 * @param      array  $attributes
	 * @param string|null $content optional content to display
	 *
	 * @return null|string output to display
	 */
	function process_shortcode( array $attributes, $content = null ) {

		if ( isset( $attributes['show'] ) ) {
			$show = strtotime( $attributes['show'] );
		} else {
			$show = 0;
		}

		if ( isset( $attributes['hide'] ) ) {
			$hide = strtotime( $attributes['hide'] );
		} else {
			$hide = PHP_INT_MAX;
		}

		$now_gmt = intval( gmdate( 'U' ) );
		if ( ( $now_gmt > $show ) && ( $hide > $now_gmt ) ) {
			return $content;
		} else {
			return '';
		}

	}

}

$GreaterMediaTimedContent = new GreaterMediaTimedContent ();
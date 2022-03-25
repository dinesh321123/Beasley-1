<?php
/**
 * Created by Eduard
 */

class CronTasks {

	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'syndication_setup_schedule' ) );
		add_filter( 'cron_schedules', array( __CLASS__, 'syndication_cron_intervals' ) );
		add_action( 'syndication_five_minute_run_event', array( __CLASS__, 'run_syndication' ) );
		add_action( 'gmr_do_syndication', array( __CLASS__, 'do_syndication' ) );
		add_filter( 'ep_sync_insert_permissions_bypass', array( __CLASS__, 'ep_sync_insert_permissions_bypass' ) );
	}

	/**
	 * Setup hourly event to run syndication
	 */
	public static function syndication_setup_schedule() {
		// Remove old cron with recurrence as ‘hourly’	
		wp_clear_scheduled_hook('syndication_five_minute_event');

		// Change recurrence as ‘hourly’ to custom '20minute'
		if ( ! wp_next_scheduled( 'syndication_five_minute_run_event' ) ) {
			wp_schedule_event( self::get_time_for_syndication(), '20minute', 'syndication_five_minute_run_event' );
		}
	}

	/**
	 * Runs actual syndication
	 */
	public static function run_syndication() {
		if ( function_exists( 'wp_async_task_add' ) ) {
			$active_subscriptions = BlogData::GetActiveSubscriptions();
			foreach ( $active_subscriptions as $active_subscription ) {
				wp_async_task_add( 'gmr_do_syndication', array( 'subscription' => $active_subscription->ID ), 'high' );
			}
		}
	}

	public static function do_syndication( $args ) {
		BlogData::run( $args['subscription'] );
	}

	/**
	 * Add custom 20 minute cron interval.
	 *
	 * @param array $schedules Cron schedules.
	 * @return array
	 */
	public static function syndication_cron_intervals( $schedules ) {
		if ( ! isset( $schedules['20minute'] ) ) {
			$schedules['20minute'] = array(
				'interval' => 20 * MINUTE_IN_SECONDS,
				'display'  => 'Every 20 minutes',
			);
		}
		return $schedules;
	}

	/**
	 * Gist from Chris to setup propper delay between the schedules
	 */
	public static function get_time_for_syndication() {
		// Gets the blog id for the current site
		$blog_id = get_current_blog_id();

		// Figures out which interval this blog should be on
		$interval = $blog_id % 12;

		// Takes the interval, and makes sure its two digits by adding zeros to the left side (only really does anything for 0 and 5)
		$offset_from_hour = str_pad( $interval * 5, 2, '0', STR_PAD_LEFT );

		// Gets current day and hour, to use when assembling the time string
		$current_date = date( 'Y-m-d' );
		$current_hour = date( 'H' );

		/*
		 * Figures out what time the first event should fire at for this blog using the current date, current hour, and offset.
		 *
		 * Blogs are offset from each other by 5 minutes to avoid syndication happening at the same time on too many blogs.
		 *
		 * Blog Offsets:
		 *  1  => H:05
		 *  2  => H:10
		 *  3  => H:15
		 *  4  => H:20
		 *  5  => H:25
		 *  6  => H:30
		 *  7  => H:35
		 *  8  => H:40
		 *  9  => H:45
		 *  10 => H:50
		 *  11 => H:55
		 *  12 => H:00
		 *  13 => H:05
		 *  etc...
		 *
		 * This ends up creating a string in format Y-m-d H:i:s
		 */
		$next_time = "{$current_date} {$current_hour}:{$offset_from_hour}:00";

		// Converts this back to unix timestamp
		$time_string = strtotime( $next_time );

		// If we end up with a time in the past, we add an hour to it, so that the first event happens in the future
		if ( $time_string < time() ) {
			$time_string = $time_string + ( 60 * MINUTE_IN_SECONDS );
		}

		return $time_string;
	}

	/**
	 * Async tasks don't have a user, so we need to make sure content is still indexed when coming from syndication processes
	 *
	 * @param $bypass
	 *
	 * @return bool
	 */
	public static function ep_sync_insert_permissions_bypass( $bypass ) {
		if ( defined( 'DOING_ASYNC' ) && DOING_ASYNC ) {
			$bypass = true;
		}

		return $bypass;
	}

}

CronTasks::init();

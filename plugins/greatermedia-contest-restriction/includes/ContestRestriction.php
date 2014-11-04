<?php
/**
 * Created by Eduard
 *
 */

class ContestRestriction {

	private $post_type = 'contest';
	private $gigya_session;
	private $user_age;
	private $user_ip;

	public function __construct() {
		add_action( 'template_redirect', array( $this, 'restrict_contest' ) );
	}

	public function restrict_contest() {
		global $post;
		$post_id = $post->ID;
		$post_type = $post->post_type;

		if( $post->post_type == $this->post_type ) {
			$member_only = get_post_meta( $post_id, '_member_only', true );
			$max_entries = get_post_meta( $post_id, '_max_entries', true );
			$min_age = get_post_meta( $post_id, '_min_age', true );
			$restrict_number = get_post_meta( $post_id, '_restrict_number', true );
			$restrict_age = get_post_meta( $post_id, '_restrict_age', true );
			$start = get_post_meta( $post_id, 'start-date', true );
			$end = get_post_meta( $post_id, 'end-date', true );

			// get user IP
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$this->user_ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$this->user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$this->user_ip = $_SERVER['REMOTE_ADDR'];
			}

			if( function_exists('is_gigya_user_logged_in') ) {
				if( $member_only == 'on' && !is_gigya_user_logged_in() ) {
					add_filter( "single_template", array( $this, 'member_only' ) );
					return false;
				}

				if( $restrict_age == 'on' && !is_gigya_user_logged_in() ) {
					add_filter( "single_template", array( $this, 'restrict_by_age' ) );
					$this->user_age = 18;
					return false;
				} else {
					$this->gigya_session = get_gigya_session();
					$this->user_age = 18;
				}
			}

			$current_date = new DateTime();
			$current_date = $current_date->getTimestamp();

			if( ( $current_date < $start ) ) {
				add_filter( "single_template", array( $this, 'sooner' ) );
				return false;
			} elseif ( $current_date > $end ) {
				add_filter( "single_template", array( $this, 'later' ) );
				return false;
			}

			if ( isset($_COOKIE["contest_res"]) )
			{
				if( $_COOKIE["contest_res"]['ip'] == $this->user_ip) {
					add_filter( "single_template", array( $this, 'already_entered' ) );
					return false;
				}

			} else {
				setcookie( "contest_res[ip]", $this->user_ip );
				setcookie( "contest_res[age]", $this->user_age );
			}

		}
	}


	public function member_only() {
		$single_template = GMEDIA_CONTEST_RESTRICTION_PATH . '/templates/member_only.php';
		return $single_template;
	}

	public function restrict_by_age() {
		$single_template = GMEDIA_CONTEST_RESTRICTION_PATH . '/templates/restrict_by_age.php';
		return $single_template;
	}

	public function sooner() {
		$single_template = GMEDIA_CONTEST_RESTRICTION_PATH . '/templates/sooner.php';
		return $single_template;
	}

	public function later() {
		$single_template = GMEDIA_CONTEST_RESTRICTION_PATH . '/templates/later.php';
		return $single_template;
	}

	public function already_entered() {
		$single_template = GMEDIA_CONTEST_RESTRICTION_PATH . '/templates/already_entered.php';
		return $single_template;
	}
}

$ContestRestriction = new ContestRestriction();
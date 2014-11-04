<?php
/**
 * Created by Eduard
 *
 */

class ContestRestriction {

	private $post_type = 'contest';
	private $gigya_session;
	private $user_age = 0;
	private $user_ip;

	public function __construct() {
		add_action( 'template_redirect', array( $this, 'restrict_contest' ) );
		add_action( 'wp_ajax_check_age', array( $this, 'check_user_age' ) );
		add_action( 'wp_ajax_nopriv_check_age', array( $this, 'check_user_age' ) );
	}

	public function check_user_age() {
		$url = get_site_url();
		$path = parse_url( $url );
		if( isset($_POST['user_age']) ) {
			$user_age = intval( $_POST['user_age'] );
			setcookie( "contest_res[age]", intval($_POST['user_age']), strtotime( '+30 days' ), "/", "." .$path['host'] );
		}
	}

	public function enqueue_dialog_scripts() {
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script(
			'dialog-script',
			GMEDIA_CONTEST_RESTRICTION_URL . '/assets/js/dialog.js',
			array( 'jquery' )
		);
		wp_localize_script( 'dialog-script', 'ajaxData', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
		wp_enqueue_style( 'restrict_meta_jquery_ui', GMEDIA_CONTEST_RESTRICTION_URL . "assets/css/jquery-ui.min.css", array(), '1.11.2' );

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
			$contestants = count( get_posts( array( 'post_parent' => $post_id) ) );

			// get user IP
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$this->user_ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$this->user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$this->user_ip = $_SERVER['REMOTE_ADDR'];
			}

			if( function_exists('is_gigya_user_logged_in') ) {
				if ( $member_only == 'on' && ! is_gigya_user_logged_in() ) {
					add_filter( "single_template", array( $this, 'member_only' ) );

					return false;
				}
			}
				if( $restrict_number == 'on' && $contestants >= $max_entries ) {
					add_filter( "single_template", array( $this, 'restrict_by_number' ) );
					return false;
				}

				if( $restrict_age == 'on' && !is_gigya_user_logged_in() ) {
					if( isset($_COOKIE["contest_res"]) && $_COOKIE["contest_res"]['age'] < $min_age ) {
						add_filter( "single_template", array( $this, 'restrict_by_age' ) );
						add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_dialog_scripts' ) );
						return false;
					}
				} else {
					$this->gigya_session = get_gigya_session();
					$this->user_age = 18;
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
				// TODO add this only on success
				//setcookie( "contest_res[ip]", $this->user_ip, strtotime( '+30 days' ) );
				//setcookie( "contest_res[age]", $this->user_age, strtotime( '+30 days' ) );
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
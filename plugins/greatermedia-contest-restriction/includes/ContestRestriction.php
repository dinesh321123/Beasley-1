<?php
/**
 * Created by Eduard
 *
 */

class ContestRestriction {

	private $gigya_session;
	private $user_age = 0;
	private $user_ip;

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
		global $post;
		if( isset( $post->ID ) && $post->post_type == GreaterMediaContests::CPT_SLUG ) {
			$post_id = $post->ID;
			$min_age = get_post_meta( $post_id, '_min_age', true );
			$max_entries = get_post_meta( $post_id, '_max_entries', true );
			$login_url = esc_url_raw( home_url( '/members/login/' ) );
			wp_enqueue_script( 'cookies-js' );
			wp_enqueue_script( 'restrict_contest', GMEDIA_CONTEST_RESTRICTION_URL . "assets/js/greatermedia_contest_restriction.js", array( 'jquery', 'cookies-js' ), '1.0.0' );
			wp_localize_script( 'restrict_contest', 'restrict_data', array( 'min_age' => $min_age, 'post_id' => $post_id, 'login_url' => $login_url ) );
		}
	}

	public static function restrict_contest( $post_id ) {
		$post = get_post( $post_id );
		$post_type = $post->post_type;

		if( $post->post_type == GreaterMediaContests::CPT_SLUG ) {
			$return = '';

			$member_only = get_post_meta( $post_id, '_member_only', true );
			$restrict_age = get_post_meta( $post_id, '_restrict_age', true );
			$restrict_number = get_post_meta( $post_id, '_restrict_number', true );
			$contestants = count( get_posts( array( 'post_type'=> 'contest_entry', 'post_parent' => $post_id ) ) );
			$max_entries = get_post_meta( $post_id, '_max_entries', true );
			$single_entry = get_post_meta( $post->ID, '_single_entry', true );

			if ( $member_only == 'on' ) {
				$return .= ' member_only';
			}

			if( $restrict_age == 'on' ) {
				$return .= ' restrict_age';
			}

			if( $restrict_number == 'on' && $contestants >= $max_entries ) {
				$return .= ' max_entries';
			}

			if( $single_entry == 'on' ) {
				$return .= ' single_entry';
			}

			return $return;
		}
	}

}

$ContestRestriction = new ContestRestriction();
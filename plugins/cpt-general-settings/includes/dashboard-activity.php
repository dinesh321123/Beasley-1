<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
class DashboardActivity {
	function __construct()
	{
		add_action( 'wp_dashboard_setup', array( $this, 'wp_init_callback' ) );
		add_action( 'admin_init', array( $this, 'wp_admin_init' ), 1 );
	}
	public function wp_admin_init() {
		add_action( 'save_post_affiliate_marketing', array( $this, 'remove_dashboard_activity_cache_result'), 10, 3 );
		add_action( 'save_post_gmr_gallery', array( $this, 'remove_dashboard_activity_cache_result'), 10, 3 );
		add_action( 'save_post_listicle_cpt', array( $this, 'remove_dashboard_activity_cache_result'), 10, 3 );
		add_action( 'post_updated', array($this, 'check_values'), 10, 3 );
		// add_action( 'save_post', array( $this, 'remove_dashboard_activity_cache_result_post'), 10, 3 );
		// add_action( 'save_post', array( $this, 'remove_dashboard_activity_cache_result'), 10, 3 );
	}
	public function remove_dashboard_activity_cache_result_post( $post_id, $post, $update ) {
		// bail out if this is an autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Only set for post_type = post !
		/* if ( 'post' !== $post->post_type && 'affiliate_marketing' !== $post->post_type && 'gmr_gallery' !== $post->post_type && 'listicle_cpt' !== $post->post_type ) {
			return;
		} */
		$found_dashboard	= false;
		$key				= md5('bbgi_recent_published_posts');
		$cachedata			= wp_cache_get( $key, 'bbgi', false, $found_dashboard );
		if ( $found_dashboard ) {
			wp_cache_delete($key, 'bbgi');
		}
	}
	public function check_values($post_ID, $post_after, $post_before){
		echo '<pre></pre><b>Post ID:</b><br />';
		var_dump($post_ID);
		echo '<b>Post Object AFTER update:</b><br />';
		var_dump($post_after);
		echo '<b>Post Object BEFORE update:</b><br />';
		var_dump($post_before);
		$found_dashboard	= false;
		$key				= md5('bbgi_recent_published_posts');
		$cachedata			= wp_cache_get( $key, 'bbgi', false, $found_dashboard );
		if ( $found_dashboard ) {
			wp_cache_delete($key, 'bbgi');
		}
	}

	public function remove_dashboard_activity_cache_result( $post_id, $post, $update ) {
		// bail out if this is an autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Only set for post_type = post !
		/* if ( 'post' !== $post->post_type && 'affiliate_marketing' !== $post->post_type && 'gmr_gallery' !== $post->post_type && 'listicle_cpt' !== $post->post_type ) {
			return;
		} */
		$found_dashboard	= false;
		$key				= md5('bbgi_recent_published_posts');
		$cachedata			= wp_cache_get( $key, 'bbgi', false, $found_dashboard );
		if ( $found_dashboard ) {
			wp_cache_delete($key, 'bbgi');
		}
	}

	public function wp_init_callback() {
		if ( is_blog_admin() ) {
			wp_add_dashboard_widget(
				'dashboard_recently_published_posts_activity',
				__( 'Recent Activity' ),
				array( $this,'wp_dashboard_site_recently_published_posts_activity'),
				"",
				"",
				"side",
				"high"
			);
		}
	}

	public function wp_dashboard_site_recently_published_posts_activity() {
		echo '<div id="recently_published_posts_activity-widget">';
		$found				 = false;
		$key				 = md5('bbgi_recent_published_posts');
		$dashboard_activity_result = wp_cache_get( $key, 'bbgi', false, $found );
		// echo " - Found: ", $found, " ";
		if ( ! $found ) {
			echo "Records from database.";
			$dashboard_activity_result = $this->wp_dashboard_recent_published_posts(
				array(
					'max'    => 25,
					'status' => 'publish',
					'post_type' => array( 'post', 'gmr_gallery', 'listicle_cpt', 'affiliate_marketing' ),
					'order'  => 'DESC',
					'title'  => __( 'Recently Published' ),
					'id'     => 'recently-published-posts',
				)
			);
			// Set the cache to expire the data after 1800 seconds = 30 min
			// $expiration = get_site_option( 'recently_published_posts_expiration' );
			// $value      = ( ( isset( $expiration ) && $expiration != "" ) ? $expiration : 15 );
			wp_cache_set( $key, $dashboard_activity_result, 'bbgi', '1800' );
		}
		echo  $dashboard_activity_result;
		echo '</div>';
	}
	public function wp_dashboard_recent_published_posts( $args ) {
		$html_result	= "";
		$query_args = array(
			'post_type'      => $args['post_type'],
			'post_status'    => $args['status'],
			'orderby'        => 'date',
			'order'          => $args['order'],
			'posts_per_page' => (int) $args['max'],
			'no_found_rows'  => true,
			'cache_results'  => false,
			'perm'           => ( 'future' === $args['status'] ) ? 'editable' : 'readable',
		);

		$posts = new WP_Query( $query_args );

		if ( $posts->have_posts() ) {

			$html_result .= '<div id="' . $args['id'] . '" class="activity-block">';

			$html_result .= '<h3>' . $args['title'] . '</h3>';

			$html_result .= '<ul>';

			$today    = current_time( 'Y-m-d' );
			$tomorrow = current_datetime()->modify( '+1 day' )->format( 'Y-m-d' );
			$year     = current_time( 'Y' );

			while ( $posts->have_posts() ) {
				$posts->the_post();

				$time = get_the_time( 'U' );

				if ( gmdate( 'Y-m-d', $time ) === $today ) {
					$relative = __( 'Today' );
				} elseif ( gmdate( 'Y-m-d', $time ) === $tomorrow ) {
					$relative = __( 'Tomorrow' );
				} elseif ( gmdate( 'Y', $time ) !== $year ) {
					/* translators: Date and time format for recent posts on the dashboard, from a different calendar year, see https://www.php.net/manual/datetime.format.php */
					$relative = date_i18n( __( 'M jS Y' ), $time );
				} else {
					/* translators: Date and time format for recent posts on the dashboard, see https://www.php.net/manual/datetime.format.php */
					$relative = date_i18n( __( 'M jS' ), $time );
				}

				// Use the post edit link for those who can edit, the permalink otherwise.
				$recent_post_link = current_user_can( 'edit_post', get_the_ID() ) ? get_edit_post_link() : get_permalink();

				$draft_or_post_title = _draft_or_post_title();
				$html_result .= sprintf(
					'<li><span>%1$s</span> <a href="%2$s" aria-label="%3$s">%4$s</a></li>',
					/* translators: 1: Relative date, 2: Time. */
					sprintf( _x( '%1$s, %2$s', 'dashboard' ), $relative, get_the_time() ),
					$recent_post_link,
					/* translators: %s: Post title. */
					esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $draft_or_post_title ) ),
					$draft_or_post_title
				);
			}
			$html_result .= '</ul>';
			$html_result .= '</div>';
		} else {
			return $html_result;
		}
		wp_reset_postdata();

		return $html_result;
	}

}
new DashboardActivity();

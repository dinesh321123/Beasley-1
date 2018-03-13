<?php
/**
 * Class GMP_Player
 *
 * This class constructs a podcast player to use on the front end of a website
 */
class GMP_Player{

	/**
	 * Hook into the appropriate actions when the class is initiated.
	 */
	public static function init() {

		//add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 100 );
		add_action( 'gmp_display_podcasts', array( __CLASS__, 'render_podcasts' ) );
		add_action( 'gmp_audio', array( __CLASS__, 'podcast_audio_file' ) );

	}

	/**
	 * A query to render a list of podcasts for the front end
	 */
	public static function render_podcasts() {

		global $post;

		$slug = get_post( $post )->post_name;
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		$args = array(
			'post_type'                 => 'episode',
			'_podcast'                  => $slug,
			'cache_results'             => true,
			'update_post_meta_cache'    => true,
			'update_post_term_cache'    => true,
			'paged'                     => $paged,
			'post_parent'				=> $post->ID
		);

		$podcasts = new WP_Query( $args );

		if ( $podcasts->have_posts() ) : while ( $podcasts->have_posts() ) : $podcasts->the_post();

			$content = get_the_content();
			$pattern = get_shortcode_regex();

			if (   preg_match_all( '/'. $pattern .'/s', $content, $matches )
			       && array_key_exists( 2, $matches )
			       && in_array( 'audio', $matches[2] ) ) {

				// Return first audio shortcode (don't assume audio is the first one)
				foreach ( $matches[0] as $shortcode ){
					if ( preg_match( '#^\[audio#', $shortcode ) && empty( $html ) ){
						echo trim( do_shortcode( $shortcode ) );
						break;
					}
				}
			}

		endwhile;

		else : ?>

			<article id="post-not-found" class="hentry cf">
				<header class="article-header">
					<h1><?php _e( 'Oops, Post Not Found!', 'gmpodcasts' ); ?></h1>
				</header>
				<section class="entry-content">
					<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'gmpodcasts' ); ?></p>
				</section>
			</article>

		<?php endif;

		return $podcasts;

	}

	public static function get_podcast_episode() {
		$post = get_post();
		if ( ! is_a( $post, '\WP_Post' ) ) {
			return '';
		}

		if ( ! is_singular( \GMP_CPT::EPISODE_POST_TYPE ) ) {
			$audio_url = apply_filters( 'beasley-episode-audio-url', false, $post );
			if ( filter_var( $audio_url, FILTER_VALIDATE_URL ) ) {
				return do_shortcode( '[audio mp3="' . esc_url( $audio_url ) . '"][/audio]' );
			}
		}

		$content = $post->post_content;
		$pattern = get_shortcode_regex();

		$matches = array();
		if ( preg_match_all( '/'. $pattern .'/s', $content, $matches ) && array_key_exists( 2, $matches ) ) {
			$keys = array( 'embed', 'audio' );
			foreach ( $keys as $key ) {
				if ( in_array( $key, $matches[2] ) ) {
					$shortcode = $matches[0][ array_search( $key, $matches[2] ) ];

					remove_filter( 'the_content', 'wpautop' );
					$html = apply_filters( 'the_content', $shortcode );
					add_filter( 'the_content', 'wpautop' );

					return $html;
				}
			}
		}

		return '';
	}

	/**
	 * Helper function for the podcast file
	 */
	public static function podcast_audio_file() {

		global $post;

		$gmp_audio_url = get_post_meta( $post->ID, 'gmp_audio_file_meta_key', true );

		echo '<audio controls>';
		echo '<source src="' . esc_url( $gmp_audio_url ) . '" type="audio/mpeg">';
		echo '</audio>';

	}

	/**
	 * Generate an HTML5 audio player for the podcast
	 */
	public static function render_audio_player( $post_id ) {

		$audio_file = self::podcast_audio_file();

		echo '<audio controls>';
		echo '<source src="' . $audio_file . '" type="audio/mpeg">';
		echo '</audio>';

	}

	/**
	 * Enqueue scripts and styles
	 */
	public static function enqueue_scripts() {
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_style( 'gmpodcasts-css', GMPODCASTS_URL . "assets/css/gmp{$postfix}.css", array(), GMPODCASTS_VERSION );
	}

	public static function custom_pagination( $query ) {
		$current_page = $query->get( 'paged' );
		$total_pages = $query->max_num_pages;

		$args = array(
			'current' => $current_page,
			'total' => $total_pages,
			'prev_next' => false
		);
		$str = '<div class="pagination">';
		$str .= paginate_links( $args );
		$str .= '</div>';

		return $str;
	}

	public static function custom_pagination1( $pages = '', $range = 2 ) {
		$showitems = ($range * 2)+1;

		global $paged;

		if( empty( $paged ) ) {
			$paged = 1;
		}

		if($pages == '') {
			global $wp_query;

			$pages = $wp_query->max_num_pages;
			if( !$pages ) {
				$pages = 1;
			}
		}

		if( 1 != $pages ) {
			echo "<div class='pagination'>";

			if($paged > 2 && $paged > $range+1 && $showitems < $pages) {
				echo "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
			}

			if($paged > 1 && $showitems < $pages) {
				echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";
			}

			for ($i=1; $i <= $pages; $i++) {
				if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ) ) {
					if( $paged == $i ) {
						echo "<span class='current'>".$i."</span>";
					} else {
						echo "<a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
					}
				}
			}

			if ($paged < $pages && $showitems < $pages) {
				echo "<a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";
			}

			if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) {
				echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
			}

			echo "</div>\n";
		}
}

}

GMP_Player::init();

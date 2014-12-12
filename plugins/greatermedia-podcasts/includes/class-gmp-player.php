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

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'gmp_display_podcasts', array( __CLASS__, 'render_podcasts' ) );
		add_action( 'gmp_audio', array( __CLASS__, 'podcast_audio_file' ) );

	}

	/**
	 * A query to render a list of podcasts for the front end
	 */
	public static function render_podcasts() {

		global $post;

		$slug = get_post( $post )->post_name;

		$args = array(
			'post_type'                 => 'episode',
			'_podcast'                  => $slug,
			'cache_results'             => true,
			'update_post_meta_cache'    => true,
			'update_post_term_cache'    => true
		);

		$podcasts = new WP_Query( $args );

		if ( $podcasts->have_posts() ) : while ( $podcasts->have_posts() ) : $podcasts->the_post();

			$episode_attr = array(
				'link'              => get_the_permalink(),
				'title'             => get_the_title()
			);

			echo esc_html( $episode_attr['title'] );
			echo self::podcast_audio_file();
			echo esc_html( $slug );

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
		wp_register_script( 'gmpodcasts-js', GMPODCASTS_URL . "assets/js/gmp{$postfix}.js", array( 'jquery' ), GMPODCASTS_VERSION, true );

		wp_enqueue_script( 'gmpodcasts-js' );

		wp_enqueue_style( 'gmpodcasts-css', GMPODCASTS_URL . "assets/css/gmp{$postfix}.css", array(), GMPODCASTS_VERSION );

	}

}

GMP_Player::init();
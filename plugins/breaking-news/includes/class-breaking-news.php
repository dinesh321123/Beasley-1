<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}

if ( !class_exists( "Breaking_News" ) ) {
	class Breaking_News {

		public function __construct() {
			add_action( 'post_submitbox_misc_actions', array( $this, 'add_meta_checkbox' ) );
			add_action( 'save_post', array( $this, 'save_breaking_news_meta_option' ) );
			add_action( 'send_breaking_news_notices', array( $this, 'send_breaking_news_notices' ) );
			add_action( 'show_breaking_news_banner', array( $this, 'show_breaking_news_banner' ) );
			add_action( 'show_latest_breaking_news_item', array( $this, 'show_breaking_news_banner' ) );
		}

		/**
		 * Add meta meta fields to the post edit page.
		 *
		 * @return void
		 */
		public static function add_meta_checkbox() {
			global $post;

			if ( 'post' !== get_post_type() ) {
				return;
			}

			wp_nonce_field( 'save_breaking_news_meta', 'breaking_news_nonce' );

			$is_breaking_news = self::sanitize_boolean( get_post_meta( $post->ID, '_is_breaking_news', true ) );
			?>
			<div id="breaking-news-meta-fields">
				<div id="breaking-news-meta" class="misc-pub-section">
					<input type="checkbox" name="breaking_news_option" id="breaking_news_option" <?php checked( $is_breaking_news ); ?> /> <label for="breaking_news_option"><?php _e( 'Show Breaking News alert', 'breaking_news' ); ?></label>
				</div>
			</div>

			<?php
		}

		/**
		 * Save the post meta.
		 *
		 * @param  int $post_id
		 * @return void
		 */
		public function save_breaking_news_meta_option( $post_id ) {

			// Defaults
			$is_breaking_news = 0;

			if ( ! isset( $_POST['breaking_news_nonce'] ) || ! wp_verify_nonce( $_POST['breaking_news_nonce' ], 'save_breaking_news_meta' ) ) {
				return;
			}

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			if ( isset( $_POST['breaking_news_option'] ) ) {
				$is_breaking_news = $this->sanitize_boolean( $_POST['breaking_news_option'] );
			}

			update_post_meta( $post_id, '_is_breaking_news', $is_breaking_news );

			// Send notices
			if ( 1 === $is_breaking_news ) {
				// do_action( 'send_breaking_news_notices' );
			}
		}

		/**
		 * Show a site-wide breaking news banner.
		 *
		 * @return void
		 */
		public function show_breaking_news_banner() {
			global $post;
			$breaking_post = $this->get_latest_breaking_news_item();
			
			// Bail if no post.
			if ( ! $breaking_post ) {
				return; 
			}

			$post = $breaking_post;
			setup_postdata( $post );
			
			?>
				<a href="<?php the_permalink(); ?>">
					<div id="breaking-news-banner" class="breaking-news-banner">
						<div class='breaking-news-banner__inner'>
							<span class="breaking-news-banner__title"><?php the_title(); ?>:</span> 
							<span class="breaking-news-banner__excerpt"><?php echo wp_kses_post( $this->get_post_excerpt( $post, 25 ) ); ?></span>
						</div>
					</div>
				</a>
			<?php
			
			wp_reset_postdata();
		}

		/**
		 * Get the excerpt for a breaking news post.
		 *
		 * @param  WP_Post $post
		 * @param  int $num_words
		 * @return string
		 */
		function get_post_excerpt( $post = null, $num_words = 50 ) {
			$excerpt = '';

			if ( ! empty( $post ) ) {
				// Get the custom excerpt field if not empty
				if ( !empty( $post->post_excerpt ) ) {
					$excerpt = get_the_excerpt();

					// Trim at a reasonable number of words.
					$excerpt = wp_trim_words( $excerpt, 100 );
				} else {
					$content = get_the_content();
					$content = strip_shortcodes( $content );

					$excerpt = wp_trim_words( $content, $num_words );
				}
			}

			return $excerpt;
		}
		/**
		 * Get latest breaking news item.
		 *
		 * @return WP_Post|null
		 */
		public static function get_latest_breaking_news_item() {
			$args = array(
				'post_type' => 'post',
				'posts_per_page' => 1,
				'order' => 'DESC',
				'orderby' => 'post_date',
				'meta_query' => array(
					array(
						'key'     => '_is_breaking_news',
						'value'   => 1,
						'compare' => '=',
					),
				),
			);

			$posts = get_posts( $args );

			if ( ! empty( $posts ) ) {
				return $posts[0];
			}

			return null;
		}

		/**
		 * Send notifications via email, sms, etc.
		 *
		 * @return void
		 */
		public function send_breaking_news_notices() {
			// Nothing yet
		}

		/**
		 * Sanitize a boolean option.
		 *
		 * @param  int|string $input
		 * @return int
		 */
		public static function sanitize_boolean( $input ) {
			$new_input = 0;
			if( $input === 'on' ) {
				$input = 1; // account for browsers POSTing input values as "on"
			}
			$input = absint( intval( $input ) );

			if ( in_array( $input, array( 0, 1 ) ) ) {
				$new_input = $input;
			}

			return $new_input;
		}

		public static function init() {
			static $instance = null;
			if ( is_null( $instance ) ) {
				$instance = new \Breaking_News();
			}
		}
	}

	Breaking_News::init();
}

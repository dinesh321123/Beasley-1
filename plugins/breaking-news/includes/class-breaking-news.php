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
	    	add_action( 'show_latest_breaking_news_item', array( $this, 'show_latest_breaking_news_item' ) );
	    	add_action( 'wp_enqueue_scripts', array( $this, 'breaking_news_enqueue_scripts' ) );
	    	add_action( 'admin_enqueue_scripts', array( $this, 'breaking_news_admin_enqueue_scripts' ) );
	    }

	    /**
	     * Enqueue supporing admin scripts.
	     *
	     * @return void
	     */
	    public function breaking_news_admin_enqueue_scripts() {
	    	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	    	if ( 'post' === get_post_type() ) {
				wp_enqueue_script( 'breaking-news-admin-js', BREAKING_NEWS_URL . "assets/js/breaking-news{$postfix}.js", array( 'jquery'), BREAKING_NEWS_VERSION, true );
			}
		}

	    /**
	     * Enqueue supporing front-end scripts.
	     *
	     * @return void
	     */
	    public function breaking_news_enqueue_scripts() {
	    	$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';

	    	wp_enqueue_style( 'breaking-news',
				BREAKING_NEWS_URL . "assets/css/breaking-news{$postfix}.css",
				array(), BREAKING_NEWS_VERSION, 'all' );
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
			$show_site_wide_notification = self::sanitize_boolean( get_post_meta( $post->ID, '_show_in_site_wide_notification', true ) );
			?>

			<style>
				#post-body #site-wide-notification-meta {
					padding-left: 35px;
				}
			</style>

			<div id="breaking-news-meta-fields">
				<div id="breaking-news-meta" class="misc-pub-section">
					<input type="checkbox" name="breaking_news_option" id="breaking_news_option" value="1" <?php checked( 1, $is_breaking_news ); ?> /> <label for="breaking_news_option"><?php _e( 'Breaking News', 'breaking_news' ); ?></label>
				</div>

				<div id="site-wide-notification-meta" class="misc-pub-section">
					<input type="checkbox" name="site_wide_notification_option" id="site_wide_notification_option" value="1" <?php checked( 1, $show_site_wide_notification ); ?> /> <label for="site_wide_notification_option"><?php _e( 'Site-wide Notification', 'breaking_news' ); ?></label>
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
			global $post;

			// Defaults
			$is_breaking_news = 0;
			$show_site_wide_notification = 0;

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

			if ( isset( $_POST['site_wide_notification_option'] ) ) {
				$show_site_wide_notification = $this->sanitize_boolean( $_POST['site_wide_notification_option'] );
			}

			// If the post isn't breaking news, don't enable the site-wide notification either.
			if ( 0 === $is_breaking_news ) {
				$show_site_wide_notification = 0;
			}

			update_post_meta( $post_id, '_is_breaking_news', $is_breaking_news );
			update_post_meta( $post_id, '_show_in_site_wide_notification', $show_site_wide_notification );

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
			$post = $this->get_latest_breaking_news_item();

			if ( ! empty( $post ) ) {
				$show_banner = self::sanitize_boolean( get_post_meta( $post->ID, '_show_in_site_wide_notification', true ) );

				if ( 1 === $show_banner ) {
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
			}
		}

		/**
		 * Show latest breaking news item on homepage or somewhere else. To override, add a function called breaking_news_get_latest_item() to override.
		 *
		 * @return void
		 */
		public function show_latest_breaking_news_item() {
			global $post;

			if ( function_exists( 'breaking_news_get_latest_item' ) ) {
				breaking_news_get_latest_item();
			} else {
				$post = $this->get_latest_breaking_news_item();

				if ( ! empty( $post ) ) {
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
			}
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
	        $input = absint( intval( $input ) );

	        if ( in_array( $input, array( 0, 1 ) ) ) {
	            $new_input = $input;
	        }

	        return $new_input;
	    }

	    public static function init() {
	        if ( ! isset( $this ) || null === $this ) {
	            new self;
	        }
	    }
	}

	Breaking_News::init();
}

<?php

Class SyndicationDashboardWidget {

	public function __construct()
	{
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
	}

	/**
	 * Add dashboard widget
	 */
	public function add_dashboard_widgets()
	{
		wp_add_dashboard_widget(
			'syndication-dashboard-widget',
			'Latest Syndicated Posts',
			array( $this, 'RenderDashboardWidget' )
		);
	}


	/**
	 * Render dashboard widget
	 */
	public function RenderDashboardWidget()
	{
		$count = 0;
		$posts = $this->get_syndicated_posts( 'syndication_imported_posts' );

		$posts = explode( ',', $posts );
		$last_performed = intval( get_option( 'syndication_last_performed' ) );

		echo "<h4>Syndication is last performed on: " . esc_html( date( 'l jS \of F Y h:i:s A', $last_performed ) ) . '</h4>';
		echo '<div id="syndicated_posts" class="syndicated_posts">';
		echo '<ul class="syndicated_posts">';
		if( !empty( $posts ) ) {
			foreach ( $posts as $post_id ) {
				$post = get_post( $post_id );
				if( !is_null( $post) ) {
					echo '<li>';
					$post_link = '<a class="syndicated_post_title" title="';
					$post_link .= esc_attr( $post->post_title ) . '" href="' . esc_attr( get_edit_post_link( $post_id ) ) . '">';
					$post_link .= esc_html( $post->post_title );
					$post_link .= '</a>';
					echo $post_link;

					if( $count < 2 ) {
						echo '<div class="syndicated_post_excerpt">';
						echo esc_html( $this->custom_excerpt( $post ) );
						echo '</div>';
					}
					echo '</li>';
				}
				$count++;
			}
		} else {
			echo "There are no posts to show right now, please check later!";
		}
		echo '</ul>';
		echo '</div>';
		if( $count > 3) {
			echo '<div>';
			echo '<a id="show_all_syndicated_posts" href="#">See all</a>';
			echo '</div>';
		}
	}

	/**
	 * Get syndicated post IDs from transinet cache or otpions
	 *
	 * @param $name Option name
	 *
	 * @return mixed|string|void
	 */
	public function get_syndicated_posts( $name ) {
		$name = sanitize_text_field( $name );

		$options = get_transient( $name );
		if( !$options ) {
			$options = get_option( $name );
		}

		if ( strlen( $options ) == 0 ) {
			$options = '';
		}

		return $options;
	}

	/**
	 * Get post excerpt from the WP_Post object or generate from the content
	 *
	 * @param     $post
	 * @param int $length
	 *
	 * @return string
	 */
	public function custom_excerpt( $post, $length = 55 ) {
		if( $post->post_excerpt ) {
			$content = get_the_excerpt();
		} else {
			$content = $post->post_content;
			$content = wp_trim_words( $content , $length );
		}
		return $content;
	}
}

$wdw = new SyndicationDashboardWidget();
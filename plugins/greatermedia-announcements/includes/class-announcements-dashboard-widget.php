<?php
/**
 * Created by Eduard
 * Date: 10.12.2014 0:49
 */

class AnnouncementsDashboardWidget {

	private $content_site_id;

	private $taxonomies = array( 'collection' );

	public function __construct()
	{
		if( defined( 'GMR_CONTENT_SITE_ID' ) ) {
			$this->content_site_id = GMR_CONTENT_SITE_ID;
		} elseif ( is_multisite() ) {
			$this->content_site_id = get_current_site()->blog_id;
			add_action( 'admin_notices', array( $this, 'add_notice_for_undefined' ) );
		} else {
			$this->content_site_id = get_current_blog_id();
		}
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
	}

	public function add_notice_for_undefined() {
		?>
		<div class="error">
			<p>
				No Content Factory site ID is defined!
				Using default ID <?php echo $this->content_site_id; ?>!
				Please define GMR_CONTENT_SITE_ID in config
			</p>

		</div>
		<?php
	}

	public function GetCollections() {
		global $switched;

		$terms = array();
		$data = array();

		switch_to_blog( $this->content_site_id );

		foreach ( $this->taxonomies as $taxonomy ) {
			if( taxonomy_exists( $taxonomy ) ) {

				$args = array(
					'get'        => 'all',
					'hide_empty' => false
				);

				$terms[ $taxonomy ] = get_terms( $taxonomy, $args );

				foreach ( $terms[$taxonomy] as $single_term ) {

					$args = array(
						'post_type'     => 'announcement',
						'post_status'   => 'publish',
						'numberposts'   =>  5,
						'tax_query'     => array(
							array(
								'taxonomy' => $taxonomy,
								'field'    => 'slug',
								'terms'    => array( $single_term->slug ),
							),
						),
					);

					$posts =  get_posts( $args );

					$data[ $single_term->slug ][ 'posts' ]      =   $posts;
					$data[ $single_term->slug ][ 'term_obj' ]   =   $single_term;

				}
			}
		}

		restore_current_blog();

		return $data;
	}

	/**
	 * Add dashboard widget
	 */
	public function add_dashboard_widgets()
	{
		// get collections from the conent site
		$data  = $this->GetCollections();

		// add dashboard widget for each content
		foreach ( $data as $single_data ) {
			if( !empty( $single_data['posts'] ) ) {
				wp_add_dashboard_widget(
					$single_data['term_obj']->slug,
					$single_data['term_obj']->name,
					array( $this, 'RenderDashboardWidget' ),
					null,
					$single_data['posts']
				);
			}
		}

	}


	/**
	 * Render dashboard widget
	 */
	public function RenderDashboardWidget( $post, $callback_args)
	{
		$data = $callback_args['args'];

		echo '<div class="rss-widget"><ul>';
		foreach ( $data as $single_post ) {
			$date = date( 'F j, Y', strtotime( $single_post->post_date ) );
			$link = strip_tags( $single_post->guid );
			$title = $single_post->post_title;
			$content = $single_post->post_content;
			echo '<li>' . esc_html( $title ) . ' - '
			     .'<span class="rss-date">' . esc_html( $date ) .'</span>'
			     .'<div class="rssSummary">'
			     . wp_kses_post( $content )
			     . '</div></li>';
		}

		echo '</ul></div>';
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


$AnnouncementsDashboardWidget = new AnnouncementsDashboardWidget();

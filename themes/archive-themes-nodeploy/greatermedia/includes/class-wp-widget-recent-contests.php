<?php
/**
 * Widget API: WP_Widget_Recent_Contests class
 *
 */

/**
 * Core class used to implement a Recent Contests widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class WP_Widget_Recent_Contests extends WP_Widget {

	/**
	 * Sets up a new Recent Contests widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget-recent-contests',
			'description'                 => 'Your site&#8217;s most recent Contests.',
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'recent-contests', 'Recent Contests', $widget_ops );
		$this->alt_option_name = 'widget_recent_contests';
	}

	/**
	 * Outputs the content for the current Recent Contests widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Recent Contests widget instance.
	 */
	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$title = ! empty( $instance['title'] ) ? $instance['title'] : 'Recent Contests';
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}

		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

		/**
		 * Filters the arguments for the Recent Contests widget.
		 *
		 * @since 3.4.0
		 *
		 * @see WP_Query::get_posts()
		 *
		 * @param array $args An array of arguments used to retrieve the recent posts.
		 */
		$now = time();
		$query_meta_params = array(
			'relation' => 'AND',
			array(
				'relation' => 'OR',
				/* This is a contest with an valid end timestamp */
				array(
					'key'     => 'contest-end',
					'type'    => 'NUMERIC',
					'value'   => $now,
					'compare' => '>',
				),
				/* any other post/type which matches the search query */
				array(
					'key'     => 'contest-end',
					'type'    => 'NUMERIC',
					'value'   => '',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'   => 'contest-end',
					'type'  => 'NUMERIC',
					'value' => 0,
				),
			),
			array(
				'relation' => 'OR',
				array(
					'key'     => 'secret',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => 'secret',
					'type'    => 'NUMERIC',
					'value'   => 1,
					'compare' => '!=',
				),
			),
		);

		$r = new WP_Query( apply_filters( 'widget_posts_args', array(
			'post_type'           => 'contest',
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'meta_query'          => $query_meta_params
		) ) );

		if ( $r->have_posts() ) :
			echo $args['before_widget'];

			if ( $title ) :
				echo $args['before_title'] . $title . $args['after_title'];
			endif;

			?><ul><?php
				while ( $r->have_posts() ) :
					$r->the_post();

					$post_id = get_the_ID();
					$thumb_id = get_post_thumbnail_id( $post_id );

					?><li>
						<a href="<?php the_permalink(); ?>">
							<div class="widget-recent-contests__meta">
								<?php if ( $thumb_id ) : ?>
									<img src="<?php echo esc_attr( bbgi_get_image_url( $thumb_id, 45, 45 ) ); ?>" alt="<?php the_title_attribute(); ?>" />
								<?php endif; ?>

								<span><?php the_title(); ?></span>
							</div>
						</a>
					</li><?php
				endwhile;
			?></ul>

			<div class="more-contests">
				<a class="more-contests-btn" href="<?php echo esc_url( get_post_type_archive_link( 'contest' ) ); ?>">
					More Contests
				</a>
			</div><?php

			echo $args['after_widget'];

			// Reset the global $the_post as this query will have stomped on it
			wp_reset_postdata();
		endif;
	}

	/**
	 * Handles updating the settings for the current Recent Contests widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
		return $instance;
	}

	/**
	 * Outputs the settings form for the Recent Contests widget.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
		<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox"<?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label></p>
<?php
	}
}

/**
 * Registers the Recent Contests widget.
 *
 * @action widgets_init
 */
function register_recent_contests_widget() {
	register_widget( 'WP_Widget_Recent_Contests' );
}

add_action( 'widgets_init', 'register_recent_contests_widget' );

<?php
/**
 * Widget API: WP_Widget_Triton_Song_History class
 *
 */

/**
 * Core class used to implement a Text widget.
 *
 * @since 4.6.2
 *
 * @see WP_Widget
 */
class WP_Widget_Triton_Song_History extends WP_Widget {

	/**
	 * Sets up a new Triton Song History widget instance.
	 *
	 * @since 4.6.2
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_triton_song_history',
			'description' => __( 'Triton Song History.' ),
			'customize_selective_refresh' => true,
		);
		$control_ops = array( 'width' => 400, 'height' => 350 );
		parent::__construct( 'triton_song_history', __( 'Triton Song History' ), $widget_ops, $control_ops );
	}

	/**
	 * Outputs the content for the current Triton Song History widget instance.
	 *
	 * @since 4.6.2
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Text widget instance.
	 */
	public function widget( $args, $instance ) {

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		$station = ! empty( $instance['station'] ) ? $instance['station'] : '';
		$count = ! empty( $instance['count'] ) ? $instance['count'] : '5';

		echo $args['before_widget'];
		?>
			<div class="triton-song-history">
				<td-songhistory
          id="td-songhistory"
          station="<?php echo esc_attr( $station ) ?>"
          songsdisplayed="<?php echo absint( $count ) ?>"
          title= "<?php echo esc_attr( $title ) ?>"
          >
			  </td-songhistory>
				<script src="//widgets.listenlive.co/1.0/tdwidgets.min.js"></script>
			</div>
		<?php
		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current Triton Song History widget instance.
	 *
	 * @since 4.6.2
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['station'] = sanitize_text_field( $new_instance['station'] );
		$instance['count'] = ( ! filter_var( $new_instance['count'], FILTER_VALIDATE_INT ) === false ) ? sanitize_text_field( $new_instance['count'] ) : '5';

		return $instance;
	}

	/**
	 * Outputs the Triton Song History widget settings form.
	 *
	 * @since 4.6.2
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'station' => '', 'count' => '5' ) );
		$title = sanitize_text_field( $instance['title'] );
		$station = sanitize_text_field( $instance['station'] );
		$count = sanitize_text_field( $instance['count'] );

		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'station' ); ?>"><?php _e( 'Station:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('station'); ?>" name="<?php echo $this->get_field_name('station'); ?>" type="text" value="<?php echo esc_attr($station); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Song Count:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo esc_attr($count); ?>" /></p>

		<?php
	}
}

/**
 * Registers the Triton Song History widget.
 *
 * @action widgets_init
 */
function register_triton_song_history_widget() {
	register_widget( 'WP_Widget_Triton_Song_History' );
}

add_action( 'widgets_init', 'register_triton_song_history_widget' );

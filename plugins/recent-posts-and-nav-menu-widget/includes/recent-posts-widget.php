<?php
class RPMW_Widget_Recent_Posts extends WP_Widget {
	var $defaults;
	var $customs;
	var $ints;

	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'recent-posts-and-nav-menu-widget recent-posts-widget-with-thumbnails',
			'description'                 => __( 'Your site most recent posts with thumbnails.' ),
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		);
		$this->defaults[ 'plugin_slug' ]		= 'recent-posts-and-nav-menu-widget';
		$this->defaults[ 'thumb_alt' ]			= '';
		$this->defaults[ 'category_ids' ]		= array( 0 );
		$this->defaults[ 'thumb_dimensions' ]	= 'large';
		parent::__construct( $this->defaults[ 'plugin_slug' ], __( 'Recent Posts Based On Category' ), $widget_ops );
		$this->alt_option_name = 'recent-posts-and-nav-menu-widget';
		add_action( 'save_post',				array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post',				array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme',				array( $this, 'flush_widget_cache' ) );
	}
	function flush_widget_cache() {
		wp_cache_delete( $this->defaults[ 'plugin_slug' ], 'widget' );
	}

	/**
	 * Mega menu - Returns array of post type for recent post widgets.
	 */
	/* public function allow_megamenu_recent_posts_posttype() {
		return (array) apply_filters( 'allow-megamenu-recent-posts-for-posttypes', array( 'post', 'gmr_gallery', 'listicle_cpt', 'affiliate_marketing' )  );
	} */

	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}
		$number			= isset( $instance['number'] ) ? absint( $instance['number'] ) : 4;
		$default_title	= __( 'Recent Posts' );
		$title			= ( ! empty( $instance['title'] ) ) ? $instance['title'] : $default_title;
		$title			= apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$show_thumb		= isset( $instance['show_thumb'] ) ? (bool) $instance['show_thumb'] : false;
		$thumbnail_size	= isset( $instance['show_thumb'] ) ? (bool) $instance['show_thumb'] : false;

		$category_ids	= ( ! empty( $instance[ 'category_ids' ] ) ) ? array_map('absint', $instance[ 'category_ids' ] ) : array(0);
		if ( in_array( 0, $category_ids ) ) {
			$category_ids = 0;
		}
		/* $query_args		= array('post_type' => $this->allow_megamenu_recent_posts_posttype(), 'posts_per_page' => $number, 'no_found_rows' => true,'post_status' => 'publish', 'ignore_sticky_posts' => true);
		if ($category_ids != 0 && !in_array( 0, $category_ids ) ) {
			$query_args[ 'category__in' ] = $category_ids;
		} */
		/* $thumb_dimensions = ( ! empty( $instance[ 'thumb_dimensions' ] ) ) ? $instance[ 'thumb_dimensions' ] : $this->defaults[ 'thumb_dimensions' ];
		list( $ints[ 'thumb_width' ], $ints[ 'thumb_height' ] ) = $this->get_image_dimensions( $thumb_dimensions );
		$this->customs[ 'thumb_dimensions' ] = $thumb_dimensions;
		$r = new WP_Query($query_args); */

		// if ($r->have_posts()) {
			echo '<div id="rpwwt-recent-posts-widget-with-thumbnails-3" class="rpwwt-widget rpwwt-widget-'.$args['widget_id'].'">';
			echo $args['before_widget'];

			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			/* $format = current_theme_supports( 'html5', 'navigation-widgets' ) ? 'html5' : 'xhtml';
			$format = apply_filters( 'navigation_widgets_format', $format );
			if ( 'html5' === $format ) {
				$title      = trim( strip_tags( $title ) );
				$aria_label = $title ? $title : $default_title;
				echo '<nav aria-label="' . esc_attr( $aria_label ) . '">';
			} */

			echo sprintf(
					'<div class="megamenu-recent-posts-endpoint"
						  data-postsperpage="%s"
						  data-categories="%s"
						  data-showthumb="%s"
						  data-showthumbsize="%s"
						  data-menuareaid="%s"></div>',
					$number,
					implode( ',', $category_ids ),
					$show_thumb,
					$thumbnail_size,
					$args['widget_id']
			);

			/* echo '<ul>';
			while ( $r->have_posts() )
			{
				$r->the_post();
				$post_title   = get_the_title();
				$title        = ( ! empty( $post_title ) ) ? $post_title : __( '(no title)' );
				$aria_current = '';
				if ( get_queried_object_id() === get_the_ID() ) {
					$aria_current = ' aria-current="page"';
				} ?>
				<li>
					<a href="<?php the_permalink(); ?>"<?php echo $aria_current; ?>>
						<?php
						if ( $show_thumb ){
							the_post_thumbnail($this->customs[ 'thumb_dimensions' ]);
						}?>
						<span class="rpwwt-post-title"><?php echo $title; ?></span>
					</a>
				</li><?php
			}
			echo '</ul>';
			if ( 'html5' === $format ) {
				echo '</nav>';
			} */
			echo $args['after_widget'];
			echo '</div>';

		// } close if
	}

	/**
	 * Handles updating the settings for the current Recent Posts widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['title']     = sanitize_text_field( $new_instance['title'] );
		$instance['number']    = (int) $new_instance['number'];
		$instance['show_thumb'] = isset( $new_instance['show_thumb'] ) ? (bool) $new_instance['show_thumb'] : false;
		$instance[ 'thumb_dimensions' ] = strip_tags( $new_instance[ 'thumb_dimensions' ] );
		$instance[ 'category_ids' ]  = array_map( 'absint', $new_instance[ 'category_ids' ] );
		return $instance;
	}

	/**
	 * Outputs the settings form for the Recent Posts widget.
	 *
	 * @since 2.8.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 4;
		$show_thumb = isset( $instance['show_thumb'] ) ? (bool) $instance['show_thumb'] : false;
		$thumb_dimensions = isset( $instance[ 'thumb_dimensions' ] ) ? $instance[ 'thumb_dimensions' ] : $this->defaults[ 'thumb_dimensions' ];
		// compute ids only once to improve performance
		$field_ids = array();
		$field_ids[ 'category_ids' ]	= $this->get_field_id( 'category_ids' );
		$field_ids[ 'thumb_alt' ]		= $this->get_field_id( 'thumb_alt' );
		$field_ids[ 'thumb_dimensions' ]= $this->get_field_id( 'thumb_dimensions' );
		$field_ids[ 'title' ]			= $this->get_field_id( 'title' );

		// get texts and values for image sizes dropdown
		global $_wp_additional_image_sizes;
		$wp_standard_image_size_labels = array();
		$label = 'Full Size';	$wp_standard_image_size_labels[ 'full' ]		= __( $label );
		$label = 'Large';		$wp_standard_image_size_labels[ 'large' ]		= __( $label );
		$label = 'Medium';		$wp_standard_image_size_labels[ 'medium' ]		= __( $label );
		$label = 'Thumbnail';	$wp_standard_image_size_labels[ 'thumbnail' ]	= __( $label );
		$media_trail = ( current_user_can( 'manage_options' ) ) ? sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( admin_url( 'options-media.php' ) ), esc_html( $label ) ) : sprintf( '<em>%s</em>', esc_html( $label ) );
		$wp_standard_image_size_names = array_keys( $wp_standard_image_size_labels );
		$size_options = array();
		foreach ( get_intermediate_image_sizes() as $size_name ) {
			// Don't take numeric sizes that appear
			if( is_integer( $size_name ) ) {
				continue;
			}
			$option_values = array();
			// Set technical name
			$option_values[ 'size_name' ] = $size_name;
			// Set name
			$option_values[ 'name' ] = in_array( $size_name, $wp_standard_image_size_names ) ? $wp_standard_image_size_labels[$size_name] : $size_name;
			// Set width
            $option_values[ 'width' ] = isset( $_wp_additional_image_sizes[$size_name]['width'] ) ? $_wp_additional_image_sizes[$size_name]['width'] : get_option( "{$size_name}_size_w" );
            // Set height
            $option_values[ 'height' ] = isset( $_wp_additional_image_sizes[$size_name]['height'] ) ? $_wp_additional_image_sizes[$size_name]['height'] : get_option( "{$size_name}_size_h" );
			// add option to options list
			$size_options[] = $option_values;
		}
		$category_ids = ( isset( $instance[ 'category_ids' ] ) ) ? $instance[ 'category_ids' ] : $this->defaults[ 'category_ids' ];
		$all_text = __( 'All Categories' );
		$label_all_cats = $all_text;
		// get categories
		$categories = get_categories( array( 'hide_empty' => 0, 'hierarchical' => 1 ) );
		$number_of_cats = count( $categories );

		// get size (number of rows to display) of selection box: not more than 10
		$number_of_rows = ( 10 > $number_of_cats ) ? $number_of_cats + 1 : 10;

		// start selection box
		$selection_element = sprintf(
			'<select name="%s[]" id="%s" class="rpwwt-cat-select" multiple size="%d">',
			$this->get_field_name( 'category_ids' ),
			$field_ids[ 'category_ids' ],
			$number_of_rows
		);
		$selection_element .= "\n";

		// make selection box entries
		$cat_list = array();
		if ( 0 < $number_of_cats ) {

			// make a hierarchical list of categories
			while ( $categories ) {
				// go on with the first element in the categories list:
				// if there is no parent
				if ( '0' == $categories[ 0 ]->parent ) {
					// get and remove it from the categories list
					$current_entry = array_shift( $categories );
					// append the current entry to the new list
					$cat_list[] = array(
						'id'	=> absint( $current_entry->term_id ),
						'name'	=> esc_html( $current_entry->name ),
						'depth'	=> 0
					);
					// go on looping
					continue;
				}
				// if there is a parent:
				// try to find parent in new list and get its array index
				$parent_index = $this->get_cat_parent_index( $cat_list, $categories[ 0 ]->parent );
				// if parent is not yet in the new list: try to find the parent later in the loop
				if ( false === $parent_index ) {
					// get and remove current entry from the categories list
					$current_entry = array_shift( $categories );
					// append it at the end of the categories list
					$categories[] = $current_entry;
					// go on looping
					continue;
				}
				$depth = $cat_list[ $parent_index ][ 'depth' ] + 1;
				$new_index = $parent_index + 1;
				foreach( $cat_list as $entry ) {
					if ( $depth <= $entry[ 'depth' ] ) {
						$new_index = $new_index + 1;
						continue;
					}
					$current_entry = array_shift( $categories );
					$end_array = array_splice( $cat_list, $new_index );
					$cat_list[] = array(
						'id'	=> absint( $current_entry->term_id ),
						'name'	=> esc_html( $current_entry->name ),
						'depth'	=> $depth
					);
					$cat_list = array_merge( $cat_list, $end_array );
					break;
				}
			}

			$selected = ( in_array( 0, $category_ids ) ) ? ' selected="selected"' : '';
			$selection_element .= "\t";
			$selection_element .= '<option value="0"' . $selected . '>' . $label_all_cats . '</option>';
			$selection_element .= "\n";

			foreach ( $cat_list as $category ) {
				$cat_name = apply_filters( 'rpwwt_list_cats', $category[ 'name' ], $category );
				$pad = ( 0 < $category[ 'depth' ] ) ? str_repeat('&ndash;&nbsp;', $category[ 'depth' ] ) : '';
				$selection_element .= "\t";
				$selection_element .= '<option value="' . $category[ 'id' ] . '"';
				$selection_element .= ( in_array( $category[ 'id' ], $category_ids ) ) ? ' selected="selected"' : '';
				$selection_element .= '>' . $pad . $cat_name . '</option>';
				$selection_element .= "\n";
			}

		}
		$selection_element .= "</select>\n";

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
			<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
		</p>

		<h4><?php esc_html_e( 'Filter by category' ); ?></h4>

		<p>
			<label for="<?php echo $field_ids[ 'category_ids' ];?>">
				<?php esc_html_e( 'Show posts of selected categories only?', RPMW_TEXT_DOMAIN ); ?>
			</label>

			<?php echo $selection_element; ?>
			<em>
				<?php printf( esc_html__( 'Click on the categories with pressed CTRL key to select multiple categories. If &#8220;%s&#8221; was selected then other selections will be ignored.', RPMW_TEXT_DOMAIN ), $label_all_cats ); ?>
			</em>
		</p>
		<h4><?php esc_html_e( 'Thumbnail Settings' ); ?></h4>
		<p><input class="checkbox" type="checkbox" <?php checked( $show_thumb ); ?> id="<?php echo $field_ids[ 'show_thumb' ]; ?>" name="<?php echo $this->get_field_name( 'show_thumb' ); ?>" />
		<label for="<?php echo $field_ids[ 'show_thumb' ]; ?>"><?php esc_html_e( 'Show thumbnail?', RPMW_TEXT_DOMAIN ); ?></label><br>
		<em><?php esc_html_e( 'By default, the featured image of the post is used as long as the next checkboxes do not specify anything different.', RPMW_TEXT_DOMAIN ); ?></em></p>

		<p><label for="<?php echo $field_ids[ 'thumb_dimensions' ]; ?>"><?php esc_html_e( 'Size of thumbnail', RPMW_TEXT_DOMAIN ); ?>:</label>
			<select id="<?php echo $field_ids[ 'thumb_dimensions' ]; ?>" name="<?php echo $this->get_field_name( 'thumb_dimensions' ); ?>">
				<option value=""><?php esc_html_e( 'Select Size of thumbnail', RPMW_TEXT_DOMAIN ); ?></option>
				<?php $thumbsizearray = array( "thumbnail", "medium", "large", "full" );
				foreach ( $thumbsizearray as $thumbsize ) { ?>
					<option value="<?php echo esc_attr( $thumbsize ); ?>"<?php selected( $thumb_dimensions, $thumbsize ); ?>> <?php echo esc_html( $thumbsize ); ?> </option>
				<?php } ?>
				<?php /* foreach ( $size_options as $option ) { ?>
						<?php if( in_array( $option[ 'size_name' ], array( "thumbnail", "medium", "large", "full" ) ) ) ?>
					<option value="<?php echo esc_attr( $option[ 'size_name' ] ); ?>"<?php selected( $thumb_dimensions, $option[ 'size_name' ] ); ?>><?php echo esc_html( $option[ 'name' ] ); ?> (<?php echo absint( $option[ 'width' ] ); ?> &times; <?php echo absint( $option[ 'height' ] ); ?>)</option>
				<?php }	*/ ?>
			</select>
			<em><?php printf( esc_html__( 'If you use a specified size the following sizes will be taken, otherwise they will be ignored and the selected dimension as stored in %s will be used:', RPMW_TEXT_DOMAIN ), $media_trail ); ?></em>
		</p><?php
	}

	private function get_image_dimensions ( $size = 'thumbnail' ) {

		$width  = 0;
		$height = 0;
		// check if selected size is in registered images sizes
		if ( in_array( $size, get_intermediate_image_sizes() ) ) {
			// if in WordPress standard image sizes
			if ( in_array( $size, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$width  = get_option( $size . '_size_w' );
				$height = get_option( $size . '_size_h' );
			} else {
				// custom image sizes, formerly added via add_image_size()
				global $_wp_additional_image_sizes;
				$width  = $_wp_additional_image_sizes[ $size ][ 'width' ];
				$height = $_wp_additional_image_sizes[ $size ][ 'height' ];
			}
		}
		if ( ! $width )  $width  = absint( round( get_option( 'thumbnail_size_h', 110 ) / 2 ) );
		if ( ! $height ) $height = absint( round( get_option( 'thumbnail_size_w', 110 ) / 2 ) );
		return array( $width, $height );
	}

	private function get_cat_parent_index( $arr, $id ) {
		$len = count( $arr );
		if ( 0 == $len ) {
			return false;
		}
		$id = absint( $id );
		for ( $i = 0; $i < $len; $i++ ) {
			if ( $id == $arr[ $i ][ 'id' ] ) {
				return $i;
			}
		}
		return false;
	}
}
function RPMW_register_recent_posts_widget_with_thumbnails () {
	register_widget( 'RPMW_Widget_Recent_Posts' );
}
add_action( 'widgets_init', 'RPMW_register_recent_posts_widget_with_thumbnails', 1 );

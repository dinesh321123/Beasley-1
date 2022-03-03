<?php

class RPMW_Nav_Menu_Widget extends WP_Widget {

	/**
	 * Sets up a new Navigation Menu widget instance.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$widget_ops = array(
			'description'                 => __( 'Add a navigation menu to your sidebar.' ),
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		);
		parent::__construct( 'rpmw_nav_menu', __( 'Navigation Menu Custom Widget' ), $widget_ops );
	}
	function get_custom_menu( $args ) {
		$menu = wp_get_nav_menu_object( $args['menu'] );
	    $menu_items = wp_get_nav_menu_items($menu->term_id);
	    $menu_list  = '<nav>';
	    $menu_list .= '<ul class="main-nav">';
	    $count = 0;
	    $menu_item_count = 0;
	    $submenu = false;
	 	foreach( $menu_items as $menu_item )
	 	{
	        $link = $menu_item->url;
	        $title = $menu_item->title;
	        if ( !$menu_item->menu_item_parent )
	        {
	        	$parent_id = $menu_item->ID;
	            $menu_list .= '<li class="item">';
	            $menu_list .= '<a href="'.$link.'" class="title">'.$title.'</a>';
	        }
	        if( $parent_id == $menu_item->menu_item_parent )
	        {
	            if ( !$submenu ) {
	                $submenu = true;
	                $menu_list .= '<ul class="sub-menu">';
	            }
	            $menu_list .= '<li class="item">';
	            $menu_list .= '<a href="'.$link.'" class="title">'.$title.'</a>';
	            $menu_list .= '</li>'; 
	            if ( $menu_items[ $count + 1 ]->menu_item_parent != $parent_id && $submenu ){
	                $menu_list .= '</ul>';
	                $submenu = false;
	            } 
	        } 
	        if ( isset($menu_items[ $count + 1 ]) && $menu_items[ $count + 1 ]->menu_item_parent != $parent_id ) { 
	            $menu_list .= '</li>';      
	            $submenu = false;
		        if($menu_item_count == 2){
		    		$menu_list .= '</ul><ul class="main-nav">';
		        	$menu_item_count=0;
		        }
		        else{
		        	$menu_item_count++;
		        }
	        }
	        $count++;
	    }         
	    $menu_list .= '</ul>';
	    $menu_list .= '</nav>';
	    echo $menu_list;
	}
	/**
	 * Outputs the content for the current Navigation Menu widget instance.
	 *
	 * @since 3.0.0
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Navigation Menu widget instance.
	 */
	public function widget( $args, $instance ) {
		// Get menu.
		$nav_menu = ! empty( $instance['nav_menu'] ) ? wp_get_nav_menu_object( $instance['nav_menu'] ) : false;

		if ( ! $nav_menu ) {
			return;
		}

		$default_title = __( 'Menu' );
		$title         = ! empty( $instance['title'] ) ? $instance['title'] : '';

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$format = current_theme_supports( 'html5', 'navigation-widgets' ) ? 'html5' : 'xhtml';

		/**
		 * Filters the HTML format of widgets with navigation links.
		 *
		 * @since 5.5.0
		 *
		 * @param string $format The type of markup to use in widgets with navigation links.
		 *                       Accepts 'html5', 'xhtml'.
		 */
		$format = apply_filters( 'navigation_widgets_format', $format );

		if ( 'html5' === $format ) {
			// The title may be filtered: Strip out HTML and make sure the aria-label is never empty.
			$title      = trim( strip_tags( $title ) );
			$aria_label = $title ? $title : $default_title;

			$nav_menu_args = array(
				'fallback_cb'          => '',
				'menu'                 => $nav_menu,
				'container'            => 'nav',
				'container_aria_label' => $aria_label,
				'items_wrap'           => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			);
		} else {
			$nav_menu_args = array(
				'fallback_cb' => '',
				'menu'        => $nav_menu,
			);
		}
		$this->get_custom_menu($nav_menu_args);

		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current Navigation Menu widget instance.
	 *
	 * @since 3.0.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		if ( ! empty( $new_instance['title'] ) ) {
			$instance['title'] = sanitize_text_field( $new_instance['title'] );
		}
		if ( ! empty( $new_instance['nav_menu'] ) ) {
			$instance['nav_menu'] = (int) $new_instance['nav_menu'];
		}
		return $instance;
	}

	public function form( $instance ) {
		global $wp_customize;
		$title    = isset( $instance['title'] ) ? $instance['title'] : '';
		$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';

		// Get menus.
		$menus = wp_get_nav_menus();

		$empty_menus_style     = '';
		$not_empty_menus_style = '';
		if ( empty( $menus ) ) {
			$empty_menus_style = ' style="display:none" ';
		} else {
			$not_empty_menus_style = ' style="display:none" ';
		}

		$nav_menu_style = '';
		if ( ! $nav_menu ) {
			$nav_menu_style = 'display: none;';
		}

		// If no menus exists, direct the user to go and create some.
		?>
		<p class="nav-menu-widget-no-menus-message" <?php echo $not_empty_menus_style; ?>>
			<?php
			if ( $wp_customize instanceof WP_Customize_Manager ) {
				$url = 'javascript: wp.customize.panel( "nav_menus" ).focus();';
			} else {
				$url = admin_url( 'nav-menus.php' );
			}

			/* translators: %s: URL to create a new menu. */
			printf( __( 'No menus have been created yet. <a href="%s">Create some</a>.' ), esc_attr( $url ) );
			?>
		</p>
		<div class="nav-menu-widget-form-controls" <?php echo $empty_menus_style; ?>>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'nav_menu' ); ?>"><?php _e( 'Select Menu:' ); ?></label>
				<select id="<?php echo $this->get_field_id( 'nav_menu' ); ?>" name="<?php echo $this->get_field_name( 'nav_menu' ); ?>">
					<option value="0"><?php _e( '&mdash; Select &mdash;' ); ?></option>
					<?php foreach ( $menus as $menu ) : ?>
						<option value="<?php echo esc_attr( $menu->term_id ); ?>" <?php selected( $nav_menu, $menu->term_id ); ?>>
							<?php echo esc_html( $menu->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
			<?php if ( $wp_customize instanceof WP_Customize_Manager ) : ?>
				<p class="edit-selected-nav-menu" style="<?php echo $nav_menu_style; ?>">
					<button type="button" class="button"><?php _e( 'Edit Menu' ); ?></button>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}
}
function RPMW_register_nav_menu_widget () {
	register_widget( 'RPMW_Nav_Menu_Widget' );
}

function enqueue_font_awesome_scripts() {
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		wp_register_style('font-awesome',RPMW_URL . "assets/css/font-awesome" . $postfix . ".css", array(), RPMW_VERSION, 'all');
		wp_enqueue_style('font-awesome');
}
add_action( 'widgets_init', 'RPMW_register_nav_menu_widget', 1 );

add_action( 'wp_enqueue_scripts', 'enqueue_font_awesome_scripts' );
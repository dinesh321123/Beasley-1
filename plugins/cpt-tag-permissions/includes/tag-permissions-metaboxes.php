<?php
/**
 * Class TagPermissionsMetaboxes
 */
class TagPermissionsMetaboxes {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public static function init() {
		
			add_action( 'admin_menu', array( __CLASS__, 'tags_meta_box_remove' ) );
			
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
			add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
			add_action( 'save_post',array( __CLASS__, 'tag_permissions_save') );

			add_action( 'wp_ajax_nopriv_is_tag_available', array( __CLASS__, 'is_tag_available' ) );
			add_action( 'wp_ajax_is_tag_available', array( __CLASS__, 'is_tag_available' ) );
	}

	/**
	 * Enqueues admin scripts and styles.
	 *
	 * @global string $typenow The current type.
	 * @global string $pagenow The current page.
	 */
	public static function enqueue_scripts() {
		global $typenow, $pagenow;
		$post_types = self::tag_permissions_posttype_list();
	   if ( in_array( $typenow, $post_types ) && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
	      // $postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		   // wp_enqueue_script( 'jquery-ui-autocomplete', "http://beasley.test/wp-includes/js/jquery/ui/autocomplete.min.js?ver=1.11.4" );
			if ( ! current_user_can( 'manage_categories' ) ) {
				wp_register_style('tag-permissions-admin', TAG_PERMISSIONS_URL . "assets/css/tp_admin.css", array(), TAG_PERMISSIONS_VERSION, 'all');
				wp_enqueue_style('tag-permissions-admin');  	
		   		wp_enqueue_script( 'tag-permissions-admin', TAG_PERMISSIONS_URL . "assets/js/tp_admin.js", array('jquery'), TAG_PERMISSIONS_VERSION, true);
				wp_localize_script( 'tag-permissions-admin', 'my_ajax_object', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
		   }
	   }
   }


	public static function tag_permissions_posttype_list() {
		return (array) apply_filters( 'tag-permissions-allow-post-types', array( 'post', 'gmr_gallery', 'show', 'gmr_album', 'tribe_events', 'announcement', 'contest', 'podcast', 'episode', 'content-kit' )  );
		// return array( 'post', 'gmr_gallery', 'show', 'gmr_album', 'tribe_events', 'subscription' );
	}

	public static function tags_meta_box_remove() {
		if ( current_user_can( 'manage_categories' ) ) {
			$id = 'tag-permissions-post_tag';
			$post_type = self::tag_permissions_posttype_list();
			$position = 'side';
			remove_meta_box( $id, $post_type, $position );
		}
	}

	/**
	 * Adds the meta box container for Episodes.
	 *
	 * @param $post_type
	 */
	public static function add_meta_box( $post_type ) {
		/* echo current_user_can('administrator');
		$user = wp_get_current_user(); // getting & setting the current user 
		$roles = ( array ) $user->roles; // obtaining the role 
		print_r($user);
  exit; */
		if ( ! current_user_can( 'manage_categories' ) ) {
			$post_types = self::tag_permissions_posttype_list();
			if ( in_array( $post_type, $post_types ) ) {
				add_meta_box( 'tag-permissions-post_tag', 'Custom Tags', array( __CLASS__, 'render_footer_metabox' ), $post_type, 'side', 'core' );
			}
		}

	}
	public static function remove_first_space( $val ){
		return trim( $val );
	}

	public static function is_tag_available() {
		$output = array();
		
		if ( ! empty( $_POST['tags_data'] ) && $_POST['tags_data'] != '' ) {
			// print_r( $_REQUEST );
			$available_tag_html = array();
			$available_tag_array = array();
			$not_available_tag_array = array();
			
			if( !empty( $_POST['tags_data'] ) ){

				$tags_array = explode( ",", $_POST['tags_data'] );
				$prior_tags_array = explode( ",", $_POST['prior_tags_data'] );
				$new_tags_array_without_first_space = array_map( array( __CLASS__, 'remove_first_space' ), $tags_array );
				$prior_tags_array_without_first_space = array_map( array( __CLASS__, 'remove_first_space' ), $prior_tags_array );

				if( isset( $_POST['activity'] ) && $_POST['activity'] == 'remove' ){
					// print_r( $tags_array );
					// print_r( $prior_tags_array );
					if ( ( $key = array_search( $tags_array[0], $prior_tags_array ) ) !== false) {
						unset($prior_tags_array[$key]);
						$merge_tag_array = $prior_tags_array;
					}
				} else {
					$merge_tag_array = array_unique( array_merge( $new_tags_array_without_first_space, $prior_tags_array_without_first_space ) );
				}
				
				

				// print_r($merge_tag_array);
				// echo "<pre> ", print_r( $tags_array ), print_r($prior_tags_array_without_first_space), " ---- Final array ---- ", print_r( $new_tags_array );
				$counter = 0;
				foreach( $merge_tag_array as $tag_name ){
					if( !empty( $tag_name ) && !ctype_space( $tag_name ) && $tag_name != "" ) {
						// echo ctype_space( $tag_name ), ' -- ';
						// echo $tag_name.' -- ';
						$tag_status = term_exists( $tag_name, 'post_tag' );
						// print_r( $tag_status );
						if( !empty( $tag_status ) ) {
							$available_tag_array[] = $tag_name;
							// $available_tag_html[] = '<li><button type="button" id="post_tag-check-num-'. $counter .'" class="tp_ntdelbutton ntdelbutton"><span class="remove-tag-icon" aria-hidden="true"></span><span class="screen-reader-text">Remove term: '. ltrim($tag_name) .'</span></button>&nbsp;'. ltrim($tag_name) .'</li>';
							$available_tag_html[] = '<li><button type="button" id="post_tag-check-num-'. $counter .'" class="ntdelbutton" value="'. trim($tag_name) .'"><span class="remove-tag-icon" aria-hidden="true"></span><span class="screen-reader-text">Remove term: '. trim($tag_name) .'</span></button>'. trim($tag_name) .'</li>';
							$counter++;
						} else {
							// echo $tag_name.'  --- '; 
							// $not_available_tag_array[] = '<p>'.$tag_name.' tag not available</p>';
							$not_available_tag_array[] = $tag_name;
						}
					}
				}

				
			}
			// print_r($not_available_tag_array);
			$output['available_tag_html'] = array_unique( $available_tag_html );
			$output['available_tag_string'] = implode( ",", $available_tag_array );
			$output['not_available_tag_string'] = $not_available_tag_array;
		}
		// print_r( $output );
		wp_send_json( $output );
	}

	public static function render_footer_metabox( \WP_Post $post ) {
		wp_nonce_field( '_tag_permissions_nonce', '_tag_permissions_nonce' );
		$tags_array = get_the_tags( $post->ID );
		$tags_string = "";
		// $am_footer_description = self::tp_get_metavalue( 'am_footer_description' );
		?>
			<div class="inside">
				<div class="tagsdiv" id="post_tag">
					<div class="jaxtag">
						<div class="ajaxtag hide-if-no-js">
							<label class="screen-reader-text" for="new-tag-post_tag">Add New Tag</label>
							<p>
								<input data-wp-taxonomy="post_tag" type="text" id="new-tag-post_tag" name="newtag[post_tag]" class="newtag form-input-tip ui-autocomplete-input tag-permissions-value" size="16" autocomplete="off" aria-describedby="new-tag-post_tag-desc" value="" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-owns="ui-id-1">
							</p>
							<input type="button" class="tag-permissions-add" value="Add">
						</div>
						<p class="howto" id="new-tag-post_tag-desc">Separate tags with commas</p>
					</div>
					<div id="error_msg">
					</div>
					<ul class="tagchecklist" id="available-tagchecklist" role="list">
						<?php
							if ($tags_array) {
								$tags_string = array();
								$tag_count = 0;
								foreach($tags_array as $tag) {
									$tags_string[] = $tag->name;
									// echo '<li><button type="button" id="post_tag-check-num-'. $tag->name .'" class="tp_ntdelbutton ntdelbutton"><span class="remove-tag-icon" aria-hidden="true"></span><span class="screen-reader-text">Remove term: '. $tag->name .'</span></button>&nbsp;'. $tag->name .'</li>';
									echo '<li><button type="button" id="post_tag-check-num-'. $tag_count .'" class="ntdelbutton" value="'. $tag->name .'" ><span class="remove-tag-icon" aria-hidden="true"></span><span class="screen-reader-text">Remove term: '. $tag->name .'</span></button>&nbsp;'. $tag->name .'</li>';
									$tag_count++;
								}
								$tags_string = implode( ',', $tags_string );
							}
						?>
					</ul>
					<div class="">
					<?php //nojs-tags hide-if-js?>
							<label for="tax-input-post_tag">Add or remove tags</label>
							<p>
								<textarea name="tag_permissions_post_tag" rows="3" cols="20" class="the-tags" id="tag_permissions_post_tag" aria-describedby="new-tag-post_tag-desc"> <?php echo $tags_string;?> </textarea>
							</p>
						</div>
				</div>
			</div>
		<?php 
	}

	function tag_permissions_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! isset( $_POST['_tag_permissions_nonce'] ) || ! wp_verify_nonce( $_POST['_tag_permissions_nonce'], '_tag_permissions_nonce' ) ) return;
		if ( ! current_user_can( 'edit_post' ) ) return;

		if ( isset( $_POST['tag_permissions_post_tag'] ) ) {
			$post_tag_array = explode( ',', $_POST['tag_permissions_post_tag'] );
			wp_set_post_tags( $post_id, $post_tag_array); // Set tags to Post
		}
	}

	public static function tp_get_metavalue( $value ) {
		global $post;
		$field = get_post_meta( $post->ID, $value, true );
		if ( ! empty( $field ) ) {
			return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
		} else {
			return false;
		}
	}
}

TagPermissionsMetaboxes::init();

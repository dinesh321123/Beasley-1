<?php

class GMR_QuickPost {

	const ADMIN_ACTION = 'gmr_quicklink';

	/**
	 * Singletone instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @access private
	 * @var GMR_QuickPost
	 */
	private static $_isntance = null;

	/**
	 * Returns class instance. Initializes it if it is not exists yet.
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @access public
	 * @return GMR_QuickPost The class instance.
	 */
	public static function get_intance() {
		if ( is_null( self::$_isntance ) ) {
			self::$_isntance = new GMR_QuickPost();

			add_action( 'wp_dashboard_setup', array( self::$_isntance, 'register_dashboard_widget' ) );
			add_action( 'admin_action_' . self::ADMIN_ACTION, array( self::$_isntance, 'process_quicklink_popup' ) );
			add_action( 'admin_bar_menu', array( self::$_isntance, 'add_admin_bar_items' ), 100 );
			add_action( 'wp_enqueue_scripts', array( self::$_isntance, 'enqueue_scripts' ) );
		}

		return self::$_isntance;
	}

	/**
	 * Registers dashboard widget.
	 *
	 * @since 1.0.0
	 * @action wp_dashboard_setup
	 *
	 * @access public
	 */
	public function register_dashboard_widget() {
		if ( current_user_can( 'edit_posts' ) ) {
			wp_add_dashboard_widget( 'quickpost', 'Quick Live Link', array( $this, 'render_tool_box' ) );
		}
	}

	/**
	 * Rendes Quick Post tool box.
	 *
	 * @since 1.0.0
	 * @action tool_box
	 *
	 * @access public
	 */
	public function render_tool_box() {
		if ( ! current_user_can( 'edit_posts' ) )  {
			return;
		}

		$link = admin_url( 'admin.php?action=' . self::ADMIN_ACTION );
		$link = str_replace( array( "\r", "\n", "\t" ), '', "
				javascript:
				var d=document,
				w=window,
				e=w.getSelection,
				k=d.getSelection,
				x=d.selection,
				s=(e?e()+'':(k)?k()+'':(x?x.createRange().text:0)),
				f='{$link}',
				l=d.location,
				e=encodeURIComponent,
				u=f+'&u='+e(l.href)+'&t='+e(d.title)+'&s='+e(s)+'&v=4';
				a=function(){var w=720,h=250,t=(screen.width/2)-(w/2),p=(screen.height/2)-(h/2);
				if(!window.open(u, 't', 'toolbar=0,resizable=1,copyhistory=0,scrollbars=1,status=1,width='+w+',height='+h+',top='+p+',left='+t))l.href=u;};
				if(/Firefox/.test(navigator.userAgent))setTimeout(a, 0);else a();
				void(0)
		" );

		?><div class="tool-box">
			<p>Quick Live Link is a bookmarklet: a little app that runs in your browser and lets you quickly create live links from the websites you visit.</p>

			<p class="description">
				Drag-and-drop the following link to your bookmarks bar or right click it and add it to your favorites for a posting shortcut.
			</p>

			<p class="pressthis">
				<a onclick="return false;" oncontextmenu="if(window.navigator.userAgent.indexOf('WebKit')!=-1||window.navigator.userAgent.indexOf('MSIE')!=-1){jQuery('.quickpost-code').show().find('textarea').focus().select();return false;}" href="<?php echo esc_attr( $link ); ?>">
					<span>Quick Live Link</span>
				</a>
			</p>

		</div><?php
	}

	/**
	 * Adds bookmarklet link to the admin bar menu.
	 *
	 * @since 1.0.0
	 * @action admin_bar_menu 100
	 *
	 * @access public
	 * @param WP_Admin_Bar $admin_bar The admin bar object.
	 */
	public function add_admin_bar_items( WP_Admin_Bar $admin_bar ) {
		if ( ! is_admin() && current_user_can( 'create_post' ) ) {
			$admin_bar->add_menu( array(
				'id'    => 'add-live-link',
				'title' => 'Create Live Link',
				'href'  => '#',
			) );
		}
	}

	/**
	 * Enqueues scripts.
	 *
	 * @since 1.0.0
	 * @access wp_enqueue_scripts
	 *
	 * @access public
	 */
	public function enqueue_scripts() {
		if ( is_user_logged_in() && current_user_can( 'create_post' ) ) {
			$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_script( 'gmr-quick-live-links', GMEDIA_LIVE_LINK_URL . "/assets/js/quick-links{$postfix}.js", array( 'jquery' ), GMEDIA_LIVE_LINK_VERSION, true );
			wp_localize_script( 'gmr-quick-live-links', 'live_links', array( 'url'  => admin_url( 'admin.php?action=' . self::ADMIN_ACTION ) ) );
		}
	}

	/**
	 * Processes QuickPost popup request.
	 *
	 * @since 1.0.0
	 * @action admin_action_gmr_quicklink
	 *
	 * @access public
	 */
	public function process_quicklink_popup() {
		define( 'IFRAME_REQUEST' , true );
		header( sprintf( 'Content-Type: %s; charset=%s', get_option( 'html_type' ), get_option( 'blog_charset' ) ) );

		// Set Variables
		$url = filter_input( INPUT_GET, 'u', FILTER_VALIDATE_URL );
		$title = isset( $_GET['t'] ) ? strip_tags( html_entity_decode( wp_unslash( $_GET['t'] ), ENT_QUOTES ) ) : '';
		$title = trim( preg_replace( '#\W+#', ' ', $title ) );

		// process submitted posts.
		$saved = false;
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			check_admin_referer( 'quicklink' );
			$this->_save_quick_link();
			$saved = true;
		}

		$post = get_default_post_to_edit( GMR_LIVE_LINK_CPT, ! $saved );

		// die if user don't have permissions
		if ( ! current_user_can( get_post_type_object( GMR_LIVE_LINK_CPT )->cap->create_posts, $post ) ) {
			wp_die( __( 'Cheatin&#8217; uh?' ) );
		}

		wp_enqueue_style( 'colors' );
		wp_enqueue_script( 'post' );

		remove_action( 'media_buttons', 'media_buttons' );

		_wp_admin_html_begin(); ?>
				<title>Quick Live Link</title>

				<style type="text/css">
					body.press-this.quick-link {
						min-height: 100%;
					}

					#redirectdiv {
						margin-top: 1em;
					}

					#redirectdiv b {
						font-size: 1.2em;
						margin-bottom: 0.5em;
						display: block;
					}

					#redirectdiv i {
						margin-top: 0.75em;
						display: block;
					}

					#redirect_to {
						padding: 3px 8px;
						font-size: 1.4em;
						line-height: 100%;
						height: 1.7em;
						width: 100%;
						outline: none;
						margin: 0;
						background-color: #fff;
					}
				</style>
					
				<?php $this->_do_header_actions() ?>

				<script type="text/javascript">
					<?php if ( $saved ) : ?>window.close();<?php endif; ?>

					(function($) {
						$(document).ready(function() {
							// Resize screen.
							window.resizeTo(720, 250);

							$('#title').unbind();
							$('#publish, #save').click(function() {
								$('.press-this #publishing-actions .spinner').css('display', 'inline-block');
							});
						});
					})(jQuery);
				</script>
			</head>
			<body class="press-this quick-link wp-admin wp-core-ui">
				<form action="<?php echo esc_url( admin_url( 'admin.php?action=' . self::ADMIN_ACTION ) ) ?>" method="post">
					<div id="poststuff" class="metabox-holder">

						<div id="side-sortables" class="press-this-sidebar">
							<div class="sleeve">
								<?php wp_nonce_field( 'quicklink' ) ?>
								<input type="hidden" name="post_type" id="post_type" value="text">
								<input type="hidden" name="autosave" id="autosave">
								<input type="hidden" id="original_post_status" name="original_post_status" value="draft">
								<input type="hidden" id="prev_status" name="prev_status" value="draft">
								<input type="hidden" id="post_id" name="post_id" value="<?php echo (int) $post->ID; ?>">

								<!-- This div holds the photo metadata -->
								<div class="photolist"></div>

								<div id="submitdiv" class="postbox">
									<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle' ); ?>"><br /></div>
									<div class="inside">
										<p id="publishing-actions">
											<?php
												if ( current_user_can( 'publish_posts' ) ) {
													submit_button( __( 'Publish' ), 'primary', 'publish', false );
												} else {
													echo '<br><br>';
													submit_button( __( 'Submit for Review' ), 'primary', 'review', false );
												}
											?>
											<span class="spinner" style="display: none;"></span>
											<?php submit_button( __( 'Save Draft' ), 'button', 'draft', false, array( 'id' => 'save' ) ); ?>
										</p><?php

										if ( current_theme_supports( 'post-formats' ) ) :
											$post_formats = get_theme_support( 'post-formats' );
											if ( is_array( $post_formats[0] ) ) :
												if ( preg_match( "/youtube\.com/i", $url ) || preg_match( "/vimeo\.com/i", $url ) ) {
													$default_format = 'video';
												} elseif ( preg_match( "/soundcloud\.com/i", $url ) ) {
													$default_format = 'audio';
												} else {
													$default_format = 'link';
												}

												?><p>
													<label for="post_format"><?php _e( 'Post Format:' ); ?></label>
													<select name="post_format" id="post_format" class="widefat">
														<option value="0"><?php echo get_post_format_string( 'standard' ); ?></option>
														<?php foreach ( $post_formats[0] as $format ): ?>
															<?php if ( in_array( $format, array( 'link', 'video', 'standard', 'audio' ) ) ) : ?>
																<option<?php selected( $default_format, $format ); ?> value="<?php echo esc_attr( $format ); ?>">
																	<?php echo esc_html( get_post_format_string( $format ) ); ?>
																</option>
															<?php endif; ?>
														<?php endforeach; ?>
													</select>
												</p><?php
											endif;
										endif;

										do_action( 'gmr_quicklink_submitbox_misc_actions', $post );

									?></div>
								</div>
							</div>
						</div>

						<div class="posting">
							<div id="wphead">
								<h1 id="site-heading">
									<a href="<?php echo home_url( '/' ); ?>" target="_blank">
										<span id="site-title"><?php bloginfo( 'name' ); ?></span>
									</a>
								</h1>
							</div>

							<div id="titlediv">
								<div class="titlewrap">
									<input name="title" id="title" class="text" type="text" value="<?php echo esc_attr( $title ); ?>">
								</div>
							</div>

							<div id="redirectdiv">
								<b>Redirects To:</b>
								<input type="url" id="redirect_to" class="widefat" name="redirect" value="<?php echo esc_attr( $url ); ?>">
								<i>
									<strong>Pro Tip:</strong> Use Quick Link while browsing
									<?php echo esc_html( parse_url( home_url( '/' ), PHP_URL_HOST ) ); ?>
									to rapidly create new live links from existing posts, contests,
									events and galleries.
								</i>
							</div>
						</div>
					</div>
				</form>

				<?php $this->_do_footer_actions() ?>
			</body>
		</html><?php
		
		exit;
	}

	/**
	 * Saves new quick post.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @return int The ID of the quick post.
	 */
	private function _save_quick_link() {
		$post = get_default_post_to_edit( GMR_LIVE_LINK_CPT );
		$post = get_object_vars( $post );
		$post_id = $post['ID'] = filter_input( INPUT_POST, 'post_id', FILTER_VALIDATE_INT );

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( __( 'You are not allowed to edit this post.' ) );
		}

		$post['post_title'] = isset( $_POST['title'] ) ? $_POST['title'] : '';

		// Set the post_content and status.
		$post['post_content'] = '';
		if ( isset( $_POST['publish'] ) && current_user_can( 'publish_posts' ) ) {
			$post['post_status'] = 'publish';
		} elseif ( isset( $_POST['review'] ) ) {
			$post['post_status'] = 'pending';
		} else {
			$post['post_status'] = 'draft';
		}

		// Post formats.
		if ( isset( $_POST['post_format'] ) ) {
			if ( current_theme_supports( 'post-formats', $_POST['post_format'] ) ) {
				set_post_format( $post_id, $_POST['post_format'] );
			} elseif ( '0' == $_POST['post_format'] ) {
				set_post_format( $post_id, false );
			}
		}

		$post_id = wp_update_post( apply_filters( 'gmr_quicklink_post_data', $post ) );
		if ( $post_id ) {
			update_post_meta( $post_id, 'redirect', filter_input( INPUT_POST, 'redirect', FILTER_VALIDATE_URL ) );
		}
		
		do_action( 'gmr_quicklink_post_created', $post_id );

		return $post_id;
	}

	/**
	 * Calls required header actions.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function _do_header_actions() {
		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_enqueue_scripts', 'quicklink' );

		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_print_styles' );

		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_print_scripts' );

		/** This action is documented in wp-admin/admin-header.php */
		do_action( 'admin_head' );
	}

	/**
	 * Calls required footer actions.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function _do_footer_actions() {
		/** This action is documented in wp-admin/admin-footer.php */
		do_action( 'admin_footer' );
		/** This action is documented in wp-admin/admin-footer.php */
		do_action( 'admin_print_footer_scripts' );
	}

}

// initialize class instance
GMR_QuickPost::get_intance();
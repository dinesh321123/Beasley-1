<?php
/*
Plugin Name: Greater Media QuickPost
Description: QuickPost functionality gives access to an extremely rapid and no-frills way to create new posts formatted as text snippets, links, embedded video, images, or calls to action.
Version: 1.0
Author: 10up
Author URI: http://10up.com/
*/

class GMR_QuickPost {

	const ADMIN_ACTION = 'gmr_quickpost';

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
	 * Private constructor.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Private clone method.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function __clone() {}

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
			add_action( 'admin_action_' . self::ADMIN_ACTION, array( self::$_isntance, 'process_quickpost_popup' ) );
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
			wp_add_dashboard_widget( 'quickpost', 'Quick Post', array( $this, 'render_tool_box' ) );
		}
	}

	/**
	 * Renders featured image metabox.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param array $params The quickpost params.
	 */
	public function render_featured_image_metabox( $params ) {
		$images = $this->_get_images( $params['url'] );
		if ( empty( $images ) ) {
			echo '<i>No images were found.</i>';
			return;
		}
		
		?><script type="text/javascript">
			(function($) {
				$(document).ready(function() {
					var selected_image = 0,
						images = <?php $this->_process_ajax_photo_images_request( $params['url'] ) ?>;

					$('#quickpost-featured-image-number span:last-child').text(images.length);
					
					function updateImage() {
						$('#quickpost-featured-image img').attr('src', images[selected_image]);
						$('#quickpost-featured-image-controls a:last-child').toggle(selected_image != images.length - 1);
						$('#quickpost-featured-image-controls a:first-child').toggle(selected_image != 0);
						$('#quickpost-featured-image-number span:first-child').text(selected_image + 1);
						$('input[name="featured_image"]').val(images[selected_image]);
					}

					$('#quickpost-featured-image-controls a:first-child').click(function() {
						selected_image--;
						updateImage();
						return false;
					});

					$('#quickpost-featured-image-controls a:last-child').click(function() {
						selected_image++;
						updateImage();
						return false;
					});

					updateImage();
				});
			})(jQuery);
		</script>

		<input type="hidden" name="featured_image" value="">

		<div id="quickpost-featured-image">
			<div id="quickpost-featured-image-number">
				<span></span>/<span></span>
			</div>
			<img src="">
		</div>
		<div id="quickpost-featured-image-controls">
			<a href="#" style="display:none">&laquo; prev</a>
			<a href="#">next &raquo;</a>
		</div><?php
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
				a=function(){if(!w.open(u,'t','toolbar=0,resizable=1,scrollbars=1,status=1,width=720,height=570'))l.href=u;};
				if (/Firefox/.test(navigator.userAgent)) setTimeout(a, 0); else a();
				void(0)
		" );

		?><div class="tool-box">
			<p>Quick Post is a bookmarklet: a little app that runs in your browser and lets you grab bits of the web.</p>

			<p class="description">
				Use Quick Post to clip text, images and videos from any web page. Then edit and add more straight from Quick Post before you save or publish it in a post on your site.
				Drag-and-drop the following link to your bookmarks bar or right click it and add it to your favorites for a posting shortcut.
			</p>

			<p class="pressthis">
				<a onclick="return false;" oncontextmenu="if(window.navigator.userAgent.indexOf('WebKit')!=-1||window.navigator.userAgent.indexOf('MSIE')!=-1){jQuery('.quickpost-code').show().find('textarea').focus().select();return false;}" href="<?php echo esc_attr( $link ); ?>">
					<span>Quick Post</span>
				</a>
			</p>

			<div class="quickpost-code" style="display:none;">
				<p class="description">If your bookmarks toolbar is hidden: copy the code below, open your Bookmarks manager, create new bookmark, type Quick Post into the name field and paste the code into the URL field.</p>
				<p><textarea rows="5" cols="120" readonly="readonly"><?php echo esc_textarea( $link ); ?></textarea></p>
			</div>
		</div><?php
	}

	/**
	 * Processes QuickPost popup request.
	 *
	 * @since 1.0.0
	 * @action admin_action_gmr_quickpost
	 *
	 * @access public
	 */
	public function process_quickpost_popup() {
		// die if user don't have permissions
		if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( get_post_type_object( 'post' )->cap->create_posts ) ) {
			wp_die( __( 'Cheatin&#8217; uh?' ) );
		}

		define( 'IFRAME_REQUEST' , true );
		header( sprintf( 'Content-Type: %s; charset=%s', get_option( 'html_type' ), get_option( 'blog_charset' ) ) );

		// Set Variables
		$url = isset( $_GET['u'] ) ? esc_url( $_GET['u'] ) : '';
		$image = isset( $_GET['i'] ) ? $_GET['i'] : '';
		$title = isset( $_GET['t'] ) ? strip_tags( html_entity_decode( wp_unslash( $_GET['t'] ), ENT_QUOTES ) ) : '';
		$title = trim( preg_replace( '#\W+#', ' ', $title ) );

		$selection = '';
		if ( ! empty( $_GET['s'] ) ) {
			$selection = str_replace( '&apos;', "'", wp_unslash( $_GET['s'] ) );
			$selection = trim( htmlspecialchars( html_entity_decode( $selection, ENT_QUOTES ) ) );
		}

		if ( ! empty( $selection ) ) {
			$selection = preg_replace( '/(\r?\n|\r)/', '</p><p>', $selection );
			$selection = '<p>' . str_replace( '<p></p>', '', $selection ) . '</p>';
		}

		// process ajax requests
		$this->_process_ajax_request( $title, $selection, $image, $url );
		// process submitted posts.
		$saved = false;
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			check_admin_referer( 'quickpost' );
			$this->_save_quick_post();
			$saved = true;
		}

		$post = get_default_post_to_edit( 'post', ! $saved );
		$post_id = $post->ID;

		wp_enqueue_style( 'colors' );
		wp_enqueue_script( 'post' );
		add_thickbox();

		$admin_body_class = ( is_rtl() ) ? 'rtl' : '';
		$admin_body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) );

		remove_action( 'media_buttons', 'media_buttons' );
		add_action( 'media_buttons', array( $this, 'substitute_media_buttons' ) );

		add_meta_box( 'quickpost_featured_image', 'Featured Image', array( $this, 'render_featured_image_metabox' ), 'quickpost', 'side', 'high' );
		
		do_action( 'gmr_quickpost_add_metaboxes', 'quickpost', $post_id );

		_wp_admin_html_begin(); ?>
				<title><?php _e('Quick Post') ?></title>

				<?php if ( $saved ) : ?>
					<script type="text/javascript">window.close();</script>
				<?php endif; ?>

				<style type="text/css" media="screen">
					#quickpost-featured-image {
						width: 214px;
						height: 160px;
						overflow: hidden;
						margin-bottom: 10px;
						position: relative;
					}

					#quickpost-featured-image img {
						width: 100%;
						height: auto;
					}

					#quickpost-featured-image-controls {
						text-align: center;
					}

					#quickpost-featured-image-controls a {
						text-decoration: none;
					}

					#quickpost-featured-image-controls a:first-child {
						margin-right: 10px;
					}

					#quickpost-featured-image-number {
						position: absolute;
						top: 0;
						right: 0;
						background: rgba(0, 0, 0, 0.5);
						color: white;
						font-weight: bold;
						padding: 3px 10px;
					}

					#quickpost-featured-image-number span {
					}
				</style>

				<script type="text/javascript">
					//<![CDATA[
					addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
					var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>', pagenow = 'quickpost', isRtl = <?php echo (int) is_rtl(); ?>;
					var photostorage = false;
					//]]>
				</script>

				<?php $this->_do_header_actions() ?>

				<script type="text/javascript">
					var wpActiveEditor = 'content';

					function insert_plain_editor(text) {
						if ( typeof(QTags) != 'undefined' )
							QTags.insertContent(text);
					}

					function set_editor(text) {
						if ( '' == text || '<p></p>' == text )
							text = '<p><br /></p>';

						if ( tinyMCE.activeEditor )
							tinyMCE.execCommand('mceSetContent', false, text);
					}

					function insert_editor(text) {
						if ( '' != text && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden()) {
							tinyMCE.execCommand('mceInsertContent', false, '<p>' + decodeURI(tinymce.DOM.decode(text)) + '</p>', {format : 'raw'});
						} else {
							insert_plain_editor(decodeURI(text));
						}
					}

					function append_editor(text) {
						if ( '' != text && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden()) {
							tinyMCE.execCommand('mceSetContent', false, tinyMCE.activeEditor.getContent({format : 'raw'}) + '<p>' + text + '</p>');
						} else {
							insert_plain_editor(text);
						}
					}

					function show(tab_name) {
						jQuery('#extra-fields').html('');
						switch(tab_name) {
							case 'video' :
								jQuery('#extra-fields').load('<?php echo admin_url( 'admin.php?action=' . self::ADMIN_ACTION . '&ajax=video&s=' . urlencode( $selection ) ); ?>', function() {
								});
								jQuery('#extra-fields').show();
								return false;
								break;
							case 'photo' :
								function setup_photo_actions() {
									jQuery('.close').click(function() {
										jQuery('#extra-fields').hide();
										jQuery('#extra-fields').html('');
									});
									jQuery('.refresh').click(function() {
										photostorage = false;
										show('photo');
									});
									jQuery('#photo-add-url').click(function(){
										var form = jQuery('#photo-add-url-div').clone();
										jQuery('#img_container').empty().append( form.show() );
									});
									jQuery('#waiting').hide();
									jQuery('#extra-fields').show();
								}

								jQuery('#waiting').show();
								if(photostorage == false) {
									jQuery.ajax({
										type: "GET",
										cache : false,
										url: "<?php echo esc_url( admin_url( 'admin.php' ) ); ?>",
										data: {
											action: '<?php echo self::ADMIN_ACTION ?>',
											ajax: 'photo_js',
											u: '<?php echo $url ?>'
										},
										dataType : "script",
										success : function(data) {
											eval(data);
											photostorage = jQuery('#extra-fields').html();
											setup_photo_actions();
										}
									});
								} else {
									jQuery('#extra-fields').html(photostorage);
									setup_photo_actions();
								}
								return false;
								break;
						}
					}

					jQuery(document).ready(function($) {
						var $contnet = $( '#content' );

						// Resize screen.
						window.resizeTo(760,580);

						// Set button actions.
						jQuery('#photo_button').click(function() { show('photo'); return false; });
						jQuery('#video_button').click(function() { show('video'); return false; });

						// Auto select.
						<?php if ( preg_match("/flickr\.com/i", $url ) ) : ?>
							show('photo');
						<?php endif; ?>
						jQuery('#title').unbind();
						jQuery('#publish, #save').click(function() { jQuery('.press-this #publishing-actions .spinner').css('display', 'inline-block'); });

						$('#tagsdiv-post_tag, #categorydiv').children('h3, .handlediv').click(function(){
							$(this).siblings('.inside').toggle();
						});

						if ( $( '#wp-content-wrap' ).hasClass( 'html-active' ) && window.switchEditors &&
							( tinyMCEPreInit.mceInit.content && tinyMCEPreInit.mceInit.content.wpautop ) ) {
							// The Text editor is default, run the initial content through pre_wpautop() to convert the paragraphs
							$contnet.text( window.switchEditors.pre_wpautop( $contnet.text() ) );
						}
					});
				</script>
			</head>
			<body class="press-this wp-admin wp-core-ui <?php echo $admin_body_class; ?>">
				<form action="<?php echo esc_url( admin_url( 'admin.php?action=' . self::ADMIN_ACTION ) ) ?>" method="post">
					<div id="poststuff" class="metabox-holder">

						<div id="side-sortables" class="press-this-sidebar">
							<div class="sleeve">
								<?php wp_nonce_field( 'quickpost' ) ?>
								<input type="hidden" name="post_type" id="post_type" value="text">
								<input type="hidden" name="autosave" id="autosave">
								<input type="hidden" id="original_post_status" name="original_post_status" value="draft">
								<input type="hidden" id="prev_status" name="prev_status" value="draft">
								<input type="hidden" id="post_id" name="post_id" value="<?php echo (int) $post_id; ?>">

								<!-- This div holds the photo metadata -->
								<div class="photolist"></div>

								<div id="submitdiv" class="postbox">
									<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle' ); ?>"><br /></div>
									<h3 class="hndle"><?php _e( 'Quick Post' ) ?></h3>
									<div class="inside"><?php

										if ( current_theme_supports( 'post-formats' ) ) :
											$post_formats = get_theme_support( 'post-formats' );
											if ( is_array( $post_formats[0] ) ) :
												if ( ! empty( $selection ) ) {
													$default_format = '0';
												} elseif ( preg_match( "/youtube\.com/i", $url ) || preg_match( "/vimeo\.com/i", $url ) ) {
													$default_format = 'video';
												} elseif ( preg_match( "/soundcloud\.com/i", $url ) ) {
													$default_format = 'audio';
												} else {
													$default_format = 'link';
												}

												?><p>
													<label for="post_format"><?php _e( 'Post Format:' ); ?>
														<select name="post_format" id="post_format">
															<option value="0"><?php echo get_post_format_string( 'standard' ); ?></option>
															<?php foreach ( $post_formats[0] as $format ): ?>
																<?php if ( in_array( $format, array( 'link', 'video', 'standard', 'audio' ) ) ) : ?>
																	<option<?php selected( $default_format, $format ); ?> value="<?php echo esc_attr( $format ); ?>">
																		<?php echo esc_html( get_post_format_string( $format ) ); ?>
																	</option>
																<?php endif; ?>
															<?php endforeach; ?>
														</select>
													</label>
												</p><?php
											endif;
										endif;

										do_action( 'gmr_quickpost_submitbox_misc_actions', $post );

										?><p id="publishing-actions">
											<?php submit_button( __( 'Save Draft' ), 'button', 'draft', false, array( 'id' => 'save' ) ); ?>
											<?php
												if ( current_user_can( 'publish_posts' ) ) {
													submit_button( __( 'Publish' ), 'primary', 'publish', false );
												} else {
													echo '<br><br>';
													submit_button( __( 'Submit for Review' ), 'primary', 'review', false );
												}
											?>
											<span class="spinner" style="display: none;"></span>
										</p>
									</div>
								</div>

								<?php do_meta_boxes( 'quickpost', 'side', array(
									'post_id' => $post_id,
									'url'     => $url,
								) ); ?>
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

							<div id="waiting" style="display: none"><span class="spinner"></span> <span><?php esc_html_e( 'Loading&hellip;' ); ?></span></div>
							<div id="extra-fields" style="display: none"></div>

							<div class="postdivrich"><?php
								$content = $selection ? $selection : '';
								if ( $url ) {
									if ( wp_oembed_get( $url ) ) {
										if ( ! empty( $content ) ) {
											$content .= PHP_EOL;
										}
										$content .= $url;
									} else {
										$content .= $selection ? '<p>' . __( 'via ' ) : '<p>';
										$content .= sprintf( "<a href='%s'>%s</a>.</p>", esc_url( $url ), esc_html( $title ) );
									}
								}
								
								wp_editor( $content, 'content', array( 'teeny' => true, 'textarea_rows' => '15' ) );
							?></div>
						</div>
					</div>
				</form>

				<div id="photo-add-url-div" style="display:none;">
					<table>
						<tr>
							<td><label for="this_photo"><?php _e( 'URL' ) ?></label></td>
							<td><input type="text" id="this_photo" name="this_photo" class="tb_this_photo text" onkeypress="if(event.keyCode==13) image_selector(this);"></td>
						</tr>
						<tr>
							<td><label for="this_photo_description"><?php _e( 'Description' ) ?></label></td>
							<td><input type="text" id="this_photo_description" name="photo_description" class="tb_this_photo_description text" onkeypress="if(event.keyCode==13) image_selector(this);" value="<?php echo esc_attr( $title );?>"></td>
						</tr>
						<tr>
							<td><input type="button" class="button" onclick="image_selector(this)" value="<?php esc_attr_e( 'Insert Image' ); ?>"></td>
						</tr>
					</table>
				</div>

				<?php $this->_do_footer_actions() ?>

				<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
			</body>
		</html><?php
		exit;
	}

	/**
	 * Processes AJAX requests.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @param string $title Page title.
	 * @param string $selection Selected fragment.
	 * @param string $image Selected image.
	 * @param string $url Page URL.
	 */
	private function _process_ajax_request( $title, $selection, $image, $url ) {
		if ( ! empty( $_REQUEST['ajax'] ) ) {
			switch ( $_REQUEST['ajax'] ) {
				case 'video':
					$this->_process_ajax_video_request( $selection );
					break;
				case 'photo_thickbox':
					$this->_process_ajax_photo_thickbox_request( $title, $image );
					break;
				case 'photo_images':
					$this->_process_ajax_photo_images_request( $url );
					break;
				case 'photo_js':
					$this->_process_ajax_photo_js_request( $url );
					break;
			}

			exit;
		}
	}

	/**
	 * Proceses "video" AJAX request.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @param string $selection Selected text on a page.
	 */
	private function _process_ajax_video_request( $selection ) {
		?><script type="text/javascript">/* <![CDATA[ */
			jQuery('.select').click(function() {
				append_editor(jQuery('#embed-code').val());
				jQuery('#extra-fields').hide();
				jQuery('#extra-fields').html('');
			});
			jQuery('.close').click(function() {
				jQuery('#extra-fields').hide();
				jQuery('#extra-fields').html('');
			});
		/* ]]> */</script>
		<div class="postbox">
			<h2><label for="embed-code"><?php _e( 'Embed Code' ) ?></label></h2>
			<div class="inside">
				<textarea name="embed-code" id="embed-code" rows="8" cols="40"><?php echo esc_textarea( $selection ); ?></textarea>
				<p id="options"><a href="#" class="select button"><?php _e( 'Insert Video' ); ?></a> <a href="#" class="close button"><?php _e( 'Cancel' ); ?></a></p>
			</div>
		</div><?php
	}

	/**
	 * Processes "photo_thickbox" AJAX request.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @param string $title The selected page title.
	 * @param string $image The selected image title.
	 */
	private function _process_ajax_photo_thickbox_request( $title, $image ) {
		?><script type="text/javascript">/* <![CDATA[ */
			jQuery('.cancel').click(function() {
				tb_remove();
			});
			jQuery('.select').click(function() {
				image_selector(this);
			});
		/* ]]> */</script>
		<h3 class="tb"><label for="tb_this_photo_description"><?php _e( 'Description' ) ?></label></h3>
		<div class="titlediv">
			<div class="titlewrap">
				<input id="tb_this_photo_description" name="photo_description" class="tb_this_photo_description tbtitle text" type="text" onkeypress="if(event.keyCode==13) image_selector(this);" value="<?php echo esc_attr( $title ); ?>"/>
			</div>
		</div>

		<p class="centered">
			<input type="hidden" name="this_photo" value="<?php echo esc_attr( $image ); ?>" id="tb_this_photo" class="tb_this_photo" />
			<a href="#" class="select">
				<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( __( 'Click to insert.' ) ); ?>" title="<?php echo esc_attr( __( 'Click to insert.' ) ); ?>" />
			</a>
		</p>

		<p id="options"><a href="#" class="select button"><?php _e( 'Insert Image' ); ?></a> <a href="#" class="cancel button"><?php _e( 'Cancel' ); ?></a></p><?php
	}

	/**
	 * Returns images array.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @param string $url The URL to fetch images links from.
	 */
	private function _get_images( $url ) {
		$images = array();

		$url = wp_kses( urldecode( $url ), null );
		$url = preg_replace( '/\/#.+?$/', '', $url );
		if ( preg_match( '/\.(jpe?g|jpe|gif|png)\b/i', $url ) && ! strpos( $url, 'blogger.com' ) ) {
			$images[] = html_entity_decode( $url );
		} else {
			$content = wp_remote_fopen( $url );
			if ( false !== $content ) {
				$host = parse_url( $url );
				$pattern = '/<img ([^>]*)src=(\"|\')([^<>\'\"]+)(\2)([^>]*)\/*>/i';
				$content = str_replace( array( "\n", "\t", "\r" ), '', $content );
				if ( preg_match_all( $pattern, $content, $matches ) && ! empty( $matches[0] ) ) {
					foreach ( $matches[3] as $src ) {
						// If no http in URL.
						if ( strpos( $src, 'http' ) === false ) {
							// If it doesn't have a relative URI.
							if ( strpos( $src, '//' ) === 0 ) {
								$src = ( is_ssl() ? 'https:' : 'http:' ) . $src;
							} elseif ( strpos( $src, '../' ) === false && strpos( $src, './' ) === false && strpos( $src, '/' ) === 0 ) {
								$src = 'http://' . str_replace( '//', '/', $host['host'] . '/' . $src );
							} else {
								$src = 'http://' . str_replace( '//', '/', $host['host'] . '/' . dirname( $host['path'] ) . '/' . $src );
							}
						}

						// add only jpeg and png fomrats
						if ( preg_match( '#\.(jpe?g|jpe|png)#i', $src ) ) {
							$images[] = esc_url( $src );
						}
					}
				}
			}
		}

		return $images;
	}

	/**
	 * Processes "photo_images" AJAX request.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @param string $url The URL to fetch images links from.
	 */
	private function _process_ajax_photo_images_request( $url ) {
		$images = $this->_get_images( $url );
		$images = "'" . implode( "','", $images ) . "'";
		
		echo 'new Array(', $images, ')';
	}

	/**
	 * Processes "photo_js" AJAX request.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @param string $url The selected page URL.
	 */
	private function _process_ajax_photo_js_request( $url ) {
		?>// Gather images and load some default JS.
		var last = null
		var img, img_tag, aspect, w, h, skip, i, strtoappend = "";
		if(photostorage == false) {
		var my_src = eval(
			jQuery.ajax({
				type: "GET",
				url: "<?php echo esc_url( admin_url( 'admin.php' ) ); ?>",
				cache : false,
				async : false,
				data: "action=<?php echo self::ADMIN_ACTION ?>&ajax=photo_images&u=<?php echo urlencode( $url ); ?>",
				dataType : "script"
			}).responseText
		);
		if(my_src.length == 0) {
			var my_src = eval(
				jQuery.ajax({
					type: "GET",
					url: "<?php echo esc_url( admin_url( 'admin.php' ) ); ?>",
					cache : false,
					async : false,
					data: "actin=<?php echo self::ADMIN_ACTION ?>&ajax=photo_images&u=<?php echo urlencode( $url ); ?>",
					dataType : "script"
				}).responseText
			);
			if(my_src.length == 0) {
				strtoappend = '<?php _e( 'Unable to retrieve images or no images on page.' ); ?>';
			}
		}
		}
		for (i = 0; i < my_src.length; i++) {
			img = new Image();
			img.src = my_src[i];
			img_attr = 'id="img' + i + '"';
			skip = false;

			maybeappend = '<a href="?action=<?php echo self::ADMIN_ACTION ?>&ajax=photo_thickbox&amp;i=' + encodeURIComponent(img.src) + '&amp;u=<?php echo urlencode( $url ); ?>&amp;height=400&amp;width=500" title="" class="thickbox"><img src="' + img.src + '" ' + img_attr + '/></a>';

			if (img.width && img.height) {
				if (img.width >= 30 && img.height >= 30) {
					aspect = img.width / img.height;
					scale = (aspect > 1) ? (71 / img.width) : (71 / img.height);

					w = img.width;
					h = img.height;

					if (scale < 1) {
						w = parseInt(img.width * scale);
						h = parseInt(img.height * scale);
					}
					img_attr += ' style="width: ' + w + 'px; height: ' + h + 'px;"';
					strtoappend += maybeappend;
				}
			} else {
				strtoappend += maybeappend;
			}
		}

		function pick(img, desc) {
			if (img) {
				if('object' == typeof jQuery('.photolist input') && jQuery('.photolist input').length != 0) length = jQuery('.photolist input').length;
				if(length == 0) length = 1;
				jQuery('.photolist').append('<input name="photo_src[' + length + ']" value="' + img +'" type="hidden"/>');
				jQuery('.photolist').append('<input name="photo_description[' + length + ']" value="' + desc +'" type="hidden"/>');
				insert_editor( "\n\n" + encodeURI('<p style="text-align: center;"><a href="<?php echo $url; ?>"><img src="' + img +'" alt="' + desc + '" /></a></p>'));
			}
			return false;
		}

		function image_selector(el) {
			var desc, src, parent = jQuery(el).closest('#photo-add-url-div');

			if ( parent.length ) {
				desc = parent.find('input.tb_this_photo_description').val() || '';
				src = parent.find('input.tb_this_photo').val() || ''
			} else {
				desc = jQuery('#tb_this_photo_description').val() || '';
				src = jQuery('#tb_this_photo').val() || ''
			}

			tb_remove();
			pick(src, desc);
			jQuery('#extra-fields').hide();
			jQuery('#extra-fields').html('');
			return false;
		}

		jQuery('#extra-fields').html('<div class="postbox"><h2><?php _e( 'Add Photos' ); ?> <small id="photo_directions">(<?php _e( "click images to select" ) ?>)</small></h2><ul class="actions"><li><a href="#" id="photo-add-url" class="button button-small"><?php _e( "Add from URL" ) ?> +</a></li></ul><div class="inside"><div class="titlewrap"><div id="img_container"></div></div><p id="options"><a href="#" class="close button"><?php _e( 'Cancel' ); ?></a><a href="#" class="refresh button"><?php _e( 'Refresh' ); ?></a></p></div>');
		jQuery('#img_container').html(strtoappend);<?php
	}

	/**
	 * Saves new quick post.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @return int The ID of the quick post.
	 */
	private function _save_quick_post() {
		$post = get_default_post_to_edit( 'post' );
		$post = get_object_vars( $post );
		$post_id = $post['ID'] = (int) $_POST['post_id'];

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( __( 'You are not allowed to edit this post.' ) );
		}

		$post['post_category'] = isset( $_POST['post_category'] ) ? $_POST['post_category'] : '';
		$post['tax_input'] = isset( $_POST['tax_input'] ) ? $_POST['tax_input'] : '';
		$post['post_title'] = isset( $_POST['title'] ) ? $_POST['title'] : '';
		$content = isset( $_POST['content'] ) ? $_POST['content'] : '';

		$upload = false;
		if ( current_user_can( 'upload_files' ) ) {
			if ( ! empty( $_POST['photo_src'] ) ) {
				foreach ( (array) $_POST['photo_src'] as $key => $image ) {
					// See if files exist in content - we don't want to upload non-used selected files.
					if ( strpos( $_POST['content'], htmlspecialchars( $image ) ) !== false ) {
						$desc = isset( $_POST['photo_description'][$key] ) ? $_POST['photo_description'][$key] : '';
						$upload = media_sideload_image( $image, $post_id, $desc );

						// Replace the POSTED content <img> with correct uploaded ones. Regex contains fix for Magic Quotes
						if ( !is_wp_error( $upload ) ) {
							$content = preg_replace( '/<img ([^>]*)src=\\\?(\"|\')' . preg_quote( htmlspecialchars( $image ), '/' ) . '\\\?(\2)([^>\/]*)\/*>/is', $upload, $content );
						}
					}
				}
			}

			// featured image
			$featured_image = filter_input( INPUT_POST, 'featured_image', FILTER_VALIDATE_URL );
			if ( ! empty( $featured_image ) ) {
				preg_match( '/[^\?]+\.(jpe?g|jpe|png)\b/i', $featured_image, $matches );

				$file_array = array();
				$file_array['name'] = basename( $matches[0] );
				$file_array['tmp_name'] = download_url( $featured_image );
				if ( strpos( $file_array['name'], '.' ) === false ) {
					$file_array['name'] = $file_array['name'] . '.jpg';
				}

				if ( ! is_wp_error( $file_array['tmp_name'] ) ) {
					$id = media_handle_sideload( $file_array, $post_id, '' );
					if ( ! is_wp_error( $id ) ) {
						set_post_thumbnail( $post_id, $id );
					}
				}
			}
		}

		// Set the post_content and status.
		$post['post_content'] = $content;
		if ( isset( $_POST['publish'] ) && current_user_can( 'publish_posts' ) ) {
			$post['post_status'] = 'publish';
		} elseif ( isset( $_POST['review'] ) ) {
			$post['post_status'] = 'pending';
		} else {
			$post['post_status'] = 'draft';
		}

		// Error handling for media_sideload.
		if ( is_wp_error( $upload ) ) {
			wp_delete_post( $post_id );
			wp_die( $upload );
		}

		// Post formats.
		if ( isset( $_POST['post_format'] ) ) {
			if ( current_theme_supports( 'post-formats', $_POST['post_format'] ) ) {
				set_post_format( $post_id, $_POST['post_format'] );
			} elseif ( '0' == $_POST['post_format'] ) {
				set_post_format( $post_id, false );
			}
		}

		$post_id = wp_update_post( apply_filters( 'gmr_quickpost_post_data', $post ) );
		
		do_action( 'gmr_quickpost_post_created', $post_id );

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
		do_action( 'admin_enqueue_scripts', 'quickpost' );

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

	/**
	 * Substitutes media buttons for post editor.
	 *
	 * @since 1.0.0
	 * @action media_buttons
	 *
	 * @access public
	 */
	public function substitute_media_buttons() {
		esc_html_e( 'Add:' );

		if ( current_user_can('upload_files') ) {
			?><a id="photo_button" title="<?php esc_attr_e( 'Insert an Image' ); ?>" href="#">
				<img alt="<?php esc_attr_e( 'Insert an Image' ); ?>" src="<?php echo esc_url( admin_url( 'images/media-button-image.gif?ver=20100531' ) ); ?>">
			</a><?php
		}

		?><a id="video_button" title="<?php esc_attr_e( 'Embed a Video' ); ?>" href="#">
			<img alt="<?php esc_attr_e( 'Embed a Video' ); ?>" src="<?php echo esc_url( admin_url( 'images/media-button-video.gif?ver=20100531' ) ); ?>"/>
		</a><?php
	}

}

// initialize class instance
GMR_QuickPost::get_intance();
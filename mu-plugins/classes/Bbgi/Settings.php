<?php

namespace Bbgi;

class Settings extends \Bbgi\Module {

	const option_group = 'greatermedia_site_options';

	/**
	 * Contains the slug of the settings page once it's registered
	 *
	 * @access protected
	 * @var string
	 */
	protected $_settings_page_hook;

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		add_action( 'admin_menu', $this( 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Adds settings page.
	 *
	 * @access public
	 * @action admin_menu
	 */
	public function add_settings_page() {
		$this->_settings_page_hook = add_options_page( 'Station Settings', 'Station Settings', 'manage_options', 'greatermedia-settings', array( $this, 'render_settings_page' ) );
	}

	/**
	 * Renders settings page.
	 *
	 * @access public
	 */
	public function render_settings_page() {
		echo '<form action="options.php" method="post" style="max-width:750px;">';
			settings_fields( self::option_group );
			do_settings_sections( $this->_settings_page_hook );
			submit_button( 'Submit' );
		echo '</form>';
	}

	/**
	 * Registers settings.
	 *
	 * @access public
	 * @action admin_init
	 */
	public function register_settings() {
		// Fallback Thumbnails Section
		$section_info = bbgi_settings_section_info( 'Select fallback images which will be used as thumbnails when original thumbnail of a post will not be selected.' );
		add_settings_section( 'bbgi_fallback_thumbs', 'Fallback Thumbnails', $section_info, 'media' );

		$callback = array( $this, 'render_fallback_image_field' );
		$types = get_post_types( array( 'public' => true ), 'object' );

		// Sort the Post types in the UI
		ksort( $types, SORT_ASC );

		foreach ( $types as $type => $type_object ) {
			// Post types to exclude
			$exclude = array(
				'listener_submissions',
				'advertiser',
				'survey',
				'show',
			);

			// If the Post type is in the exclude list, then don't add to Media Page
			if ( true === in_array( $type_object->name, $exclude ) ) {
				continue;
			}

			if ( post_type_supports( $type, 'thumbnail' ) ) {
				$option_name = "{$type}_fallback";
				add_settings_field( $option_name, $type_object->label, $callback, 'media', 'bbgi_fallback_thumbs', array( 'option_name' => $option_name ) );
				register_setting( 'media', $option_name, 'intval' );
			}
		}
		
		/**
		 * Allows us to register extra settings that are not necessarily always present on all child sites.
		 */
		do_action( 'bbgi_register_settings', self::option_group, $this->_settings_page_hook );
	}

	/**
	 * Renders fallback image selection field.
	 * 
	 * @access public
	 * @param array $args
	 */
	public function render_fallback_image_field( $args ) {
		static $render_script = true;

		$name = $args['option_name'];

		$image = '';
		$image_id = intval( get_option( $name ) );
		if ( $image_id ) {
			$image = current( (array) wp_get_attachment_image_src( $image_id, 'medium' ) );
		}

		$img_id = $name . '-fallback-image';
		$input_id = $img_id . '-id';
		
		echo '<input id="', esc_attr( $input_id ), '" name="', esc_attr( $name ), '" type="hidden" value="', esc_attr( $image_id ), '">';
		echo '<img id="', esc_attr( $img_id ), '" src="', esc_attr( $image ), '" style="width:100px;height:auto">';
		echo '<div>';
			echo '<button class="select-fallback-image button button-primary" data-img="#', esc_attr( $img_id ), '" data-input="#', esc_attr( $input_id ), '">';
				echo 'Choose Image';
			echo '</button> ';
			echo '<button class="remove-fallback-image button" data-img="#', esc_attr( $img_id ), '" data-input="#', esc_attr( $input_id ), '" style="', ! $image_id ? 'display:none' : '', '">';
				echo 'Remove Image';
			echo '</button>';
		echo '</div>';

		if ( $render_script ) {
			$render_script = false;
			wp_enqueue_media();
			
			?><script>
				(function ($) {
					$(document).ready(function () {
						var imageFrame,
							selectedImage,
							selectedInput,
							nextButton;

						$('.select-fallback-image').click(function() {
							var $this = $(this);

							selectedImage = $this.data('img');
							selectedInput = $this.data('input');
							nextButton = $this.next();

							// if the frame already exists, open it
							if (imageFrame) {
								imageFrame.open();
								return false;
							}

							// set our settings
							imageFrame = wp.media({
								title: 'Choose Image',
								multiple: false,
								library: { type: 'image' },
								button: { text: 'Use This Image' }
							});

							// set up our select handler
							imageFrame.on( 'select', function() {
								var selection = imageFrame.state().get('selection');
								if ( ! selection ) {
									return;
								}

								// loop through the selected files
								selection.each( function( attachment ) {
									//console.log(attachment);
									var src = attachment.attributes.sizes.full.url;
									var id = attachment.id;

									$(selectedImage).attr('src', src);
									$(selectedInput).val(id);
									nextButton.show();
								} );
							});

							// open the frame
							imageFrame.open();

							return false;
						});

						// the remove image link, removes the image id from the hidden field and replaces the image preview
						$('.remove-fallback-image').click(function() {
							var $this = $(this);

							$($this.data('input')).val('');
							$($this.data('img')).attr('src', '');
							$this.hide();

							return false;
						});
					});
				})(jQuery);
			</script><?php
		}
	}

}

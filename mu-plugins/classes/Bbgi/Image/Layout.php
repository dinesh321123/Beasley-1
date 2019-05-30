<?php

namespace Bbgi\Image;

class Layout extends \Bbgi\Module {

	public function register() {
		add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ), 1000 );
		add_action( 'admin_footer', array( $this, 'admin_enqueue_scripts' ), 20, 0 );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	/**
	 * Render feature image preference field in the post submitbox
	 */
	public function post_submitbox_misc_actions() {
		global $post;
		if ( ! post_type_supports( $post->post_type, 'flexible-feature-image' ) ) {
			return;
		}

		$feature_image_preference = self::sanitize_feature_image_preference( get_post_meta( $post->ID, 'post_feature_image_preference', true ) );
		$feature_image_preference_desc = self::feature_image_preference_description( $feature_image_preference );

		?><div class="misc-pub-section feature-image-preference misc-pub-feature-image-preference">
            <span id="feature-image-preference-value">Feature Image Layout: <b><?php echo esc_html( $feature_image_preference_desc ); ?></b></span>
            <a href="#edit_feature_image_preference" class="edit-feature-image-preference hide-if-no-js">
                <span aria-hidden="true">Edit</span> <span class="screen-reader-text">Edit feature image preference</span>
            </a>
            <div id='featureimagepreferencediv' class='hide-if-js'></div>
        </div><?php
	}

	/**
	 * Make sure an age restriction value is one of the accepted ones
	 *
	 * @param string $input value to sanitize
	 * @return string valid age restriction value or ''
	 */
	protected static function sanitize_feature_image_preference( $input ) {
		// Immediate check for something way wrong
		if ( ! is_string( $input ) ) {
			return '';
		}

		static $valid_values;
		if ( !isset( $valid_values ) ) {
			$valid_values = array( 'poster', 'top', 'inline', 'none' );
		}

		// Sanitize
		if ( in_array( $input, $valid_values ) ) {
			return $input;
        }

        return '';
	}

	/**
	 * Returns a translated description of a feature image preference
	 *
	 * @param string $feature_image_preference
	 *
	 * @return string description
	 */
	protected static function feature_image_preference_description( $feature_image_preference ) {
		if ( 'none' === $feature_image_preference ) {
			return 'None';
		} else if ( 'poster' === $feature_image_preference ) {
			return 'Poster';
		} else if ( 'inline' === $feature_image_preference ) {
			return 'Inline';
        }

        return 'Inline';
	}

	/**
	 * Print out HTML form elements for editing post or comment publish date.
	 *
	 * @param int|bool $edit              Accepts 1|true for editing the date, 0|false for adding the date.
	 * @param int      $age_restriction   Current age restriction setting
	 * @param int      $tab_index         Starting tab index
	 * @param int      $multi             Optional. Whether the additional fields and buttons should be added.
	 *                                    Default 0|false.
	 *
	 * @return string HTML
	 * @see  touch_time() in wp-admin/includes/template.php
	 * @todo use a template instead of string concatenation for building HTML
	 */
	public function touch_feature_image_preference( $edit = 1, $feature_image_preference = '', $tab_index = 0, $multi = 0 ) {
		global $wp_locale;

		$html = '';
		$tab_index_attribute = '';
		if ( (int) $tab_index > 0 ) {
			$tab_index_attribute = ' tabindex="' . intval( $tab_index ) . '"';
		}

		$feature_image_preference = self::sanitize_feature_image_preference( $feature_image_preference );

		$supported = apply_filters( 'bbgi_supported_featured_image_layouts', array( 'poster', 'top', 'inline' ) );

		$html .= wp_nonce_field( 'feature_image_preference_meta_boxes', '__feature_image_preference_nonce', true, false );
		$html .= '<div class="feature-image-preference-wrap">';
		$html .= '<label for="fip_status" class="screen-reader-text">Feature Image Preference</label>';
		$html .= '<fieldset id="fip_status"' . $tab_index_attribute . ">\n";

		if ( in_array( 'poster', $supported ) ) {
			$html .= '<p><input type="radio" name="fip_status" value="poster" ' . checked( 'poster', $feature_image_preference, false ) . ' /> Poster</p>';
		}

		if ( in_array( 'top', $supported ) ) {
			$html .= '<p><input type="radio" name="fip_status" value="top" ' . ( empty( $feature_image_preference ) ? '' : checked( 'top', $feature_image_preference, false ) ) . ' /> Top</p>';
		}

		if ( in_array( 'inline', $supported ) ) {
			if ( empty( $feature_image_preference ) ) {
				$html .= '<p><input type="radio" name="fip_status" value="inline" checked=checked /> Inline</p>';
			} else {
				$html .= '<p><input type="radio" name="fip_status" value="inline" ' . checked( 'inline', $feature_image_preference, false ) . ' /> Inline</p>';
			}
		}

		$html .= '<p><input type="radio" name="fip_status" value="none" ' . checked( 'none', $feature_image_preference, false ) . ' /> None</p>';
		$html .= '<input type="hidden" id="hidden_feature_image_preference" name="hidden_feature_image_preference" value="' . esc_attr( $feature_image_preference ) . '" />';
		$html .= '</fieldset>';
		$html .= '<p>';
		$html .= '<a href="#edit_feature_image_preference" class="save-feature-image-preference hide-if-no-js button">OK</a>';
		$html .= '<a href="#edit_feature_image_preference" class="cancel-feature-image-preference hide-if-no-js button-cancel">Cancel</a>';
		$html .= '</p>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Enqueue JavaScript and CSS resources for admin functionality as needed
	 */
	public function admin_enqueue_scripts() {
		global $post, $pagenow;

		if ( $pagenow == 'post.php' && $post && post_type_supports( $post->post_type, 'flexible-feature-image' ) ) {
			$feature_image_preference = get_post_meta( $post->ID, 'post_feature_image_preference', true );

            ?><script>
                (function($) {
                    function feature_image_preference_description( feature_image_preference ) {
                        if ( 'string' !== typeof feature_image_preference ) {
                            return 'No restriction';
                        }

                        if ( 'poster' === feature_image_preference ) {
                            return 'Poster';
                        } else if ( 'top' === feature_image_preference ) {
                            return 'Top';
                        } else if ( 'inline' === feature_image_preference ) {
                            return 'Inline';
                        }

                        return 'None';
                    }

                    $(function() {
                        // Implement the postbox feature
                        var feature_image_preference_div = $('#featureimagepreferencediv');
                        feature_image_preference_div.html(<?php echo json_encode( self::touch_feature_image_preference( 1, $feature_image_preference ) ) ?>);

                        // Show the radio buttons
                        $("a[href='#edit_feature_image_preference']").click(function () {
                            feature_image_preference_div.slideDown();
                            if (true !== feature_image_preference_div.data('populated')) {
                                feature_image_preference_div.find('input').filter('[name=fip_status]').filter('[value="' + $('#hidden_feature_image_preference').val() + '"]').attr('checked', 'checked');
                                feature_image_preference_div.data('populated', true);
                            }
                        });

                        // Cancel button
                        feature_image_preference_div.find('.cancel-feature-image-preference').click(function () {
                            feature_image_preference_div.find('input').filter('[name=fip_status]').filter('[value="' + $('#hidden_feature_image_preference').val() + '"]').attr('checked', 'checked');
                            feature_image_preference_div.slideUp();
                        });

                        // Update hidden fields
                        feature_image_preference_div.find('.save-feature-image-preference').click(function () {
                            var checked_option = feature_image_preference_div.find('input').filter('[name=fip_status]').filter(':checked');
                            $('#hidden_feature_image_preference').val(checked_option.val());
                            $('#featureimagepreferencediv').slideUp();
                            $('#feature-image-preference-value').find('b').text(checked_option.parent().text());
                        });
                    });
                })(jQuery);
            </script><?php
		}
	}

	/**
	 * On admin UI post save, update the feature image preference postmeta
	 *
	 * @param int $post_id Post ID
	 */
	public function save_post( $post_id ) {
		$post = get_post( $post_id );
		if ( post_type_supports( $post->post_type, 'flexible-feature-image' ) ) {
			// Check the user's permissions.
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Verify that the form nonce is valid.
			if ( ! wp_verify_nonce( filter_input( INPUT_POST, '__feature_image_preference_nonce' ), 'feature_image_preference_meta_boxes' ) ) {
				return;
			}

			delete_post_meta( $post_id, 'post_feature_image_preference' );

			if ( isset( $_POST['fip_status'] ) ) {
				$feature_image_preference = self::sanitize_feature_image_preference( $_POST['fip_status'] );
				if ( '' !== $feature_image_preference ) {
					add_post_meta( $post_id, 'post_feature_image_preference', $feature_image_preference );
				}
			}
		} else {
			// Clean up any post expiration data that might already exist, in case the post support changed
			delete_post_meta( $post_id, 'post_feature_image_preference' );
			return;
		}
	}

}

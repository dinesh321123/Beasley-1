<?php
/**
 * Sets up settings page and shortcode for Audience.io
 */

namespace Bbgi\Integration;

class Audience extends \Bbgi\Module {

	/**
	 * Registers this module.
	 *
	 * @access public
	 */
	public function register() {
		add_action ( 'after_wp_tiny_mce', $this( 'audience_tinymce_extra_vars' ) );
		add_action( 'admin_enqueue_scripts', $this( 'audience_tinymce_enqueue_scripts' ) );
		add_filter( 'mce_external_plugins', $this( 'audience_add_buttons' ) );
		add_filter( 'mce_buttons', $this( 'audience_register_buttons' ) );

		// add shortcodes
		add_shortcode( 'audience-promo', $this( 'audience_render_shortcode' ) );
	}

	public function audience_tinymce_enqueue_scripts() {
		$postfix = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script(
			'audience-tinymce-scripts',
			plugins_url( 'assets/js/audience-tinymce'.$postfix.'.js', __FILE__ ),
			array('jquery'),
			'1.0.0',
			true
		);
	}

	public function audience_add_buttons( $plugin_array ) {
		$plugin_array['audience-io-button'] = plugins_url( 'assets/js/audience-tinymce.js', __FILE__ );
        return $plugin_array;
    }

	public function audience_register_buttons( $buttons ) {
		array_push( $buttons, 'audience-io-button' );
        return $buttons;
    }

	public function audience_tinymce_extra_vars() {
		?>
		<script type="text/javascript">

			var tinyMCE_object = <?php echo json_encode(
			array(
				'button_name' => esc_html__('', 'mythemeslug'),
				'button_title' => esc_html__('Audience IO shortcode', 'mythemeslug'),
				'image' => plugins_url( 'assets/image/audience-icone.png', __FILE__ ),
			));
		 ?>;
		</script>
		<style type="text/css">
			button#mceu_13-button {
			    padding: 3px;
			}
		</style>
 		<?php
 	}

	/**
	 * Renders audience-promo shortcode.
	 *
	 * @access public
	 * @param array $attributes Array of shortcode arguments.
	 * @return string Shortcode markup.
	 */
	public function audience_render_shortcode( $atts ) {
		$attributes = shortcode_atts( array(
			'widget-id' => '',
			'widget-type' => '',
			'widget-url' => ''
		), $atts, 'audience-promo' );

		if ( empty( $attributes['widget-id'] ) && $attributes['widget-url']  ) {
			return '';
		}

		if ( ! empty($attributes['widget-url']) ) {
			$pattern = "/#(?<type>[a-z]+)\-*(?<subtype>[a-z]*)\/(?<widgetid>[0-9]+)/";
			preg_match($pattern, $attributes['widget-url'], $urimatches);

			$attributes['widget-id'] = $urimatches['widgetid'];
			$attributes['widget-type'] = $urimatches['type'];
		}

		$embed = sprintf(
			'<div class="audience-embed" data-widgetid="%s" data-type="%s"></div>',
			esc_attr( $attributes['widget-id']),
			esc_attr( $attributes['widget-type']));

		return apply_filters( 'audience_embed_html', $embed, $attributes );
	}

}

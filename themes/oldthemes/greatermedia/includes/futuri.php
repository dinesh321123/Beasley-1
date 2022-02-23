<?php
/**
 * Add Futuri Engage shortcode to allow Futuri's engage product to be embedded.
 */
class BeasleyFuturi {

	public static function init()
	{
		add_shortcode( 'futuriengage', array( __CLASS__, 'handle_shortcode' ) );
	}

	public static function handle_shortcode( $atts, $content = null )
	{
		$atts = shortcode_atts( array(
			'key' => '',
		), $atts );

		$uniqid = uniqid();

		ob_start();

		?>
		<div id="futuri-engage-<?php echo $uniqid ?>" class="futuri futuri-engage">
			<?php if ( ! empty( $atts['key'] ) ) { ?>
			<div id="ldr_widget"></div>

			<script type="text/javascript">
				(function ($, document) {
					var $document = $(document),
						__ready;

					__ready = function() {
						$('#ldr_widget').each(function() {
							var $futuriembed = $(this),
								$script = $(document.createElement('script'));

							$script.attr('src', '//widget.ldrhub.com/embed.php?key=<?php echo esc_attr( $atts['key'] ) ?>');

							$futuriembed.after($script);
						});
					};

					$document.bind('pjax:end', __ready).ready(__ready);
				})(jQuery, document);
			</script>

			<?php } else { ?>
			<!-- Futuri Engage shortcode requires that a key be defined. -->
			<?php } ?>
		</div>
		<?php

		return ob_get_clean();
	}

}

BeasleyFuturi::init();

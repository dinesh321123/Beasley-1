<?php

namespace Greater_Media\Auction_Nudge;

class ShortcodeHandler
{
	public function __construct()
	{
		add_shortcode( 'auction', array( $this, 'handle_shortcode' ) );
	}

	public function handle_shortcode( $atts, $content = null )
	{
		$atts = shortcode_atts( array(
			'username' => '',
      'maxentries' => '100',
      'theme' => 'responsive',
      'category_list' => true,
      'img_size' => '80',
      'keyword' => '',
      'sort_order' => '',
		), $atts );

		$uniqid = uniqid();

    if ( empty( $atts['username'] ) ) {
      return;
    }

    // Filter inputs to known API parameters
    $atts['maxentries'] = filter_var( $atts['maxentries'], FILTER_VALIDATE_INT, array( 'options' => array( 'default' => 100, 'min_range' => 1, 'max_range' => 100 ) ) );

    $atts['theme'] = filter_var( $atts['theme'], FILTER_VALIDATE_REGEXP, array( 'options' => array( 'default' => 'responsive', 'regexp' => '/^responsive|columns|simple_list/' ) ) );

    $atts['category_list'] = filter_var( $atts['category_list'], FILTER_VALIDATE_BOOLEAN );

    $atts['img_size'] = filter_var( $atts['img_size'], FILTER_VALIDATE_INT, array( 'options' => array( 'default' => 80, 'min_range' => 0, 'max_range' => 300 ) ) );

    $atts['sort_order'] = filter_var( $atts['sort_order'], FILTER_VALIDATE_REGEXP, array( 'options' => array( 'default' => '', 'regexp' => '/^StartTimeNewest|PricePlusShippingHighest|PricePlusShippingLowest|BestMatch/' ) ) );

		ob_start();

    // Render widget
		?>
    <script type="text/javascript" src="//www.auctionnudge.com/item_build/js/SellerID/<?php echo esc_attr( $atts['username'] ); ?>/siteid/0/theme/<?php echo esc_attr( $atts['theme'] ); ?>/MaxEntries/<?php echo esc_attr( $atts['maxentries'] ); ?><?php if ( $atts['category_list'] ) { echo '/cats_output/dropdown'; } ?>/page/init/show_logo/1/img_size/<?php echo esc_attr( $atts['img_size'] ); ?>/blank/1<?php if( ! empty( $atts['keyword'] ) ) { echo '/keyword/' . esc_attr( $atts['keyword'] ); } ?><?php if( ! empty( $atts['sort_order'] ) ) { echo '/sortOrder/' . esc_attr( $atts['sort_order'] ); } ?>"></script>
    <div id="auction-nudge-items" class="auction-nudge"></div>

		<?php

		return ob_get_clean();
	}
}

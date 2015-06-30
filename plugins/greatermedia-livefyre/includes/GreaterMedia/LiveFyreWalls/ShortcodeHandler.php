<?php

namespace GreaterMedia\LiveFyreWalls;

class ShortcodeHandler {

	public function __construct() {
		add_shortcode( 'livefyre-wall', array( $this, 'handle_shortcode' ) );
	}

	public function handle_shortcode( $atts, $content = null ) {
		ob_start();

		if ( ! empty( $atts['order'] ) && strtolower( $atts['order'] ) == 'asc' ) {
			$this->_render_asc_wall( $atts );
		} else {
			$this->_render_desc_wall( $atts );
		}

		return ob_get_clean();
	}

	private function _render_asc_wall( $atts ) {
		$uniqid = uniqid();

		$atts = shortcode_atts( array(
			'network_id' => '',
			'site_id'    => '',
			'article_id' => '',
			'initial'    => 10,
			'columns'    => '',
			'min_content_width' => 350,
			'show_more' => 10,
			'ad_interval' => 5,
			'ad_inventory_id' => ''
		), $atts );

		?><div id="wall-<?php echo esc_attr( $uniqid ); ?>" class="livefyre-mediawall"></div>
		<script src="//cdn.livefyre.com/Livefyre.js"></script>
		<script type="text/javascript">// <![CDATA[
		if ((typeof Livefyre) !== "undefined") {
			Livefyre.require(['streamhub-wall#3', 'streamhub-sdk#2'], function(LiveMediaWall, SDK) {
				window.SDK_<?php echo esc_attr( $uniqid ); ?> = SDK;

				var collection = new SDK.Collection({
					"network": "<?php echo esc_js( $atts['network_id'] ); ?>",
					"siteId": "<?php echo esc_js( $atts['site_id'] ); ?>",
					"articleId": "<?php echo esc_js( $atts['article_id'] ); ?>"
				});

				window.wall_<?php echo esc_attr( $uniqid ); ?> = new LiveMediaWall({
					el: document.getElementById("wall-<?php echo esc_attr( $uniqid ); ?>"),
					initial: <?php echo esc_js( $atts['initial'] ); ?>,
					<?php if ( empty( $atts['ad_inventory_id'] ) && !empty($atts['columns'])) { ?>
					columns: <?php echo esc_js( $atts['columns'] ); ?>,
					<?php } ?>
					<?php if ( empty($atts['columns']) ) { ?>
					minContentWidth: <?php echo esc_js( $atts['min_content_width'] ); ?>,
					<?php } ?>
					showMore: <?php echo esc_js( $atts['show_more'] ); ?>,
					postButton: false
				});

				var createdAtDescending = window.wall_<?php echo esc_attr( $uniqid ); ?>._wallView.comparator;
				var createdAtAscending = function (a, b) {
					return -1 * createdAtDescending.call(this, a, b);
				};
				patchWallViewWithComparator(window.wall_<?php echo esc_attr( $uniqid ); ?>._wallView, createdAtAscending);

				collection.createArchive({
					comparator: 'CREATED_AT_ASCENDING'
				}).pipe(window.wall_<?php echo esc_attr( $uniqid ); ?>.more);

				function patchWallViewWithComparator(wallView, comparator) {
					// patch WallView
					wallView.comparator = comparator;
					// patch current columnViews
					wallView._columnViews.forEach(function (columnView) {
						columnView.comparator = comparator;
					});
					// patch future columnViews (e.g. after relayout)
					wallView._createColumnView = (function (ogCreateColumnView) {
						return function newCreateColumnView() {
							var columnView = ogCreateColumnView.apply(this, arguments);
							columnView.comparator = comparator;
							return columnView;
						}
					}(wallView._createColumnView));
				}

				<?php if ( ! empty( $atts['ad_inventory_id'] )) { ?>

				var count = 0;
				var injectingAds = false;
				var lastInsertIndex = 0;
				var adIntervalCount = <?php echo esc_js( $atts['ad_interval'] ); ?>;
				var openxInventoryId = "<?php echo esc_js( $atts['ad_inventory_id'] ); ?>";

				function generateAdTag(){
				     var cacheBuster = (new Date()).getTime();
				     return '<iframe style="margin:0 auto;display:block;" src="http://ox-d.greatermedia.com/w/1.0/afr?auid=' + openxInventoryId + '&cb=' + cacheBuster + '" frameborder="0" scrolling="no" width="300" height="250"><a href="http://ox-d.greatermedia.com/w/1.0/rc?cb=' + cacheBuster + '" ><img src="http://ox-d.greatermedia.com/w/1.0/ai?auid=' + openxInventoryId + '&cb=' + cacheBuster + '" border="0" alt=""></a></iframe>';
				}

				function injectAds() {
				        var currentNodes = window.wall_<?php echo esc_attr( $uniqid ); ?>._wallView.$el.find('article');
				        var lowestAdIndex = currentNodes.length;

				        if (currentNodes.length > 0){
				            for (var n = 0; n < currentNodes.length; n++){
				                if (currentNodes[n].innerHTML.indexOf('ox-d.greatermedia.com') != -1){
				                    lowestAdIndex = n;
				                    break;
				                }
				            }

				            if ((lowestAdIndex - adIntervalCount) > 0){
				                for (n = lowestAdIndex - adIntervalCount; n >= 0; n -= adIntervalCount){
				                    window.wall_<?php echo esc_attr( $uniqid ); ?>._wallView.add(new window.SDK_<?php echo esc_attr( $uniqid ); ?>.Content({body: generateAdTag()}), n);
				                }
				            }

				            currentNodes = window.wall_<?php echo esc_attr( $uniqid ); ?>._wallView.$el.find('article');
				            var highestAdIndex = 0;

				            for (var n = currentNodes.length - 1; n > 0; n--){
				                if (currentNodes[n].innerHTML.indexOf('ox-d.greatermedia.com') != -1){
				                    highestAdIndex = n;
				                    break;
				                }
				            }

				            if ((highestAdIndex + adIntervalCount) < currentNodes.length){
				                for (n = highestAdIndex + adIntervalCount; n < currentNodes.length; n += adIntervalCount){
				                    window.wall_<?php echo esc_attr( $uniqid ); ?>._wallView.add(new window.SDK_<?php echo esc_attr( $uniqid ); ?>.Content({body: generateAdTag()}), n);
				                }
				            }

				        }

				}

				setInterval(injectAds, 5000);

				<?php } ?>
			});
		}
		// ]]></script><?php
	}

	private function _render_desc_wall( $atts ) {
		$uniqid = uniqid();

		$atts = shortcode_atts( array(
			'network_id' => '',
			'site_id'    => '',
			'article_id' => '',
			'initial'    => 10,
			'columns'    => '',
			'min_content_width' => 350,
			'show_more' => 10,
			'ad_interval' => 5,
			'ad_inventory_id' => ''
		), $atts );

		?><div id="wall-<?php echo esc_attr( $uniqid ); ?>" class="livefyre-mediawall"></div>
		<script src="//cdn.livefyre.com/Livefyre.js"></script>
		<script>
			Livefyre.require(['streamhub-wall#3', 'streamhub-sdk#2'], function(LiveMediaWall, SDK) {
				window.SDK_<?php echo esc_attr( $uniqid ); ?> = SDK;
				window.wall_<?php echo esc_attr( $uniqid ); ?> = new LiveMediaWall({
					el: document.getElementById("wall-<?php echo esc_attr( $uniqid ); ?>"),
					initial: <?php echo esc_js( $atts['initial'] ); ?>,
					<?php if ( empty( $atts['ad_inventory_id'] ) && !empty($atts['columns'])) { ?>
					columns: <?php echo esc_js( $atts['columns'] ); ?>,
					<?php } ?>
					<?php if ( empty($atts['columns']) ) { ?>
					minContentWidth: <?php echo esc_js( $atts['min_content_width'] ); ?>,
					<?php } ?>
					showMore: <?php echo esc_js( $atts['show_more'] ); ?>,
					collection: new (SDK.Collection)({
						"network": "<?php echo esc_js( $atts['network_id'] ); ?>",
						"siteId": "<?php echo esc_js( $atts['site_id'] ); ?>",
						"articleId": "<?php echo esc_js( $atts['article_id'] ); ?>"
					}),
					postButton: false
				});

				<?php if ( ! empty( $atts['ad_inventory_id'] )) { ?>

				var count = 0;
				var injectingAds = false;
				var lastInsertIndex = 0;
				var adIntervalCount = <?php echo esc_js( $atts['ad_interval'] ); ?>;
				var openxInventoryId = "<?php echo esc_js( $atts['ad_inventory_id'] ); ?>";

				function generateAdTag(){
				     var cacheBuster = (new Date()).getTime();
				     return '<iframe style="margin:0 auto;display:block;" src="http://ox-d.greatermedia.com/w/1.0/afr?auid=' + openxInventoryId + '&cb=' + cacheBuster + '" frameborder="0" scrolling="no" width="300" height="250"><a href="http://ox-d.greatermedia.com/w/1.0/rc?cb=' + cacheBuster + '" ><img src="http://ox-d.greatermedia.com/w/1.0/ai?auid=' + openxInventoryId + '&cb=' + cacheBuster + '" border="0" alt=""></a></iframe>';
				}

				function injectAds() {
				        var currentNodes = window.wall_<?php echo esc_attr( $uniqid ); ?>._wallView.$el.find('article');
				        var lowestAdIndex = currentNodes.length;

				        if (currentNodes.length > 0){
				            for (var n = 0; n < currentNodes.length; n++){
				                if (currentNodes[n].innerHTML.indexOf('ox-d.greatermedia.com') != -1){
				                    lowestAdIndex = n;
				                    break;
				                }
				            }

				            if ((lowestAdIndex - adIntervalCount) > 0){
				                for (n = lowestAdIndex - adIntervalCount; n >= 0; n -= adIntervalCount){
				                    window.wall_<?php echo esc_attr( $uniqid ); ?>._wallView.add(new window.SDK_<?php echo esc_attr( $uniqid ); ?>.Content({body: generateAdTag()}), n);
				                }
				            }

				            currentNodes = window.wall_<?php echo esc_attr( $uniqid ); ?>._wallView.$el.find('article');
				            var highestAdIndex = 0;

				            for (var n = currentNodes.length - 1; n > 0; n--){
				                if (currentNodes[n].innerHTML.indexOf('ox-d.greatermedia.com') != -1){
				                    highestAdIndex = n;
				                    break;
				                }
				            }

				            if ((highestAdIndex + adIntervalCount) < currentNodes.length){
				                for (n = highestAdIndex + adIntervalCount; n < currentNodes.length; n += adIntervalCount){
				                    window.wall_<?php echo esc_attr( $uniqid ); ?>._wallView.add(new window.SDK_<?php echo esc_attr( $uniqid ); ?>.Content({body: generateAdTag()}), n);
				                }
				            }

				        }

				}

				setInterval(injectAds, 5000);

				<?php } ?>
			});
		</script><?php
	}

}

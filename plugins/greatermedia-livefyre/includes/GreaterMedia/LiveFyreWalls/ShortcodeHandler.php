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
			'initial'    => 50,
			'columns'    => 3,
		), $atts );

		?><div id="wall-<?php echo esc_attr( $uniqid ); ?>"></div>
		<script src="//cdn.livefyre.com/Livefyre.js"></script>
		<script type="text/javascript">// <![CDATA[
		if ((typeof Livefyre) !== "undefined") {
			Livefyre.require(['streamhub-sdk#2', 'streamhub-wall#3'], function(SDK, LiveMediaWall) {
				var collection = new SDK.Collection({
					"network": "<?php echo esc_js( $atts['network_id'] ); ?>",
					"siteId": "<?php echo esc_js( $atts['site_id'] ); ?>",
					"articleId": "<?php echo esc_js( $atts['article_id'] ); ?>"
				});

				var wall = window.wall = new LiveMediaWall({
					el: document.getElementById("wall-<?php echo esc_attr( $uniqid ); ?>"),
					columns: <?php echo esc_js( $atts['columns'] ); ?>,
					initial: <?php echo esc_js( $atts['initial'] ); ?>,
					postButton: false
				});
				var createdAtDescending = wall._wallView.comparator;
				var createdAtAscending = function (a, b) {
					return -1 * createdAtDescending.call(this, a, b);
				};
				patchWallViewWithComparator(wall._wallView, createdAtAscending);

				collection.createArchive({
					comparator: 'CREATED_AT_ASCENDING'
				}).pipe(wall.more);

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
			'columns'    => 2,
		), $atts );

		?><div id="wall-<?php echo esc_attr( $uniqid ); ?>"></div>
		<script src="//cdn.livefyre.com/Livefyre.js"></script>
		<script>
			Livefyre.require(['streamhub-wall#3', 'streamhub-sdk#2'], function(LiveMediaWall, SDK) {
				window.wall_<?php echo esc_attr( $uniqid ); ?> = new LiveMediaWall({
					el: document.getElementById("wall-<?php echo esc_attr( $uniqid ); ?>"),
					initial: <?php echo esc_js( $atts['initial'] ); ?>,
					columns: <?php echo esc_js( $atts['columns'] ); ?>,
					collection: new (SDK.Collection)({
						"network": "<?php echo esc_js( $atts['network_id'] ); ?>",
						"siteId": "<?php echo esc_js( $atts['site_id'] ); ?>",
						"articleId": "<?php echo esc_js( $atts['article_id'] ); ?>"
					})
				});
			});
		</script><?php
	}

}

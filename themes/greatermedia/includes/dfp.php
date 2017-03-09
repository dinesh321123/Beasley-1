<?php

function greatermedia_init_dfp_settings( $group, $page ) {
	add_settings_section( 'greatermedia_dfp_settings', 'DoubleClick for Publishers', 'greatermedia_render_dfp_settings_section', $page );

	register_setting( $group, 'dfp_network_code', 'sanitize_text_field' );
	register_setting( $group, 'dfp_targeting_market', 'sanitize_text_field' );
	register_setting( $group, 'dfp_targeting_genre', 'sanitize_text_field' );
	register_setting( $group, 'dfp_targeting_ctest', 'sanitize_text_field' );
	register_setting( $group, 'dfp_ad_leaderboard_pos1', 'sanitize_text_field' );
	register_setting( $group, 'dfp_ad_leaderboard_pos2', 'sanitize_text_field' );
	register_setting( $group, 'dfp_ad_incontent_pos1', 'sanitize_text_field' );
	register_setting( $group, 'dfp_ad_incontent_pos2', 'sanitize_text_field' );
	register_setting( $group, 'dfp_ad_inlist_infinite', 'sanitize_text_field' );
	register_setting( $group, 'dfp_ad_interstitial', 'sanitize_text_field' );
	register_setting( $group, 'dfp_ad_wallpaper', 'sanitize_text_field' );
	register_setting( $group, 'dfp_ad_playersponsorship', 'sanitize_text_field' );
}
add_action( 'greatermedia-settings-register-settings', 'greatermedia_init_dfp_settings', 10, 2 );

function greatermedia_render_dfp_settings_section() {
	echo '<div class="gmr__option">';
		echo '<label for="dfp_network_code" class="gmr__option--label">Network Code</label>';
		echo '<input type="text" class="gmr__option--input" name="dfp_network_code" id="dfp_network_code" value="', esc_attr( get_option( 'dfp_network_code' ) ), '">';
		echo '<div class="gmr-option__field--desc"></div>';
	echo '</div>';

	$settings = array(
		'dfp_targeting_market' => 'Market Targeting Value',
		'dfp_targeting_genre'  => 'Genre Targeting Value',
		'dfp_targeting_ctest'  => 'CTest Targeting Value',
	);

	echo '<hr>';
	echo '<h3>Global Targeting</h3>';

	foreach ( $settings as $key => $label ) {
		echo '<div class="gmr__option">';
			echo '<label for="', esc_attr( $key ), '" class="gmr__option--label">', esc_html( $label ), '</label>';
			echo '<input type="text" class="gmr__option--input" name="', esc_attr( $key ), '" id="', esc_attr( $key ), '" value="', esc_attr( get_option( $key ) ), '">';
			echo '<div class="gmr-option__field--desc"></div>';
		echo '</div>';
	}

	$settings = array(
		'dfp_ad_leaderboard_pos1'  => 'Header Leaderboard',
		'dfp_ad_leaderboard_pos2'  => 'Footer Leaderboard',
		'dfp_ad_incontent_pos1'    => 'In Content (pos1)',
		'dfp_ad_incontent_pos2'    => 'In Content (pos2)',
		'dfp_ad_inlist_infinite'   => 'In List (infinite)',
		'dfp_ad_interstitial'      => 'Out-of-Page',
		'dfp_ad_wallpaper'         => 'Wallpaper',
		'dfp_ad_playersponsorship' => 'Player Sponsorship',
	);

	echo '<hr>';
	echo '<h3>Unit Codes</h3>';

	foreach ( $settings as $key => $label ) {
		echo '<div class="gmr__option">';
			echo '<label for="', esc_attr( $key ), '" class="gmr__option--label">', esc_html( $label ), '</label>';
			echo '<input type="text" class="gmr__option--input" name="', esc_attr( $key ), '" id="', esc_attr( $key ), '" value="', esc_attr( get_option( $key ) ), '">';
			echo '<div class="gmr-option__field--desc"></div>';
		echo '</div>';
	}
}

function greatermedia_is_dfp_active() {
	$network_id = trim( get_option( 'dfp_network_code' ) );
	return ! empty( $network_id );
}

function greatermedia_dfp_head() {
	$network_id = trim( get_option( 'dfp_network_code' ) );
	if ( empty( $network_id ) ) {
		return;
	}

	$dfp_ad_interstitial = get_option( 'dfp_ad_interstitial' );
	$dfp_ad_wallpaper = get_option( 'dfp_ad_wallpaper' );
	$dfp_ad_playersponsorship = get_option( 'dfp_ad_playersponsorship' );

	?><script async="async" src="https://www.googletagservices.com/tag/js/gpt.js"></script>
	<script>
		var googletag = googletag || {};
		googletag.cmd = googletag.cmd || [];

		googletag.beasley = googletag.beasley || {};
		googletag.beasley.targeting = googletag.beasley.targeting || [];

		googletag.cmd.push(function() {
			<?php if ( ! empty( $dfp_ad_interstitial ) || ! empty( $dfp_ad_wallpaper ) ) : ?>
			var sizeMapping = googletag.sizeMapping()
				.addSize([1024, 200], [[1, 1]])
				.addSize([0, 0], [])
				.build();
			<?php endif; ?>

			<?php if ( ! empty( $dfp_ad_wallpaper ) ) : ?>
			googletag.defineOutOfPageSlot('/<?php echo esc_js( $network_id ); ?>/<?php echo esc_js( $dfp_ad_wallpaper ); ?>', 'div-gpt-ad-1487289548015-0').defineSizeMapping(sizeMapping).addService(googletag.pubads());
			<?php endif; ?>

			<?php if ( ! empty( $dfp_ad_interstitial ) ) : ?>
			googletag.defineOutOfPageSlot('/<?php echo esc_js( $network_id ); ?>/<?php echo esc_js( $dfp_ad_interstitial ); ?>', 'div-gpt-ad-1484200509775-3').defineSizeMapping(sizeMapping).addService(googletag.pubads());
			<?php endif; ?>

			<?php if ( ! empty( $dfp_ad_playersponsorship ) ) : ?>
			googletag.defineSlot('/<?php echo esc_js( $network_id ); ?>/<?php echo esc_js( $dfp_ad_playersponsorship ); ?>', ['fluid'], 'div-gpt-ad-1487117572008-0').addService(googletag.pubads());
			<?php endif; ?>

			googletag.pubads().enableSingleRequest();
			googletag.pubads().collapseEmptyDivs(true);

			googletag.enableServices();
		});
	</script><?php
}
add_action( 'wp_head', 'greatermedia_dfp_head', 7 );

function greatermedia_dfp_footer() {
	$network_id = trim( get_option( 'dfp_network_code' ) );
	if ( empty( $network_id ) ) {
		return;
	}

	$unit_codes = array(
		'dfp_ad_leaderboard_pos1'  => get_option( 'dfp_ad_leaderboard_pos1' ),
		'dfp_ad_leaderboard_pos2'  => get_option( 'dfp_ad_leaderboard_pos2' ),
		'dfp_ad_incontent_pos1'    => get_option( 'dfp_ad_incontent_pos1' ),
		'dfp_ad_incontent_pos2'    => get_option( 'dfp_ad_incontent_pos2' ),
		'dfp_ad_right_rail_pos1'   => get_option( 'dfp_ad_right_rail_pos1' ),
		'dfp_ad_right_rail_pos2'   => get_option( 'dfp_ad_right_rail_pos2' ),
		'dfp_ad_inlist_infinite'   => get_option( 'dfp_ad_inlist_infinite' ),
	);

	$sizes = array(
		'dfp_ad_leaderboard_pos1'  => array( array( 728, 90 ), array( 970, 90 ), array( 970, 66 ), array( 320, 50 ), array( 320, 100 ) ),
		'dfp_ad_leaderboard_pos2'  => array( array( 728, 90 ), array( 970, 90 ), array( 320, 50 ), array( 320, 100 ) ),
		'dfp_ad_incontent_pos1'    => array( array( 300, 250 ) ),
		'dfp_ad_incontent_pos2'    => array( array( 300, 250 ) ),
		'dfp_ad_inlist_infinite'   => array( array( 300, 250 ) ),
		'dfp_ad_right_rail_pos1'   => array( array( 300, 600 ), array( 300, 250 ) ),
		'dfp_ad_right_rail_pos2'   => array( array( 300, 600 ), array( 300, 250 ) ),
	);

	?><script type="text/javascript">
		(function($, googletag) {
			var slotsIndex = 0, __ready, __cleanup;

			__cleanup = function() {
				var slots = [];

				$('.main [data-dfp-slot] .gmr-ad').each(function() {
					var slot = $(this).data('slot');

					if (slot) {
						slots.push(slot);
					}
				});

				if (slots.length > 0) {
					googletag.destroySlots(slots);
				}

				googletag.pubads().clearTargeting();
			};

			__ready = function() {
				var unitCodes = <?php echo json_encode( $unit_codes ); ?>,
					sizes = <?php echo json_encode( $sizes ) ?>,
					slots = [],
					unitCode;

				$('[data-dfp-slot]:empty').each(function() {
					var $this = $(this),
						slotType = $this.attr('data-dfp-slot'),
						slotSizes = JSON.parse($this.attr('data-sizes')),
						targeting = JSON.parse($this.attr('data-targeting')),
						id;

					unitCode = unitCodes[slotType] || slotType;
					if (unitCode) {
						id = 'dfp-slot-' + ++slotsIndex;
						$this.html('<div id="' + id + '" class="gmr-ad"></div>');

						slots.push([
							'/<?php echo esc_js( $network_id ); ?>/' + unitCode,
							slotSizes || sizes[slotType],
							id,
							targeting,
							slotType
						]);
					}
				});

				googletag.cmd.push(function() {
					var i, j, slot, targeting, sizeMapping;

					for (i in slots) {
						slot = googletag.defineSlot(slots[i][0], slots[i][1], slots[i][2]);

						sizeMapping = false;
						if ('dfp_ad_leaderboard_pos1' == slots[i][4]) {
							sizeMapping = googletag.sizeMapping()
								.addSize([1024, 200], [[970, 66], [970, 90], [728, 90]])
								.addSize([768, 200], [728, 90])
								.addSize([0, 0], [[320, 50], [320, 100]])
								.build();
						} else if ('dfp_ad_leaderboard_pos2' == slots[i][4]) {
							sizeMapping = googletag.sizeMapping()
								.addSize([1024, 200], [[970, 90], [728, 90]])
								.addSize([768, 200], [728, 90])
								.addSize([0, 0], [[320, 50], [320, 100]])
								.build();
						} else if ('dfp_ad_incontent_pos1' == slots[i][4] || 'dfp_ad_incontent_pos2' == slots[i][4]) {
							sizeMapping = googletag.sizeMapping()
								.addSize([768, 200], [])
								.addSize([0, 0], [300, 250])
								.build();
						} else if ($(document.getElementById(slots[i][2])).parents('.widget_gmr-dfp-widget').length > 0) {
							sizeMapping = googletag.sizeMapping()
								.addSize([768, 200], slots[i][1])
								.addSize([0, 0], [])
								.build();
						}

						if (sizeMapping) {
							slot.defineSizeMapping(sizeMapping);
						}

						for (j in slots[i][3]) {
							slot.setTargeting(slots[i][3][j][0], slots[i][3][j][1]);
						}

						slot.addService(googletag.pubads());

						$(document.getElementById(slots[i][2])).data('slot', slot);
					}

					while ((targeting = googletag.beasley.targeting.pop())) {
						googletag.pubads().setTargeting(targeting[0], targeting[1]);
					}
				});

				googletag.cmd.push(function() {
					for (var i in slots) {
						googletag.display(slots[i][2]);
					}
				});
			};

			$(document)
				.on('pjax:start', __cleanup)
				.on('pjax:end gmr_lazy_load_end', __ready)
				.ready(__ready);
		})(jQuery, googletag);
	</script><?php
}
add_action( 'wp_footer', 'greatermedia_dfp_footer', 1000 );

function greatermedia_display_dfp_slot( $slot, $sizes = false, $single_targeting = array(), $echo = true, $class = '' ) {
	static $targeting = null;

	if ( ! greatermedia_is_dfp_active() ) {
		return;
	}

	$render_targeting = false;
	if ( is_null( $targeting ) && 'dfp_ad_playersponsorship' != $slot ) {
		$render_targeting = true;
		$targeting = array(
			array( 'cdomain', parse_url( home_url( '/' ), PHP_URL_HOST ) ),
			array( 'cpage', untrailingslashit( current( explode( '?', $_SERVER['REQUEST_URI'], 2 ) ) ) ), // strip query part and trailing slash of the current uri
			array( 'ctest', trim( get_option( 'dfp_targeting_ctest' ) ) ),
			array( 'genre', trim( get_option( 'dfp_targeting_genre' ) ) ),
			array( 'market', trim( get_option( 'dfp_targeting_market' ) ) ),
		);

		if ( is_singular() ) {
			$post_id = get_queried_object_id();
			$targeting[] = array( 'cpostid', $post_id );

			if ( class_exists( 'ShowsCPT' ) && defined( 'ShowsCPT::SHOW_TAXONOMY' ) ) {
				$terms = get_the_terms( $post_id, ShowsCPT::SHOW_TAXONOMY );
				if ( $terms && ! is_wp_error( $terms ) ) {
					$targeting[] = array( 'shows', implode( ",", wp_list_pluck( $terms, 'slug' ) ) );
				}
			}

			if ( class_exists( 'GMP_CPT' ) && defined( 'GMP_CPT::PODCAST_POST_TYPE' ) && defined( 'GMP_CPT::EPISODE_POST_TYPE' ) ) {
				$podcast = false;

				$post = get_post( $post_id );
				$post_type = get_post_type( $post );
				if ( GMP_CPT::PODCAST_POST_TYPE == $post_type ) {
					$podcast = $post->post_name;
				}

				if ( GMP_CPT::EPISODE_POST_TYPE == $post_type ) {
					$parent_podcast_id = wp_get_post_parent_id( $post );
					if ( $parent_podcast_id && ! is_wp_error( $parent_podcast_id ) ) {
						$parent_podcast = get_post( $parent_podcast_id );
						$podcast = $parent_podcast->post_name;
					}
				}

				if ( $podcast ) {
					$targeting[] = array( 'podcast', $podcast );
				}
			}

			$categories = wp_get_post_categories( get_queried_object_id() );
			if ( ! empty( $categories ) ) {
				$categories = array_filter( array_map( 'get_category', $categories ) );
				$categories = wp_list_pluck( $categories, 'slug' );
				$targeting[] = array( 'category', implode( ',', $categories ) );
			}
		}
	}

	$remnant_slots = array(
		'dfp_ad_leaderboard_pos1',
		'dfp_ad_leaderboard_pos2',
		'dfp_ad_right_rail_pos1',
		'dfp_ad_right_rail_pos2',
		'dfp_ad_incontent_pos1',
		'dfp_ad_incontent_pos2',
	);

	if ( in_array( $slot, $remnant_slots ) ) {
		$single_targeting[] = array( 'remnant', 'yes' );
	}

	$html = '';
	if ( $render_targeting ) {
		$html .= '<script type="text/javascript">googletag.beasley.targeting=' . json_encode( $targeting ) . ';</script>';
	}

	$html .= '<div class="' . $class . '" data-dfp-slot="' . esc_attr( $slot ) . '" data-sizes="' . esc_attr( json_encode( $sizes ) ) . '" data-targeting="' . esc_attr( json_encode( $single_targeting ) ) . '"></div>';

	if ( $echo ) {
		echo $html;
	}

	return $html;
}
add_action( 'dfp_tag', 'greatermedia_display_dfp_slot', 10, 3 );

function greatermedia_display_dfp_playersponsorship() {
	$network_id = trim( get_option( 'dfp_network_code' ) );
	if ( empty( $network_id ) ) {
		return;
	}

	$dfp_ad_playersponsorship = get_option( 'dfp_ad_playersponsorship' );
	if ( $dfp_ad_playersponsorship ) :
		?><!-- /<?php echo esc_html( $network_id ); ?>/<?php echo esc_html( $dfp_ad_playersponsorship ); ?> -->
		<div id='div-gpt-ad-1487117572008-0'>
			<script type="text/javascript">
				googletag.cmd.push(function() { googletag.display('div-gpt-ad-1487117572008-0'); });
			</script>
		</div><?php
	endif;
}
add_action( 'dfp_sponsorship_tag', 'greatermedia_display_dfp_playersponsorship' );

function greatermedia_display_dfp_outofpage() {
	$network_id = trim( get_option( 'dfp_network_code' ) );
	if ( empty( $network_id ) ) {
		return;
	}

	$dfp_ad_interstitial = get_option( 'dfp_ad_interstitial' );
	if ( ! empty( $dfp_ad_interstitial ) ) :
		?><!-- /<?php echo esc_html( $network_id ); ?>/<?php echo esc_html( $dfp_ad_interstitial ); ?> -->
		<div id="div-gpt-ad-1484200509775-3" style="height:-1px; width:-1px;">
			<script type="text/javascript">
				googletag.cmd.push(function() { googletag.display('div-gpt-ad-1484200509775-3'); });
			</script>
		</div><?php
	endif;
}
add_action( 'wp_footer', 'greatermedia_display_dfp_outofpage', 1 );

function greatermedia_display_dfp_wallpaper() {
	$network_id = trim( get_option( 'dfp_network_code' ) );
	if ( empty( $network_id ) ) {
		return;
	}

	$dfp_ad_wallpaper = get_option( 'dfp_ad_wallpaper' );
	if ( $dfp_ad_wallpaper ) :
		?><div class="ad__wallpaper">
			<!-- /<?php echo esc_html( $network_id ); ?>/<?php echo esc_html( $dfp_ad_wallpaper ); ?> -->
			<div id='div-gpt-ad-1487289548015-0'>
				<script type="text/javascript">
					googletag.cmd.push(function() { googletag.display('div-gpt-ad-1487289548015-0'); });
				</script>
			</div>
		</div><?php
	endif;
}
add_action( 'dfp_wallpaper_tag', 'greatermedia_display_dfp_wallpaper' );

function greatermedia_display_dfp_incontent( $content ) {
	if ( ! is_single() || ! greatermedia_is_dfp_active() ) {
		return $content;
	}

	$parts = explode( '</p>', $content );
	$new_content = '';

	for ( $i = 0, $len = count( $parts ); $i < $len; $i++ ) {
		$new_content .= $parts[ $i ] . '</p>';

		// in-content pos1 slot after first 2 paragraphs
		if ( 1 == $i || $len < 2 ) {
			$new_content .= greatermedia_display_dfp_slot( 'dfp_ad_incontent_pos1', false, array(), false, 'ad__in-content ad__in-content--mobile' );
		}

		// in-content pos2 slot after 6th parapraph
		if ( 5 == $i && $len > 6 ) {
			$new_content .= greatermedia_display_dfp_slot( 'dfp_ad_incontent_pos2', false, array(), false, 'ad__in-content ad__in-content--mobile' );
		}
	}

	return $new_content;
}
add_filter( 'the_content', 'greatermedia_display_dfp_incontent', 1000 );

function greatermedia_register_dfp_widget() {
	register_widget( 'GreatermediaDfpWidget' );
}
add_action( 'widgets_init', 'greatermedia_register_dfp_widget' );

class GreatermediaDfpWidget extends \WP_Widget {

	public static $sizes = array(
		'mrec'      => '300x250',
		'half-page' => '300x600',
	);

	public function __construct() {
		$widget_ops = array( 'description' => 'Displays DFP slot.' );
		parent::__construct( 'gmr-dfp-widget', 'DFP widget', $widget_ops );
	}

	public function update( $new_instance, $old_instance ) {
		$new_instance['unit-code'] = sanitize_text_field( $new_instance['unit-code'] );
		$new_instance['sizes'] = sanitize_text_field( implode( ',', (array) $new_instance['sizes'] ) );
		$new_instance['pos'] = is_numeric( $new_instance['pos'] ) ? absint( $new_instance['pos'] ) : '';

		return $new_instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( $instance, array(
			'unit-code' => '',
			'sizes'     => '',
			'pos'       => '',
		) );

		$sizes = explode( ',', $instance['sizes'] );

		?><p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'unit-code' ) ); ?>">Unit Code:</label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'unit-code' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'unit-code' ) ); ?>" value="<?php echo esc_attr( $instance['unit-code'] ); ?>">
		</p>
		<p>
			<label>Sizes:</label><br>
			<?php foreach ( self::$sizes as $key => $label ) : ?>
				<label>
					<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'sizes[]' ) ); ?>" value="<?php echo esc_attr( $key ); ?>"<?php checked( in_array( $key, $sizes ) ); ?>>
					<?php echo esc_html( $label ); ?>
				</label><br>
			<?php endforeach; ?>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pos' ) ); ?>"><code>pos</code> value:</label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pos' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pos' ) ); ?>" value="<?php echo esc_attr( $instance['pos'] ); ?>">
		</p><?php
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, array(
			'unit-code' => '',
			'sizes'     => '',
			'pos'       => '',
		) );

		$sizes = array();
		foreach ( explode( ',', $instance['sizes'] ) as $size ) {
			if ( isset( self::$sizes[ $size ] ) ) {
				$sizes[] = array_map( 'intval', explode( 'x', self::$sizes[ $size ] ) );
			}
		}

		$targeting = array();
		if ( is_numeric( $instance['pos'] ) ) {
			$targeting[] = array( 'pos', absint( $instance['pos'] ) );
		}

		if ( ! empty( $instance['unit-code'] ) && ! empty( $sizes ) ) {
			echo $args['before_widget'];
				do_action( 'dfp_tag', $instance['unit-code'], $sizes, $targeting );
			echo $args['after_widget'];
		}
	}

}

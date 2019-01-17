<?php

$custom_logo_id = get_theme_mod( 'custom_logo' );
$colors = ee_get_css_colors();
$color = $colors['--brand-primary'];

?><div id="splash-screen">
	<style>
		html, body {
			overflow: hidden;
		}

		#splash-screen {
			position: fixed;
			left: 0;
			top: 0;
			right: 0;
			bottom: 0;
			background-color: #fff;
			z-index: 999999;
		}

		.splash-screen-logo {
			position: absolute;
			left: 50%;
			top: 50%;
			width: 250px;
			height: 250px;
			transform: translate(-50%, -50%);
			background-repeat: no-repeat;
			background-position: center;
			background-size: contain;
			<?php if ( $custom_logo_id ) : ?>
			background-image: url(<?php echo esc_url( bbgi_get_image_url( $custom_logo_id, 250, 250, 'crop', true ) ); ?>);
			<?php endif; ?>
		}

		.splash-screen-progress {
			bottom: 0;
			border-radius: 2px;
			height: 5px;
			left: 0;
			overflow-x: hidden;
			position: absolute;
			width: 100%;
		}

		.splash-screen-line {
			background: <?php echo esc_html( $color ); ?>;
			height: 5px;
			opacity: .4;
			position: absolute;
			width: 150%;
		}

		.splash-screen-subline {
			background: <?php echo esc_html( $color ); ?>;
			height: 5px;
			position: absolute;
		}

		.splash-screen-subline.-inc {
			animation: increase 2s infinite;
		}

		.splash-screen-subline.-dec {
			animation: decrease 2s 0.5s infinite;
		}

		@keyframes increase {
			from { left: -5%; width: 5%; }
			to { left: 130%; width: 100%;}
		}
		@keyframes decrease {
			from { left: -80%; width: 80%; }
			to { left: 110%; width: 10%;}
		}
	</style>
	<div class="splash-screen-logo">
		<div class="splash-screen-progress">
			<div class="splash-screen-line"></div>
			<div class="splash-screen-subline -inc"></div>
			<div class="splash-screen-subline -dec"></div>
		</div>
	</div>
</div>

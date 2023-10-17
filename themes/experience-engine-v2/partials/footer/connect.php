<div class="connect">
	<h6>Connect</h6><?php

	if ( has_nav_menu( 'connect-nav' ) ) :
		wp_nav_menu( array( 'theme_location' => 'connect-nav' ) );
	endif;

	?>
	<ul class="social">
		<?php if ( ee_has_publisher_information( 'facebook' ) ) : ?>
			<li>
				<a href="<?php echo esc_url( ee_get_publisher_information( 'facebook' ) ); ?>" aria-label="Go to station's Facebook page" target="_blank" rel="noopener">
					<svg width="10" height="20" fill="none" xmlns="http://www.w3.org/2000/svg"><title>Facebook</title><path d="M6.12 19.428H2.448V9.714H0V6.366h2.449l-.004-1.973C2.445 1.662 3.19 0 6.435 0h2.7v3.348H7.449c-1.263 0-1.324.468-1.324 1.342l-.005 1.675h3.036l-.358 3.348-2.675.001-.003 9.714z"/></svg>
				</a>
			</li>
		<?php endif; ?>

		<?php if ( ee_has_publisher_information( 'twitter' ) ) : ?>
			<li>
				<a href="<?php echo esc_url( ee_get_publisher_information( 'twitter' ) ); ?>" aria-label="Go to station's Twitter page" target="_blank" rel="noopener">
					<svg xmlns="http://www.w3.org/2000/svg" width="21" height="18" fill="none" viewBox="0 0 1200 1227">
						<title>Twitter</title>
						<path d="M714.163 519.284L1160.89 0H1055.03L667.137 450.887L357.328 0H0L468.492 681.821L0 1226.37H105.866L515.491 750.218L842.672 1226.37H1200L714.137 519.284H714.163ZM569.165 687.828L521.697 619.934L144.011 79.6944H306.615L611.412 515.685L658.88 583.579L1055.08 1150.3H892.476L569.165 687.854V687.828Z"/>
					</svg>
				</a>
			</li>
		<?php endif; ?>

		<?php if ( ee_has_publisher_information( 'instagram' ) ) : ?>
			<li>
				<a href="<?php echo esc_url( ee_get_publisher_information( 'instagram' ) ); ?>" aria-label="Go to station's Instagram page" target="_blank" rel="noopener">
					<svg width="17" height="18" fill="none" xmlns="http://www.w3.org/2000/svg"><title>Instagram</title><path d="M15.3.976H1.7c-.935 0-1.7.765-1.7 1.7v13.6c0 .935.765 1.7 1.7 1.7h13.6c.935 0 1.7-.765 1.7-1.7v-13.6c0-.935-.765-1.7-1.7-1.7zm-6.8 5.1c1.87 0 3.4 1.53 3.4 3.4 0 1.87-1.53 3.4-3.4 3.4a3.41 3.41 0 0 1-3.4-3.4c0-1.87 1.53-3.4 3.4-3.4zm-6.375 10.2c-.255 0-.425-.17-.425-.425V8.626h1.785c-.085.255-.085.595-.085.85 0 2.805 2.295 5.1 5.1 5.1 2.805 0 5.1-2.295 5.1-5.1 0-.255 0-.595-.085-.85H15.3v7.225c0 .255-.17.425-.425.425H2.125zM15.3 4.8c0 .255-.17.425-.425.425h-1.7c-.255 0-.425-.17-.425-.425V3.1c0-.255.17-.425.425-.425h1.7c.255 0 .425.17.425.425v1.7z"/></svg>
				</a>
			</li>
		<?php endif; ?>

		<?php if ( ee_has_publisher_information( 'youtube' ) ) : ?>
			<li>
				<a href="<?php echo esc_url( ee_get_publisher_information( 'youtube' ) ); ?>" aria-label="Go to station's Youtube page" target="_blank" rel="noopener">
					<svg width="20" height="16" fill="none" xmlns="http://www.w3.org/2000/svg"><title>Youtube</title><path d="M19.22 2.184C18.5 1.326 17.167.976 14.62.976H5.38C2.776.976 1.42 1.348.7 2.262 0 3.152 0 4.465 0 6.282v3.462c0 3.52.832 5.307 5.38 5.307h9.24c2.208 0 3.43-.31 4.222-1.066.812-.777 1.158-2.045 1.158-4.24V6.281c0-1.916-.054-3.236-.78-4.098zM12.84 8.49l-4.196 2.193a.644.644 0 0 1-.944-.572V5.741a.645.645 0 0 1 .943-.573l4.196 2.179a.645.645 0 0 1 .001 1.144z"/></svg>
				</a>
			</li>
		<?php endif; ?>
	</ul>
</div>

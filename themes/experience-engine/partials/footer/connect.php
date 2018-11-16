<div class="connect">
	<h6>Connect</h6><?php

	if ( has_nav_menu( 'connect-nav' ) ) :
		wp_nav_menu( array( 'theme_location' => 'connect-nav' ) );
	endif;

	?>
	<ul class="social">
		<?php if ( ee_has_publisher_information( 'facebook' ) ) : ?>
			<li>
				<a href="<?php echo esc_url( ee_get_publisher_information( 'facebook' ) ); ?>" aria-label="Go to station's Facebook page" target="_blank" rel="noopener noreferrer">
					<svg width="10" height="20" fill="none" xmlns="http://www.w3.org/2000/svg"><title>Facebook</title><path d="M6.12 19.428H2.448V9.714H0V6.366h2.449l-.004-1.973C2.445 1.662 3.19 0 6.435 0h2.7v3.348H7.449c-1.263 0-1.324.468-1.324 1.342l-.005 1.675h3.036l-.358 3.348-2.675.001-.003 9.714z"/></svg>
				</a>
			</li>
		<?php endif; ?>

		<?php if ( ee_has_publisher_information( 'twitter' ) ) : ?>
			<li>
				<a href="<?php echo esc_url( ee_get_publisher_information( 'twitter' ) ); ?>" aria-label="Go to station's Twitter page" target="_blank" rel="noopener noreferrer">
					<svg width="21" height="18" fill="none" xmlns="http://www.w3.org/2000/svg"><title>Twitter</title><path d="M20.13 2.896a8.31 8.31 0 0 1-2.372.645 4.115 4.115 0 0 0 1.816-2.266c-.798.47-1.682.81-2.623.994A4.14 4.14 0 0 0 13.937.976c-2.281 0-4.13 1.833-4.13 4.095 0 .322.036.634.107.934A11.757 11.757 0 0 1 1.4 1.725a4.051 4.051 0 0 0-.559 2.06c0 1.42.73 2.674 1.838 3.409A4.139 4.139 0 0 1 .809 6.68v.052c0 1.985 1.423 3.64 3.312 4.016a4.172 4.172 0 0 1-1.865.07 4.13 4.13 0 0 0 3.858 2.845 8.33 8.33 0 0 1-5.129 1.754c-.333 0-.662-.02-.985-.058a11.758 11.758 0 0 0 6.33 1.84c7.597 0 11.75-6.24 11.75-11.654 0-.177-.003-.354-.011-.53a8.352 8.352 0 0 0 2.06-2.12z"/></svg>
				</a>
			</li>
		<?php endif; ?>

		<?php if ( ee_has_publisher_information( 'instagram' ) ) : ?>
			<li>
				<a href="<?php echo esc_url( ee_get_publisher_information( 'instagram' ) ); ?>" aria-label="Go to station's Instagram page" target="_blank" rel="noopener noreferrer">
					<svg width="17" height="18" fill="none" xmlns="http://www.w3.org/2000/svg"><title>Instagram</title><path d="M15.3.976H1.7c-.935 0-1.7.765-1.7 1.7v13.6c0 .935.765 1.7 1.7 1.7h13.6c.935 0 1.7-.765 1.7-1.7v-13.6c0-.935-.765-1.7-1.7-1.7zm-6.8 5.1c1.87 0 3.4 1.53 3.4 3.4 0 1.87-1.53 3.4-3.4 3.4a3.41 3.41 0 0 1-3.4-3.4c0-1.87 1.53-3.4 3.4-3.4zm-6.375 10.2c-.255 0-.425-.17-.425-.425V8.626h1.785c-.085.255-.085.595-.085.85 0 2.805 2.295 5.1 5.1 5.1 2.805 0 5.1-2.295 5.1-5.1 0-.255 0-.595-.085-.85H15.3v7.225c0 .255-.17.425-.425.425H2.125zM15.3 4.8c0 .255-.17.425-.425.425h-1.7c-.255 0-.425-.17-.425-.425V3.1c0-.255.17-.425.425-.425h1.7c.255 0 .425.17.425.425v1.7z"/></svg>
				</a>
			</li>
		<?php endif; ?>

		<?php if ( ee_has_publisher_information( 'youtube' ) ) : ?>
			<li>
				<a href="<?php echo esc_url( ee_get_publisher_information( 'youtube' ) ); ?>" aria-label="Go to station's Youtube page" target="_blank" rel="noopener noreferrer">
					<svg width="20" height="16" fill="none" xmlns="http://www.w3.org/2000/svg"><title>Youtube</title><path d="M19.22 2.184C18.5 1.326 17.167.976 14.62.976H5.38C2.776.976 1.42 1.348.7 2.262 0 3.152 0 4.465 0 6.282v3.462c0 3.52.832 5.307 5.38 5.307h9.24c2.208 0 3.43-.31 4.222-1.066.812-.777 1.158-2.045 1.158-4.24V6.281c0-1.916-.054-3.236-.78-4.098zM12.84 8.49l-4.196 2.193a.644.644 0 0 1-.944-.572V5.741a.645.645 0 0 1 .943-.573l4.196 2.179a.645.645 0 0 1 .001 1.144z"/></svg>
				</a>
			</li>
		<?php endif; ?>
	</ul>
</div>

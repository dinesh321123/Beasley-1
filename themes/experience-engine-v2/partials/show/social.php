<?php

$show = ee_get_current_show();
if ( ! $show ) :
	return;
endif;

$facebook_url = ee_get_show_meta( $show, 'facebook' );
$twitter_url = ee_get_show_meta( $show, 'twitter' );
$instagram_url = ee_get_show_meta( $show, 'instagram' );
$google_url = ee_get_show_meta( $show, 'google' );

if ( ! $facebook_url && ! $twitter_url && ! $instagram_url && ! $google_url ) :
	return;
endif;

?><div class="social">
	<p>Social</p>
	<?php if ( $facebook_url ) : ?>
		<a href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener" aria-label="Visit this show's Facebook page">
			<svg xmlns="http://www.w3.org/2000/svg" width="8" height="17">
				<path d="M4.78 16.224H1.911v-7.65H0V5.938l1.912-.001-.003-1.553c0-2.151.583-3.46 3.117-3.46h2.11v2.637H5.816c-.987 0-1.034.368-1.034 1.056l-.004 1.32H7.15l-.28 2.636H4.781l-.002 7.65z"/>
			</svg>
		</a>
	<?php endif; ?>

	<?php if ( $twitter_url ) : ?>
		<a href="<?php echo esc_url( $twitter_url ); ?>" target="_blank" rel="noopener" aria-label="Visit this show's Twitter page">
			<svg xmlns="http://www.w3.org/2000/svg" fill="#000000" viewBox="0 0 1200 1227" width="16" height="14">
<path d="M714.163 519.284L1160.89 0H1055.03L667.137 450.887L357.328 0H0L468.492 681.821L0 1226.37H105.866L515.491 750.218L842.672 1226.37H1200L714.137 519.284H714.163ZM569.165 687.828L521.697 619.934L144.011 79.6944H306.615L611.412 515.685L658.88 583.579L1055.08 1150.3H892.476L569.165 687.854V687.828Z"/>
</svg>
		</a>
	<?php endif; ?>

	<?php if ( $instagram_url ) : ?>
		<a href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener" aria-label="Visit this show's Instagram page">
		<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16">
			<path d="M11.585 0h-7.17C1.981 0 0 1.898 0 4.231v6.87c0 2.334 1.98 4.232 4.415 4.232h7.17c2.434 0 4.415-1.898 4.415-4.231v-6.87C16 1.897 14.02 0 11.585 0zm2.995 11.102c0 1.583-1.344 2.87-2.995 2.87h-7.17c-1.652.001-2.995-1.287-2.995-2.87v-6.87c0-1.584 1.343-2.872 2.995-2.872h7.17c1.651 0 2.995 1.288 2.995 2.871v6.87z"/>
			<path d="M8.002 3.715c-2.274 0-4.123 1.772-4.123 3.95 0 2.18 1.85 3.952 4.123 3.952s4.122-1.773 4.122-3.951c0-2.179-1.849-3.951-4.122-3.951zm0 6.541c-1.49 0-2.704-1.162-2.704-2.59 0-1.429 1.213-2.59 2.704-2.59 1.49 0 2.703 1.161 2.703 2.59 0 1.428-1.213 2.59-2.703 2.59zM12.299 2.563c-.274 0-.542.106-.736.292a.982.982 0 0 0-.305.705c0 .262.112.52.306.706a1.073 1.073 0 0 0 1.471 0 .984.984 0 0 0 .305-.706c0-.263-.11-.52-.305-.705a1.07 1.07 0 0 0-.736-.292z"/>
		</svg>

		</a>
	<?php endif; ?>

	<?php if ( $google_url ) : ?>
		<a href="<?php echo esc_url( $google_url ); ?>" target="_blank" rel="noopener">Google+</a>
	<?php endif; ?>
</div>

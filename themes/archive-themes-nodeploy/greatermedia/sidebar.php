<?php
/**
 * The default sidebar
 *
 * @package Greater Media
 * @since   0.1.0
 */

if ( greatermedia_is_jacapps() ) {
	return;
}
?>

<aside class="sidebar">
	<?php dynamic_sidebar( 'liveplayer_sidebar' ); ?>
</aside>
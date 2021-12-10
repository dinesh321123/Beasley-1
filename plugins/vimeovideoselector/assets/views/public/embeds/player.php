<?php

/**
 * Playlist Float Player Shortcode view/template.
 * Shortcode Represenation: [vimeovideoselector key="XXXXX"]
 *
 * @author Vimeo Video
 * @copyright Vimeo Video <https://www.vvs.com>
 * @package VimeoVideoSelector
 * @version 1.0.1.2
 */
?>
<?php
	$iFrameWidth	= isset($width) && $width != "" ? $width : "100%";
	$iFrameHeight	= isset($height) && $height != "auto" ? $height : "350px";
?>

<iframe src="https://player.vimeo.com/video/<?php echo $key; ?>" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen="" style="height:<?php echo $iFrameHeight; ?>; width:<?php echo $iFrameWidth; ?>" ></iframe>

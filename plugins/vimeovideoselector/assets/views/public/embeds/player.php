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

<iframe src="https://player.vimeo.com/video/<?php echo $key; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen=""></iframe>

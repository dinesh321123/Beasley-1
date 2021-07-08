<?php

/**
 * Single Video Float Player Shortcode view/template.
 * Shortcode Represenation: [sendtonews type="float" key="XXX-XXX-XXX"]
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.1
 */
?>
<div class="s2nPlayer k-<?php echo $key; ?>" data-type="float"></div>
<script type="text/javascript" src="//embed.sendtonews.com/player3/embedcode.js?SC=<?php echo $key; ?>&cid=<?php echo $cid; ?>&autoplay=on" data-type="s2nScript"></script>

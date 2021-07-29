<?php

/**
 * Single Video Responsive Player Shortcode view/template.
 * Shortcode Represenation: [sendtonews type="single" key="XXX-XXX-XXX"]
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.2
 */ 
?>
<div class="s2nPlayer-<?php echo $key; ?>" data-type="single"></div>
<script type="text/javascript" src="//embed.sendtonews.com/player2/embedcode.php?SC=<?php echo $key; ?>&cid=<?php echo $cid; ?>&autoplay=on" data-type="s2nScript"></script>

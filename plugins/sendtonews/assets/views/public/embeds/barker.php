<?php

/**
 * Barker Playlist Player Shortcode view/template.
 * Shortcode Represenation: [sendtonews type="barker" key="XXXXXX"]
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.1.2
 */ 
?>
<div class="s2nPlayer-<?php echo $key; ?>" data-type="barker"></div>
<script type="text/javascript" src="//embed.sendtonews.com/player2/embedcode.php?fk=<?php echo $key; ?>&cid=<?php echo $cid; ?>" data-type="s2nScript"></script>

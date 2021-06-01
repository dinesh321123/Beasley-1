<?php

/**
 * Playlist AMP Player Shortcode view/template.
 * Shortcode Represenation: [sendtonews type="amplist" key="XXX-XXX"]
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.0
 */ 
?>
<amp-iframe width="640" height="360" sandbox="allow-scripts allow-popups allow-same-origin" layout="responsive" frameborder="0" resizable src="//embed.sendtonews.com/amp/?v=2&fk=<?php echo $key; ?>&cid=<?php echo $cid; ?>"><amp-img overflow layout="fill" src="//cdnmedia.sendtonews.com/players/library/placeholder.png"></amp-img><amp-img placeholder layout="fill" src="//cdnmedia.sendtonews.com/players/library/placeholder.png"></amp-img></amp-iframe>

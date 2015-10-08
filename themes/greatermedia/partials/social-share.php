<?php

$encoded_title = urlencode( get_the_title() );
$encoded_url = urlencode( get_permalink() );

?>

<a class="icon-facebook social__link popup" target='_blank' href="http://www.facebook.com/sharer/sharer.php?u=<?php echo $encoded_url; ?>&title=<?php echo $encoded_title; ?>"></a>
<a class="icon-twitter social__link popup" target='_blank' href="http://twitter.com/home?status=<?php echo $encoded_title; ?>+<?php echo $encoded_url; ?>"></a>
<a class="icon-google-plus social__link popup" target='_blank' href="https://plus.google.com/share?url=<?php echo $encoded_url; ?>"></a>
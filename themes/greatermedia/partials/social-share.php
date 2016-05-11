<?php
$encoded_title = urlencode( get_the_title() );
$encoded_url = urlencode( get_permalink() );

if ( class_exists( 'WPSEO_OpenGraph' ) && class_exists( 'WPSEO_Meta' ) ){
  // Use Yoast SEO to dictate title of page
  $facebook_encoded_title = urlencode( WPSEO_OpenGraph::og_title( false ) );

  $twitter_encoded_title = urlencode( WPSEO_Meta::get_value( 'twitter-title' ) );
  if ( ! is_string( $twitter_encoded_title ) || '' === $twitter_encoded_title ) {
    $twitter_encoded_title = $encoded_title;
  }
}

?>

<a class="icon-facebook social__link popup" target='_blank' href="http://www.facebook.com/sharer/sharer.php?u=<?php echo $encoded_url; ?>&title=<?php echo ( $facebook_encoded_title ? $facebook_encoded_title : $encoded_title ); ?>"></a>
<a class="icon-twitter social__link popup" target='_blank' href="http://twitter.com/home?status=<?php echo ( $twitter_encoded_title ? $twitter_encoded_title : $encoded_title ); ?>+<?php echo $encoded_url; ?>"></a>
<a class="icon-google-plus social__link popup" target='_blank' href="https://plus.google.com/share?url=<?php echo $encoded_url; ?>"></a>

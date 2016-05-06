<?php
namespace GreaterMedia\HomepageCuration\KeepOffHomepage;

wp_nonce_field( NONCE_STRING, NONCE_NAME );
?>
<p><label for="<?php echo esc_attr( META_KEY ); ?>"><input type="checkbox" name="<?php echo esc_attr( META_KEY ); ?>" id="<?php echo esc_attr( META_KEY ); ?>" value="1" <?php checked( get_post_meta( get_the_ID(), META_KEY, true ) ); ?>> Keep Off Homepage</label></p>

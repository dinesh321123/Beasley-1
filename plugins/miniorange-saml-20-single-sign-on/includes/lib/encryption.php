<?php


class AESEncryption
{
    public static function encrypt_data($K9, $Z1)
    {
        $Z1 = openssl_digest($Z1, "\163\150\141\62\65\x36");
        $tO = "\x61\145\163\x2d\61\62\70\x2d\145\x63\x62";
        $wo = openssl_encrypt($K9, $tO, $Z1, OPENSSL_RAW_DATA || OPENSSL_ZERO_PADDING);
        return base64_encode($wo);
    }
    public static function decrypt_data($K9, $Z1)
    {
        $nY = base64_decode($K9);
        $Z1 = openssl_digest($Z1, "\x73\x68\141\x32\x35\x36");
        $tO = "\101\x45\x53\x2d\61\x32\70\55\x45\x43\x42";
        $Tx = openssl_cipher_iv_length($tO);
        $Ie = substr($nY, 0, $Tx);
        $K9 = substr($nY, $Tx);
        $uX = openssl_decrypt($K9, $tO, $Z1, OPENSSL_RAW_DATA || OPENSSL_ZERO_PADDING, $Ie);
        return $uX;
    }
}
?>

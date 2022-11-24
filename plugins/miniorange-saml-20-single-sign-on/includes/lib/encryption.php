<?php


class AESEncryption
{
    public static function encrypt_data($Tb, $XC)
    {
        $XC = openssl_digest($XC, "\163\x68\141\62\65\66");
        $zd = "\141\145\x73\x2d\x31\62\70\55\145\x63\x62";
        $pc = openssl_encrypt($Tb, $zd, $XC, OPENSSL_RAW_DATA || OPENSSL_ZERO_PADDING);
        return base64_encode($pc);
    }
    public static function decrypt_data($Tb, $XC)
    {
        $wk = base64_decode($Tb);
        $XC = openssl_digest($XC, "\x73\150\141\62\x35\x36");
        $zd = "\101\105\x53\55\x31\x32\x38\55\105\x43\102";
        $TB = openssl_cipher_iv_length($zd);
        $p2 = substr($wk, 0, $TB);
        $Tb = substr($wk, $TB);
        $wi = openssl_decrypt($Tb, $zd, $XC, OPENSSL_RAW_DATA || OPENSSL_ZERO_PADDING, $p2);
        return $wi;
    }
}

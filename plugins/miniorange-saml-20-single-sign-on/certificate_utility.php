<?php


class CertificateUtility
{
    public static function generate_certificate($yl, $GM, $Eq)
    {
        $JU = openssl_pkey_new();
        $Mq = openssl_csr_new($yl, $JU, $GM);
        $B6 = openssl_csr_sign($Mq, null, $JU, $Eq, $GM, time());
        openssl_csr_export($Mq, $tz);
        openssl_x509_export($B6, $OX);
        openssl_pkey_export($JU, $X5);
        uLk:
        if (!(($ZE = openssl_error_string()) !== false)) {
            goto Ism;
        }
        error_log("\x43\145\x72\x74\x69\146\151\143\141\x74\x65\x55\x74\151\154\x69\x74\171\72\40\105\x72\162\x6f\162\40\147\145\x6e\x65\162\141\x74\x69\x6e\x67\40\x63\145\162\x74\x69\x66\151\143\x61\x74\145\56\x20" . $ZE);
        goto uLk;
        Ism:
        $zK = array("\160\165\142\154\151\143\x5f\x6b\145\x79" => $OX, "\160\x72\151\166\x61\164\x65\137\x6b\x65\x79" => $X5);
        return $zK;
    }
}

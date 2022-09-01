<?php


class CertificateUtility
{
    public static function generate_certificate($ez, $SS, $Pi)
    {
        $dA = openssl_pkey_new();
        $SP = openssl_csr_new($ez, $dA, $SS);
        $SU = openssl_csr_sign($SP, null, $dA, $Pi, $SS, time());
        openssl_csr_export($SP, $Px);
        openssl_x509_export($SU, $DX);
        openssl_pkey_export($dA, $cP);
        eN:
        if (!(($wD = openssl_error_string()) !== false)) {
            goto TI;
        }
        error_log("\x43\145\x72\x74\x69\146\151\143\141\x74\145\125\x74\x69\154\x69\164\171\72\40\x45\x72\x72\x6f\x72\x20\147\145\x6e\145\x72\141\x74\151\156\x67\40\143\145\162\x74\151\146\151\x63\141\164\x65\56\40" . $wD);
        goto eN;
        TI:
        $re = array("\x70\x75\142\x6c\151\143\137\153\x65\x79" => $DX, "\160\x72\x69\x76\141\x74\x65\x5f\x6b\x65\x79" => $cP);
        return $re;
    }
}
